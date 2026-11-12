<?php
    require 'database/db_connect.php'; 
    require 'veikt_rezervaciju.php';
?>
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Burgernīca - ļoti gardi burgeri</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="script.js" defer></script>
</head>
<body>

    <header>
        <div class="menu-title">
            <div class="hamburgerMenuClass">
                <!-- Hamburger -->
                <button class="hamburger-btn" id="menu-toggle" aria-label="Atvērt izvēlni">
                    <i class="fa-solid fa-bars"></i>
                </button>
            </div>
            <h1><a href="#">Bibliotēka</a></h1>
        </div>
        <nav>
            <a href="#">Sākums</a>
            <a href="#jaunumi" class="navPoga">Jaunumi</a>
            <a href="#edienkarte">Grāmatas</a>
            <a href="#kontakti">Kontakti</a>
        </nav>
        <button class="cart-btn" onclick="atvertGrozu()">Grozs (<span id="cart-count-btn">0</span>)</button>
    </header>

    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <!-- Hamburger poga tikai sidebar -->
            <button class="sidebar-close-btn" id="menu-close" aria-label="Aizvērt izvēlni">
                <i class="fa-solid fa-bars"></i>
            </button>
            <h2><a href="#">Burgernīca</a></h2>
        </div>
        <a href="#" class="navPoga">Sākums</a>
        <a href="#jaunumi" class="navPoga">Jaunumi</a>
        <a href="#edienkarte" class="navPoga">Grāmatas</a>
        <a href="#kontakti" class="navPoga">Kontakti</a>
    </div>

    <?php if(isset($_SESSION['gatavs'])): ?>
        <!-- Attiesta sesiju, lai pēc lapas atjauninašanas nerādītos pasūtīšanas apstiprinājums atkārtoti -->
        <?php unset($_SESSION['gatavs']); ?>
        <div class="success-overlay" id="successPopup">
            <div class="success-box">
                <h2>Rezervācija pieņemta!</h2>
                <p>Paldies! Jūsu rezervācija ir veiksmīgi saņemta. Mēs ar jums sazināsimies!</p>
                <button onclick="document.getElementById('successPopup').style.display='none'">Labi</button>
            </div>
        </div>
    <?php endif; ?>

    <?php if(isset($_SESSION['kluda'])): ?>
        <div class="success-overlay" id="errorPopup">
            <div class="success-box error">
                <h2>Kļūda!</h2>
                <p>
                    <?php
                        if(isset($_SESSION['kludas_zinojums'])){
                            echo $_SESSION['kludas_zinojums'];
                        } else{
                            echo "Veicot rezervāciju radās kļūda. Lūdzu mēģiniet vēlreiz!";
                        }
                    ?>
                </p>
                <button onclick="document.getElementById('errorPopup').style.display='none'">Labi</button>
            </div>
        </div>
        <?php
            unset($_SESSION['kluda']);
            unset($_SESSION['kludas_zinojums']);
        ?>
    <?php endif; ?>

    <section class="hero" id="sakums">
        <h2>Grāmatas ik vienam</h2>
        <p>Vai meklē nākamo aizraujošo lasāmvielu vai klusu vietu mācībām? Nāc un atklāj mūsu piedāvājumu!</p>
    </section>

    <section class="books-section" id="jaunumi">
        <h2>Jaunākās grāmatas</h2>
        <div class="books-grid">
            <?php
            $query = "SELECT * FROM gramatas WHERE statuss!='dzēsts' ORDER BY id DESC LIMIT 6";
            $result = mysqli_query($conn, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $attels = 'img/' . $row['bilde'];
                    ?>
                        <div class="book-card">
                            <div class="book-image">
                                <img src="<?=$attels ?>" alt="<?= htmlspecialchars($row['nosaukums'])?>">
                            </div>
                            <div class="book-info">
                                <h3><?=htmlspecialchars($row['nosaukums']);?></h3>
                                <p class="author"><?=htmlspecialchars($row['autors']);?></p>
                            </div>
                            <div class="add-book">
                                <?php if ($row['statuss'] == 'pieejams'): ?> 
                                    <button class="add-to-cart" data-id="<?= $row['id']?>" data-nosaukums="<?= htmlspecialchars($row['nosaukums'], ENT_QUOTES);?>" onclick="pievienotGrozam(this)">+ Grozā</button>
                                <?php elseif ($row['statuss'] == 'rezervēts'): ?>
                                    <button class="add-to-cart disabled">Rezervēts</button>
                                <?php else: ?>
                                    <button class="add-to-cart disabled">Nav pieejama</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                }
            } else {
                echo "<p>Radusies problēma ar grāmatu ielādi. Lūdzu, ienāciet vēlāk!</p>";
            }
            ?>
        </div>
    </section>

    <section class="books-section" id="edienkarte">
        <h2>Visas grāmatas</h2>
        <div class="books-grid">
            <?php // kārto sākuma pieejamās, tad rezervētās, tad pārējās
            $query = "SELECT * FROM gramatas WHERE statuss!='dzēsts' ORDER BY (statuss='pieejams') DESC, (statuss='rezervēts') DESC, id DESC";
            $result = mysqli_query($conn, $query);
            
            if ($result && mysqli_num_rows($result) > 0) {
                while($row = mysqli_fetch_assoc($result)) {
                    $attels = 'img/' . $row['bilde'];
                    ?>
                        <div class="book-card">
                            <div class="book-image">
                                <img src="<?=$attels ?>" alt="<?= htmlspecialchars($row['nosaukums'])?>">
                            </div>
                            <div class="book-info">
                                <h3><?=htmlspecialchars($row['nosaukums']);?></h3>
                                <p class="author"><?=htmlspecialchars($row['autors']);?></p>
                            </div>
                            <div class="add-book">
                                <?php if ($row['statuss'] == 'pieejams'): ?> 
                                    <button class="add-to-cart" data-id="<?= $row['id']?>" data-nosaukums="<?= htmlspecialchars($row['nosaukums'], ENT_QUOTES);?>" onclick="pievienotGrozam(this)">+ Grozā</button>
                                <?php elseif ($row['statuss'] == 'rezervēts'): ?>
                                    <button class="add-to-cart disabled">Rezervēts</button>
                                <?php else: ?>
                                    <button class="add-to-cart disabled">Nav pieejama</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php
                }
            } else {
                echo "<p>Radusies problēma ar grāmatu ielādi. Lūdzu, ienāciet vēlāk!</p>";
            }
            ?>
        </div>
    </section>

    <section class="contact-section" id="kontakti">
        <div class="contact-info">
            <h2>Kontakti</h2>
            <p>Bibliotēka ir atvērta visiem gandrīz katru dienu, tāpēc droši nāc un izmantot mūsu pakalpojumus! Darbinieki vienmēr ir gatavi palīdzēt!</p>
            <br>
            <p><strong>Adrese:</strong><br>Grāmatu iela 1, Liepāja</p>
            <p><strong>Darba laiks:</strong><br>Pirmdiena - Piektdiena: 10:00 - 20:00<br>Sestdiena: 11:00 - 19:00<br>Svētdiena: brīvs</p>
            <p><strong>Telefons:</strong><br>+371 20000000</p>
            <p><strong>E-pasts:</strong><br>info@biblioteka.lv</p>
        </div>
        <div class="contact-map">
            <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m12!1m3!1d3702.2297088563864!2d21.01746762663211!3d56.51324793121215!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!5e0!3m2!1slv!2slv!4v1788280992863!5m2!1slv!2slv" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    </section>

    <div class="footer">
        Bibliotēka &copy; 2026
    </div>

    <div class="modal-overlay" id="cart-modal">
        <div class="modal-content">
            <button class="close-btn" onclick="aizvertGrozu()">&times;</button>
            
            <div id="cart-items-view">
                <h2>Jūsu grozs</h2>
                <div id="cart-items-list" style="margin: 20px 0;"></div>
                
                <div class="modal-footer">
                    <button class="add-to-cart" style="width: 100%; padding: 15px; font-size: 1.1rem;" onclick="parietUzNoformesanu()">Tālāk uz rezervēšanu</button>
                </div>
            </div>

            <div id="checkout-form-view">
                <h2>Noformēt rezervēšanu</h2>
                <button onclick="atpakalUzGrozu()" style="margin-bottom: 15px; font-size: 1rem; background: none; border: none; color: var(--primary); cursor: pointer;">&larr; Atpakaļ uz grozu</button>
                
                <form method="POST" action="" class="order-form-container" style="max-width: 100%;">
                    <input type="text" name="vards" placeholder="Jūsu vārds" required>
                    <input type="text" name="uzvards" placeholder="Jūsu uzvārds" required>
                    <input type="text" name="epasts" placeholder="Jūsu e-pasts" required>
                    <input type="hidden" name="groza_dati" id="hidden_cart_data">
                    
                    <button type="submit" name="veikt_rezervaciju">Apstiprināt rezervāciju</button>
                </form>
            </div>

        </div>
    </div>

    <button id="uz-augsu-btn" onclick="uzAugsu()" title="Uz augšu">
        <i class="fa-solid fa-arrow-up"></i>
    </button>
</body>
</html>