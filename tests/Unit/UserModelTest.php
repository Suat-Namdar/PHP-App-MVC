<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Database;
use App\Models\User;
use Tests\TestCase;

class UserModelTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testCreateAndFindUser(): void
    {
        $id = $this->user->create('testuser', 'test@example.com', 'secret123');

        $this->assertGreaterThan(0, $id);

        $found = $this->user->findById($id);
        $this->assertNotNull($found);
        $this->assertSame('testuser', $found['username']);
        $this->assertSame('test@example.com', $found['email']);
    }

    public function testFindByUsername(): void
    {
        $this->user->create('alice', 'alice@example.com', 'pass1234');

        $found = $this->user->findByUsername('alice');
        $this->assertNotNull($found);
        $this->assertSame('alice', $found['username']);
    }

    public function testFindByEmail(): void
    {
        $this->user->create('bob', 'bob@example.com', 'pass1234');

        $found = $this->user->findByEmail('bob@example.com');
        $this->assertNotNull($found);
        $this->assertSame('bob', $found['username']);
    }

    public function testFindByUsernameReturnsNullForUnknown(): void
    {
        $this->assertNull($this->user->findByUsername('nobody'));
    }

    public function testPasswordIsHashed(): void
    {
        $id   = $this->user->create('hashtest', 'hash@example.com', 'plaintext');
        $user = $this->user->findById($id);

        $this->assertNotSame('plaintext', $user['password']);
        $this->assertTrue(password_verify('plaintext', $user['password']));
    }

    public function testVerifyPassword(): void
    {
        $id   = $this->user->create('verifyme', 'v@example.com', 'correct-pass');
        $user = $this->user->findById($id);

        $this->assertTrue($this->user->verifyPassword('correct-pass', $user['password']));
        $this->assertFalse($this->user->verifyPassword('wrong-pass', $user['password']));
    }

    public function testUpdateUser(): void
    {
        $id = $this->user->create('old', 'old@example.com', 'pass');
        $this->user->update($id, 'new', 'new@example.com');

        $user = $this->user->findById($id);
        $this->assertSame('new', $user['username']);
        $this->assertSame('new@example.com', $user['email']);
    }

    public function testUpdatePassword(): void
    {
        $id = $this->user->create('puser', 'p@example.com', 'oldpass');
        $this->user->updatePassword($id, 'newpass');

        $user = $this->user->findById($id);
        $this->assertTrue(password_verify('newpass', $user['password']));
        $this->assertFalse(password_verify('oldpass', $user['password']));
    }

    public function testDeleteUser(): void
    {
        $id = $this->user->create('todelete', 'del@example.com', 'pass');
        $this->user->delete($id);

        $this->assertNull($this->user->findById($id));
    }

    public function testAll(): void
    {
        $this->user->create('u1', 'u1@example.com', 'pass');
        $this->user->create('u2', 'u2@example.com', 'pass');

        $all = $this->user->all();
        $this->assertCount(2, $all);
    }
}
