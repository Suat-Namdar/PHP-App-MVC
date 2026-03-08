<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Db;
use App\Core\View;

final class AuthController
{
    public function showLogin(array $app, array $vars = []): void
    {
        if (Auth::check()) {
            header('Location: /admin/customers');
            exit;
        }

        View::render('auth.login', [
            'title' => 'Login',
            'csrfToken' => Csrf::token(),
        ], 'auth');
    }

    public function login(array $app, array $vars = []): void
    {
        $pdo = Db::getInstance($app['db']);

        $email = trim((string) ($_POST['email'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');

        if ($email === '' || $password === '') {
            http_response_code(422);
            View::render('auth.login', [
                'title' => 'Login',
                'error' => 'Email ve sifre zorunludur.',
                'csrfToken' => Csrf::token(),
            ], 'auth');
            return;
        }

        if (!Auth::attempt($pdo, $email, $password)) {
            http_response_code(401);
            View::render('auth.login', [
                'title' => 'Login',
                'error' => 'Gecersiz giris bilgileri.',
                'csrfToken' => Csrf::token(),
            ], 'auth');
            return;
        }

        header('Location: /admin/customers');
        exit;
    }

    public function logout(array $app, array $vars = []): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
