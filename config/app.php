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
    'version' => '1.0.0',
];
