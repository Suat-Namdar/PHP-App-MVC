<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Role;
use App\Models\Permission;
use Tests\TestCase;

class RoleModelTest extends TestCase
{
    private Role $role;
    private Permission $permission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->role       = new Role();
        $this->permission = new Permission();
    }

    public function testCreateAndFindRole(): void
    {
        $id   = $this->role->create('admin', 'Administrator');
        $role = $this->role->findById($id);

        $this->assertNotNull($role);
        $this->assertSame('admin', $role['name']);
        $this->assertSame('Administrator', $role['description']);
    }

    public function testFindByName(): void
    {
        $this->role->create('editor', 'Content Editor');
        $role = $this->role->findByName('editor');

        $this->assertNotNull($role);
        $this->assertSame('editor', $role['name']);
    }

    public function testFindByNameReturnsNullForUnknown(): void
    {
        $this->assertNull($this->role->findByName('ghost'));
    }

    public function testUpdateRole(): void
    {
        $id = $this->role->create('old', 'Old Desc');
        $this->role->update($id, 'new', 'New Desc');

        $role = $this->role->findById($id);
        $this->assertSame('new', $role['name']);
        $this->assertSame('New Desc', $role['description']);
    }

    public function testDeleteRole(): void
    {
        $id = $this->role->create('todelete', '');
        $this->role->delete($id);

        $this->assertNull($this->role->findById($id));
    }

    public function testAll(): void
    {
        $this->role->create('r1', '');
        $this->role->create('r2', '');

        $this->assertCount(2, $this->role->all());
    }

    public function testSyncPermissions(): void
    {
        $roleId = $this->role->create('myrole', '');
        $p1     = $this->permission->create('user.view', '');
        $p2     = $this->permission->create('user.edit', '');
        $p3     = $this->permission->create('user.delete', '');

        $this->role->syncPermissions($roleId, [$p1, $p2]);
        $perms = $this->role->getPermissions($roleId);
        $this->assertCount(2, $perms);

        // Re-sync with different permissions
        $this->role->syncPermissions($roleId, [$p3]);
        $perms = $this->role->getPermissions($roleId);
        $this->assertCount(1, $perms);
        $this->assertSame('user.delete', $perms[0]['name']);
    }

    public function testGetPermissionsReturnsEmptyForNoPermissions(): void
    {
        $roleId = $this->role->create('empty', '');
        $this->assertEmpty($this->role->getPermissions($roleId));
    }
}
