<?php
return [
    'public_key' => getenv('PAYSTACK_PUBLIC_KEY') ?: '',
    'secret_key' => getenv('PAYSTACK_SECRET_KEY') ?: '',
    'callback_url' => (getenv('APP_URL') ?: 'http://localhost:8000') . '/checkout/callback',
    'webhook_secret' => getenv('PAYSTACK_WEBHOOK_SECRET') ?: '',
    'currency' => 'GHS',
];
