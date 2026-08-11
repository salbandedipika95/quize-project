<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-6">
<div class="card">
    <div class="card-header-custom d-flex align-items-center gap-2">
        <a href="<?= site_url('admin/users') ?>" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h6 class="fw-bold mb-0">
            <?= isset($user) ? 'Edit Admin User' : 'Create Admin User' ?>
        </h6>
    </div>
    <div class="card-body p-4">
        <form method="post" action="<?= isset($user) ? site_url('admin/users/update/' . $user['id']) : site_url('admin/users/store') ?>">
            <?= csrf_field() ?>

            <!-- Name -->
            <div class="mb-3">
                <label class="form-label">Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control"
                       placeholder="Enter full name"
                       value="<?= esc(old('name', $user['name'] ?? '')) ?>" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label class="form-label">Email Address <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control"
                       placeholder="admin@example.com"
                       value="<?= esc(old('email', $user['email'] ?? '')) ?>" required>
            </div>

            <!-- Password -->
            <div class="mb-3">
                <label class="form-label">
                    Password
                    <?php if (isset($user)): ?>
                        <small class="text-muted fw-normal">(leave blank to keep current)</small>
                    <?php else: ?>
                        <span class="text-danger">*</span>
                    <?php endif; ?>
                </label>
                <input type="password" name="password" class="form-control"
                       placeholder="Min 6 characters"
                       <?= isset($user) ? '' : 'required' ?> minlength="6">
            </div>

            <!-- Role -->
            <div class="mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <select name="role" class="form-select" required>
                    <option value="">-- Select Role --</option>
                    <option value="admin"
                        <?= (old('role', $user['role'] ?? '') === 'admin') ? 'selected' : '' ?>>
                        Admin
                    </option>
                    <option value="super_admin"
                        <?= (old('role', $user['role'] ?? '') === 'super_admin') ? 'selected' : '' ?>>
                        Super Admin
                    </option>
                </select>
                <div class="form-text">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Super Admin</strong> can manage admin users.
                    <strong>Admin</strong> manages quizzes, categories and attempts only.
                </div>
            </div>

            <!-- Status -->
            <div class="mb-4">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" <?= (old('status', $user['status'] ?? '1')) === '1' ? 'selected' : '' ?>>Active</option>
                    <option value="0" <?= (old('status', $user['status'] ?? '1')) === '0' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-<?= isset($user) ? 'check-lg' : 'plus-lg' ?> me-1"></i>
                    <?= isset($user) ? 'Update Admin' : 'Create Admin' ?>
                </button>
                <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</div>
</div>

<?= $this->endSection() ?>
