<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($page_title ?? 'Admin') ?> — Quiz Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* ── Variables ── */
        :root {
            --sidebar-bg:     #0f172a;
            --sidebar-hover:  #1e293b;
            --sidebar-active: #6366f1;
            --sidebar-w:      260px;
            --topbar-h:       64px;
            --body-bg:        #f1f5f9;
            --card-radius:    14px;
        }

        * { box-sizing: border-box; }
        body { background: var(--body-bg); font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: var(--sidebar-bg);
            position: fixed;
            top: 0; left: 0;
            z-index: 200;
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 22px 20px 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            border-bottom: 1px solid rgba(255,255,255,.07);
        }
        .sidebar-brand .brand-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem; color: #fff;
            flex-shrink: 0;
        }
        .sidebar-brand .brand-text {
            font-size: 1rem; font-weight: 800;
            color: #fff; letter-spacing: -.2px;
        }
        .sidebar-brand .brand-sub {
            font-size: .65rem; color: #64748b;
            text-transform: uppercase; letter-spacing: .08em;
        }

        .sidebar-section {
            padding: 18px 12px 6px;
            font-size: .65rem;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .1em;
            font-weight: 600;
        }

        .sidebar nav { padding: 0 10px 20px; flex: 1; }

        .nav-item-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 10px;
            margin-bottom: 2px;
            color: #94a3b8;
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            transition: all .18s;
        }
        .nav-item-link i {
            font-size: 1.05rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }
        .nav-item-link:hover {
            background: var(--sidebar-hover);
            color: #e2e8f0;
        }
        .nav-item-link.active {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #fff;
            box-shadow: 0 4px 12px rgba(99,102,241,.35);
        }
        .nav-item-link.logout {
            color: #f87171;
            margin-top: 8px;
        }
        .nav-item-link.logout:hover {
            background: rgba(239,68,68,.15);
            color: #fca5a5;
        }

        /* ── Main area ── */
        .main-content {
            margin-left: var(--sidebar-w);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Topbar ── */
        .topbar {
            height: var(--topbar-h);
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .topbar-title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .admin-avatar {
            width: 34px; height: 34px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            color: #fff; font-weight: 700; font-size: .85rem;
        }

        /* ── Page body ── */
        .page-body { padding: 24px 28px 40px; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: var(--card-radius);
            box-shadow: 0 1px 6px rgba(0,0,0,.06);
        }
        .card-header-custom {
            background: #fff;
            border-bottom: 1px solid #f1f5f9;
            border-radius: var(--card-radius) var(--card-radius) 0 0 !important;
            padding: 16px 20px;
        }

        /* ── Stat cards ── */
        .stat-card {
            border-radius: var(--card-radius);
            padding: 22px 20px;
            color: #fff;
            position: relative;
            overflow: hidden;
        }
        .stat-card::after {
            content: '';
            position: absolute;
            width: 90px; height: 90px;
            border-radius: 50%;
            bottom: -25px; right: -25px;
            background: rgba(255,255,255,.12);
        }
        .stat-card .sc-icon {
            width: 44px; height: 44px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.3rem;
            margin-bottom: 14px;
        }
        .stat-card .sc-value { font-size: 2rem; font-weight: 800; line-height: 1; }
        .stat-card .sc-label { font-size: .78rem; opacity: .85; margin-top: 4px; }
        .sc-indigo  { background: linear-gradient(135deg, #6366f1, #4f46e5); }
        .sc-violet  { background: linear-gradient(135deg, #8b5cf6, #7c3aed); }
        .sc-emerald { background: linear-gradient(135deg, #10b981, #059669); }
        .sc-amber   { background: linear-gradient(135deg, #f59e0b, #d97706); }
        .sc-cyan    { background: linear-gradient(135deg, #06b6d4, #0891b2); }
        .sc-rose    { background: linear-gradient(135deg, #f43f5e, #e11d48); }
        .sc-teal    { background: linear-gradient(135deg, #14b8a6, #0d9488); }
        .sc-orange  { background: linear-gradient(135deg, #f97316, #ea580c); }

        /* ── Tables ── */
        .table thead th {
            background: #f8fafc;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #64748b;
            font-weight: 700;
            border-bottom: 2px solid #e2e8f0;
            padding: 12px 16px;
        }
        .table td { padding: 12px 16px; vertical-align: middle; }
        .table tbody tr:hover { background: #f8fafc; }

        /* ── Badges ── */
        .badge-active   { background: #dcfce7; color: #166534; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }
        .badge-inactive { background: #fee2e2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: .72rem; font-weight: 600; }

        /* ── Alerts ── */
        .alert { border: none; border-radius: 12px; }
        .alert-success { background: #f0fdf4; color: #166534; }
        .alert-danger  { background: #fef2f2; color: #b91c1c; }
        .alert-info    { background: #f0f9ff; color: #0369a1; }

        /* ── Forms ── */
        .form-control, .form-select {
            border-radius: 10px;
            border-color: #e2e8f0;
            font-size: .875rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.15);
        }
        .form-label { font-weight: 600; font-size: .875rem; color: #374151; }

        /* ── Buttons ── */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            border: none;
            border-radius: 10px;
        }
        .btn-primary:hover { background: linear-gradient(135deg, #4f46e5, #6d28d9); border: none; }
        .btn-outline-primary { border-radius: 10px; }
        .btn-outline-secondary { border-radius: 10px; }
        .btn-outline-danger { border-radius: 10px; }
        .btn-outline-warning { border-radius: 10px; }
        .btn-outline-success { border-radius: 10px; }
        .btn-outline-info { border-radius: 10px; }
        .btn-sm { border-radius: 8px !important; }

        /* ── Page header row ── */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 22px;
        }
        .page-header h5 {
            font-size: 1.2rem;
            font-weight: 800;
            color: #0f172a;
            margin: 0;
        }

        /* ── Search bar ── */
        .filter-bar {
            background: #fff;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 18px;
            box-shadow: 0 1px 4px rgba(0,0,0,.05);
        }

        /* ── Scrollbar ── */
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: #334155; border-radius: 2px; }
    </style>
</head>
<body>

<!-- ══ SIDEBAR ══ -->
<div class="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-patch-question-fill"></i></div>
        <div>
            <div class="brand-text">Quiz Admin</div>
            <div class="brand-sub">Management Panel</div>
        </div>
    </div>

    <nav>
        <div class="sidebar-section">Main Menu</div>

        <a href="<?= site_url('admin/dashboard') ?>"
           class="nav-item-link <?= uri_string() === 'admin/dashboard' ? 'active' : '' ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <?php if (($admin_role ?? '') === 'super_admin'): ?>
        <a href="<?= site_url('admin/users') ?>"
           class="nav-item-link <?= str_starts_with(uri_string(), 'admin/users') ? 'active' : '' ?>">
            <i class="bi bi-people-fill"></i> Admin Users
        </a>
        <?php endif; ?>
        <a href="<?= site_url('admin/categories') ?>"
           class="nav-item-link <?= str_starts_with(uri_string(), 'admin/categories') ? 'active' : '' ?>">
            <i class="bi bi-tags-fill"></i> Categories
        </a>
        <a href="<?= site_url('admin/quizzes') ?>"
           class="nav-item-link <?= str_starts_with(uri_string(), 'admin/quizzes') ? 'active' : '' ?>">
            <i class="bi bi-journals"></i> Quizzes
        </a>
        <a href="<?= site_url('admin/attempts') ?>"
           class="nav-item-link <?= str_starts_with(uri_string(), 'admin/attempts') ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-fill"></i> Attempts
        </a>

        <div class="sidebar-section" style="margin-top:10px;">Account</div>

        <a href="<?= site_url('admin/profile') ?>"
           class="nav-item-link <?= str_starts_with(uri_string(), 'admin/profile') ? 'active' : '' ?>">
            <i class="bi bi-person-circle"></i> Profile
        </a>
        <a href="<?= site_url('admin/logout') ?>" class="nav-item-link logout">
            <i class="bi bi-box-arrow-left"></i> Logout
        </a>
    </nav>
</div>

<!-- ══ MAIN CONTENT ══ -->
<div class="main-content">

    <!-- Topbar -->
    <div class="topbar">
        <div class="topbar-title">
            <?= esc($page_title ?? '') ?>
        </div>
        <div class="topbar-right">
            <div class="text-end">
                <div class="fw-semibold small" style="color:#0f172a;"><?= esc($admin_name ?? '') ?></div>
                <div style="font-size:.72rem;">
                    <?php if (($admin_role ?? '') === 'super_admin'): ?>
                        <span style="color:#7c3aed;font-weight:600;">Super Admin</span>
                    <?php else: ?>
                        <span class="text-muted">Administrator</span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="admin-avatar">
                <?= strtoupper(substr($admin_name ?? 'A', 0, 1)) ?>
            </div>
        </div>
    </div>

    <!-- Flash messages -->
    <div class="page-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                <span><?= session()->getFlashdata('success') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4">
                <i class="bi bi-exclamation-circle-fill text-danger fs-5"></i>
                <span><?= session()->getFlashdata('error') ?></span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-circle-fill text-danger fs-5 mt-1"></i>
                    <ul class="mb-0 ps-2">
                        <?php foreach ((array) session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
setTimeout(function () {
    document.querySelectorAll('.alert').forEach(function (el) {
        bootstrap.Alert.getOrCreateInstance(el).close();
    });
}, 2000);
</script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
