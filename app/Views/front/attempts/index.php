<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h4 class="page-heading mb-0">
        <i class="bi bi-bar-chart me-2 text-primary"></i>My Attempts
        <span class="badge bg-secondary ms-2 fs-6"><?= $total ?></span>
    </h4>
    <a href="<?= site_url('user/quizzes') ?>" class="btn fw-semibold"
       style="background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border-radius:12px;">
        <i class="bi bi-journals me-2"></i>Browse Quizzes
    </a>
</div>

<div class="card table-card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="px-4">#</th>
                        <th>Quiz</th>
                        <th>Category</th>
                        <th>Score</th>
                        <th>Percentage</th>
                        <th>Result</th>
                        <th>Date</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($attempts as $i => $a): ?>
                    <?php $passed = (float) $a['percentage'] >= (float) $a['passing_percentage']; ?>
                    <tr>
                        <td class="px-4 text-muted small"><?= $i + 1 ?></td>
                        <td class="fw-semibold" style="color:#0f172a;"><?= esc($a['quiz_title']) ?></td>
                        <td>
                            <span class="cat-badge cat-<?= $i % 8 ?>">
                                <?= esc($a['category_name']) ?>
                            </span>
                        </td>
                        <td><?= $a['score'] ?> / <?= $a['total_marks'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height:6px;border-radius:3px;width:70px;">
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
                            <?= $a['completed_at'] ? date('d M Y, h:i A', strtotime($a['completed_at'])) : '—' ?>
                        </td>
                        <td>
                            <?php if ($a['status'] === '1'): ?>
                                <a href="<?= site_url('user/result/' . $a['id']) ?>"
                                   class="btn btn-sm fw-semibold"
                                   style="background:#ede9fe;color:#7c3aed;border-radius:8px;font-size:.78rem;">
                                    <i class="bi bi-eye me-1"></i>Result
                                </a>
                            <?php else: ?>
                                <a href="<?= site_url('user/attempt/' . $a['id']) ?>"
                                   class="btn btn-sm fw-semibold"
                                   style="background:#fef9c3;color:#a16207;border-radius:8px;font-size:.78rem;">
                                    <i class="bi bi-play-fill me-1"></i>Resume
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($attempts)): ?>
                    <tr>
                        <td colspan="8" class="text-center py-5">
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
    <?php if (isset($pager)): ?>
        <div class="card-footer bg-white border-0 py-3"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
