<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<!-- Welcome row -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold mb-1" style="color:#0f172a;">
            Welcome back, <?= esc($admin_name) ?> 👋
        </h5>
        <p class="text-muted small mb-0">Here's what's happening with your quiz platform today.</p>
    </div>
    <a href="<?= site_url('admin/quizzes/create') ?>" class="btn btn-primary px-4">
        <i class="bi bi-plus-lg me-2"></i>New Quiz
    </a>
</div>

<!-- Stat Cards Row 1 -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card sc-indigo">
            <div class="sc-icon"><i class="bi bi-people-fill"></i></div>
            <div class="sc-value"><?= $stats['total_users'] ?></div>
            <div class="sc-label">Total Users</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card sc-violet">
            <div class="sc-icon"><i class="bi bi-tags-fill"></i></div>
            <div class="sc-value"><?= $stats['total_categories'] ?></div>
            <div class="sc-label">Categories</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card sc-emerald">
            <div class="sc-icon"><i class="bi bi-journals"></i></div>
            <div class="sc-value"><?= $stats['total_quizzes'] ?></div>
            <div class="sc-label">Total Quizzes</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card sc-amber">
            <div class="sc-icon"><i class="bi bi-bar-chart-fill"></i></div>
            <div class="sc-value"><?= $stats['total_attempts'] ?></div>
            <div class="sc-label">Total Attempts</div>
        </div>
    </div>
</div>

<!-- Quick links row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <a href="<?= site_url('admin/users') ?>" class="text-decoration-none">
            <div class="card p-3 d-flex flex-row align-items-center gap-3"
                 style="border-left:4px solid #6366f1;">
                <div style="width:40px;height:40px;background:#ede9fe;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-people-fill text-primary fs-5"></i>
                </div>
                <div>
                    <div class="fw-semibold small" style="color:#0f172a;">Manage Users</div>
                    <div class="text-muted" style="font-size:.75rem;"><?= $stats['active_users'] ?? $stats['total_users'] ?> active</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted small"></i>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= site_url('admin/categories') ?>" class="text-decoration-none">
            <div class="card p-3 d-flex flex-row align-items-center gap-3"
                 style="border-left:4px solid #8b5cf6;">
                <div style="width:40px;height:40px;background:#f3e8ff;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-tags-fill fs-5" style="color:#8b5cf6;"></i>
                </div>
                <div>
                    <div class="fw-semibold small" style="color:#0f172a;">Categories</div>
                    <div class="text-muted" style="font-size:.75rem;"><?= $stats['total_categories'] ?> total</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted small"></i>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= site_url('admin/quizzes') ?>" class="text-decoration-none">
            <div class="card p-3 d-flex flex-row align-items-center gap-3"
                 style="border-left:4px solid #10b981;">
                <div style="width:40px;height:40px;background:#dcfce7;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-journals fs-5 text-success"></i>
                </div>
                <div>
                    <div class="fw-semibold small" style="color:#0f172a;">Quizzes</div>
                    <div class="text-muted" style="font-size:.75rem;"><?= $stats['active_quizzes'] ?? $stats['total_quizzes'] ?> active</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted small"></i>
            </div>
        </a>
    </div>
    <div class="col-sm-6 col-xl-3">
        <a href="<?= site_url('admin/attempts') ?>" class="text-decoration-none">
            <div class="card p-3 d-flex flex-row align-items-center gap-3"
                 style="border-left:4px solid #f59e0b;">
                <div style="width:40px;height:40px;background:#fef9c3;border-radius:10px;display:flex;align-items:center;justify-content:center;">
                    <i class="bi bi-bar-chart-fill fs-5 text-warning"></i>
                </div>
                <div>
                    <div class="fw-semibold small" style="color:#0f172a;">Attempts</div>
                    <div class="text-muted" style="font-size:.75rem;"><?= $stats['completed_attempts'] ?? $stats['total_attempts'] ?> completed</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted small"></i>
            </div>
        </a>
    </div>
</div>

<!-- Tables row -->
<div class="row g-3">
    <!-- Recent Users -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <span style="width:8px;height:8px;background:#6366f1;border-radius:50%;display:inline-block;"></span>
                    Recent Users
                </h6>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>Name</th><th>Email</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_users as $u): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div style="width:30px;height:30px;background:linear-gradient(135deg,#6366f1,#8b5cf6);border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0;">
                                        <?= strtoupper(substr($u['name'], 0, 1)) ?>
                                    </div>
                                    <span class="fw-semibold small"><?= esc($u['name']) ?></span>
                                </div>
                            </td>
                            <td class="text-muted small"><?= esc($u['email']) ?></td>
                            <td>
                                <span class="<?= $u['status'] === '1' ? 'badge-active' : 'badge-inactive' ?>">
                                    <?= $u['status'] === '1' ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_users)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4 small">No users yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Recent Attempts -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header-custom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <span style="width:8px;height:8px;background:#10b981;border-radius:50%;display:inline-block;"></span>
                    Recent Attempts
                </h6>
                <a href="<?= site_url('admin/attempts') ?>" class="btn btn-sm btn-outline-success">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead><tr><th>User</th><th>Quiz</th><th>Score</th></tr></thead>
                    <tbody>
                    <?php foreach ($recent_attempts as $a): ?>
                        <tr>
                            <td class="fw-semibold small"><?= esc($a['user_name']) ?></td>
                            <td class="text-muted small"><?= esc(substr($a['quiz_title'], 0, 25)) ?><?= strlen($a['quiz_title']) > 25 ? '…' : '' ?></td>
                            <td>
                                <span class="fw-bold" style="color:<?= (float)$a['percentage'] >= 50 ? '#16a34a' : '#dc2626' ?>;">
                                    <?= $a['percentage'] ?>%
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recent_attempts)): ?>
                        <tr><td colspan="3" class="text-center text-muted py-4 small">No attempts yet.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
