<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- @yield('title'): Vùng động cho phép các trang con (như Quản lý Bài viết, Người dùng) 
         truyền tiêu đề riêng lên tab trình duyệt thông qua cặp thẻ @section('title', '...') --}}
    <title>@yield('title') - VietTravel Admin</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* ── Biến cấu hình CSS Toàn cục (:root) ──
           Thiết lập các mã màu chủ đạo theo hệ Brand Luxury (Navy, Vàng Gold, Kem) giúp dễ dàng quản lý và tái sử dụng */
        :root {
            --navy:      #0F172A;
            --gold:      #D4A373;
            --gold-dark: #b8864e;
            --cream:     #FAF7F2;
            --beige:     #E7D7C9;
            --slate:     #334155;
            --forest:    #1a3a2a;
            --sidebar-w: 260px; /* Định nghĩa chiều rộng cố định của thanh Menu bên trái */
            --glass-border: rgba(212,163,115,0.18);
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:var(--cream); color:var(--navy); min-height:100vh; }

        /* ── Cấu trúc thanh điều hướng bên trái (Sidebar) ── */
        .admin-sidebar {
            width: var(--sidebar-w);
            background: var(--navy);
            min-height: 100vh;
            position: fixed; /* Ghim chặt Sidebar cố định ở rìa trái màn hình khi cuộn trang */
            left: 0; top: 0;
            z-index: 100;
            transition: transform 0.3s ease; /* Hiệu ứng trượt mượt mà phục vụ Responsive trên Mobile */
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
            border-left: 3px solid transparent; /* Tạo đường viền ẩn bên mép trái, sẽ sáng lên khi Active */
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
        /* Hiệu ứng di chuột (Hover) tương tác trên Menu */
        .admin-sidebar .nav-link:hover {
            color: var(--gold) !important;
            background: rgba(212,163,115,0.08);
            border-left-color: rgba(212,163,115,0.4);
        }
        .admin-sidebar .nav-link:hover i { color: var(--gold); }
        
        /* Trạng thái Menu đang được chọn tích cực (.active) */
        .admin-sidebar .nav-link.active {
            color: var(--gold) !important;
            background: rgba(212,163,115,0.12);
            border-left-color: var(--gold); /* Làm bừng sáng viền trái màu vàng Gold */
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

        /* ── Vùng không gian hiển thị nội dung Admin (Content) ── */
        .admin-content {
            /* Đẩy lùi lề trái sang một khoảng bằng đúng chiều rộng Sidebar để tránh hai khối đè lấp lên nhau */
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

        /* ── Thẻ