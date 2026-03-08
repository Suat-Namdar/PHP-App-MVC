<?php $pageTitle = 'Kullanıcılar'; ?>
<?php require BASE_PATH . '/app/Views/layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="bi bi-people-fill me-2"></i>Kullanıcılar</h4>
    <?php if (can('user.create') || has_role('admin')): ?>
        <a href="/users/create" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Yeni Kullanıcı
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
                        <th>Kullanıcı Adı</th>
                        <th>E-posta</th>
                        <th>Kayıt Tarihi</th>
                        <th class="text-end">İşlemler</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-4 d-block mb-1"></i>
                                Henüz kullanıcı bulunmuyor.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?= e($u['id']) ?></td>
                                <td>
                                    <i class="bi bi-person-circle text-secondary me-1"></i>
                                    <?= e($u['username']) ?>
                                </td>
                                <td><?= e($u['email']) ?></td>
                                <td><?= e($u['created_at'] ?? '') ?></td>
                                <td class="text-end">
                                    <?php if (can('user.edit') || has_role('admin')): ?>
                                        <a href="/users/<?= e($u['id']) ?>/edit"
                                           class="btn btn-sm btn-outline-primary me-1">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if (can('user.delete') || has_role('admin')): ?>
                                        <form action="/users/<?= e($u['id']) ?>/delete"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Bu kullanıcıyı silmek istediğinizden emin misiniz?')">
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
