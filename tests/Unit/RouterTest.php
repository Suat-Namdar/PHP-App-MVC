<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        $this->router = new Router();
    }

    public function testSimpleRouteDispatches(): void
    {
        $called = false;
        $this->router->get('/hello', function () use (&$called) {
            $called = true;
        });

        ob_start();
        $this->router->dispatch('GET', '/hello');
        ob_end_clean();

        $this->assertTrue($called);
    }

    public function testRouteWithParameter(): void
    {
        $capturedId = null;
        $this->router->get('/users/{id}', function (string $id) use (&$capturedId) {
            $capturedId = $id;
        });

        ob_start();
        $this->router->dispatch('GET', '/users/42');
        ob_end_clean();

        $this->assertSame('42', $capturedId);
    }

    public function testPostRouteDoesNotMatchGet(): void
    {
        $called = false;
        $this->router->post('/submit', function () use (&$called) {
            $called = true;
        });

        ob_start();
        $this->router->dispatch('GET', '/submit');
        ob_end_clean();

        $this->assertFalse($called);
    }

    public function testUnknownRouteReturns404(): void
    {
        ob_start();
        $this->router->dispatch('GET', '/nonexistent');
        ob_end_clean();

        $this->assertSame(404, http_response_code());
    }
}
