<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;

/**
 * Ensures the user is authenticated before accessing a route.
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function handle(): void
    {
        if (!Session::has('user_id')) {
            header('Location: /login');
            exit();
        }
    }
}
