<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (Session::has('user_id')) {
            $this->redirect('/dashboard');
        }
        $error = Session::getFlash('error');
        $this->view('auth.login', ['error' => $error]);
    }

    public function login(): void
    {
        $this->validateCsrf();

        $username = trim((string)$this->input('username', ''));
        $password = (string)$this->input('password', '');

        if ($username === '' || $password === '') {
            Session::flash('error', 'Kullanıcı adı ve şifre gereklidir.');
            $this->redirect('/login');
        }

        $userModel = new User();
        $user      = $userModel->findByUsername($username);

        if (!$user || !$userModel->verifyPassword($password, $user['password'])) {
            Session::flash('error', 'Geçersiz kullanıcı adı veya şifre.');
            $this->redirect('/login');
        }

        Session::set('user_id', $user['id']);
        Session::set('username', $user['username']);

        $this->redirect('/dashboard');
    }

    public function logout(): void
    {
        Session::destroy();
        $this->redirect('/login');
    }
}
