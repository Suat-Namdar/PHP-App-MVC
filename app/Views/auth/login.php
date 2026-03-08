<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş – PHP MVC RBAC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; }
    </style>
</head>
<body class="d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5 col-lg-4">
                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-5">
                        <div class="text-center mb-4">
                            <i class="bi bi-shield-lock-fill text-primary" style="font-size:3rem"></i>
                            <h4 class="fw-bold mt-2">PHP MVC RBAC</h4>
                            <p class="text-muted small">Hesabınıza giriş yapın</p>
                        </div>

                        <?php if ($error ?? null): ?>
                            <div class="alert alert-danger">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                <?= e($error) ?>
                            </div>
                        <?php endif; ?>

                        <form action="/login" method="POST" novalidate>
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="username" class="form-label fw-semibold">Kullanıcı Adı</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" id="username" name="username"
                                           class="form-control" placeholder="kullaniciadi"
                                           value="<?= e($_POST['username'] ?? '') ?>"
                                           required autofocus>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="password" class="form-label fw-semibold">Şifre</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" id="password" name="password"
                                           class="form-control" placeholder="••••••••" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Giriş Yap
                            </button>
                        </form>

                        <hr class="my-4">
                        <p class="text-muted text-center small mb-0">
                            Varsayılan: <strong>admin</strong> / <strong>admin123</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
