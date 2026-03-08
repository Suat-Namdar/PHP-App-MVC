<?php

declare(strict_types=1);

use App\Controllers\Admin\CustomerController;
use App\Controllers\AuthController;
use App\Core\Auth;
use App\Core\Csrf;
use FastRoute\Dispatcher;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require dirname(__DIR__) . '/bootstrap/app.php';

$dispatcher = FastRoute\simpleDispatcher(static function (FastRoute\RouteCollector $r): void {
    $r->addRoute('GET', '/login', [AuthController::class, 'showLogin']);
    $r->addRoute('POST', '/login', [AuthController::class, 'login']);
    $r->addRoute('POST', '/logout', [AuthController::class, 'logout']);

    $r->addRoute('GET', '/admin/customers', [CustomerController::class, 'index']);
    $r->addRoute('POST', '/admin/customers/dt', [CustomerController::class, 'dataTable']);
});

$httpMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$uri = $_SERVER['REQUEST_URI'] ?? '/';

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

if ($httpMethod === 'POST' && !Csrf::validateRequest()) {
    http_response_code(419);
    exit('CSRF token mismatch.');
}

if (str_starts_with($uri, '/admin') && !Auth::check()) {
    header('Location: /login');
    exit;
}

$routeInfo = $dispatcher->dispatch($httpMethod, $uri);

switch ($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        http_response_code(404);
        echo '404 Not Found';
        break;

    case Dispatcher::METHOD_NOT_ALLOWED:
        http_response_code(405);
        echo '405 Method Not Allowed';
        break;

    case Dispatcher::FOUND:
        [$class, $method] = $routeInfo[1];
        $vars = $routeInfo[2];

        $controller = new $class();
        $controller->{$method}($app, $vars);
        break;
}
