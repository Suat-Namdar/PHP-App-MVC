<?php

declare(strict_types=1);

namespace App\Middleware;

class CanManagePermissions extends RbacMiddleware
{
    protected string $permission = 'permission.manage';
}
