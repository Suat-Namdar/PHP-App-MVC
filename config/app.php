<?php

declare(strict_types=1);

use App\Core\Application;
use App\Core\Router;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\UserController;
use App\Controllers\RoleController;
use App\Middleware\AuthMiddleware;
use App\Middleware\CanCreateUser;
use App\Middleware\CanEditUser;
use App\Middleware\CanDeleteUser;
use App\Middleware\CanManageRoles;

$router = new Router();

// ── Auth routes (public) ──────────────────────────────────────────────────────
$router->get('/login',  [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

// Root redirect
$router->get('/', function () {
    header('Location: /dashboard');
    exit();
});

// ── Protected routes ──────────────────────────────────────────────────────────
$router->get('/dashboard', [DashboardController::class, 'index'], [AuthMiddleware::class]);

// Users
$router->get('/users',              [UserController::class, 'index'],  [AuthMiddleware::class]);
$router->get('/users/create',       [UserController::class, 'create'], [AuthMiddleware::class, CanCreateUser::class]);
$router->post('/users',             [UserController::class, 'store'],  [AuthMiddleware::class, CanCreateUser::class]);
$router->get('/users/{id}/edit',    [UserController::class, 'edit'],   [AuthMiddleware::class, CanEditUser::class]);
$router->post('/users/{id}',        [UserController::class, 'update'], [AuthMiddleware::class, CanEditUser::class]);
$router->post('/users/{id}/delete', [UserController::class, 'delete'], [AuthMiddleware::class, CanDeleteUser::class]);

// Roles
$router->get('/roles',              [RoleController::class, 'index'],  [AuthMiddleware::class, CanManageRoles::class]);
$router->get('/roles/create',       [RoleController::class, 'create'], [AuthMiddleware::class, CanManageRoles::class]);
$router->post('/roles',             [RoleController::class, 'store'],  [AuthMiddleware::class, CanManageRoles::class]);
$router->get('/roles/{id}/edit',    [RoleController::class, 'edit'],   [AuthMiddleware::class, CanManageRoles::class]);
$router->post('/roles/{id}',        [RoleController::class, 'update'], [AuthMiddleware::class, CanManageRoles::class]);
$router->post('/roles/{id}/delete', [RoleController::class, 'delete'], [AuthMiddleware::class, CanManageRoles::class]);

return new Application($router);
