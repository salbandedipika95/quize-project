<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="mb-3">
    <a href="<?= site_url('admin/attempts') ?>" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card p-3">
            <h6 class="fw-semibold mb-3">Attempt Summary</h6>
            <table class="table table-sm mb-0">
                <tr><td class="text-muted">User</td><td><?= esc($attempt['user_name']) ?></td></tr>
                <tr><td class="text-muted">Quiz</td><td><?= esc($attempt['quiz_title']) ?></td></tr>
                <tr><td class="text-muted">Score</td><td><?= $attempt['score'] ?> / <?= $attempt['total_marks'] ?></td></tr>
                <tr><td class="text-muted">Percentage</td><td><strong><?= $attempt['percentage'] ?>%</strong></td></tr>
                <tr>
                    <td class="text-muted">Status</td>
                    <td>
                        <?= $attempt['status'] === '1'
                            ? '<span class="badge badge-active">Completed</span>'
                            : '<span class="badge bg-warning text-dark">In Progress</span>' ?>
                    </td>
                </tr>
                <tr><td class="text-muted">Started</td><td><?= $attempt['started_at'] ?></td></tr>
                <tr><td class="text-muted">Completed</td><td><?= $attempt['completed_at'] ?? '—' ?></td></tr>
            </table>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-white border-0 pt-3">
        <h6 class="fw-semibold mb-0">Answer Analysis</h6>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Question</th><th>Selected</th><th>Correct</th><th>Marks</th><th>Result</th></tr>
            </thead>
            <tbody>
            <?php foreach ($answers as $i => $ans): ?>
                <tr class="<?= $ans['is_correct'] === '1' ? 'table-success' : 'table-danger' ?>">
                    <td><?= $i + 1 ?></td>
                    <td><?= esc($ans['question_text']) ?></td>
                    <td><?= esc($ans['selected_option_text'] ?? '—') ?></td>
                    <td><?= esc($ans['correct_option_text'] ?? '—') ?></td>
                    <td><?= $ans['marks_obtained'] ?></td>
                    <td><?= $ans['is_correct'] === '1' ? '✅' : '❌' ?></td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($answers)): ?>
                <tr><td colspan="6" class="text-center text-muted py-3">No answers recorded.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
