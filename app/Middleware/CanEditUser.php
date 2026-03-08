<?php

declare(strict_types=1);

namespace App\Middleware;

class CanEditUser extends RbacMiddleware
{
    protected string $permission = 'user.edit';
}
