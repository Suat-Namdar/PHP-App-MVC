<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\MiddlewareInterface;

class Router
{
    /** @var array<string, array<string, array{callable, array}>> */
    private array $routes = [];

    public function get(string $uri, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('GET', $uri, $handler, $middleware);
    }

    public function post(string $uri, callable|array $handler, array $middleware = []): void
    {
        $this->addRoute('POST', $uri, $handler, $middleware);
    }

    private function addRoute(string $method, string $uri, callable|array $handler, array $middleware): void
    {
        $this->routes[$method][$uri] = [$handler, $middleware];
    }

    public function dispatch(string $method, string $uri): void
    {
        $routes = $this->routes[$method] ?? [];

        foreach ($routes as $pattern => [$handler, $middleware]) {
            $params = $this->matchRoute($pattern, $uri);
            if ($params !== null) {
                // Run middleware chain
                foreach ($middleware as $middlewareClass) {
                    /** @var MiddlewareInterface $mw */
                    $mw = new $middlewareClass();
                    $mw->handle();
                }
                $this->callHandler($handler, $params);
                return;
            }
        }

        // 404
        http_response_code(404);
        require BASE_PATH . '/app/Views/errors/404.php';
    }

    /**
     * Match a route pattern against a URI, return params array or null.
     *
     * @return array<string,string>|null
     */
    private function matchRoute(string $pattern, string $uri): ?array
    {
        if ($pattern === $uri) {
            return [];
        }

        // Convert {param} placeholders to named capture groups
        $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        $regex = '#^' . $regex . '$#';

        if (preg_match($regex, $uri, $matches)) {
            $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            return $params;
        }

        return null;
    }

    /**
     * @param callable|array{0:string,1:string} $handler
     * @param array<string,string> $params
     */
    private function callHandler(callable|array $handler, array $params): void
    {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }

        [$controllerClass, $method] = $handler;
        $controller = new $controllerClass();
        call_user_func_array([$controller, $method], $params);
    }
}
