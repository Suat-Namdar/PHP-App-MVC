<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\Permission;
use Tests\TestCase;

class PermissionModelTest extends TestCase
{
    private Permission $permission;

    protected function setUp(): void
    {
        parent::setUp();
        $this->permission = new Permission();
    }

    public function testCreateAndFindPermission(): void
    {
        $id   = $this->permission->create('user.view', 'View users');
        $perm = $this->permission->findById($id);

        $this->assertNotNull($perm);
        $this->assertSame('user.view', $perm['name']);
        $this->assertSame('View users', $perm['description']);
    }

    public function testFindByName(): void
    {
        $this->permission->create('user.create', 'Create users');
        $perm = $this->permission->findByName('user.create');

        $this->assertNotNull($perm);
        $this->assertSame('user.create', $perm['name']);
    }

    public function testFindByNameReturnsNullForUnknown(): void
    {
        $this->assertNull($this->permission->findByName('ghost.action'));
    }

    public function testUpdatePermission(): void
    {
        $id = $this->permission->create('old.perm', 'Old desc');
        $this->permission->update($id, 'new.perm', 'New desc');

        $perm = $this->permission->findById($id);
        $this->assertSame('new.perm', $perm['name']);
        $this->assertSame('New desc', $perm['description']);
    }

    public function testDeletePermission(): void
    {
        $id = $this->permission->create('to.delete', '');
        $this->permission->delete($id);

        $this->assertNull($this->permission->findById($id));
    }

    public function testAll(): void
    {
        $this->permission->create('p1', '');
        $this->permission->create('p2', '');
        $this->permission->create('p3', '');

        $this->assertCount(3, $this->permission->all());
    }
}
