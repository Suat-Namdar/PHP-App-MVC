<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Tests\TestCase;

/**
 * Tests for the RBAC relationships: user ↔ role ↔ permission.
 */
class RbacTest extends TestCase
{
    private User $userModel;
    private Role $roleModel;
    private Permission $permModel;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userModel = new User();
        $this->roleModel = new Role();
        $this->permModel = new Permission();
    }

    private function createFixtures(): array
    {
        $userId    = $this->userModel->create('rbacuser', 'rbac@example.com', 'pass');
        $adminId   = $this->roleModel->create('admin', 'Administrator');
        $viewerId  = $this->roleModel->create('viewer', 'Read only');
        $viewPerm  = $this->permModel->create('user.view', '');
        $editPerm  = $this->permModel->create('user.edit', '');

        // admin role has both permissions
        $this->roleModel->syncPermissions($adminId, [$viewPerm, $editPerm]);
        // viewer role has only view permission
        $this->roleModel->syncPermissions($viewerId, [$viewPerm]);

        return compact('userId', 'adminId', 'viewerId', 'viewPerm', 'editPerm');
    }

    public function testAssignRole(): void
    {
        ['userId' => $userId, 'adminId' => $adminId] = $this->createFixtures();

        $this->userModel->assignRole($userId, $adminId);

        $roles = $this->userModel->getRoles($userId);
        $this->assertCount(1, $roles);
        $this->assertSame('admin', $roles[0]['name']);
    }

    public function testAssignRoleIdempotent(): void
    {
        ['userId' => $userId, 'adminId' => $adminId] = $this->createFixtures();

        $this->userModel->assignRole($userId, $adminId);
        $this->userModel->assignRole($userId, $adminId); // second call should not duplicate

        $this->assertCount(1, $this->userModel->getRoles($userId));
    }

    public function testRemoveRole(): void
    {
        ['userId' => $userId, 'adminId' => $adminId, 'viewerId' => $viewerId] = $this->createFixtures();

        $this->userModel->syncRoles($userId, [$adminId, $viewerId]);
        $this->assertCount(2, $this->userModel->getRoles($userId));

        $this->userModel->removeRole($userId, $adminId);
        $roles = $this->userModel->getRoles($userId);
        $this->assertCount(1, $roles);
        $this->assertSame('viewer', $roles[0]['name']);
    }

    public function testSyncRoles(): void
    {
        ['userId' => $userId, 'adminId' => $adminId, 'viewerId' => $viewerId] = $this->createFixtures();

        $this->userModel->syncRoles($userId, [$adminId]);
        $this->assertCount(1, $this->userModel->getRoles($userId));

        // Replace with different roles
        $this->userModel->syncRoles($userId, [$viewerId]);
        $roles = $this->userModel->getRoles($userId);
        $this->assertCount(1, $roles);
        $this->assertSame('viewer', $roles[0]['name']);
    }

    public function testHasPermissionThroughRole(): void
    {
        ['userId' => $userId, 'adminId' => $adminId] = $this->createFixtures();

        $this->userModel->assignRole($userId, $adminId);

        $this->assertTrue($this->userModel->hasPermission($userId, 'user.view'));
        $this->assertTrue($this->userModel->hasPermission($userId, 'user.edit'));
        $this->assertFalse($this->userModel->hasPermission($userId, 'role.manage'));
    }

    public function testHasPermissionReturnsFalseWithNoRoles(): void
    {
        ['userId' => $userId] = $this->createFixtures();

        $this->assertFalse($this->userModel->hasPermission($userId, 'user.view'));
    }

    public function testHasRole(): void
    {
        ['userId' => $userId, 'adminId' => $adminId] = $this->createFixtures();

        $this->assertFalse($this->userModel->hasRole($userId, 'admin'));

        $this->userModel->assignRole($userId, $adminId);

        $this->assertTrue($this->userModel->hasRole($userId, 'admin'));
        $this->assertFalse($this->userModel->hasRole($userId, 'viewer'));
    }

    public function testViewerCannotEdit(): void
    {
        ['userId' => $userId, 'viewerId' => $viewerId] = $this->createFixtures();

        $this->userModel->assignRole($userId, $viewerId);

        $this->assertTrue($this->userModel->hasPermission($userId, 'user.view'));
        $this->assertFalse($this->userModel->hasPermission($userId, 'user.edit'));
    }

    public function testPermissionsAreRemovedWhenRoleIsDeleted(): void
    {
        ['userId' => $userId, 'adminId' => $adminId] = $this->createFixtures();

        $this->userModel->assignRole($userId, $adminId);
        $this->assertTrue($this->userModel->hasPermission($userId, 'user.view'));

        $this->roleModel->delete($adminId);

        $this->assertFalse($this->userModel->hasPermission($userId, 'user.view'));
    }
}
