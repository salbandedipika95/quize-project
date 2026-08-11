<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-7">
<div class="card">
    <div class="card-header bg-white border-0 pt-3 d-flex align-items-center gap-2">
        <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h6 class="fw-semibold mb-0"><?= isset($quiz) ? 'Edit Quiz' : 'Create Quiz' ?></h6>
    </div>
    <div class="card-body">
        <form method="post" action="<?= isset($quiz) ? site_url('admin/quizzes/update/' . $quiz['id']) : site_url('admin/quizzes/store') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Quiz Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control"
                       value="<?= esc(old('title', $quiz['title'] ?? '')) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Category <span class="text-danger">*</span></label>
                <select name="category_id" class="form-select" required>
                    <option value="">-- Select Category --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>"
                            <?= (old('category_id', $quiz['category_id'] ?? '') == $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= esc(old('description', $quiz['description'] ?? '')) ?></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Duration (minutes) <span class="text-danger">*</span></label>
                    <input type="number" name="duration" class="form-control"
                           value="<?= old('duration', $quiz['duration'] ?? 30) ?>" min="1" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-semibold">Passing % <span class="text-danger">*</span></label>
                    <input type="number" name="passing_percentage" class="form-control"
                           value="<?= old('passing_percentage', $quiz['passing_percentage'] ?? 50) ?>"
                           min="1" max="100" step="0.01" required>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= (old('status', $quiz['status'] ?? '1')) === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (old('status', $quiz['status'] ?? '1')) === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <?= isset($quiz) ? 'Update Quiz' : 'Create Quiz' ?>
                </button>
                <a href="<?= site_url('admin/quizzes') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?= $this->endSection() ?>
