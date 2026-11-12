<?php
    if(!empty($_GET['session_id'])){
        session_start();
        require_once "config.php";
        require "../database/db_connect.php";

        try{
            $checkout_session = \Stripe\Checkout\Session::retrieve($_GET['session_id']);
            // $customer_email = $checkout_session->customer_details->email;

            $paymentIntent = \Stripe\PaymentIntent::retrieve($checkout_session->payment_intent);

            if($paymentIntent->status === 'succeeded'){
                $metadata = $checkout_session->metadata;

                $vards = $metadata->vards;
                $telefons = $metadata->telefons;
                $adrese = $metadata->adrese;
                $pasutijuma_vienibas = json_decode($metadata->pasutijuma_vienibas, true);

                $conn->begin_transaction();
                $gatavs = true;


                // $transaction_id = $paymentIntent->id;

                // Pievieno klientu
                $klients = $conn->prepare("INSERT INTO klienti (vards, telefons, adrese) VALUES (?, ?, ?)");
                $klients->bind_param("sss", $vards, $telefons, $adrese);
                if(!$klients->execute()) $gatavs = false;
                $klienta_id = $conn->insert_id;
                $klients->close();
                
                // Ja iepriekšējais solis nogāja greizi, tad šis pat nesāksies - datu drošības dēļ, lai visi ieraksti parādās bāzē
                if($gatavs) {
                    // Rēķina summu čekā
                    $kopsumma = 0;
                    foreach($pasutijuma_vienibas as $prece) {
                        $kopsumma += $prece['cena'] *$prece['skaits'];
                    }
                    
                    // $datums = date("Y-m-d H:i:s");
                    // Pievieno pasūtījumu
                    $pasutijums = $conn->prepare("INSERT INTO pasutijumi (klienta_id, kopsumma, statuss) VALUES (?, ?, 'Jauns')");
                    $pasutijums->bind_param("is", $klienta_id, $kopsumma);
                    if(!$pasutijums->execute()) $gatavs = false;
                    $pasutijuma_id = $conn->insert_id;
                    $pasutijums->close();
                }

                // Ja iepriekšējie soļi nogāja greizi, tad šis pat nesāksies
                if($gatavs){
                    // Pievieno pasūtījuma vienības
                    $pas_vieniba = $conn->prepare("INSERT INTO pasutijuma_vienibas (pasutijuma_id, ediena_id, skaits) VALUES (?, ?, ?)");
                    foreach($pasutijuma_vienibas as $prece) {
                        $ediena_id = $prece['ediena_id'];
                        $skaits = $prece['skaits'];
                        $pas_vieniba->bind_param("iii", $pasutijuma_id, $ediena_id, $skaits);
                        if(!$pas_vieniba->execute()) $gatavs = false;
                    }
                    $pas_vieniba->close();
                }

                // Gala pārbaude, kas paziņo klientam par pasūtījuma statusu
                if($gatavs){
                    $conn->commit();
                    $_SESSION['gatavs'] = true;
                } else{
                    $conn->rollback();
                    $_SESSION['kluda'] = true;
                }

            }else{ 
                // $_SESSION['pazinojums'] = "Problēmas ar maksājuma apstrādi!";
                $_SESSION['kluda'] = true;
            }
        }catch(Exception $e){
            // $_SESSION['pazinojums'] = "Nav iespējams iegūt maksājuma informāciju: ".$e->getMessage();
            $_SESSION['kluda'] = true;
        }
    }

    header("Location: ../index.php");
    exit();
?>
