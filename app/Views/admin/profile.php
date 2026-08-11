<?= $this->extend('admin/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row g-3">
    <!-- Profile Info -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Profile Information</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/profile/update') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="<?= esc($admin['name'] ?? '') ?>" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               value="<?= esc($admin['email'] ?? '') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="col-lg-6">
        <div class="card">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-semibold mb-0">Change Password</h6>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/profile/password') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Current Password</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">New Password</label>
                        <input type="password" name="new_password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Confirm New Password</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
