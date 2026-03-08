<?php

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\Db;

$pdo = Db::getInstance($app['db']);
$currentUser = Auth::user($pdo);
$csrfToken = Csrf::token();
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
    <title><?= htmlspecialchars($title ?? 'Admin', ENT_QUOTES, 'UTF-8') ?></title>
    <link rel="stylesheet" href="/assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/vendor/datatables/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="/assets/vendor/select2/css/select2.min.css">
    <link rel="stylesheet" href="/assets/vendor/sweetalert2/sweetalert2.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg bg-dark navbar-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="/admin/customers">Admin Panel</a>
            <div class="d-flex align-items-center gap-2 text-white">
                <span><?= htmlspecialchars($currentUser['name'] ?? 'User', ENT_QUOTES, 'UTF-8') ?></span>
                <form method="post" action="/logout" class="m-0">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Logout</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4">
        <?= $content ?>
    </main>

    <script src="/assets/vendor/jquery/jquery.min.js"></script>
    <script src="/assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/vendor/datatables/jquery.dataTables.min.js"></script>
    <script src="/assets/vendor/datatables/dataTables.bootstrap5.min.js"></script>
    <script src="/assets/vendor/select2/js/select2.min.js"></script>
    <script src="/assets/vendor/sweetalert2/sweetalert2.all.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
    </script>
</body>
</html>
