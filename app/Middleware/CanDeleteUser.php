<?php

declare(strict_types=1);

namespace App\Middleware;

class CanDeleteUser extends RbacMiddleware
{
    protected string $permission = 'user.delete';
}
