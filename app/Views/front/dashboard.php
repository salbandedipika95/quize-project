<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<!-- Welcome -->
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="page-heading mb-1">
            <i class="bi bi-hand-wave me-2" style="color:#f59e0b;"></i>Hello, <?= esc($user_name) ?>!
        </h4>
        <p class="text-muted small mb-0">Ready to test your knowledge today?</p>
    </div>
    <a href="<?= site_url('user/quizzes') ?>" class="btn fw-semibold px-4"
       style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:12px;">
        <i class="bi bi-journals me-2"></i>Browse Quizzes
    </a>
</div>

<!-- Stat Boxes -->
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="stat-box blue">
            <i class="bi bi-bar-chart-fill d-block"></i>
            <div class="stat-value"><?= $stats['total'] ?></div>
            <div class="stat-label">Total Attempts</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box green">
            <i class="bi bi-patch-check-fill d-block"></i>
            <div class="stat-value"><?= $stats['passed'] ?></div>
            <div class="stat-label">Passed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box red">
            <i class="bi bi-x-circle-fill d-block"></i>
            <div class="stat-value"><?= $stats['failed'] ?></div>
            <div class="stat-label">Failed</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="stat-box amber">
            <i class="bi bi-trophy-fill d-block"></i>
            <div class="stat-value"><?= $stats['avg_percentage'] ?>%</div>
            <div class="stat-label">Avg Score</div>
        </div>
    </div>
</div>

<!-- Recent Attempts -->
<div class="card table-card">
    <div class="card-header bg-white border-0 pt-4 pb-3 px-4 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0" style="color:#0f172a;">
            <i class="bi bi-clock-history me-2 text-primary"></i>Recent Attempts
        </h6>
        <a href="<?= site_url('user/attempts') ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
            View All
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4">Quiz</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Result</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_attempts as $a): ?>
                    <?php $passed = (float)$a['percentage'] >= (float)$a['passing_percentage']; ?>
                    <tr>
                        <td class="px-4 fw-semibold" style="color:#0f172a;"><?= esc($a['quiz_title']) ?></td>
                        <td><?= $a['score'] ?> / <?= $a['total_marks'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;border-radius:3px;width:80px;">
                                    <div class="progress-bar <?= $passed ? 'bg-success' : 'bg-danger' ?>"
                                         style="width:<?= $a['percentage'] ?>%"></div>
                                </div>
                                <span class="fw-semibold small"><?= $a['percentage'] ?>%</span>
                            </div>
                        </td>
                        <td>
                            <span class="<?= $passed ? 'badge-pass' : 'badge-fail' ?>">
                                <?= $passed ? 'PASS' : 'FAIL' ?>
                            </span>
                        </td>
                        <td class="text-muted small">
                            <?= $a['completed_at'] ? date('d M Y', strtotime($a['completed_at'])) : '—' ?>
                        </td>
                        <td>
                            <a href="<?= site_url('user/result/' . $a['id']) ?>"
                               class="btn btn-sm fw-semibold"
                               style="background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:.78rem;">
                                <i class="bi bi-eye me-1"></i>View
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_attempts)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-5">
                            <i class="bi bi-journals" style="font-size:2.5rem;color:#cbd5e1;"></i>
                            <p class="text-muted mt-2 mb-2">No attempts yet.</p>
                            <a href="<?= site_url('user/quizzes') ?>" class="btn btn-primary btn-sm">
                                Take Your First Quiz
                            </a>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>