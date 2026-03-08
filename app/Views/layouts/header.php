<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'PHP MVC App') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { min-height: calc(100vh - 56px); background: #343a40; }
        .sidebar .nav-link { color: #adb5bd; }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,0.1); border-radius: 4px; }
        .sidebar .nav-link i { margin-right: 8px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="/dashboard">
            <i class="bi bi-shield-lock-fill me-1"></i> PHP MVC RBAC
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>
                        <?= e(\App\Core\Session::get('username', 'Kullanıcı')) ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <form action="/logout" method="POST" class="d-inline">
                                <?= csrf_field() ?>
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right me-1"></i> Çıkış
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar py-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= ($_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : '') ?>"
                       href="/dashboard">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <?php if (can('user.view') || has_role('admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/users') ? 'active' : '') ?>"
                       href="/users">
                        <i class="bi bi-people-fill"></i> Kullanıcılar
                    </a>
                </li>
                <?php endif; ?>
                <?php if (can('role.manage') || has_role('admin')): ?>
                <li class="nav-item">
                    <a class="nav-link <?= (str_starts_with($_SERVER['REQUEST_URI'], '/roles') ? 'active' : '') ?>"
                       href="/roles">
                        <i class="bi bi-person-badge-fill"></i> Roller
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>

        <!-- Main content -->
        <main class="col-md-10 py-4">
            <?php if (\App\Core\Session::hasFlash('success')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i>
                    <?= e(\App\Core\Session::getFlash('success')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (\App\Core\Session::hasFlash('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                    <?= e(\App\Core\Session::getFlash('error')) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
