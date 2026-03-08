<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <h1 class="h4 mb-3">Giris Yap</h1>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <form method="post" action="/login" class="needs-validation" novalidate>
            <input type="hidden" name="_token" value="<?= htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8') ?>">

            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input class="form-control" id="email" name="email" type="email" required>
            </div>

            <div class="mb-3">
                <label class="form-label" for="password">Sifre</label>
                <input class="form-control" id="password" name="password" type="password" required>
            </div>

            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
    </div>
</div>
