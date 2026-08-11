<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-header bg-white border-0 pt-3 pb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <h6 class="fw-semibold mb-0">Quizzes <span class="badge bg-secondary ms-1"><?= $total ?></span></h6>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <form method="get" class="d-flex flex-wrap align-items-center gap-2 mb-0">
                    <input type="text" name="search" class="form-control form-control-sm" style="width:180px;"
                           placeholder="Search title..." value="<?= esc($search) ?>">
                    <select name="category_id" class="form-select form-select-sm" style="width:150px;">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == ($category_id ?? 0)) ? 'selected' : '' ?>>
                                <?= esc($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btn btn-sm btn-outline-secondary">Filter</button>
                    <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-sm btn-outline-danger">Clear</a>
                </form>
                <a href="<?= site_url('admin/quizzes/create') ?>" class="btn btn-sm btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Add Quiz
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr><th>#</th><th>Title</th><th>Category</th><th>Duration</th><th>Questions</th><th>Pass%</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php foreach ($quizzes as $i => $q): ?>
                <tr>
                    <td class="text-muted small"><?= $i + 1 ?></td>
                    <td><?= esc($q['title']) ?></td>
                    <td><span class="badge bg-light text-dark"><?= esc($q['category_name']) ?></span></td>
                    <td><?= $q['duration'] ?> min</td>
                    <td><?= $q['total_questions'] ?></td>
                    <td><?= $q['passing_percentage'] ?>%</td>
                    <td>
                        <span class="badge <?= $q['status'] === '1' ? 'badge-active' : 'badge-inactive' ?> px-2 py-1">
                            <?= $q['status'] === '1' ? 'Active' : 'Inactive' ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= site_url('admin/questions/' . $q['id']) ?>" class="btn btn-sm btn-outline-info" title="Questions">
                            <i class="bi bi-list-ul"></i>
                        </a>
                        <a href="<?= site_url('admin/quizzes/edit/' . $q['id']) ?>" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <button class="btn btn-sm btn-outline-<?= $q['status'] === '1' ? 'warning' : 'success' ?> btn-toggle-quiz"
                                data-id="<?= $q['id'] ?>">
                            <i class="bi bi-<?= $q['status'] === '1' ? 'toggle-on' : 'toggle-off' ?>"></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($quizzes)): ?>
                <tr><td colspan="8" class="text-center text-muted py-4">No quizzes found.</td></tr>
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
$(document).on('click', '.btn-toggle-quiz', function () {
    const id = $(this).data('id');
    $.post('<?= site_url('admin/quizzes/toggle/') ?>' + id,
        { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
        function (res) { if (res.status) location.reload(); },
        'json'
    );
});
</script>
<?= $this->endSection() ?>
