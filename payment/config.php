<?php
    require_once __DIR__ . '/../vendor/autoload.php';

    $envFile = __DIR__ . '/../.env';

    // Lasa env failu - stripe secret key
    if (file_exists($envFile)) {
        $env = parse_ini_file($envFile);
        $stripe_secret_key = $env['STRIPE_SECRET_KEY'] ?? '';
    } else {
        $stripe_secret_key = '';
    }

    \Stripe\Stripe::setApiKey($stripe_secret_key);
?>