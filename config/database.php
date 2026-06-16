<?php
$default = [
    'driver' => 'mysql',
    'host' => getenv('DB_HOST') ?: ($_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? 'localhost'),
    'port' => getenv('DB_PORT') ?: ($_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '3306'),
    'database' => getenv('DB_DATABASE') ?: ($_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? 'celer_market'),
    'username' => getenv('DB_USERNAME') ?: ($_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? 'root'),
    'password' => getenv('DB_PASSWORD') ?: ($_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? ''),
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
];

$databaseUrl = getenv('DATABASE_URL') ?: ($_ENV['DATABASE_URL'] ?? $_SERVER['DATABASE_URL'] ?? '');
// Also check common Railway MySQL env var names
if (!$databaseUrl) {
    $databaseUrl = getenv('MYSQL_URL') ?: ($_ENV['MYSQL_URL'] ?? $_SERVER['MYSQL_URL'] ?? '');
}
if (!$databaseUrl) {
    $databaseUrl = getenv('JAWSDB_URL') ?: ($_ENV['JAWSDB_URL'] ?? $_SERVER['JAWSDB_URL'] ?? '');
}
if (!$databaseUrl) {
    $databaseUrl = getenv('CLEARDB_DATABASE_URL') ?: ($_ENV['CLEARDB_DATABASE_URL'] ?? $_SERVER['CLEARDB_DATABASE_URL'] ?? '');
}
if ($databaseUrl) {
    $parts = parse_url($databaseUrl);
    if ($parts && isset($parts['scheme'])) {
        $default['host'] = $parts['host'] ?? $default['host'];
        $default['port'] = $parts['port'] ?? $default['port'];
        $default['username'] = $parts['user'] ?? $default['username'];
        $default['password'] = $parts['pass'] ?? $default['password'];
        $default['database'] = ltrim($parts['path'] ?? '', '/') ?: $default['database'];
    }
}

return [
    'default' => 'mysql',
    'connections' => [
        'mysql' => $default,
    ],
];
