<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Quiz') ?> — Online Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --primary:   #6366f1;
            --secondary: #8b5cf6;
            --success:   #10b981;
            --danger:    #ef4444;
            --warning:   #f59e0b;
            --info:      #06b6d4;
            --dark:      #0f172a;
            --card-bg:   #ffffff;
        }

        * { box-sizing: border-box; }

        body {
            background: #f1f5f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }

        /* ── Navbar ── */
        .navbar {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%) !important;
            box-shadow: 0 2px 20px rgba(99,102,241,.25);
            padding: 12px 0;
        }
        .navbar-brand {
            font-weight: 800;
            font-size: 1.3rem;
            color: #fff !important;
            letter-spacing: -.3px;
        }
        .navbar-brand i { color: #818cf8; }
        .nav-link {
            color: rgba(255,255,255,.75) !important;
            font-size: .875rem;
            font-weight: 500;
            padding: 6px 14px !important;
            border-radius: 8px;
            transition: all .2s;
        }
        .nav-link:hover { color: #fff !important; background: rgba(255,255,255,.1); }
        .nav-link.active { color: #fff !important; background: rgba(99,102,241,.5); }
        .nav-link.text-danger { color: #fca5a5 !important; }
        .nav-link.text-danger:hover { color: #fff !important; background: rgba(239,68,68,.3); }

        /* ── Page wrapper ── */
        .page-wrapper { padding: 28px 0 48px; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
            transition: transform .2s, box-shadow .2s;
        }
        .card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.1); }

        /* ── Quiz card category badge colours ── */
        .cat-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .72rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            margin-bottom: 8px;
        }
        .cat-0  { background:#ede9fe; color:#7c3aed; }
        .cat-1  { background:#dbeafe; color:#1d4ed8; }
        .cat-2  { background:#dcfce7; color:#15803d; }
        .cat-3  { background:#fef9c3; color:#a16207; }
        .cat-4  { background:#fee2e2; color:#b91c1c; }
        .cat-5  { background:#e0f2fe; color:#0369a1; }
        .cat-6  { background:#fce7f3; color:#be185d; }
        .cat-7  { background:#f3e8ff; color:#7e22ce; }

        /* ── Stat boxes ── */
        .stat-box {
            border-radius: 16px;
            padding: 22px 18px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .stat-box::before {
            content: '';
            position: absolute;
            width: 80px; height: 80px;
            border-radius: 50%;
            top: -20px; right: -20px;
            opacity: .15;
        }
        .stat-box.blue   { background: linear-gradient(135deg,#6366f1,#818cf8); color:#fff; }
        .stat-box.blue::before   { background: #fff; }
        .stat-box.green  { background: linear-gradient(135deg,#10b981,#34d399); color:#fff; }
        .stat-box.green::before  { background: #fff; }
        .stat-box.red    { background: linear-gradient(135deg,#ef4444,#f87171); color:#fff; }
        .stat-box.red::before    { background: #fff; }
        .stat-box.amber  { background: linear-gradient(135deg,#f59e0b,#fbbf24); color:#fff; }
        .stat-box.amber::before  { background: #fff; }
        .stat-box .stat-value { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-box .stat-label { font-size: .8rem; opacity: .9; margin-top: 4px; }
        .stat-box i { font-size: 1.6rem; opacity: .8; margin-bottom: 8px; }

        /* ── Badge pass/fail ── */
        .badge-pass { background: #dcfce7; color: #15803d; padding: 4px 10px; border-radius: 20px; font-size:.75rem; }
        .badge-fail { background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 20px; font-size:.75rem; }
        .badge-active   { background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 20px; font-size:.75rem; }
        .badge-inactive { background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 20px; font-size:.75rem; }

        /* ── View Quiz button ── */
        .btn-view-quiz {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: .875rem;
            padding: 9px;
            transition: opacity .2s, transform .1s;
        }
        .btn-view-quiz:hover { color:#fff; opacity:.9; transform:scale(1.02); }

        /* ── Search bar ── */
        .search-wrap {
            background: #fff;
            border-radius: 14px;
            padding: 18px 20px;
            margin-bottom: 24px;
            box-shadow: 0 1px 6px rgba(0,0,0,.07);
        }
        .search-wrap .form-control,
        .search-wrap .form-select {
            border-radius: 10px;
            border-color: #e2e8f0;
            font-size: .875rem;
        }
        .search-wrap .form-control:focus,
        .search-wrap .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }

        /* ── Tables ── */
        .table thead th {
            background: #f8fafc;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }
        .table td { vertical-align: middle; }

        /* ── Alerts ── */
        .alert { border-radius: 12px; border: none; }
        .alert-success { background: #f0fdf4; color: #15803d; }
        .alert-danger  { background: #fef2f2; color: #b91c1c; }
        .alert-info    { background: #f0f9ff; color: #0369a1; }
        .alert-warning { background: #fffbeb; color: #92400e; }

        /* ── Page title ── */
        .page-heading {
            font-weight: 800;
            color: #0f172a;
            font-size: 1.4rem;
            margin-bottom: 20px;
        }

        /* ── Attempts table card ── */
        .table-card { border-radius: 16px; overflow: hidden; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container">
        <a class="navbar-brand" href="<?= site_url('user/dashboard') ?>">
            <i class="bi bi-patch-question-fill me-2"></i>Quiz App
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <i class="bi bi-list text-white fs-4"></i>
        </button>
        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-1 mt-2 mt-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(),'user/dashboard') ? 'active' : '' ?>"
                       href="<?= site_url('user/dashboard') ?>">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(),'user/quizzes') ? 'active' : '' ?>"
                       href="<?= site_url('user/quizzes') ?>">
                        <i class="bi bi-journals me-1"></i>Quizzes
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(),'user/attempts') ? 'active' : '' ?>"
                       href="<?= site_url('user/attempts') ?>">
                        <i class="bi bi-bar-chart me-1"></i>My Attempts
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= str_starts_with(uri_string(),'user/profile') ? 'active' : '' ?>"
                       href="<?= site_url('user/profile') ?>">
                        <i class="bi bi-person-circle me-1"></i><?= esc($user_name ?? 'Profile') ?>
                    </a>
                </li>
                <li class="nav-item ms-lg-2">
                    <a class="nav-link text-danger" href="<?= site_url('logout') ?>">
                        <i class="bi bi-box-arrow-left me-1"></i>Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Flash messages -->
<div class="container page-wrapper">
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-check-circle-fill fs-5"></i>
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-exclamation-circle-fill fs-5"></i>
            <?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('info')): ?>
        <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <?= session()->getFlashdata('info') ?>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('errors')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <ul class="mb-0">
                <?php foreach ((array) session()->getFlashdata('errors') as $e): ?>
                    <li><?= esc($e) ?></li>
                <?php endforeach; ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?= $this->renderSection('content') ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
setTimeout(function () {
    document.querySelectorAll('.alert').forEach(function (el) {
        var a = bootstrap.Alert.getOrCreateInstance(el);
        a.close();
    });
}, 2000);
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
