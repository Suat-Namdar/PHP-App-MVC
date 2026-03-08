<?php $pageTitle = 'Kullanıcı Düzenle'; ?>
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="bi bi-pencil-square me-2"></i>Kullanıcı Düzenle</h4>
    <a href="/users" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Geri
    </a>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <li><?= e($err) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        <form action="/users/<?= e($user['id']) ?>" method="POST" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="username" class="form-label fw-semibold">Kullanıcı Adı</label>
                <input type="text" id="username" name="username" class="form-control"
                       value="<?= e($user['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label fw-semibold">E-posta</label>
                <input type="email" id="email" name="email" class="form-control"
                       value="<?= e($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label fw-semibold">Yeni Şifre</label>
                <input type="password" id="password" name="password"
                       class="form-control" minlength="6">
                <div class="form-text">Değiştirmek istemiyorsanız boş bırakın.</div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Roller</label>
                <div class="row">
                    <?php foreach ($roles as $role): ?>
                        <div class="col-md-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="roles[]" value="<?= e($role['id']) ?>"
                                       id="role_<?= e($role['id']) ?>"
                                       <?= in_array($role['id'], $userRoleIds, false) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="role_<?= e($role['id']) ?>">
                                    <?= e($role['name']) ?>
                                    <?php if ($role['description']): ?>
                                        <small class="text-muted d-block"><?= e($role['description']) ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle me-1"></i> Güncelle
            </button>
            <a href="/users" class="btn btn-outline-secondary ms-2">İptal</a>
        </form>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
