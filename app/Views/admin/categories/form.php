<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header bg-white border-0 pt-3 d-flex align-items-center gap-2">
        <a href="<?= site_url('admin/categories') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h6 class="fw-semibold mb-0">Edit Category</h6>
    </div>
    <div class="card-body">
        <form method="post" action="<?= site_url('admin/categories/update/' . $category['id']) ?>">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Category Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       value="<?= esc(old('name', $category['name'])) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Description</label>
                <textarea name="description" class="form-control" rows="3"><?= esc(old('description', $category['description'] ?? '')) ?></textarea>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= (old('status', $category['status'])) === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (old('status', $category['status'])) === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="<?= site_url('admin/categories') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?= $this->endSection() ?>
