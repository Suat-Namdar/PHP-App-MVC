<?php

declare(strict_types=1);

namespace App\Middleware;

class CanManageRoles extends RbacMiddleware
{
    protected string $permission = 'role.manage';
}
