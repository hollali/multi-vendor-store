<?php
$appUrl = getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'http://localhost:8000');
$pubKey = getenv('PAYSTACK_PUBLIC_KEY') ?: ($_ENV['PAYSTACK_PUBLIC_KEY'] ?? $_SERVER['PAYSTACK_PUBLIC_KEY'] ?? '');
$secKey = getenv('PAYSTACK_SECRET_KEY') ?: ($_ENV['PAYSTACK_SECRET_KEY'] ?? $_SERVER['PAYSTACK_SECRET_KEY'] ?? '');
$webhook = getenv('PAYSTACK_WEBHOOK_SECRET') ?: ($_ENV['PAYSTACK_WEBHOOK_SECRET'] ?? $_SERVER['PAYSTACK_WEBHOOK_SECRET'] ?? '');

return [
    'public_key' => $pubKey,
    'secret_key' => $secKey,
    'callback_url' => $appUrl . '/checkout/callback',
    'webhook_secret' => $webhook,
    'currency' => 'GHS',
];
