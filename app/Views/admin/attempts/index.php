<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="fw-semibold mb-0">All Attempts <span class="badge bg-secondary ms-1"><?= $total ?></span></h6>
        <form method="get" class="d-flex gap-2">
            <input type="text" name="search" class="form-control form-control-sm"
                   placeholder="Search user or quiz..." value="<?= esc($search) ?>">
            <button class="btn btn-sm btn-outline-secondary">Search</button>
            <?php if ($search): ?>
                <a href="<?= site_url('admin/attempts') ?>" class="btn btn-sm btn-outline-danger">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>User</th><th>Quiz</th><th>Score</th><th>%</th><th>Status</th><th>Date</th><th></th></tr>
            </thead>
            <tbody>
            <?php foreach ($attempts as $i => $a): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td>
                        <?= esc($a['user_name']) ?><br>
                        <small class="text-muted"><?= esc($a['user_email']) ?></small>
                    </td>
                    <td><?= esc($a['quiz_title']) ?></td>
                    <td><?= $a['score'] ?> / <?= $a['total_marks'] ?></td>
                    <td><strong><?= $a['percentage'] ?>%</strong></td>
                    <td>
                        <?php if ($a['status'] === '1'): ?>
                            <span class="badge badge-active px-2 py-1">Completed</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark px-2 py-1">In Progress</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-muted small">
                        <?= $a['created_on'] ? date('d M Y', strtotime($a['created_on'])) : '—' ?>
                    </td>
                    <td>
                        <a href="<?= site_url('admin/attempts/view/' . $a['id']) ?>" class="btn btn-sm btn-outline-info">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($attempts)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No attempts found.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?>
        <div class="card-footer bg-white border-0"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
