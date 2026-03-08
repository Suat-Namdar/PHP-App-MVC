<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

// Use an in-memory SQLite database for tests
$_ENV['DB_DRIVER'] = 'sqlite';
$_ENV['DB_PATH']   = ':memory:';
putenv('DB_DRIVER=sqlite');
putenv('DB_PATH=:memory:');

$_ENV['APP_DEBUG'] = 'true';
