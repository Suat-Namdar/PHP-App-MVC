<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index(): void
    {
        $roleModel = new Role();
        $roles     = $roleModel->all();

        $this->view('roles.index', ['roles' => $roles]);
    }

    public function create(): void
    {
        $permModel   = new Permission();
        $permissions = $permModel->all();
        $errors      = Session::getFlash('errors', []);
        $old         = Session::getFlash('old', []);

        $this->view('roles.create', [
            'permissions' => $permissions,
            'errors'      => $errors,
            'old'         => $old,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name        = trim((string)$this->input('name', ''));
        $description = trim((string)$this->input('description', ''));
        $permIds     = (array)($this->input('permissions') ?? []);

        $errors = [];
        if ($name === '') {
            $errors[] = 'Rol adı gereklidir.';
        }

        if ($errors) {
            Session::flash('errors', $errors);
            Session::flash('old', ['name' => $name, 'description' => $description]);
            $this->redirect('/roles/create');
        }

        $roleModel = new Role();

        if ($roleModel->findByName($name)) {
            Session::flash('errors', ['Bu isimde bir rol zaten mevcut.']);
            Session::flash('old', ['name' => $name, 'description' => $description]);
            $this->redirect('/roles/create');
        }

        $roleId = $roleModel->create($name, $description);
        $roleModel->syncPermissions($roleId, array_map('intval', $permIds));

        Session::flash('success', 'Rol başarıyla oluşturuldu.');
        $this->redirect('/roles');
    }

    public function edit(string $id): void
    {
        $roleModel = new Role();
        $role      = $roleModel->findById((int)$id);

        if (!$role) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $permModel      = new Permission();
        $permissions    = $permModel->all();
        $rolePermissions = $roleModel->getPermissions((int)$id);
        $rolePermIds    = array_column($rolePermissions, 'id');
        $errors         = Session::getFlash('errors', []);

        $this->view('roles.edit', [
            'role'        => $role,
            'permissions' => $permissions,
            'rolePermIds' => $rolePermIds,
            'errors'      => $errors,
        ]);
    }

    public function update(string $id): void
    {
        $this->validateCsrf();

        $name        = trim((string)$this->input('name', ''));
        $description = trim((string)$this->input('description', ''));
        $permIds     = (array)($this->input('permissions') ?? []);

        $errors = [];
        if ($name === '') {
            $errors[] = 'Rol adı gereklidir.';
        }

        if ($errors) {
            Session::flash('errors', $errors);
            $this->redirect('/roles/' . $id . '/edit');
        }

        $roleModel = new Role();
        $role      = $roleModel->findById((int)$id);

        if (!$role) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $roleModel->update((int)$id, $name, $description);
        $roleModel->syncPermissions((int)$id, array_map('intval', $permIds));

        Session::flash('success', 'Rol güncellendi.');
        $this->redirect('/roles');
    }

    public function delete(string $id): void
    {
        $this->validateCsrf();

        $roleModel = new Role();
        $role      = $roleModel->findById((int)$id);

        if (!$role) {
            http_response_code(404);
            require BASE_PATH . '/app/Views/errors/404.php';
            return;
        }

        $roleModel->delete((int)$id);

        Session::flash('success', 'Rol silindi.');
        $this->redirect('/roles');
    }
}
