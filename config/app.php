<?php
return [
    'name' => 'Celer Market',
    'env' => getenv('APP_ENV') ?: 'production',
    'debug' => (bool)(getenv('APP_DEBUG') ?: false),
    'url' => getenv('APP_URL') ?: 'http://localhost:8000',
    'timezone' => 'Africa/Accra',
    'locale' => 'en',
    'currency' => 'GHS',
    'currency_symbol' => 'GH₵',
    'version' => '1.0.0',
];
