<?php

declare(strict_types=1);

namespace App\Middleware;

class CanCreateUser extends RbacMiddleware
{
    protected string $permission = 'user.create';
}
