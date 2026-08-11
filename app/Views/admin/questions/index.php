<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-sm btn-outline-secondary me-2">
            <i class="bi bi-arrow-left"></i> Back
        </a>
        <span class="fw-semibold"><?= esc($quiz['title']) ?></span>
    </div>
    <a href="<?= site_url('admin/questions/create/' . $quiz['id']) ?>" class="btn btn-sm btn-primary">
        <i class="bi bi-plus-lg me-1"></i>Add Question
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Question</th><th>Marks</th><th>Options</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($questions as $i => $q): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td><?= esc(substr($q['question_text'], 0, 80)) ?><?= strlen($q['question_text']) > 80 ? '…' : '' ?></td>
                    <td><?= $q['marks'] ?></td>
                    <td><span class="badge bg-light text-dark"><?= $q['option_count'] ?></span></td>
                    <td>
                        <span class="badge <?= $q['status'] === '1' ? 'badge-active' : 'badge-inactive' ?> px-2 py-1">
                            <?= $q['status'] === '1' ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= site_url('admin/questions/edit/' . $q['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-<?= $q['status'] === '1' ? 'warning' : 'success' ?> btn-toggle-q"
                                data-id="<?= $q['id'] ?>">
                            <i class="bi bi-<?= $q['status'] === '1' ? 'toggle-on' : 'toggle-off' ?>"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($questions)): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted py-4">
                        No questions yet.
                        <a href="<?= site_url('admin/questions/create/' . $quiz['id']) ?>">Add one</a>.
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    <?php if (isset($pager)): ?>
        <div class="card-footer bg-white border-0"><?= $pager->links() ?></div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
$(document).on('click', '.btn-toggle-q', function () {
    const id = $(this).data('id');
    $.post('<?= site_url('admin/questions/toggle/') ?>' + id,
        { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        function (res) { if (res.status) location.reload(); },
        'json'
    );
});
</script>
<?= $this->endSection() ?>
