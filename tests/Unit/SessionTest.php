<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Session;
use PHPUnit\Framework\TestCase;

class SessionTest extends TestCase
{
    protected function setUp(): void
    {
        // Ensure a session is active
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION = [];
    }

    protected function tearDown(): void
    {
        $_SESSION = [];
    }

    public function testSetAndGet(): void
    {
        Session::set('key', 'value');
        $this->assertSame('value', Session::get('key'));
    }

    public function testGetWithDefault(): void
    {
        $this->assertSame('default', Session::get('missing', 'default'));
    }

    public function testHas(): void
    {
        $this->assertFalse(Session::has('x'));
        Session::set('x', 1);
        $this->assertTrue(Session::has('x'));
    }

    public function testRemove(): void
    {
        Session::set('r', 'val');
        Session::remove('r');
        $this->assertFalse(Session::has('r'));
    }

    public function testFlash(): void
    {
        Session::flash('msg', 'Hello!');
        $this->assertTrue(Session::hasFlash('msg'));
        $this->assertSame('Hello!', Session::getFlash('msg'));
        // After reading, flash should be gone
        $this->assertFalse(Session::hasFlash('msg'));
    }

    public function testCsrfToken(): void
    {
        $token = Session::csrfToken();
        $this->assertNotEmpty($token);
        // Same token on second call
        $this->assertSame($token, Session::csrfToken());
    }

    public function testValidateCsrf(): void
    {
        $token = Session::csrfToken();
        $this->assertTrue(Session::validateCsrf($token));
        $this->assertFalse(Session::validateCsrf('invalid-token'));
    }
}
