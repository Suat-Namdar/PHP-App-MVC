<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Session;
use App\Models\User;

/**
 * Role-Based Access Control middleware.
 *
 * Usage – pass required permission as constructor argument via a closure:
 *   new RbacMiddleware('user.create')
 *
 * Because middleware classes are instantiated by name in the router,
 * permissions are encoded in sub-classes (see RbacPermission trait approach),
 * OR you can register anonymous-class wrappers.
 *
 * For simplicity this base class reads the required permission from the static
 * property $permission that sub-classes set.
 */
abstract class RbacMiddleware implements MiddlewareInterface
{
    /** Override this in sub-classes. */
    protected string $permission = '';

    public function handle(): void
    {
        $userId = Session::get('user_id');
        if (!$userId) {
            header('Location: /login');
            exit();
        }

        if ($this->permission !== '') {
            $userModel = new User();
            if (!$userModel->hasPermission((int)$userId, $this->permission)) {
                http_response_code(403);
                require BASE_PATH . '/app/Views/errors/403.php';
                exit();
            }
        }
    }
}
