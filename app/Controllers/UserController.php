<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;
use App\Models\Role;

class UserController extends Controller
{
    public function index(): void
    {
        $userModel = new User();
        $users     = $userModel->all();

        $this->view('users.index', ['users' => $users]);
    }

    public function create(): void
    {
        $roleModel = new Role();
        $roles     = $roleModel->all();
        $errors    = Session::getFlash('errors', []);
        $old       = Session::getFlash('old', []);

        $this->view('users.create', [
            'roles'  => $roles,
            'errors' => $errors,
            'old'    => $old,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $username = trim((string)$this->input('username', ''));
        $email    = trim((string)$this->input('email', ''));
        $password = (string)$this->input('password', '');
        $roleIds  = (array)($this->input('roles') ?? []);

        $errors = $this->validateUserInput($username, $email, $password);

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', ['username' => $username, 'email' => $email]);
            $this->redirect('/users/create');
        }

        $userModel = new User();

        if ($userModel->findByUsername($username)) {
            Session::flash('errors', ['Kullanıcı adı zaten kullanımda.']);
            Session::flash('old', ['username' => $username, 'email' => $email]);
            $this->redirect('/users/create');
        }

        if ($userModel->findByEmail($email)) {
            Session::flash('errors', ['E-posta adresi zaten kullanımda.']);
            Session::flash('old', ['username' => $username, 'email' => $email]);
            $this->redirect('/users/create');
        }

        $userId = $userModel->create($username, $email, $password);
        $userModel->syncRoles($userId, array_map('intval', $roleIds));

        Session::flash('success', 'Kullanıcı başarıyla oluşturuldu.');
        $this->redirect('/users');
    }

    public function edit(string $id): void
    {
        $userModel = new User();
        $roleModel = new Role();

        $user = $userModel->findById((int)$id);
        if (!$user) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $roles      = $roleModel->all();
        $userRoles  = $userModel->getRoles((int)$id);
        $userRoleIds = array_column($userRoles, 'id');
        $errors      = Session::getFlash('errors', []);

        $this->view('users.edit', [
            'user'        => $user,
            'roles'       => $roles,
            'userRoleIds' => $userRoleIds,
            'errors'      => $errors,
        ]);
    }

    public function update(string $id): void
    {
        $this->validateCsrf();

        $username = trim((string)$this->input('username', ''));
        $email    = trim((string)$this->input('email', ''));
        $password = (string)$this->input('password', '');
        $roleIds  = (array)($this->input('roles') ?? []);

        $errors = $this->validateUserInput($username, $email, null);

        if ($errors) {
            Session::flash('errors', $errors);
            $this->redirect('/users/' . $id . '/edit');
        }

        $userModel = new User();
        $user      = $userModel->findById((int)$id);
        if (!$user) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        // Check username uniqueness (excluding self)
        $existing = $userModel->findByUsername($username);
        if ($existing && (int)$existing['id'] !== (int)$id) {
            Session::flash('errors', ['Kullanıcı adı zaten kullanımda.']);
            $this->redirect('/users/' . $id . '/edit');
        }

        $userModel->update((int)$id, $username, $email);

        if ($password !== '') {
            if (strlen($password) < 6) {
                Session::flash('errors', ['Şifre en az 6 karakter olmalıdır.']);
                $this->redirect('/users/' . $id . '/edit');
            }
            $userModel->updatePassword((int)$id, $password);
        }

        $userModel->syncRoles((int)$id, array_map('intval', $roleIds));

        Session::flash('success', 'Kullanıcı güncellendi.');
        $this->redirect('/users');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();

        $currentUserId = (int)Session::get('user_id');
        if ((int)$id === $currentUserId) {
            Session::flash('error', 'Kendinizi silemezsiniz.');
            $this->redirect('/users');
        }

        $userModel = new User();
        $user      = $userModel->findById((int)$id);
        if (!$user) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $userModel->delete((int)$id);

        Session::flash('success', 'Kullanıcı silindi.');
        $this->redirect('/users');
    }

    /**
     * @param string|null $password Pass null to skip password validation (for updates)
     * @return array<string>
     */
    private function validateUserInput(string $username, string $email, ?string $password): array
    {
        $errors = [];

        if ($username === '') {
            $errors[] = 'Kullanıcı adı gereklidir.';
        } elseif (strlen($username) < 3) {
            $errors[] = 'Kullanıcı adı en az 3 karakter olmalıdır.';
        }

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi giriniz.';
        }

        if ($password !== null) {
            if ($password === '') {
                $errors[] = 'Şifre gereklidir.';
            } elseif (strlen($password) < 6) {
                $errors[] = 'Şifre en az 6 karakter olmalıdır.';
            }
        }

        return $errors;
    }
}
