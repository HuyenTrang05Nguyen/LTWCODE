<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - VietTravel Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:      #0F172A;
            --gold:      #D4A373;
            --gold-dark: #b8864e;
            --cream:     #FAF7F2;
            --beige:     #E7D7C9;
            --slate:     #334155;
            --forest:    #1a3a2a;
            --sidebar-w: 260px;
            --glass-border: rgba(212,163,115,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--cream); color:var(--navy); min-height:100vh; }

        /* ── Sidebar ── */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--navy);
            min-height: 100vh;
            position: fixed;
            left: 0; top: 0;
            z-index: 100;
            transition: transform 0.3s ease;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(212,163,115,0.12);
        }
        .admin-sidebar .brand {
            padding: 1.75rem 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(212,163,115,0.12);
        }
        .admin-sidebar .brand-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.2rem;
            color: var(--gold);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .admin-sidebar .brand-sub {
            color: rgba(255,255,255,0.35);
            font-size: 0.75rem;
            margin-top: 0.2rem;
            font-weight: 400;
        }
        .admin-sidebar nav { padding: 1rem 0; flex: 1; }
        .admin-sidebar .nav-section {
            padding: 0.5rem 1.5rem 0.25rem;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
            margin-top: 0.5rem;
        }
        .admin-sidebar .nav-link {
            color: rgba(255,255,255,0.55) !important;
            padding: 0.7rem 1.5rem !important;
            transition: all 0.25s ease;
            border-left: 3px solid transparent;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 0.88rem;
            font-weight: 500;
            text-decoration: none;
        }
        .admin-sidebar .nav-link i {
            width: 20px;
            text-align: center;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.35);
            transition: color 0.25s;
        }
        .admin-sidebar .nav-link:hover {
            color: var(--gold) !important;
            background: rgba(212,163,115,0.08);
            border-left-color: rgba(212,163,115,0.4);
        }
        .admin-sidebar .nav-link:hover i { color: var(--gold); }
        .admin-sidebar .nav-link.active {
            color: var(--gold) !important;
            background: rgba(212,163,115,0.12);
            border-left-color: var(--gold);
        }
        .admin-sidebar .nav-link.active i { color: var(--gold); }
        .admin-sidebar .sidebar-footer {
            padding: 1rem 1.5rem;
            border-top: 1px solid rgba(212,163,115,0.12);
        }
        .admin-sidebar .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .admin-sidebar .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid rgba(212,163,115,0.4);
        }

        /* ── Content ── */
        .admin-content {
            margin-left: var(--sidebar-w);
            padding: 2rem;
            min-height: 100vh;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .admin-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.6rem;
            color: var(--navy);
            margin: 0;
        }

        /* ── Stat Cards ── */
        .stat-card {
            background: #fff;
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(15,23,42,0.06);
        }
        .stat-card:hover {
            border-color: rgba(212,163,115,0.4);
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(15,23,42,0.1);
        }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        .stat-card .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 2rem;
            font-weight: 700;
            color: var(--navy);
            line-height: 1.1;
        }
        .stat-card .stat-label {
            color: var(--slate);
            font-size: 0.82rem;
            font-weight: 500;
            margin-top: 0.2rem;
        }

        /* ── Tables ── */
        .table-dark-custom {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid var(--glass-border);
            box-shadow: 0 2px 8px rgba(15,23,42,0.06);
        }
        .table-dark-custom table { margin: 0; color: var(--navy); }
        .table-dark-custom th {
            background: rgba(212,163,115,0.08);
            border-bottom: 1px solid var(--glass-border);
            font-weight: 600;
            font-size: 0.82rem;
            color: var(--slate);
            padding: 0.85rem 1rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }
        .table-dark-custom td {
            border-bottom: 1px solid rgba(212,163,115,0.1);
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
        }
        .table-dark-custom tr:last-child td { border-bottom: none; }
        .table-dark-custom tr:hover td { background: rgba(212,163,115,0.04); }

        /* ── Buttons ── */
        .btn-primary-custom {
            background: linear-gradient(135deg, #D4A373, #b8864e);
            border: none;
            color: white;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: 9999px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(212,163,115,0.35);
            font-family: 'Inter', sans-serif;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(212,163,115,0.45);
            color: white;
        }

        /* ── Form Controls ── */
        .form-control-dark {
            background: #fdfcfa !important;
            border: 1px solid var(--beige) !important;
            color: var(--navy) !important;
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-family: 'Inter', sans-serif;
        }
        .form-control-dark:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(212,163,115,0.15) !important;
        }

        /* ── Card Glass ── */
        .card-glass {
            background: #fff;
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(15,23,42,0.06);
        }

        /* ── Badges ── */
        .badge-status { padding: 0.35rem 0.85rem; border-radius: 9999px; font-weight: 600; font-size: 0.75rem; }
        .badge-published { background: rgba(26,58,42,0.12); color: var(--forest); }
        .badge-draft { background: rgba(212,163,115,0.15); color: var(--gold-dark); }
        .badge-admin { background: rgba(139,92,246,0.12); color: #7c3aed; }
        .badge-user { background: rgba(15,23,42,0.08); color: var(--slate); }

        /* ── Alerts ── */
        .alert-custom { border: none; border-radius: 12px; padding: 1rem 1.5rem; font-weight: 500; }
        .alert-success-custom { background: rgba(26,58,42,0.1); color: var(--forest); border: 1px solid rgba(26,58,42,0.2) !important; }
        .alert-error-custom { background: rgba(239,68,68,0.08); color: #dc2626; border: 1px solid rgba(239,68,68,0.2) !important; }

        /* ── Pagination ── */
        .pagination .page-link { background: #fff; border: 1px solid var(--glass-border); color: var(--slate); border-radius: 8px !important; margin: 0 2px; }
        .pagination .page-link:hover { background: var(--gold); border-color: var(--gold); color: white; }
        .pagination .page-item.active .page-link { background: linear-gradient(135deg,#D4A373,#b8864e); border-color: transparent; color: white !important; }

        /* ── Mobile ── */
        @media (max-width: 768px) {
            .admin-sidebar { transform: translateX(-100%); }
            .admin-sidebar.show { transform: translateX(0); }
            .admin-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <aside class="admin-sidebar" id="sidebar">
        <div class="brand">
            <div class="brand-title">
                <i class="fas fa-paper-plane"></i> VietTravel Admin
            </div>
            <div class="brand-sub">Cẩm nang Du lịch</div>
        </div>
        <nav>
            <div class="nav-section">Tổng quan</div>
            <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <div class="nav-section">Quản lý</div>
            <a href="{{ route('admin.posts.index') }}" class="nav-link {{ request()->routeIs('admin.posts.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper"></i> Bài viết
            </a>
            <a href="{{ route('admin.categories.index') }}" class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fas fa-folder"></i> Danh mục
            </a>
            <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Người dùng
            </a>
            <a href="{{ route('admin.comments.index') }}" class="nav-link {{ request()->routeIs('admin.comments.*') ? 'active' : '' }}">
                <i class="fas fa-comments"></i> Bình luận
            </a>
            <div class="nav-section">Hệ thống</div>
            <a href="{{ route('home') }}" class="nav-link">
                <i class="fas fa-globe"></i> Về trang chủ
            </a>
            <form method="POST" action="{{ route('logout') }}">@csrf
                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-start" style="cursor:pointer;">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </button>
            </form>
        </nav>
        <div class="sidebar-footer">
            <div class="user-info">
                <img src="{{ auth()->user()->avatar_url }}" alt="" class="user-avatar">
                <div>
                    <div style="color:#fff;font-size:0.82rem;font-weight:600;">{{ auth()->user()->name }}</div>
                    <div style="color:var(--gold);font-size:0.72rem;">Quản trị viên</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="admin-content">
        <button class="btn btn-sm d-md-none mb-3" onclick="document.getElementById('sidebar').classList.toggle('show')"
                style="background:var(--navy);color:var(--gold);border:1px solid rgba(212,163,115,0.3);border-radius:8px;padding:0.4rem 0.75rem;">
            <i class="fas fa-bars"></i>
        </button>
        @if(session('success'))
        <div class="alert alert-custom alert-success-custom alert-dismissible fade show mb-3">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if(session('error'))
        <div class="alert alert-custom alert-error-custom alert-dismissible fade show mb-3">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
