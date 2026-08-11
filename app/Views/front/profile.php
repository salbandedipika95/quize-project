<?= $this->extend('front/layouts/main') ?>
<?= $this->section('content') ?>

<div class="row justify-content-center">
<div class="col-lg-8">

    <h5 class="fw-bold mb-4">My Profile</h5>

    <div class="row g-3">
        <!-- Profile info -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-person me-2 text-primary"></i>Profile Information</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('user/profile/update') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Full Name</label>
                            <input type="text" name="name" class="form-control"
                                   value="<?= esc($user['name'] ?? '') ?>" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control"
                                   value="<?= esc($user['email'] ?? '') ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Profile</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Change password -->
        <div class="col-md-6">
            <div class="card h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-semibold mb-0"><i class="bi bi-shield-lock me-2 text-warning"></i>Change Password</h6>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= site_url('user/profile/password') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Current Password</label>
                            <input type="password" name="current_password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">New Password</label>
                            <input type="password" name="new_password" class="form-control"
                                   required minlength="6">
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Change Password</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Account info -->
    <div class="card mt-3">
        <div class="card-body">
            <div class="row g-2 text-center text-md-start">
                <div class="col-md-4">
                    <div class="text-muted small">Member Since</div>
                    <div class="fw-semibold">
                        <?= isset($user['created_on']) ? date('d M Y', strtotime($user['created_on'])) : '—' ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Last Login</div>
                    <div class="fw-semibold">
                        <?= isset($user['last_login']) && $user['last_login']
                            ? date('d M Y, h:i A', strtotime($user['last_login']))
                            : '—' ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Account Status</div>
                    <div>
                        <span class="badge <?= ($user['status'] ?? '0') === '1' ? 'badge-pass' : 'badge-fail' ?> px-2 py-1">
                            <?= ($user['status'] ?? '0') === '1' ? 'Active' : 'Inactive' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
</div>

<?= $this->endSection() ?>