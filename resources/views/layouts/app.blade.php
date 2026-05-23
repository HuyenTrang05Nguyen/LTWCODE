<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Cẩm nang du lịch trực tuyến - Chia sẻ kinh nghiệm và cẩm nang du lịch Việt Nam">
    
    <title>@yield('title', 'Cẩm Nang Du Lịch') - TravelGuide</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <style>
        /* ── HỆ THỐNG BIẾN TOÀN CỤC (CSS VARIABLES) ──
           Bảng màu chủ đạo mang phong cách Cao cấp (Premium) / Nghỉ dưỡng với tone Navy, Vàng gold và Kem */
        :root {
            /* Palette Màu sắc */
            --navy:      #0F172A;
            --gold:      #D4A373;
            --gold-dark: #b8864e;
            --cream:     #FAF7F2;
            --beige:     #E7D7C9;
            --slate:     #334155;
            --forest:    #1a3a2a;
            
            /* Màu vai trò hệ thống */
            --primary:   #D4A373;
            --primary-dark: #b8864e;
            --secondary: #1a3a2a;
            --accent:    #D4A373;
            
            /* Màu nền và chữ */
            --bg-page:   #FAF7F2;
            --bg-card:   #ffffff;
            --text-primary:   #0F172A;
            --text-secondary: #334155;
            --text-muted:     #94a3b8;
            
            /* Hiệu ứng Đổ bóng & Gradient */
            --gradient-primary: linear-gradient(135deg, #D4A373 0%, #b8864e 100%);
            --gradient-hero:    linear-gradient(135deg, #FAF7F2 0%, #E7D7C9 100%);
            --glass-border: rgba(212,163,115,0.18); /* Viền mờ giả kính */
            --shadow-sm: 0 1px 4px rgba(15,23,42,0.07), 0 1px 2px rgba(15,23,42,0.04);
            --shadow-md: 0 4px 20px rgba(15,23,42,0.10), 0 2px 8px rgba(15,23,42,0.05);
            --shadow-lg: 0 20px 48px rgba(15,23,42,0.13), 0 8px 20px rgba(15,23,42,0.07);
            
            /* Bo góc */
            --radius-sm:   10px;
            --radius-md:   16px;
            --radius-lg:   24px;
            --radius-full: 9999px;
        }

        /* ── THIẾT LẬP CƠ BẢN (RESET & BASE STYLES) ── */
        * { margin:0; padding:0; box-sizing:border-box; }
        html { scroll-behavior: smooth; } /* Hiệu ứng cuộn mượt mà khi bấm link neo anchor */
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-page);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden; /* Chống vỡ khung theo chiều ngang */
            line-height: 1.7;
        }
        /* Áp dụng font Serif cổ điển cho tất cả tiêu đề */
        h1,h2,h3,h4,h5,h6 { font-family: 'Playfair Display', serif; }
        
        /* Tùy biến thanh cuộn (Scrollbar) trên trình duyệt Webkit (Chrome, Safari, Edge) */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--cream); }
        ::-webkit-scrollbar-thumb { background: var(--beige); border-radius: 3px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--gold); }

        /* ── THANH ĐIỀU HƯỚNG (NAVBAR) ── */
        .navbar-custom {
            background: rgba(255,255,255,0.97);
            backdrop-filter: blur(20px); /* Hiệu ứng kính mờ thời thượng */
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(212,163,115,0.15);
            padding: 0.85rem 0;
            transition: all 0.35s ease;
            box-shadow: 0 2px 12px rgba(15,23,42,0.06);
        }
        /* Class sẽ được kích hoạt bằng JS khi người dùng cuộn trang xuống */
        .navbar-custom.scrolled {
            background: rgba(255,255,255,0.99);
            box-shadow: var(--shadow-md);
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--navy) !important;
            letter-spacing: -0.01em;
        }
        .navbar-brand .brand-icon { color: var(--gold); }
        .nav-link {
            font-family: 'Inter', sans-serif;
            color: var(--slate) !important;
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 1rem !important;
            transition: color 0.3s ease;
            position: relative;
            letter-spacing: 0.01em;
        }
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        /* Hiệu ứng đường gạch chân chạy ra từ giữa khi hover menu */
        .nav-link::after {
            content: '';
            position: absolute;
            bottom: 0; left: 50%;
            transform: translateX(-50%);
            width: 0; height: 2px;
            background: var(--gold);
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        .nav-link:hover::after, .nav-link.active::after { width: 70%; }

        /* ── NÚT BẤM (BUTTONS) ── */
        /* Nút chính Fill màu Gradient kèm bóng đổ nổi bật */
        .btn-primary-custom {
            background: var(--gradient-primary);
            border: none;
            color: white;
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: var(--radius-full);
            transition: all 0.3s ease;
            box-shadow: 0 4px 14px rgba(212,163,115,0.35);
            letter-spacing: 0.02em;
        }
        .btn-primary-custom:hover {
            transform: translateY(-2px); /* Nhấc nút lên nhẹ khi rải chuột */
            box-shadow: 0 8px 28px rgba(212,163,115,0.45);
            color: white;
        }
        /* Nút viền (Outline) trong suốt, chuyển thành nút full màu khi hover */
        .btn-outline-custom {
            border: 2px solid var(--gold);
            color: var(--gold);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: var(--radius-full);
            transition: all 0.3s ease;
            background: transparent;
            letter-spacing: 0.02em;
        }
        .btn-outline-custom:hover {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(212,163,115,0.35);
        }

        /* ── THẺ HIỂN THỊ (CARDS - Dùng cho danh sách bài viết/địa điểm) ── */
        .card-glass {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            overflow: hidden; /* Giữ các phần tử con không tràn ra ngoài phần bo góc */
            transition: all 0.35s cubic-bezier(0.4,0,0.2,1);
            box-shadow: var(--shadow-sm);
        }
        /* Hiệu ứng nổi bật, nâng card và tăng bóng đổ khi hover */
        .card-glass:hover {
            transform: translateY(-6px);
            border-color: rgba(212,163,115,0.35);
            box-shadow: var(--shadow-lg);
        }
        /* Hiệu ứng phóng to nhẹ hình ảnh nền (Zoom in) khi hover vào card */
        .card-glass .card-img-top {
            height: 240px;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        .card-glass:hover .card-img-top { transform: scale(1.07); }
        .card-glass .card-img-wrapper { overflow: hidden; position: relative; }
        /* Lớp phủ Gradient đen mờ ở đáy ảnh giúp text (nếu có chèn đè lên) hiển thị rõ hơn */
        .card-glass .card-img-overlay-gradient {
            position: absolute; bottom: 0; left: 0; right: 0;
            height: 65%;
            background: linear-gradient(transparent, rgba(15,23,42,0.72));
            pointer-events: none;
        }
        .card-glass .card-body { padding: 1.5rem; }
        .card-glass .card-title {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 1.08rem;
            line-height: 1.4;
            margin-bottom: 0.5rem;
        }
        .card-glass .card-title a {
            color: var(--text-primary);
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .card-glass .card-title a:hover { color: var(--gold); }
        .card-glass .card-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
            line-height: 1.65;
        }

        /* ── NHÃN DANH MỤC (BADGE CATEGORY) ── */
        .badge-category {
            background: rgba(212,163,115,0.14);
            color: var(--gold-dark);
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            font-size: 0.72rem;
            padding: 0.3rem 0.85rem;
            border-radius: var(--radius-full);
            border: 1px solid rgba(212,163,115,0.3);
            display: inline-block;
            letter-spacing: 0.04em;
            text-transform: uppercase; /* Tự động viết hoa danh mục */
        }

        /* ── THÔNG TIN PHỤ (META INFO - Ngày đăng, tác giả, lượt xem...) ── */
        .meta-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-muted);
            font-size: 0.82rem;
            font-family: 'Inter', sans-serif;
        }
        .meta-info i { color: var(--gold); }

        /* ── ĐÁNH GIÁ SAO (STARS RATING) ── */
        .stars { color: #D4A373; }
        .stars .empty { color: #d6d3d1; } /* Màu dành cho sao rỗng (chưa đánh giá) */

        /* ── ĐẦU ĐỀ PHÂN ĐOẠN (SECTION HEADERS) ── */
        .section-header { margin-bottom: 2.5rem; }
        .section-header h2 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            color: var(--navy);
            letter-spacing: -0.01em;
        }
        .section-header p { color: var(--text-secondary); font-size: 1.05rem; }
        .gradient-text { color: var(--gold); } /* Có thể đổi thành gradient text nếu cần */

        /* ── CHÂN TRANG (FOOTER) ── */
        .footer {
            background: var(--navy);
            border-top: 1px solid rgba(212,163,115,0.2);
            padding: 4rem 0 1.5rem;
            margin-top: 5rem;
            color: #94a3b8;
        }
        .footer h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 600;
            margin-bottom: 1.25rem;
            color: var(--gold);
            font-size: 1.05rem;
        }
        .footer a {
            color: #94a3b8;
            text-decoration: none;
            transition: color 0.3s ease;
            display: block;
            padding: 0.25rem 0;
            font-size: 0.9rem;
        }
        .footer a:hover { color: var(--gold); }
        .footer .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff;
        }
        /* Các nút mạng xã hội hình tròn ở footer */
        .footer .social-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px; height: 38px;
            border-radius: 50%;
            border: 1px solid rgba(212,163,115,0.3);
            color: #94a3b8;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        .footer .social-icon:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: white;
            transform: translateY(-2px);
        }

        /* ── THÔNG BÁO (ALERTS - Thành công / Thất bại) ── */
        .alert-custom {
            border: none;
            border-radius: var(--radius-sm);
            padding: 1rem 1.5rem;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }
        .alert-success-custom {
            background: rgba(26,58,42,0.08);
            color: #1a3a2a;
            border: 1px solid rgba(26,58,42,0.2) !important;
        }
        .alert-error-custom {
            background: rgba(220,38,38,0.07);
            color: #dc2626;
            border: 1px solid rgba(220,38,38,0.18) !important;
        }

        /* ── PHẦN TỬ FORM Ô NHẬP LIỆU (FORM CONTROLS) ── */
        .form-control-dark {
            background: #fdfcfa !important;
            border: 1px solid var(--beige) !important;
            color: var(--text-primary) !important;
            border-radius: var(--radius-sm);
            padding: 0.7rem 1rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }
        /* Hiệu ứng đổi màu viền và đổ bóng nhẹ khi click vào ô nhập liệu */
        .form-control-dark:focus {
            border-color: var(--gold) !important;
            box-shadow: 0 0 0 3px rgba(212,163,115,0.15) !important;
            background: #ffffff !important;
        }
        .form-control-dark::placeholder { color: var(--text-muted); }
        .form-label-custom {
            color: var(--text-secondary);
            font-weight: 600;
            font-size: 0.875rem;
            margin-bottom: 0.5rem;
            display: block;
            font-family: 'Inter', sans-serif;
        }

        /* ── NÚT YÊU THÍCH / THẢ TIM (FAVORITE BUTTON) ── */
        .btn-favorite {
            border: 2px solid rgba(239,68,68,0.25);
            color: #94a3b8;
            background: transparent;
            border-radius: var(--radius-full);
            padding: 0.5rem 1.25rem;
            transition: all 0.3s ease;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }
        /* Trạng thái được kích hoạt (đã thích) hoặc khi hover vào nút */
        .btn-favorite.active, .btn-favorite:hover {
            background: rgba(239,68,68,0.08);
            border-color: #ef4444;
            color: #ef4444;
        }

        /* ── PHÂN TRANG (PAGINATION) ── */
        .pagination .page-link {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            color: var(--text-secondary);
            transition: all 0.3s ease;
            border-radius: var(--radius-sm) !important;
            margin: 0 2px;
            font-family: 'Inter', sans-serif;
        }
        .pagination .page-link:hover {
            background: var(--gold);
            border-color: var(--gold);
            color: white;
        }
        /* Số trang hiện tại đang xem */
        .pagination .page-item.active .page-link {
            background: var(--gradient-primary);
            border-color: transparent;
            color: white !important;
        }

        /* ── ẢNH ĐẠI DIỆN NGƯỜI DÙNG (USER AVATAR) ── */
        .user-avatar {
            width: 36px; height: 36px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--gold);
        }

        /* ── NÚT MENU MOBILE (NAVBAR TOGGLER) ── */
        .navbar-toggler {
            border: 1px solid var(--glass-border);
            padding: 0.4rem 0.6rem;
        }
        /* Thay thế icon mặc định của Bootstrap bằng mã SVG tùy biến có màu Slate phù hợp thiết kế */
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2851,65,85,1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* ── HIỆU ỨNG CHUYỂN ĐỘNG CSS (CSS ANIMATIONS) ── */
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease forwards; }
        /* Các class tạo độ trễ (delay) giúp các phần tử xuất hiện so le nhau sinh động hơn */
        .delay-100 { animation-delay: 0.1s; }
        .delay-200 { animation-delay: 0.2s; }
        .delay-300 { animation-delay: 0.3s; }

        /* ── MENU THẢ XUỐNG (DROPDOWN) ── */
        .dropdown-menu {
            background: var(--bg-card);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-md);
            border-radius: var(--radius-md);
            padding: 0.5rem;
        }
        .dropdown-item {
            border-radius: var(--radius-sm);
            padding: 0.5rem 1rem;
            color: var(--text-primary);
            font-weight: 500;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
        }
        .dropdown-item:hover {
            background: rgba(212,163,115,0.1);
            color: var(--gold-dark);
        }

        /* ── NỘI DUNG CHI TIẾT BÀI VIẾT (POST CONTENT) ── */
        .post-content h1,.post-content h2,.post-content h3,.post-content h4 {
            font-family: 'Playfair Display', serif;
            color: var(--navy);
            margin-top: 2rem;
            margin-bottom: 1rem;
        }
        .post-content p { margin-bottom: 1.4rem; line-height: 1.9; }
        .post-content img { max-width: 100%; border-radius: var(--radius-md); margin: 1.5rem 0; }
        /* Khung trích dẫn nổi bật bên trong nội dung bài viết */
        .post-content blockquote {
            border-left: 4px solid var(--gold);
            padding: 1rem 1.5rem;
            background: rgba(212,163,115,0.07);
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
            font-style: italic;
            color: var(--slate);
            margin: 1.5rem 0;
        }
        /* Tạo chữ cái Drop Cap phóng to ở đầu bài viết mang phong cách tạp chí */
        .post-content p:first-of-type::first-letter {
            font-family: 'Playfair Display', serif;
            font-size: 3.5rem;
            font-weight: 700;
            float: left;
            line-height: 0.85;
            margin-right: 0.12em;
            color: var(--gold);
        }

        /* ── GIAO DIỆN DI ĐỘNG (RESPONSIVE MOBILE < 768px) ── */
        @media (max-width: 768px) {
            .section-header h2 { font-size: 1.5rem; } /* Giảm cỡ chữ tiêu đề trên mobile */
            .card-glass .card-img-top { height: 200px; } /* Giảm chiều cao ảnh card để tiết kiệm không gian */
            .navbar-brand { font-size: 1.2rem; }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top" id="mainNav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <span class="brand-icon"><i class="fas fa-paper-plane me-2"></i></span>TravelGuide
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-3">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                            Trang chủ
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('posts.*') ? 'active' : '' }}" href="{{ route('posts.index') }}">
                            Bài viết
                        </a>
                    </li>
                </ul>
                
                <div class="d-flex align-items-center gap-2">
                    
                    @guest
                        <a href="{{ route('login') }}" class="btn btn-outline-custom btn-sm">
                            Đăng nhập
                        </a>
                        <a href="{{ route('register') }}" class="btn btn-primary-custom btn-sm">
                            <i class="fas fa-paper-plane me-1"></i> Đăng ký
                        </a>
                    
                    @else
                        <a href="{{ route('posts.favorites') }}" class="btn btn-sm btn-outline-custom" title="Yêu thích">
                            <i class="fas fa-heart me-1"></i> <span class="d-none d-md-inline">Yêu thích</span>
                        </a>
                        
                        <div class="dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center gap-2" href="#" role="button" data-bs-toggle="dropdown">
                                <img src="{{ auth()->user()->avatar_url }}" alt="Avatar" class="user-avatar">
                                <span class="d-none d-md-inline" style="font-size:0.9rem;font-weight:600;color:var(--navy);">{{ auth()->user()->name }}</span>
                            </a>
                            
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.show') }}">
                                        <i class="fas fa-user me-2" style="color:var(--gold);"></i> Hồ sơ
                                    </a>
                                </li>
                                
                                @if(auth()->user()->isAdmin())
                                <li>
                                    <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                                        <i class="fas fa-tachometer-alt me-2" style="color:var(--gold);"></i> Admin
                                    </a>
                                </li>
                                @endif
                                
                                <li><hr class="dropdown-divider" style="border-color: var(--glass-border);"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2" style="color:#ef4444;"></i> Đăng xuất
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    <main style="padding-top: 76px;">
        
        @if(session('success'))
        <div class="container mt-3">
            <div class="alert alert-custom alert-success-custom alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif
        
        @if(session('error'))
        <div class="container mt-3">
            <div class="alert alert-custom alert-error-custom alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                
                <div class="col-lg-4 col-md-6">
                    <div class="footer-brand mb-3">
                        <i class="fas fa-paper-plane me-2" style="color:var(--gold);"></i>TravelGuide
                    </div>
                    <p style="color:#94a3b8;font-size:0.9rem;line-height:1.75;max-width:280px;">Cẩm nang du lịch trực tuyến — Nơi chia sẻ kinh nghiệm và cảm hứng du lịch Việt Nam.</p>
                    <div class="d-flex gap-2 mt-4">
                        <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-icon"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6">
                    <h5>Khám phá</h5>
                    <a href="{{ route('posts.index') }}">Tất cả bài viết</a>
                    <a href="{{ route('posts.index', ['sort' => 'popular']) }}">Phổ biến nhất</a>
                    <a href="{{ route('posts.index', ['sort' => 'latest']) }}">Mới nhất</a>
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Danh mục</h5>
                    @php $footerCategories = \App\Models\Category::take(5)->get(); @endphp
                    
                    @foreach($footerCategories as $cat)
                    <a href="{{ route('posts.index', ['category' => $cat->slug]) }}">{{ $cat->name }}</a>
                    @endforeach
                </div>
                
                <div class="col-lg-3 col-md-6">
                    <h5>Liên hệ</h5>
                    <p style="color:#94a3b8;margin-bottom:0.5rem;font-size:0.9rem;"><i class="fas fa-envelope me-2" style="color:var(--gold);"></i>contact@travelguide.vn</p>
                    <p style="color:#94a3b8;margin-bottom:0.5rem;font-size:0.9rem;"><i class="fas fa-phone me-2" style="color:var(--gold);"></i>0123 456 789</p>
                    <p style="color:#94a3b8;font-size:0.9rem;"><i class="fas fa-map-marker-alt me-2" style="color:var(--gold);"></i>Việt Nam</p>
                </div>
            </div>
            
            <hr style="border-color: rgba(212,163,115,0.15); margin: 2.5rem 0 1.25rem;">
            
            <div class="text-center" style="color:#64748b;">
                <small style="font-family:'Inter',sans-serif;">&copy; {{ date('Y') }} TravelGuide. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <script>
        // Khởi tạo cấu hình thư viện hoạt họa cuộn trang AOS
        // duration: 800 - Mỗi hiệu ứng chuyển động diễn ra trong 800 miligiây (0.8 giây)
        // once: true - Hiệu ứng chỉ chạy duy nhất một lần khi cuộn qua, khi cuộn ngược lên không chạy lại
        // offset: 100 - Phần tử phải cách mép dưới màn hình 100px thì hiệu ứng mới bắt đầu được kích hoạt kích nổ
        AOS.init({ duration: 800, once: true, offset: 100 });

        // Lắng nghe sự kiện cuộn trang (scroll) của người dùng trên toàn bộ cửa sổ trình duyệt (window)
        window.addEventListener('scroll', function() {
            const nav = document.getElementById('mainNav');
            // Kiểm tra nếu khoảng cách cuộn dọc (window.scrollY) vượt quá 50 pixel
            if (window.scrollY > 50) { 
                nav.classList.add('scrolled'); // Tự động thêm class 'scrolled' vào thẻ nav (Để CSS thay đổi màu nền đậm hơn hoặc thêm đổ bóng)
            } else { 
                nav.classList.remove('scrolled'); // Ngược lại, khi cuộn lên sát đỉnh đầu thì gỡ class này đi để trả lại màu trong suốt nguyên bản
            }
        });

        // Hàm hẹn giờ tự động chạy (Hành động trì hoãn)
        setTimeout(function() {
            // Tìm kiếm tất cả các thẻ có class là '.alert-dismissible' đang xuất hiện trên màn hình
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) {
                // Ép buộc kích hoạt hàm close() của đối tượng Bootstrap Alert để tự động đóng/ẩn các thông báo flash session sau đúng 5000ms (5 giây)
                new bootstrap.Alert(alert).close();
            });
        }, 5000);
    </script>
    
    @stack('scripts')

    {{-- ═══════════════════════════════════════════════
         5. TRAVELBOT — AI CHATBOT WIDGET (ĐỊNH DẠNG CSS)
         ═══════════════════════════════════════════════ --}}
    <style>
    /* ── Nút Tròn Nhỏ Kích Hoạt Chatbot (Launcher) ── */
    #chatbot-launcher {
        position: fixed; /* Định vị cố định phần tử dựa trên khung nhìn màn hình trình duyệt, cuộn trang nút vẫn đứng im */
        bottom: 28px;    /* Đặt nút cách mép đáy màn hình 28px */
        right: 28px;     /* Đặt nút cách mép phải màn hình 28px */
        width: 58px;     /* Chiều rộng nút */
        height: 58px;    /* Chiều cao nút bằng chiều rộng để tạo thành một khối vuông */
        border-radius: 50%; /* Bo tròn góc tối đa 50% biến khối vuông thành một hình tròn hoàn hảo */
        background: linear-gradient(135deg, #D4A373, #b8864e); /* Tạo màu nền đổ góc nghiêng 135 độ chuyển từ vàng nhạt sang vàng đất */
        border: none;    /* Loại bỏ đường viền viền thô mặc định của thẻ button */
        cursor: pointer; /* Thay đổi con trỏ chuột thành hình bàn tay khi người dùng rê chuột vào nút bấm */
        box-shadow: 0 8px 28px rgba(212,163,115,0.55); /* Tạo đổ bóng đổ vùng rộng, có màu vàng đồng trong suốt để tạo hiệu ứng nổi 3D */
        display: flex;   /* Kích hoạt chế độ Flexbox */
        align-items: center; /* Căn giữa icon bên trong theo trục dọc */
        justify-content: center; /* Căn giữa icon bên trong theo trục ngang */
        z-index: 9999;   /* Chỉ số lớp hiển thị cao nhất hệ thống, đảm bảo nút bấm luôn nằm đè lên trên mọi nội dung trang web */
        /* cubic-bezier: Hàm toán học quy định tốc độ chuyển động giúp hiệu ứng hover co giãn phóng to diễn ra mượt mà và có độ nẩy tự nhiên */
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1); 
        color: white;    /* Màu của icon phi cơ bên trong nút */
        font-size: 1.4rem; /* Kích thước icon bên trong */
    }
    
    /* Thiết lập các thuộc tính thay đổi khi người dùng đưa con trỏ chuột vào nút kích hoạt chat */
    #chatbot-launcher:hover {
        transform: scale(1.1) translateY(-3px); /* scale(1.1): Phóng to nút lên 1.1 lần kết hợp dịch chuyển nhấc nút lên cao 3px theo trục dọc Y */
        box-shadow: 0 14px 36px rgba(212,163,115,0.65); /* Tăng cường độ đậm và độ loang của bóng đổ để tạo cảm giác nút đang được nhấc lên cao sát người nhìn */
    }
    
    /* Badge số lượng tin nhắn chưa đọc nằm đè trên góc nút chat */
    #chatbot-launcher .badge-unread {
        position: absolute; /* Định vị tuyệt đối dựa theo thẻ cha gần nhất có thuộc tính relative/fixed (là nút launcher) */
        top: -4px;          /* Nhấc lệch lên phía trên viền nút cha 4px */
        right: -4px;        /* Nhấc lệch sang phía phải viền nút cha 4px */
        width: 20px;
        height: 20px;
        background: #ef4444; /* Màu nền đỏ rực để thu hút sự chú ý */
        border-radius: 50%;
        font-size: 0.65rem;
        font-weight: 700;   /* Chữ số in đậm */
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 2px solid white; /* Tạo một vòng viền màu trắng bao quanh để tách biệt đốm đỏ ra khỏi nền của nút vàng */
        display: none;      /* Mặc định ẩn đi, khi nào có tin nhắn chưa đọc sẽ dùng JS đổi thành display: flex */
    }

    /* ── Khung Giao Diện Cửa Sổ Chat (Chatbot Window) ── */
    #chatbot-window {
        position: fixed;
        bottom: 100px;       /* Đặt cách mép dưới 100px (Nằm lơ lửng ngay phía trên nút kích hoạt tròn) */
        right: 28px;
        width: 380px;        /* Chiều rộng chuẩn của một khung chat tiện ích trên website */
        max-height: 560px;   /* Giới hạn chiều cao tối đa của khung chat để không chiếm toàn bộ màn hình máy tính */
        background: #ffffff;
        border-radius: 20px; /* Bo tròn các góc xung quanh khung chat tạo cảm giác giao diện hiện đại, mềm mại */
        box-shadow: 0 24px 64px rgba(15,23,42,0.18), 0 8px 24px rgba(15,23,42,0.1); /* Kết hợp 2 tầng đổ bóng sâu để tách biệt khung chat lên không gian */
        border: 1px solid rgba(212,163,115,0.2);
        display: flex;
        flex-direction: column; /* Thiết lập các khối con xếp chồng lên nhau theo dạng cột (Header ở trên -> Tin nhắn ở giữa -> Ô nhập ở đáy) */
        z-index: 9998;       /* Thấp hơn nút launcher 1 bậc nhưng cao hơn toàn bộ nội dung nền web */
        overflow: hidden;    /* Bắt các thành phần con nếu tràn ra ngoài phạm vi 20px bo góc sẽ bị cắt cụt đi, không bị lộ góc nhọn */
        
        /* [TRẠNG THÁI ẨN MẶC ĐỊNH]: Khung chat sẽ bị thu nhỏ còn 0.85 lần (scale), hạ thấp xuống 20px, làm mờ biến mất (opacity 0) 
           và khóa tính năng click (pointer-events: none) */
        transform: scale(0.85) translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        transform-origin: bottom right; /* Đặt tâm điểm neo của hiệu ứng xuất hiện/thu nhỏ bắt nguồn từ góc dưới cùng bên phải (Nơi đặt nút launcher) */
    }
    
    /* [TRẠNG THÁI MỞ CHAT]: Khi JavaScript thêm class '.open' vào, cửa sổ chat sẽ tự bung phóng to về kích thước gốc, 
       hiện rõ lên (opacity 1) và cho phép người dùng click tương tác nhập liệu bình thường (pointer-events: all) */
    #chatbot-window.open {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    /* ── Khối Tiêu Đề Khung Chat (Header) ── */
    .chatbot-header {
        background: linear-gradient(135deg, #0F172A 0%, #1a3a2a 100%); /* Màu chuyển huyền bí từ xanh Navy sẫm sang xanh rêu tối */
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0; /* Thuộc tính cực kỳ quan trọng: Giá trị 0 ép khối Header này luôn giữ nguyên chiều cao thiết kế, 
                           không bị co hẹp hoặc bóp méo khi danh sách tin nhắn ở dưới quá dài và phình to */
    }
    .chatbot-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D4A373, #b8864e);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        color: white;
        flex-shrink: 0; /* Không cho phép avatar bị bóp méo méo mó hình tròn */
    }
    .chatbot-header-info h6 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #ffffff;
        margin: 0; /* Xóa bỏ khoảng cách lề mặc định của thẻ h6 */
        font-size: 0.95rem;
    }
    .chatbot-header-info span {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.6);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Chấm tròn nhỏ biểu thị trạng thái đang Online (Hoạt động) */
    .chatbot-header-info .online-dot {
        width: 7px;
        height: 7px;
        background: #22c55e; /* Màu xanh lá chuẩn biểu thị trạng thái kích hoạt online */
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 2s infinite; /* Gán hiệu ứng animation tên 'pulse-dot' chạy lặp vô hạn chu kỳ 2 giây */
    }
    /* Khai báo quy trình chuyển động nhấp nháy cho đốm tròn online */
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }  /* Ở thời điểm đầu và cuối giây thứ 2: Hiện rõ 100% */
        50% { opacity: 0.4; }    /* Ở thời điểm chính giữa giây thứ 1: Làm mờ đi còn 40% để tạo hiệu ứng thở/nhấp nháy */
    }
    
    /* Nút bấm chữ X dùng để đóng nhanh khung chat */
    .chatbot-close {
        margin-left: auto; /* Mẹo Flexbox: Đẩy riêng nút đóng X này trượt về sát rìa bên phải của Header */
        background: rgba(255,255,255,0.1);
        border: none;
        color: rgba(255,255,255,0.7);
        width: 30px;
        height: 30px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .chatbot-close:hover {
        background: rgba(255,255,255,0.2);
        color: white;
    }

    /* ── Khu Vực Hiển Thị Toàn Bộ Cuộc Hội Thoại (Messages Area) ── */
    .chatbot-messages {
        flex: 1;             /* Giá trị 1 giúp vùng này tự động co giãn linh hoạt, chiếm trọn toàn bộ không gian trống còn thừa ở giữa khung chat */
        overflow-y: auto;    /* Kích hoạt thanh cuộn dọc tự động xuất hiện khi các tin nhắn vượt quá chiều cao vùng chứa */
        padding: 1.25rem;
        display: flex;
        flex-direction: column; /* Sắp xếp các hàng tin nhắn chạy dọc từ trên xuống dưới */
        gap: 0.85rem;        /* Khoảng cách giữa các dòng bong bóng tin nhắn */
        background: #FAF7F2; /* Màu nền trắng kem sang trọng, dịu mắt người đọc */
        scroll-behavior: smooth; /* Tạo hiệu ứng cuộn mượt mà khi có nội dung hoặc tin nhắn mới được đẩy vào */
    }
    
    /* Tùy biến thanh cuộn mỏng nhẹ (Chỉ tương thích trình duyệt nhân Webkit như Chrome, Safari, Edge) */
    .chatbot-messages::-webkit-scrollbar { width: 4px; } /* Thu hẹp độ rộng thanh cuộn dọc chỉ còn 4px tinh tế */
    .chatbot-messages::-webkit-scrollbar-thumb { background: #E7D7C9; border-radius: 2px; } /* Tô màu nâu nhạt cho cục kéo của thanh cuộn */

    /* ── Cấu Trúc Khung Chứa Của Một Dòng Tin Nhắn (Message Row) ── */
    .msg-row {
        display: flex;
        gap: 0.6rem;
        align-items: flex-end; /* Căn các thành phần con sát xuống đáy dòng, giúp avatar và bong bóng tin nhắn luôn thẳng chân với nhau */
    }
    /* row-reverse: Đảo ngược vị trí các phần tử bên trong hàng. 
       Giúp tin nhắn của Người dùng (User) tự động hoán đổi vị trí: hiển thị từ phải qua trái (Bong bóng trước rồi mới tới avatar nằm sát mép phải) */
    .msg-row.user { flex-direction: row-reverse; }

    .msg-avatar {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }
    .msg-avatar.bot {
        background: linear-gradient(135deg, #D4A373, #b8864e);
        color: white;
    }
    .msg-avatar.user-av {
        background: linear-gradient(135deg, #0F172A, #334155);
        color: white;
    }

    /* Bong Bóng Chứa Nội Dung Văn Bản Chữ Của Tin Nhắn */
    .msg-bubble {
        max-width: 78%;      /* Khống chế bong bóng tin nhắn không được chiếm quá 78% độ rộng khung chat, tránh việc dòng chữ quá dài kéo tràn sang lấp mất avatar đối phương */
        padding: 0.65rem 1rem;
        border-radius: 16px;
        font-size: 0.875rem;
        line-height: 1.6;    /* Thiết lập khoảng cách giữa các dòng chữ thoáng, dễ đọc */
        font-family: 'Inter', sans-serif;
        word-break: break-word; /* Ép hệ thống tự động bẻ dòng khi gặp các từ hoặc chuỗi ký tự quá dài (như đường link URL dài), ngăn việc phá vỡ cấu trúc giao diện */
    }
    
    /* Kiểu dáng tin nhắn của Bot phát ra (Nằm bên trái) */
    .msg-bubble.bot {
        background: #ffffff;
        color: #0F172A;
        border: 1px solid rgba(212,163,115,0.2);
        border-bottom-left-radius: 4px; /* Làm nhọn riêng góc dưới cùng bên trái của bong bóng để tạo hình như một cái đuôi hội thoại đang chỉ vào avatar Bot */
        box-shadow: 0 2px 8px rgba(15,23,42,0.06);
    }
    
    /* Kiểu dáng tin nhắn của Người dùng gửi đi (Nằm bên phải) */
    .msg-bubble.user {
        background: linear-gradient(135deg, #D4A373, #b8864e);
        color: white;
        border-bottom-right-radius: 4px; /* Làm nhọn riêng góc dưới cùng bên phải để tạo chiếc đuôi hội thoại chỉ vào avatar của Người dùng */
    }
    .msg-time {
        font-size: 0.65rem;
        color: #94a3b8;
        margin-top: 3px;
        text-align: right; /* Mặc định mốc thời gian hiển thị canh lề phải */
    }
    .msg-row.user .msg-time { text-align: left; } /* Đối với dòng tin nhắn của người dùng, mốc thời gian sẽ đảo sang canh lề trái cho cân đối */

    /* ── Hiệu Ứng Ba Chấm Nhấp Nháy (Bot Typing Indicator) ── */
    .typing-indicator {
        display: flex;
        gap: 4px;
        align-items: center;
        padding: 0.65rem 1rem;
        background: #ffffff;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        border: 1px solid rgba(212,163,115,0.2);
        width: fit-content; /* Thu hẹp độ rộng khối vừa vặn ôm khít lấy ba dấu chấm bên trong, không phình to hết dòng */
    }
    .typing-dot {
        width: 7px;
        height: 7px;
        background: #D4A373;
        border-radius: 50%;
        animation: typing-bounce 1.2s infinite; /* Kích hoạt hiệu ứng nẩy nhảy lên xuống lặp vô hạn chu kỳ 1.2 giây */
    }
    /* Cấu hình độ trễ thời gian (animation-delay) so le nhau giữa các chấm tròn 
       giúp dấu chấm thứ 2 và thứ 3 nhảy muộn hơn, tạo ra hiệu ứng chuyển động dạng làn sóng mượt mà */
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    
    /* Thiết lập quy trình tọa độ cho hiệu ứng sóng nhảy của 3 dấu chấm */
    @keyframes typing-bounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; } /* Tại điểm đầu, giữa và cuối: Chấm đứng yên ở vị trí gốc và mờ đi một nửa */
        30% { transform: translateY(-6px); opacity: 1; }          /* Tại mốc 30% dòng thời gian: Chấm co giật nhảy giật lên trên 6px theo trục dọc và hiện rõ nét */
    }

    /* ── Khối Chứa Các Nút Gợi Ý Câu Hỏi Nhanh (Suggestions Area) ── */
    .chatbot-suggestions {
        padding: 0.75rem 1.25rem 0;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;     /* Kích hoạt tự động xuống dòng khi các nhãn gợi ý xếp hàng ngang vượt quá chiều rộng của khung chat */
        background: #FAF7F2;
    }
    .suggestion-chip {
        background: rgba(212,163,115,0.12);
        border: 1px solid rgba(212,163,115,0.3);
        color: #b8864e;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px; /* Tạo hình dạng bo viên thuốc (Capsule) cho các thẻ nút bấm gợi ý */
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
        white-space: nowrap; /* Cấm tuyệt đối không cho chữ bên trong một nhãn bị tự động ngắt xuống dòng giữa chừng */
    }
    .suggestion-chip:hover {
        background: #D4A373;
        color: white;
        border-color: #D4A373;
    }

    /* ── Phân Khu Ô Nhập Liệu Ở Đáy Khung Chat (Input Area) ── */
    .chatbot-input-area {
        padding: 0.85rem 1.25rem 1rem;
        background: #ffffff;
        border-top: 1px solid rgba(212,163,115,0.15);
        display: flex;
        gap: 0.6rem;
        align-items: flex-end; /* Căn nút gửi nằm cố định ở đáy dòng kể cả khi ô nhập liệu (textarea) tự phình to chiều cao khi gõ văn bản dài */
        flex-shrink: 0;       /* Khóa cứng không cho vùng nhập liệu này bị co hẹp bóp méo diện tích */
    }
    .chatbot-input {
        flex: 1;
        border: 1px solid #E7D7C9;
        border-radius: 12px;
        padding: 0.6rem 0.9rem;
        font-size: 0.875rem;
        font-family: 'Inter', sans-serif;
        color: #0F172A;
        background: #FAF7F2;
        resize: none;        /* Triệt tiêu tính năng kéo giãn ô gõ thô kệch mặc định của thẻ textarea */
        outline: none;       /* Xóa bỏ đường viền viền đen thô bao quanh của trình duyệt khi người dùng click chuột vào ô gõ */
        transition: border-color 0.2s;
        max-height: 100px;   /* Khống chế ô gõ không được phép phình cao quá 100px, nếu gõ dài hơn sẽ tự sinh thanh cuộn bên trong ô gõ */
        line-height: 1.5;
    }
    .chatbot-input:focus { border-color: #D4A373; background: #ffffff; } /* Đổi màu nền sang trắng tinh và sáng viền vàng khi người dùng đang gõ chữ */
    .chatbot-input::placeholder { color: #94a3b8; } /* Đổi màu xám nhạt cho đoạn chữ gợi ý mặc định ("Nhập tin nhắn...") */
    
    .chatbot-send {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D4A373, #b8864e);
        border: none;
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
        font-size: 0.9rem;
    }
    .chatbot-send:hover { transform: scale(1.1); box-shadow: 0 4px 14px rgba(212,163,115,0.5); }
    /* Cấu hình trạng thái disabled (Ví dụ: Khi ô nhập trống không có chữ, JS sẽ khóa nút bấm lại). 
       Lúc này nút bấm sẽ mờ đi 50%, đổi con trỏ chuột thành hình biển báo cấm (not-allowed) và hủy bỏ hiệu ứng phóng to hover */
    .chatbot-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* Dòng chữ chú thích bản quyền/lưu ý nhỏ dưới đáy cùng của chatbot */
    .chatbot-footer-note {
        text-align: center;
        font-size: 0.65rem;
        color: #94a3b8;
        padding: 0 1.25rem 0.6rem;
        background: #ffffff;
        font-family: 'Inter', sans-serif;
    }

    /* ── Cấu Hình Responsive - Tối Ưu Hiển Thị Cho Thiết Bị Di Động (Mobile) ── */
    /* @media (max-width: 480px): Toàn bộ các thuộc tính CSS nằm trong khối này sẽ CHỈ được kích hoạt áp dụng 
       khi người dùng truy cập website bằng thiết bị có chiều rộng màn hình nhỏ hơn hoặc bằng 480px (Điện thoại di động) */
    @media (max-width: 480px) {
        #chatbot-window {
            /* calc(100vw - 24px): Ép chiều rộng khung chat tự động giãn căng hết chiều ngang màn hình điện thoại, chỉ chừa lại tổng cộng 24px lề lách */
            width: calc(100vw - 24px);
            right: 12px;     /* Ép sát khung chat cách lề phải điện thoại 12px */
            bottom: 90px;    /* Đẩy khung chat cách mép đáy điện thoại 90px để lộ nút bấm launcher */
            /* 70vh: 70% Viewport Height. Giới hạn chiều cao tuyệt đối của khung chat không vượt quá 70% tổng chiều cao màn hình thiết bị di động, 
               tránh việc bàn phím ảo của điện thoại khi đẩy lên sẽ làm lấp hoặc tràn vỡ toàn bộ giao diện chat */
            max-height: 70vh; 
        }
        #chatbot-launcher { 
            right: 16px;     /* Dịch nút tròn launcher sát về rìa màn hình điện thoại hơn (Cách 16px thay vì 28px) để tối ưu không gian hiển thị */
            bottom: 20px; 
        }
    }
    </style>
</body>

<button id="chatbot-launcher" onclick="toggleChatbot()" title="Chat với TravelBot AI">
        <i class="fas fa-robot" id="chatbot-icon"></i>
        <span class="badge-unread" id="chatbot-badge">1</span>
    </button>

    <div id="chatbot-window">
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i> </div>
            <div class="chatbot-header-info">
                <h6>TravelBot AI</h6>
                <span><span class="online-dot"></span> Trực tuyến • Hỗ trợ du lịch 24/7</span>
            </div>
            <button class="chatbot-close" onclick="toggleChatbot()">
                <i class="fas fa-times"></i> </button>
        </div>

        <div class="chatbot-messages" id="chatbot-messages">
            <div class="msg-row">
                <div class="msg-avatar bot"><i class="fas fa-robot" style="font-size:0.7rem;"></i></div>
                <div>
                    <div class="msg-bubble bot">
                        Xin chào! Tôi là <strong>TravelBot</strong> 🌏<br>
                        Tôi có thể giúp bạn:<br>
                        • Tư vấn địa điểm du lịch<br>
                        • Gợi ý ẩm thực & khách sạn<br>
                        • Lập lịch trình chuyến đi<br>
                        Bạn muốn khám phá đâu hôm nay? ✈️
                    </div>
                    <div class="msg-time">Vừa xong</div>
                </div>
            </div>
        </div>

        <div class="chatbot-suggestions" id="chatbot-suggestions">
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🏖️ Biển đẹp nhất VN</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🍜 Ẩm thực Hà Nội</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">💰 Du lịch tiết kiệm</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🏔️ Trekking Sapa</button>
        </div>

        <div class="chatbot-input-area">
            <textarea
                id="chatbot-input"
                class="chatbot-input"
                placeholder="Hỏi về du lịch Việt Nam..."
                rows="1"
                onkeydown="handleChatKey(event)"
                oninput="autoResizeTextarea(this)"
            ></textarea>
            <button class="chatbot-send" id="chatbot-send-btn" onclick="sendMessage()">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        <div class="chatbot-footer-note">
            Powered by Google Gemini AI • TravelGuide
        </div>
    </div>

    <script>
    // ═══════════════════════════════════════════
    // TravelBot — Chatbot Logic
    // ═══════════════════════════════════════════

    let chatHistory = [];   // Mảng rỗng dùng để lưu trữ toàn bộ lịch sử các câu hỏi và câu trả lời nhằm gửi kèm lên AI nhận diện ngữ cảnh
    let isTyping    = false; // Biến cờ hiệu (Flag) kiểm soát trạng thái: true là hệ thống đang chờ API phản hồi, ngăn người dùng bấm gửi liên tục
    let isOpen      = false; // Biến cờ hiệu lưu giữ trạng thái đóng/mở của cửa sổ chat (mặc định ban đầu là false - đang đóng)

    /**
     * Hàm xử lý hành động Mở hoặc Đóng cửa sổ Chatbot
     */
    function toggleChatbot() {
        isOpen = !isOpen; // Đảo ngược trạng thái hiện tại (Đóng thành Mở, Mở thành Đóng)
        const win    = document.getElementById('chatbot-window');
        const icon   = document.getElementById('chatbot-icon');
        const badge  = document.getElementById('chatbot-badge');

        if (isOpen) { // Nếu trạng thái là MỞ
            win.classList.add('open'); // Thêm class 'open' để CSS kích hoạt hiệu ứng bung khung chat ra màn hình
            icon.className = 'fas fa-times'; // Thay đổi icon robot ở nút tròn thành icon dấu nhân (X) để báo hiệu bấm vào sẽ đóng
            badge.style.display = 'none'; // Ẩn đốm đỏ thông báo tin nhắn chưa đọc đi
            // Hẹn giờ sau 300ms (chờ khung chat mở ra xong) thì tự động đặt con trỏ chuột nhấp nháy vào ô nhập liệu để người dùng gõ luôn
            setTimeout(() => document.getElementById('chatbot-input').focus(), 300);
        } else { // Nếu trạng thái là ĐÓNG
            win.classList.remove('open'); // Gỡ class 'open' để CSS thu nhỏ giấu khung chat đi
            icon.className = 'fas fa-robot'; // Trả lại icon hình con robot cho nút tròn launcher
        }
    }

    /**
     * Hàm xử lý khi người dùng bấm vào các nhãn gợi ý câu hỏi nhanh (Suggestion Chips)
     * @param {HTMLElement} btn - Chính là thẻ button được click
     */
    function sendSuggestion(btn) {
        // Sử dụng Biểu thức chính quy (Regex) loại bỏ toàn bộ các ký tự đặc biệt, emoji, khoảng trắng nằm ở đầu câu chữ của nút
        const text = btn.textContent.replace(/^[^\w\s]+\s*/, '').trim(); 
        // Lấy toàn bộ nội dung chữ kèm emoji gán thẳng vào làm giá trị cho ô nhập liệu textarea
        document.getElementById('chatbot-input').value = btn.textContent.trim();
        // Gọi hàm gửi tin nhắn đi luôn
        sendMessage();
        // Thực hiện ẩn hoàn toàn khu vực chứa các nút gợi ý câu hỏi nhanh sau lần đầu tiên sử dụng để nhường chỗ hiển thị hội thoại
        document.getElementById('chatbot-suggestions').style.display = 'none';
    }

    /**
     * Hàm tự động co giãn chiều cao của ô nhập liệu textarea dựa theo độ dài văn bản người dùng gõ
     * @param {HTMLElement} el - Chính là thẻ textarea đang tương tác gõ chữ
     */
    function autoResizeTextarea(el) {
        el.style.height = 'auto'; // Reset tạm thời độ cao về mặc định để tính toán chính xác lại
        // Đo đạc độ cao thực tế của khối nội dung văn bản bên trong (scrollHeight), khống chế tối đa không vượt quá 100px
        el.style.height = Math.min(el.scrollHeight, 100) + 'px';
    }

    /**
     * Hàm bắt và xử lý riêng hành vi bấm phím từ bàn phím trong ô gõ
     * @param {Event} e - Đối tượng sự kiện phím bấm hệ thống
     */
    function handleChatKey(e) {
        // Nếu người dùng nhấn phím Enter VÀ ĐỒNG THỜI KHÔNG nhấn kèm phím Shift (Viết hoa xuống dòng)
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault(); // Ngăn chặn hành vi mặc định của phím Enter (Hành vi mặc định trong textarea là nhảy xuống dòng mới)
            sendMessage();      // Kích hoạt lệnh gửi tin nhắn
        }
    }

    /**
     * Hàm cốt lõi: Thu thập dữ liệu chữ, đóng gói và thực hiện gọi API gửi tin nhắn lên Server xử lý
     */
    async function sendMessage() {
        const input = document.getElementById('chatbot-input');
        const text  = input.value.trim(); // Thu hồi chuỗi chữ trong ô gõ và dùng hàm .trim() loại bỏ các khoảng trắng thừa ở 2 đầu

        // [MÀNG LỌC BẢO VỆ]: Nếu chuỗi trống (không nhập gì) HOẶC hệ thống đang bận xử lý tin cũ (isTyping = true) thì lập tức dừng hàm, không làm gì cả
        if (!text || isTyping) return;

        // Bơm tin nhắn của Người dùng vừa nhập lên giao diện khung chat hiển thị trước
        appendMessage('user', text);
        input.value = ''; // Làm sạch trống ô gõ chữ để chuẩn bị cho lượt nhập tiếp theo
        input.style.height = 'auto'; // Đưa chiều cao ô gõ về lại kích thước 1 dòng ban đầu

        // Ẩn thanh gợi ý câu hỏi nhanh (đề phòng trường hợp người dùng tự gõ phím chứ không bấm nút chip gợi ý)
        document.getElementById('chatbot-suggestions').style.display = 'none';

        // Gọi hàm hiển thị hiệu ứng 3 chấm nhấp nháy, biểu thị Bot đang suy nghĩ
        showTyping();
        isTyping = true; // Chuyển cờ hiệu sang trạng thái bận
        document.getElementById('chatbot-send-btn').disabled = true; // Khóa (vô hiệu hóa) nút bấm gửi để chặn click lặp lại

        try {
            // Sử dụng hàm fetch gửi một yêu cầu mạng dạng asynchronous (bất đồng bộ) tới backend Laravel
            const response = await fetch('{{ route("chatbot.chat") }}', {
                method: 'POST', // Phương thức truyền dữ liệu bảo mật POST
                headers: {
                    'Content-Type': 'application/json', // Khai báo định dạng dữ liệu gửi đi là JSON
                    // X-CSRF-TOKEN: Lấy mã token bảo mật chống tấn công giả mạo từ thẻ meta gán vào header, Laravel yêu cầu bắt buộc đối với phương thức POST
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json', // Khai báo mong muốn dữ liệu nhận về từ server cũng là định dạng JSON
                },
                // Đóng gói chuyển mảng dữ liệu thành chuỗi JSON để truyền đi qua body request
                body: JSON.stringify({
                    message: text, // Nội dung câu hỏi hiện tại
                    history: chatHistory.slice(-10), // Cắt mảng lấy duy nhất 10 lượt hội thoại gần nhất gửi đi nhằm tránh quá tải token cho AI
                }),
            });

            const data = await response.json(); // Chờ chuyển đổi phản hồi từ máy chủ về dạng đối tượng Object JavaScript
            hideTyping(); // Xóa bỏ dòng hiệu ứng 3 chấm nhấp nháy suy nghĩ ra khỏi giao diện

            if (data.success) { // Nếu Controller phía Backend trả về cờ hiệu thành công (true)
                // Bơm nội dung câu trả lời của Bot lên giao diện, truyền kèm mảng bài viết gợi ý (nếu backend có tìm thấy bài viết liên quan)
                appendMessage('bot', data.message, data.posts || []);
                
                // Cập nhật lưu trữ hội thoại mới vào biến mảng lịch sử cục bộ
                chatHistory.push({ role: 'user',   content: text });
                chatHistory.push({ role: 'model', content: data.message });
                
                // [TỐI ƯU BỘ NHỚ]: Nếu mảng lịch sử tích lũy vượt quá 20 dòng dữ liệu, thực hiện cắt bớt chỉ giữ lại 20 phần tử cuối cùng
                if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);
            } else {
                // Nhánh xử lý khi server chạy bình thường nhưng thuật toán backend trả về mã lỗi logic
                appendMessage('bot', data.message || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại! 🙏', []);
            }
        } catch (err) {
            // Nhánh xử lý khi xảy ra các lỗi nghiêm trọng ngoài ý muốn (Ví dụ sập server, rớt mạng giữa chừng...)
            hideTyping();
            appendMessage('bot', 'Mất kết nối mạng. Vui lòng kiểm tra internet và thử lại! 📶', []);
        } finally {
            // Khối lệnh cuối cùng luôn luôn được chạy dù quá trình trên thành công hay thất bại
            isTyping = false; // Trả cờ hiệu về trạng thái rảnh rỗi
            document.getElementById('chatbot-send-btn').disabled = false; // Mở khóa lại nút bấm gửi tin nhắn
            document.getElementById('chatbot-input').focus(); // Tự động tập trung lại con trỏ chuột vào ô nhập liệu
        }
    }

    /**
     * Hàm xử lý tạo cấu trúc mã HTML và đẩy hiển thị tin nhắn mới lên màn hình chat
     * @param {string} role - Vai trò của đối tượng gửi tin ('bot' hoặc 'user')
     * @param {string} text - Nội dung văn bản chữ của tin nhắn
     * @param {Array} posts - Mảng chứa danh sách bài viết đề xuất đi kèm (chỉ có ở Bot)
     */
    function appendMessage(role, text, posts) {
        posts = posts || []; // Phòng hờ nếu tham số posts bị truyền thiếu, mặc định gán thành mảng rỗng []
        const container = document.getElementById('chatbot-messages');
        // Tạo chuỗi thời gian hiện tại theo định dạng chuẩn của Việt Nam dạng HH:MM (Ví dụ: 14:30)
        const now = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        // Khởi tạo các biến chứa tên Class CSS dựa theo vai trò của người gửi
        const isBot       = role === 'bot';
        const avatarClass = isBot ? 'bot' : 'user-av';
        const avatarIcon  = isBot ? 'fa-robot' : 'fa-user';
        const bubbleClass = isBot ? 'bot' : 'user';
        const rowClass    = isBot ? '' : 'user'; // Nếu là user gửi, class này sẽ kích hoạt thuộc tính đảo chiều hiển thị từ phải sang trái

        // Gọi hàm formatMessage để chuyển đổi các ký tự định dạng thô như dấu sao thành thẻ HTML chuẩn
        const formatted = formatMessage(text);

        // Vòng lặp sinh mã HTML hiển thị các thẻ danh sách bài viết liên quan (Post Cards) nếu có
        let postsHtml = '';
        if (isBot && posts.length > 0) {
            postsHtml = '<div style="margin-top:0.75rem;display:flex;flex-direction:column;gap:0.5rem;">';
            posts.forEach(function(post, idx) {
                // Tự động kết hợp liên kết URL của hệ thống với slug của bài viết được lấy ra từ cơ sở dữ liệu
                const postUrl = '{{ url("/bai-viet") }}/' + post.slug;
                // Kiểm tra nếu bài viết có lưu thông tin địa danh thì sinh HTML hiển thị icon định vị, ngược lại thì bỏ trống
                const location = post.location
                    ? '<span style="color:#94a3b8;font-size:0.7rem;"><i class="fas fa-map-marker-alt" style="color:#D4A373;margin-right:3px;"></i>' + escapeHtml(post.location) + '</span>'
                    : '';
                // Kiểm tra hiển thị đoạn trích văn bản ngắn của bài viết
                const excerpt = post.excerpt
                    ? '<p style="color:#64748b;font-size:0.75rem;margin:0.2rem 0 0;line-height:1.4;">' + escapeHtml(post.excerpt) + '</p>'
                    : '';
                
                // Cộng dồn mã HTML cấu trúc thiết kế của từng thẻ bài viết vào chuỗi tổng
                // onmouseover/onmouseout: Hiệu ứng đổi màu nền trực tiếp bằng JS khi người dùng di chuột vào thẻ bài viết
                postsHtml += `
                    <a href="${postUrl}" target="_blank" style="
                        display:block;
                        background:#FAF7F2;
                        border:1px solid rgba(212,163,115,0.3);
                        border-radius:10px;
                        padding:0.6rem 0.75rem;
                        text-decoration:none;
                        transition:all 0.2s ease;
                        cursor:pointer;
                    "
                    onmouseover="this.style.background='rgba(212,163,115,0.12)';this.style.borderColor='#D4A373';"
                    onmouseout="this.style.background='#FAF7F2';this.style.borderColor='rgba(212,163,115,0.3)';"
                    >
                        <div style="display:flex;align-items:flex-start;gap:0.5rem;">
                            <span style="
                                background:linear-gradient(135deg,#D4A373,#b8864e);
                                color:white;
                                font-size:0.65rem;
                                font-weight:700;
                                width:18px;height:18px;
                                border-radius:50%;
                                display:inline-flex;align-items:center;justify-content:center;
                                flex-shrink:0;margin-top:1px;
                            ">${idx + 1}</span>
                            <div style="flex:1;min-width:0;">
                                <div style="font-weight:600;font-size:0.82rem;color:#0F172A;line-height:1.35;margin-bottom:0.2rem;">${escapeHtml(post.title)}</div>
                                ${location}
                                ${excerpt}
                                <div style="margin-top:0.3rem;display:flex;align-items:center;gap:0.75rem;">
                                    <span style="color:#94a3b8;font-size:0.7rem;"><i class="fas fa-eye" style="color:#D4A373;margin-right:3px;"></i>${post.views} lượt xem</span>
                                    <span style="color:#D4A373;font-size:0.7rem;font-weight:600;">Đọc ngay →</span>
                                </div>
                            </div>
                        </div>
                    </a>`;
            });
            postsHtml += '</div>';
        }

        // Tạo chuỗi HTML tổng hợp chứa toàn bộ cấu trúc dòng tin nhắn, avatar, nội dung và mốc thời gian
        const html = `
            <div class="msg-row ${rowClass}">
                <div class="msg-avatar ${avatarClass}">
                    <i class="fas ${avatarIcon}" style="font-size:0.7rem;"></i>
                </div>
                <div style="max-width:85%;">
                    <div class="msg-bubble ${bubbleClass}">${formatted}${postsHtml}</div>
                    <div class="msg-time">${now}</div>
                </div>
            </div>
        `;

        // insertAdjacentHTML: Hàm tối ưu của JS giúp bơm chuỗi HTML vào ngay phía trước thời điểm kết thúc (beforeend) của thẻ container chat
        container.insertAdjacentHTML('beforeend', html);
        // Tự động cuộn thanh cuốn dọc xuống dưới cùng để luôn lộ diện tin nhắn mới nhất vừa xuất hiện
        container.scrollTop = container.scrollHeight;
    }

    /**
     * Hàm làm sạch chuỗi văn bản đầu vào, chuyển đổi các ký tự nhạy cảm thành thực thể an toàn nhằm ngăn chặn cuộc tấn công XSS (Tiêm mã độc)
     * @param {string} str - Chuỗi chữ thô cần lọc
     */
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')   // Biến đổi ký tự & thành thực thể an toàn
            .replace(/</g, '&lt;')    // Biến đổi dấu nhỏ hơn < thành thực thể để trình duyệt không hiểu nhầm là thẻ mở HTML
            .replace(/>/g, '&gt;')    // Biến đổi dấu lớn hơn >
            .replace(/"/g, '&quot;'); // Biến đổi dấu nháy kép "
    }

    /**
     * Hàm định dạng văn bản thô từ AI: Tìm kiếm các cú pháp Markdown cơ bản để biên dịch thành thẻ HTML hiển thị cho đẹp
     * @param {string} text - Văn bản chứa cú pháp markdown từ AI phát về
     */
    function formatMessage(text) {
        return text
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            // Tìm kiếm chuỗi văn bản nằm kẹp giữa cặp dấu 2 sao **...** để thay thế bằng thẻ in đậm <strong>...</strong>
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            // Tìm kiếm chuỗi nằm kẹp giữa cặp dấu 1 sao *...* để thay thế bằng thẻ in nghiêng <em>...</em>
            .replace(/\*(.*?)\*/g, '<em>$1</em>')
            // Thay thế ký tự xuống dòng (\n) của hệ thống bằng thẻ bẻ dòng <br> của HTML để hiển thị đúng bố cục dòng chữ xuống hàng
            .replace(/\n/g, '<br>');
    }

    /**
     * Hàm sinh và hiển thị cấu trúc khối 3 chấm hoạt họa nhấp nháy (Báo hiệu Bot đang gõ chữ)
     */
    function showTyping() {
        const container = document.getElementById('chatbot-messages');
        const html = `
            <div class="msg-row" id="typing-row">
                <div class="msg-avatar bot"><i class="fas fa-robot" style="font-size:0.7rem;"></i></div>
                <div class="typing-indicator">
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                    <div class="typing-dot"></div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        container.scrollTop = container.scrollHeight;
    }

    /**
     * Hàm tìm và xóa bỏ hoàn toàn thẻ chứa hiệu ứng 3 chấm nhấp nháy ra khỏi màn hình cuộc trò chuyện
     */
    function hideTyping() {
        const row = document.getElementById('typing-row');
        if (row) row.remove(); // Hàm .remove() xóa bỏ trực tiếp thẻ DOM ra khỏi cấu trúc trang
    }

    // Thiết lập bộ đếm thời gian kích hoạt duy nhất một lần sau 3 giây (3000ms) tính từ lúc trang web tải xong
    setTimeout(() => {
        if (!isOpen) { // Nếu người dùng vẫn chưa chủ động mở khung chat ra
            // Thực hiện cho hiển thị đốm đỏ thông báo số 1 ở nút tròn nhằm gây sự chú ý thu hút người dùng bấm vào chat
            document.getElementById('chatbot-badge').style.display = 'flex';
        }
    }, 3000);

    // ── CHỈ THỊ CỦA LARAVEL BLADE ──
    // @if(session('chatbot_open')): Kiểm tra xem trong bộ nhớ Session của máy chủ có lưu cờ lệnh 'chatbot_open' hay không 
    // (Thường được Controller thiết lập ngay sau khi người dùng thực hiện Đăng nhập tài khoản thành công)
    @if(session('chatbot_open'))
    setTimeout(() => {
        if (!isOpen) { // Nếu cửa sổ chat đang đóng
            toggleChatbot(); // Tự động bung mở cửa sổ chatbot ra luôn không cần đợi người dùng bấm
            
            // Tiếp tục hẹn giờ thêm 600ms để chờ khung chat mở bung hoàn chỉnh thì phát tin nhắn chào mừng cá nhân hóa tên riêng
            setTimeout(() => {
                // auth()->check(): Câu lệnh PHP của Laravel kiểm tra xem người dùng hiện tại đã đăng nhập tài khoản thành công chưa
                // Lấy ra tên hiển thị đầy đủ của tài khoản (name) thông qua Eloquent Auth
                const userName = '{{ auth()->check() ? auth()->user()->name : "" }}';
                appendMessage('bot',
                    'Chào mừng **' + escapeHtml(userName) + '** đã đăng nhập! 🎉\n'
                    + 'Tôi là TravelBot, sẵn sàng tư vấn du lịch cho bạn.\n'
                    + 'Bạn muốn khám phá điểm đến nào hôm nay? ✈️',
                    []
                );
            }, 600);
        }
    }, 500);
    @endif
    </script>
</body>
</html>