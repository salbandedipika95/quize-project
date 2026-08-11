<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Quiz Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            background: #0f172a;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        /* Left decorative panel */
        .login-left {
            flex: 1;
            background: linear-gradient(145deg, #1e1b4b 0%, #312e81 50%, #4c1d95 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 48px;
            position: relative;
            overflow: hidden;
        }
        .login-left::before {
            content: '';
            position: absolute;
            width: 400px; height: 400px;
            border-radius: 50%;
            background: rgba(99,102,241,.2);
            top: -100px; left: -100px;
        }
        .login-left::after {
            content: '';
            position: absolute;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(139,92,246,.15);
            bottom: -80px; right: -80px;
        }
        .login-left .content { position: relative; z-index: 1; text-align: center; color: #fff; }
        .login-left .big-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 24px;
            display: flex; align-items: center; justify-content: center;
            font-size: 2.2rem;
            margin: 0 auto 24px;
            box-shadow: 0 8px 32px rgba(99,102,241,.4);
        }
        .login-left h2 { font-weight: 800; font-size: 1.8rem; margin-bottom: 10px; }
        .login-left p  { color: rgba(255,255,255,.6); font-size: .9rem; max-width: 280px; margin: 0 auto; line-height: 1.6; }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.12);
            border-radius: 20px;
            padding: 7px 16px;
            font-size: .8rem;
            color: rgba(255,255,255,.8);
            margin: 6px 4px;
        }
        .stat-pill i { color: #a78bfa; }

        /* Right login form */
        .login-right {
            width: 460px;
            flex-shrink: 0;
            background: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 40px;
        }
        .login-form-wrap { width: 100%; }
        .login-form-wrap h4 { font-weight: 800; color: #0f172a; margin-bottom: 4px; }
        .login-form-wrap p  { color: #64748b; font-size: .875rem; margin-bottom: 28px; }

        .form-floating label { color: #94a3b8; font-size: .875rem; }
        .form-floating .form-control {
            border-radius: 12px;
            border-color: #e2e8f0;
            font-size: .9rem;
            padding-top: 1.4rem;
        }
        .form-floating .form-control:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        .btn-login {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 13px;
            font-size: .95rem;
            font-weight: 700;
            width: 100%;
            transition: opacity .2s, transform .1s;
        }
        .btn-login:hover { color: #fff; opacity: .9; transform: translateY(-1px); }

        @media (max-width: 768px) {
            .login-left { display: none; }
            .login-right { width: 100%; padding: 32px 24px; }
        }
    </style>
</head>
<body>

<!-- Left panel -->
<div class="login-left">
    <div class="content">
        <div class="big-icon"><i class="bi bi-patch-question-fill"></i></div>
        <h2>Quiz Admin Panel</h2>
        <p>Manage quizzes, users, categories, and track performance — all in one place.</p>
        
    </div>
</div>

<!-- Right form -->
<div class="login-right">
    <div class="login-form-wrap">
        <h4>Welcome back</h4>
        <p>Sign in to your admin account to continue.</p>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger d-flex align-items-center gap-2 mb-4" style="border-radius:12px;background:#fef2f2;border:none;">
                <i class="bi bi-exclamation-circle-fill text-danger"></i>
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('admin/login') ?>" method="post">
            <?= csrf_field() ?>

            <div class="form-floating mb-3">
                <input type="email" name="email" id="email" class="form-control"
                       placeholder="admin@example.com"
                       value="<?= old('email') ?>" required autofocus>
                <label for="email"><i class="bi bi-envelope me-1"></i>Email Address</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="Password" required>
                <label for="password"><i class="bi bi-lock me-1"></i>Password</label>
            </div>

            <button type="submit" class="btn-login">
                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
            </button>
        </form>
    </div>
</div>

</body>
</html>
