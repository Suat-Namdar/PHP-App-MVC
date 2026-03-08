<?php $pageTitle = 'Dashboard'; ?>
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h4>
    <span class="text-muted">Hoş geldiniz, <strong><?= e($user['username'] ?? '') ?></strong></span>
</div>

<!-- Summary cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-person-circle text-primary" style="font-size:2.5rem"></i>
            <h6 class="mt-2 mb-0">Kullanıcı Adı</h6>
            <p class="text-muted"><?= e($user['username'] ?? '') ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-envelope-fill text-success" style="font-size:2.5rem"></i>
            <h6 class="mt-2 mb-0">E-posta</h6>
            <p class="text-muted"><?= e($user['email'] ?? '') ?></p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm text-center p-3">
            <i class="bi bi-person-badge-fill text-warning" style="font-size:2.5rem"></i>
            <h6 class="mt-2 mb-0">Roller</h6>
            <p class="text-muted">
                <?php if (empty($roles)): ?>
                    <em>Rol atanmamış</em>
                <?php else: ?>
                    <?php foreach ($roles as $role): ?>
                        <span class="badge bg-secondary me-1"><?= e($role['name']) ?></span>
                    <?php endforeach; ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<!-- Quick links -->
<div class="row g-3">
    <?php if (can('user.view') || has_role('admin')): ?>
    <div class="col-md-4">
        <a href="/users" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 hover-card p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-people-fill fs-3 text-primary me-3"></i>
                    <div>
                        <h6 class="mb-0">Kullanıcı Yönetimi</h6>
                        <small class="text-muted">Kullanıcıları listele, ekle, düzenle</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>
    <?php if (can('role.manage') || has_role('admin')): ?>
    <div class="col-md-4">
        <a href="/roles" class="text-decoration-none">
            <div class="card border-0 shadow-sm h-100 p-3">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-badge-fill fs-3 text-warning me-3"></i>
                    <div>
                        <h6 class="mb-0">Rol Yönetimi</h6>
                        <small class="text-muted">Rol ve izinleri yönet</small>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <?php endif; ?>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
