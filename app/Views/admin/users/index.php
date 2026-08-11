<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header-custom d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-shield-fill-check me-2 text-primary"></i>
            Admin Users
            <span class="badge bg-secondary ms-1 fw-normal"><?= $total ?></span>
        </h6>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <form method="get" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm"
                       placeholder="Search name or email..." value="<?= esc($search) ?>"
                       style="width:220px;">
                <button class="btn btn-sm btn-outline-secondary">Search</button>
                <?php if ($search): ?>
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-outline-danger">Clear</a>
                <?php endif; ?>
            </form>
            <a href="<?= site_url('admin/users/create') ?>" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Add Admin
            </a>
        </div>
    </div>

    <div class="card-body p-0">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Last Login</th>
                    <th>Created On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $i => $u): ?>
                <tr <?= $u['id'] == $admin_id ? 'style="background:#f5f3ff;"' : '' ?>>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:32px;height:32px;background:linear-gradient(135deg,<?= $u['role'] === 'super_admin' ? '#7c3aed,#6366f1' : '#0891b2,#06b6d4' ?>);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.78rem;font-weight:700;flex-shrink:0;">
                                <?= strtoupper(substr($u['name'], 0, 1)) ?>
                            </div>
                            <span class="fw-semibold small">
                                <?= esc($u['name']) ?>
                                <?php if ($u['id'] == $admin_id): ?>
                                    <span style="color:#7c3aed;font-size:.72rem;font-weight:700;"> (You)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </td>
                    <td class="text-muted small"><?= esc($u['email']) ?></td>
                    <td>
                        <?php if ($u['role'] === 'super_admin'): ?>
                            <span style="background:#ede9fe;color:#7c3aed;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;">
                                <i class="bi bi-shield-fill-check me-1"></i>Super Admin
                            </span>
                        <?php else: ?>
                            <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:20px;font-size:.72rem;font-weight:700;">
                                <i class="bi bi-person-fill me-1"></i>Admin
                            </span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <span class="<?= $u['status'] === '1' ? 'badge-active' : 'badge-inactive' ?>">
                            <?= $u['status'] === '1' ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td class="text-muted small">
                        <?= $u['last_login'] ? date('d M Y, h:i A', strtotime($u['last_login'])) : '—' ?>
                    </td>
                    <td class="text-muted small">
                        <?= $u['created_on'] ? date('d M Y, h:i A', strtotime($u['created_on'])) : '—' ?>
                    </td>
                    <td>
                        <?php if ($u['id'] == $admin_id): ?>
                            <!-- Current logged-in user — no actions allowed on self -->
                            <span style="background:#f1f5f9;color:#64748b;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;">
                                <i class="bi bi-person-check me-1"></i>You
                            </span>
                        <?php else: ?>
                            <a href="<?= site_url('admin/users/edit/' . $u['id']) ?>"
                               class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-outline-<?= $u['status'] === '1' ? 'warning' : 'success' ?> btn-toggle"
                                    data-id="<?= $u['id'] ?>" title="Toggle Status">
                                <i class="bi bi-<?= $u['status'] === '1' ? 'toggle-on' : 'toggle-off' ?>"></i>
                            </button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($users)): ?>
                <tr>
                    <td colspan="8" class="text-center text-muted py-5">
                        <i class="bi bi-people" style="font-size:2rem;color:#cbd5e1;"></i>
                        <p class="mt-2 mb-0 small">No admin users found.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if (isset($pager)): ?>
        <div class="card-footer bg-white border-0 py-3"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$(document).on('click', '.btn-toggle', function () {
    const id = $(this).data('id');
    $.post('<?= site_url('admin/users/toggle/') ?>' + id,
        { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        function (res) { if (res.status) location.reload(); },
        'json'
    );
});
</script>
<?= $this->endSection() ?>
