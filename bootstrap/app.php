<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$basePath = dirname(__DIR__);

if (file_exists($basePath . '/.env')) {
    Dotenv::createImmutable($basePath)->safeLoad();
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name($_ENV['SESSION_NAME'] ?? 'php_app_mvc_session');
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

date_default_timezone_set('UTC');

return [
    'base_path' => $basePath,
    'db' => require $basePath . '/config/database.php',
];
