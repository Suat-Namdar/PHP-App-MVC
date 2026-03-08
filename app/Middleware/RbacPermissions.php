<?php

declare(strict_types=1);

namespace App\Middleware;

/** Requires user.create permission. */
class CanCreateUser extends RbacMiddleware
{
    protected string $permission = 'user.create';
}

/** Requires user.edit permission. */
class CanEditUser extends RbacMiddleware
{
    protected string $permission = 'user.edit';
}

/** Requires user.delete permission. */
class CanDeleteUser extends RbacMiddleware
{
    protected string $permission = 'user.delete';
}

/** Requires role.manage permission. */
class CanManageRoles extends RbacMiddleware
{
    protected string $permission = 'role.manage';
}

/** Requires permission.manage permission. */
class CanManagePermissions extends RbacMiddleware
{
    protected string $permission = 'permission.manage';
}
