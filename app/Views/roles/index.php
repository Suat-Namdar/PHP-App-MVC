<?php $pageTitle = 'Roller'; ?>
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="bi bi-person-badge-fill me-2"></i>Roller</h4>
    <?php if (can('role.manage') || has_role('admin')): ?>
        <a href="/roles/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Yeni Rol
        </a>
    <?php endif; ?>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Rol Adı</th>
                        <th>Açıklama</th>
                        <th>İzinler</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($roles)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Henüz rol bulunmuyor.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php
                        $roleModel = new \App\Models\Role();
                        foreach ($roles as $role):
                            $perms = $roleModel->getPermissions((int)$role['id']);
                        ?>
                            <tr>
                                <td><?= e($role['id']) ?></td>
                                <td>
                                    <span class="badge bg-primary"><?= e($role['name']) ?></span>
                                </td>
                                <td class="text-muted"><?= e($role['description'] ?? '') ?></td>
                                <td>
                                    <?php if (empty($perms)): ?>
                                        <span class="text-muted small">–</span>
                                    <?php else: ?>
                                        <?php foreach ($perms as $p): ?>
                                            <span class="badge bg-light text-dark border me-1 small">
                                                <?= e($p['name']) ?>
                                            </span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td class="text-end">
                                    <?php if (can('role.manage') || has_role('admin')): ?>
                                        <a href="/roles/<?= e($role['id']) ?>/edit"
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                        <form action="/roles/<?= e($role['id']) ?>/delete"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu rolü silmek istediğinizden emin misiniz?')">
                                            <?= csrf_field() ?>
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require BASE_PATH . '/app/Views/layouts/footer.php'; ?>
