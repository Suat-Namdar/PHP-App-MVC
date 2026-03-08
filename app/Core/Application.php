<?php

declare(strict_types=1);

namespace App\Core;

class Application
{
    private Router $router;
    private static Application $instance;

    public function __construct(Router $router)
    {
        $this->router = $router;
        self::$instance = $this;
    }

    public static function getInstance(): Application
    {
        return self::$instance;
    }

    public function run(): void
    {
        Session::start();

        $method = $_SERVER['REQUEST_METHOD'];
        $uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

        // Strip the public sub-path when running under a sub-directory
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
        if ($scriptDir !== '' && str_starts_with($uri, $scriptDir)) {
            $uri = substr($uri, strlen($scriptDir));
        }
        $uri = '/' . ltrim($uri, '/');

        try {
            $this->router->dispatch($method, $uri);
        } catch (\Throwable $e) {
            if ($_ENV['APP_DEBUG'] ?? false) {
                echo '<pre>' . htmlspecialchars($e->getMessage()) . "\n" . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            } else {
                http_response_code(500);
                require BASE_PATH . '/app/Views/errors/500.php';
            }
        }
    }
}
