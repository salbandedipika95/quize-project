<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register — Quiz App</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body { background:linear-gradient(135deg,#1e2a3a,#2e4a6e); min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .auth-card { width:100%; max-width:480px; border-radius:16px; overflow:hidden; box-shadow:0 12px 40px rgba(0,0,0,.3); }
        .auth-header { background:rgba(255,255,255,.08); backdrop-filter:blur(10px); padding:32px; text-align:center; color:#fff; }
        .auth-body { background:#fff; padding:36px; }
    </style>
</head>
<body>
<div class="auth-card">
    <div class="auth-header">
        <i class="bi bi-person-plus-fill" style="font-size:2.5rem;"></i>
        <h4 class="mt-2 mb-1">Create Account</h4>
        <p class="text-white-50 small mb-0">Join and start testing your knowledge</p>
    </div>
    <div class="auth-body">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger">
                <ul class="mb-0"><?php foreach ((array) session()->getFlashdata('errors') as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('register') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3">
                <label class="form-label fw-semibold">Full Name</label>
                <input type="text" name="name" class="form-control" value="<?= old('name') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control" value="<?= old('email') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold">Password</label>
                <input type="password" name="password" class="form-control" required minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Confirm Password</label>
                <input type="password" name="confirm_password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Create Account</button>
        </form>
        <p class="text-center mt-3 mb-0 text-muted small">Already have an account? <a href="<?= site_url('login') ?>">Login</a></p>
    </div>
</div>
</body>
</html>
