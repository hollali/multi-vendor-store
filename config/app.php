<?php
return [
    'name' => 'Celer Market',
    'env' => getenv('APP_ENV') ?: ($_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? 'production'),
    'debug' => (bool)(getenv('APP_DEBUG') ?: ($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? false)),
    'url' => getenv('APP_URL') ?: ($_ENV['APP_URL'] ?? $_SERVER['APP_URL'] ?? 'http://localhost:8000'),
    'timezone' => 'Africa/Accra',
    'locale' => 'en',
    'currency' => 'GHS',
    'currency_symbol' => 'GH₵',
    'version' => '2.0.0',
    'currencies' => ['GHS', 'NGN', 'USD', 'EUR', 'GBP', 'KES', 'ZAR', 'XOF', 'UGX', 'TZS', 'RWF', 'CAD', 'AUD', 'CNY', 'INR'],
    'base_currency' => 'GHS',
    'shipping' => [
        'default_origin' => 'GH',
        'free_shipping_threshold' => 200,
        'tax_rate' => 2.5,
        'default_weight_kg' => 0.5,
    ],
    'geolocation' => [
        'enabled' => true,
        'default_country' => 'GH',
        'ipapi_key' => getenv('IPAPI_KEY') ?: '',
    ],
];
