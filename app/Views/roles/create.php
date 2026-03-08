<?php $pageTitle = 'Yeni Rol'; ?>
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="bi bi-plus-circle-fill me-2"></i>Yeni Rol</h4>
    <a href="/roles" class="btn btn-outline-secondary btn-sm">
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
        <form action="/roles" method="POST" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label for="name" class="form-label fw-semibold">Rol Adı</label>
                <input type="text" id="name" name="name" class="form-control"
                       value="<?= e($old['name'] ?? '') ?>" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label fw-semibold">Açıklama</label>
                <input type="text" id="description" name="description" class="form-control"
                       value="<?= e($old['description'] ?? '') ?>">
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">İzinler</label>
                <div class="row">
                    <?php foreach ($permissions as $perm): ?>
                        <div class="col-md-4 col-lg-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox"
                                       name="permissions[]" value="<?= e($perm['id']) ?>"
                                       id="perm_<?= e($perm['id']) ?>">
                                <label class="form-check-label" for="perm_<?= e($perm['id']) ?>">
                                    <code><?= e($perm['name']) ?></code>
                                    <?php if ($perm['description']): ?>
                                        <small class="text-muted d-block"><?= e($perm['description']) ?></small>
                                    <?php endif; ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle me-1"></i> Oluştur
            </button>
            <a href="/roles" class="btn btn-outline-secondary ms-2">İptal</a>
        </form>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
