<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Session;
use App\Models\User;

class DashboardController extends Controller
{
    public function index(): void
    {
        $userId    = (int)Session::get('user_id');
        $userModel = new User();
        $user      = $userModel->findById($userId);
        $roles     = $userModel->getRoles($userId);

        $this->view('dashboard.index', [
            'user'  => $user,
            'roles' => $roles,
        ]);
    }
}
