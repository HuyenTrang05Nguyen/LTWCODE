<!DOCTYPE html>
<html lang="vi"> <head>
    <meta charset="UTF-8"> <meta name="viewport" content="width=device-width, initial-scale=1.0"> <meta name="description" content="Cẩm nang du lịch trực tuyến - Chia sẻ kinh nghiệm và cẩm nang du lịch Việt Nam"> <title>@yield('title', 'Cẩm Nang Du Lịch') - TravelGuide</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"> <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet"> <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet"> <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        /* Khai báo các biến thiết lập toàn cục (CSS Variables) giúp quản lý màu sắc, bo góc và đổ bóng đồng bộ toàn hệ thống */
        :root {
            --navy:      #0F172A; /* Màu xanh đen Luxury */
            --gold:      #D4A373; /* Màu vàng Gold chủ đạo */
            --gold-dark: #b8864e; /* Màu vàng Gold đậm khi hover chuột */
            --cream:     #FAF7F2; /* Màu kem nền nhã nhặn */
            --beige:     #E7D7C9; /* Màu be trung tính */
            --slate:     #334155; /* Màu xám đá cho chữ thường */
            --forest:    #1a3a2a; /* Màu xanh lá rừng sâu */
            --primary:   #D4A373;
            --primary-dark: #b8864e;
            --secondary: #1a3a2a;
            --accent:    #D4A373;
            --bg-page:   #FAF7F2; /* Màu nền trang web */
            --bg-card:   #ffffff; /* Màu nền cho các khối hộp (Card) */
            --text-primary:   #0F172A; /* Màu chữ chính (tiêu đề) */
            --text-secondary: #334155; /* Màu chữ phụ (nội dung) */
            --text-muted:     #94a3b8; /* Màu chữ mờ (ngày tháng, lượt xem) */
            --gradient-primary: linear-gradient(135deg, #D4A373 0%, #b8864e 100%); /* Dải màu Gradient vàng Gold */
            --gradient-hero:    linear-gradient(135deg, #FAF7F2 0%, #E7D7C9 100%);
            --glass-border: rgba(212,163,115,0.18); /* Đường viền mờ hiệu ứng kính */
            --shadow-sm: 0 1px 4px rgba(15,23,42,0.07), 0 1px 2px rgba(15,23,42,0.04); /* Các cấp độ đổ bóng từ nhẹ đến mạnh */
            --shadow-md: 0 4px 20px rgba(15,23,42,0.10), 0 2px 8px rgba(15,23,42,0.05);
            --shadow-lg: 0 20px 48 rgba(15,23,42,0.13), 0 8px 20px rgba(15,23,42,0.07);
            --radius-sm:   10px; /* Các thông số bo góc từ nhỏ đến tròn hẳn */
            --radius-md:   16px;
            --radius-lg:   24px;
            --radius-full: 9999px;
        }

        /* Khởi tạo mặc định CSS cho toàn trang */
        * { margin:0; padding:0; box-sizing:border-box; } /* Reset rìa và đệm về 0 để đồng bộ cách tính kích thước khối */
        html { scroll-behavior: smooth; } /* Tạo hiệu ứng cuộn trang lướt êm ái khi bấm vào các liên kết neo */
        body {
            font-family: 'Inter', sans-serif; /* Áp dụng font Inter hiện đại cho toàn bộ chữ của website */
            background: var(--bg-page); /* Gán màu nền trang web bằng biến màu kem đã khai báo */
            color: var(--text-primary); /* Gán màu chữ mặc định */
            min-height: 100vh; /* Chiều cao tối thiểu bằng 100% màn hình hiển thị */
            overflow-x: hidden; /* Chặn tuyệt đối hiện tượng vỡ giao diện xuất hiện thanh cuộn ngang */
            line-height: 1.7; /* Định khoảng cách giãn dòng hợp lý giúp người dùng không mỏi mắt khi đọc cẩm nang */
        }
        
        /* Áp dụng Font chữ có chân cổ điển, sang trọng (Playfair Display) dành riêng cho các thẻ tiêu đề từ h1 đến h6 */
        h1,h2,h3,h4,h5,h6 { font-family: 'Playfair Display', serif; }

        /* --- CẤU HÌNH THANH CUỘN TRÌNH DUYỆT (CUSTOM SCROLLBAR) --- */
        ::-webkit-scrollbar { width: 6px; } /* Độ rộng thanh cuộn dọc */
        ::-webkit-scrollbar-track { background: var(--cream); } /* Màu nền của rãnh trượt thanh cuộn */
        ::-webkit-scrollbar-thumb { background: var(--beige); border-radius: 3px; } /* Thanh chạy bên trong được bo góc */
        ::-webkit-scrollbar-thumb:hover { background: var(--gold); } /* Khi rê chuột vào, thanh chạy đổi sang màu vàng Gold nổi bật */

        /* ── CẤU HÌNH CSS THANH ĐIỀU HƯỚNG (NAVBAR) ── */
        .navbar-custom {
            background: rgba(255,255,255,0.97); /* Nền trắng đục tinh khôi */
            backdrop-filter: blur(20px); /* Hiệu ứng làm mờ kính cường lực phía sau thanh Navbar (Cao cấp) */
            -webkit-backdrop-filter: blur(20px); /* Hỗ trợ hiệu ứng mờ kính trên trình duyệt Safari của Apple */
            border-bottom: 1px solid rgba(212,163,115,0.15); /* Đường gạch chân mảnh màu vàng mờ */
            padding: 0.85rem 0; /* Khoảng đệm trên dưới giúp thanh điều hướng cân đối */
            transition: all 0.35s ease; /* Tạo hiệu ứng mượt mà khi thanh điều hướng co nhỏ hoặc đổi trạng thái */
            box-shadow: 0 2px 12px rgba(15,23,42,0.06); /* Đổ bóng nhẹ phía dưới tạo chiều sâu */
        }
        
        /* Class bổ trợ: Sẽ tự động kích hoạt thông qua JavaScript khi người dùng cuộn màn hình xuống dưới */
        .navbar-custom.scrolled {
            background: rgba(255,255,255,0.99); /* Làm nền trắng đậm đặc hơn */
            box-shadow: var(--shadow-md); /* Tăng độ đổ bóng rõ nét để phân tách hẳn với nội dung cuộn bên dưới */
        }
        
        /* Cấu hình thương hiệu/Logo chữ trên thanh Navbar */
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-weight: 700; /* Chữ đậm */
            font-size: 1.5rem;
            color: var(--navy) !important; /* Màu xanh đen bắt buộc */
            letter-spacing: -0.01em; /* Co khít khoảng cách các chữ cái lại một chút tạo sự chuyên nghiệp */
        }
        .navbar-brand .brand-icon { color: var(--gold); } /* Icon đính kèm logo chữ có màu vàng Gold */
        
        /* Cấu hình các nút liên kết menu (Trang chủ, Điểm đến, Bài viết...) */
        .nav-link {
            font-family: 'Inter', sans-serif;
            color: var(--slate) !important; /* Chữ màu xám đá tinh tế */
            font-weight: 500;
            font-size: 0.92rem;
            padding: 0.5rem 1rem !important; /* Khoảng cách bấm giữa các link thông thoáng */
            transition: color 0.3s ease; /* Hiệu ứng đổi màu chữ êm ái khi hover */
            position: relative; /* Tạo gốc tọa độ phục vụ cho các hiệu ứng gạch chân động sau này */
            letter-spacing: 0.01em;
        }
/* Khi di chuột vào liên kết (hover) hoặc khi trang đó đang được kích hoạt (active), chữ đổi sang màu vàng Gold */
        .nav-link:hover, .nav-link.active { color: var(--gold) !important; }
        
        /* Sử dụng phần tử giả ::after để tạo một đường gạch chân ẩn dưới mỗi menu */
        .nav-link::after {
            content: ''; /* Bắt buộc phải có để phần tử giả hiển thị */
            position: absolute; /* Đặt vị trí tuyệt đối dựa theo thẻ cha .nav-link */
            bottom: 0; left: 50%; /* Căn đều từ cạnh đáy và nằm chính giữa thẻ cha */
            transform: translateX(-50%); /* Dịch ngược lại 50% chiều rộng để căn giữa chính xác tuyệt đối */
            width: 0; height: 2px; /* Mặc định chiều rộng bằng 0 để ẩn đi, độ dày đường gạch là 2px */
            background: var(--gold); /* Màu đường gạch là màu vàng Gold */
            transition: width 0.3s ease; /* Tạo hiệu ứng bung chiều rộng mượt mà trong 0.3 giây */
            border-radius: 2px; /* Bo tròn nhẹ hai đầu đường gạch chân */
        }
        
        /* Khi hover chuột hoặc menu ở trạng thái active, đường gạch chân tự động bung rộng ra bằng 70% chiều rộng thẻ chữ */
        .nav-link:hover::after, .nav-link.active::after { width: 70%; }
        
        /* ── CẤU HÌNH NÚT BẤM (BUTTONS) ── */
        
        /* 1. Nút bấm dạng màu nền Gradient (Nút Đăng ký, Đăng nhập chính...) */
        .btn-primary-custom {
            background: var(--gradient-primary); /* Đổ dải màu chuyển sắc từ vàng Gold sang vàng đậm */
            border: none; /* Khử bỏ đường viền mặc định */
            color: white; /* Màu chữ trắng */
            font-family: 'Inter', sans-serif;
            font-weight: 600; /* Độ đậm chữ vừa phải */
            padding: 0.5rem 1.5rem; /* Khoảng đệm trên dưới và trái phải cân đối */
            border-radius: var(--radius-full); /* Bo tròn xoe nút bấm theo dạng viên thuốc (Pill-shaped) */
            transition: all 0.3s ease; /* Tạo hiệu ứng chuyển động mượt cho mọi thuộc tính khi tương tác */
            box-shadow: 0 4px 14px rgba(212,163,115,0.35); /* Đổ bóng mờ màu vàng Gold tạo hiệu ứng phát sáng nhẹ */
            letter-spacing: 0.02em; /* Giãn cách các ký tự chữ ra một chút cho thoáng */
        }
        
        /* Hiệu ứng khi di chuột vào nút bấm chính */
        .btn-primary-custom:hover {
            transform: translateY(-2px); /* Nút bấm hơi nhấc nhẹ lên trên 2px tạo cảm giác tương tác vật lý */
            box-shadow: 0 8px 28px rgba(212,163,115,0.45); /* Tăng phạm vi đổ bóng đậm và rộng hơn để làm nổi bật nút */
            color: white; /* Giữ nguyên chữ màu trắng */
        }
        
        /* 2. Nút bấm dạng viền rỗng (Outline Button) */
        .btn-outline-custom {
            border: 2px solid var(--gold); /* Viền ngoài dày 2px màu vàng Gold */
            color: var(--gold); /* Chữ màu vàng Gold */
            font-family: 'Inter', sans-serif;
            font-weight: 600;
            padding: 0.5rem 1.5rem;
            border-radius: var(--radius-full); /* Bo tròn xoe dạng viên thuốc */
            transition: all 0.3s ease;
            background: transparent; /* Nền trong suốt xuyên thấu */
            letter-spacing: 0.02em;
        }
        
        /* Hiệu ứng khi di chuột vào nút viền rỗng */
        .btn-outline-custom:hover {
            background: var(--gradient-primary); /* Nền chuyển từ trong suốt sang dải màu Gradient vàng Gold đầy đặc */
            border-color: transparent; /* Ẩn đường viền cũ đi */
            color: white; /* Chữ tự động chuyển sang màu trắng để nổi bật trên nền vàng */
            transform: translateY(-2px); /* Nhấc nhẹ nút lên 2px */
            box-shadow: 0 8px 24px rgba(212,163,115,0.35); /* Đổ bóng mờ màu vàng dưới chân nút */
        }
        
        /* ── CẤU HÌNH KHỐI BÀI VIẾT (CARDS) ── */
        
        /* Cấu hình khung hộp bài viết hiệu ứng kính mờ (Card Glass) */
        .card-glass {
            background: var(--bg-card); /* Màu nền trắng tinh khôi */
            border: 1px solid var(--glass-border); /* Đường viền mờ sang trọng */
            border-radius: var(--radius-md); /* Bo góc khối hộp 16px vừa vặn, hiện đại */
            overflow: hidden; /* Chặn tuyệt đối không cho hình ảnh hoặc nội dung bên trong tràn ra ngoài góc bo */
            
            /* Sử dụng thuật toán chuyển động cubic-bezier cao cấp giúp hiệu ứng mượt mà, chân thật như chuyển động vật lý */
            transition: all 0.35s cubic-bezier(0.4,0,0.2,1); 
            box-shadow: var(--shadow-sm); /* Đổ bóng nhẹ mặc định */
        }
        
        /* Hiệu ứng khi người dùng di chuột vào hộp bài viết */
        .card-glass:hover {
            transform: translateY(-6px); /* Hộp bài viết bay nhẹ lên trên 6px tạo điểm nhấn thị giác mạnh */
            border-color: rgba(212,163,115,0.35); /* Đường viền đổi sang màu vàng Gold đậm hơn một chút */
            box-shadow: var(--shadow-lg); /* Tăng bóng đổ chân thực, sâu hơn, khiến Card trông như đang nổi hẳn lên */
        }
        
        /* Cấu hình hình ảnh thu nhỏ (Thumbnail) của bài viết nằm trên đầu Card */
        .card-glass .card-img-top {
            height: 240px; /* Khóa cứng chiều cao ảnh 240px để các Card thẳng hàng tăm tắp */
            object-fit: cover; /* Tự động cắt cúp, giữ nguyên tỷ lệ ảnh không bị móp méo dù kích thước ảnh gốc ra sao */
            transition: transform 0.5s ease; /* Hiệu ứng zoom ảnh chạy êm ái trong 0.5 giây */
        }
        
        /* Khi hover vào Card, hình ảnh bên trong tự động phóng to lên 1.07 lần (hiệu ứng thu hút thị giác) */
        .card-glass:hover .card-img-top { transform: scale(1.07); }
        
        /* Khung bọc ngoài ảnh, bắt buộc có overflow:hidden để khi ảnh zoom to lên không bị tràn ra ngoài */
        .card-glass .card-img-wrapper { overflow: hidden; position: relative; }
        
        /* Lớp phủ dải màu bóng tối phía dưới ảnh (Overlay Gradient) */
        .card-glass .card-img-overlay-gradient {
            position: absolute; bottom: 0; left: 0; right: 0; /* Bo khít cạnh đáy và hai bên của ảnh */
            height: 65%; /* Phủ từ đáy lên chiếm 65% chiều cao ảnh */
            
            /* Chuyển sắc từ trong suốt ở trên gạt dần sang màu xanh đen đậm ở đáy để làm nổi bật các chữ màu trắng đè lên (nếu có) */
            background: linear-gradient(transparent, rgba(15,23,42,0.72)); 
            pointer-events: none; /* Xuyên thấu sự kiện chuột, không làm cản trở việc click vào ảnh */
        }
        
        /* Phần ruột chứa thông tin chữ bên dưới ảnh */
        .card-glass .card-body { padding: 1.5rem; } /* Tạo không gian thông thoáng 1.5rem xung quanh chữ */
        
        /* Cấu hình tiêu đề bài viết */
        .card-glass .card-title {
            font-family: 'Playfair Display', serif; /* Font chữ có chân Luxury */
            font-weight: 700; /* In đậm */
            font-size: 1.08rem; /* Kích thước vừa vặn */
            line-height: 1.4; /* Khoảng cách giãn dòng của tiêu đề bài viết hợp lý */
            margin-bottom: 0.5rem; /* Cách đoạn chữ mô tả phía dưới một khoảng nhỏ */
        }
        
        /* Thẻ liên kết bọc chữ tiêu đề */
        .card-glass .card-title a {
            color: var(--text-primary); /* Chữ màu xanh đen mặc định */
            text-decoration: none; /* Khử đường gạch chân mặc định của thẻ siêu liên kết <a> */
            transition: color 0.3s ease; /* Hiệu ứng đổi màu chữ êm ái khi hover */
        }
/* Khi di chuột vào đường link tiêu đề bài viết, chữ chuyển sang màu vàng Gold */
        .card-glass .card-title a:hover { color: var(--gold); }
        
        /* Cấu hình phần văn bản mô tả ngắn (excerpt) của bài viết nằm trong Card */
        .card-glass .card-text {
            color: var(--text-secondary); /* Chữ màu xám đá vừa phải, dễ đọc */
            font-size: 0.9rem; /* Kích thước chữ nhỏ gọn để nhường chỗ cho tiêu đề */
            line-height: 1.65; /* Giãn cách dòng vừa vặn, tạo độ thông thoáng khi đọc */
        }
        
        /* ── CẤU HÌNH NHÃN DANH MỤC (BADGE CATEGORY) ── */
        /* Dùng để hiển thị tên danh mục du lịch (Ví dụ: ẨM THỰC, ĐIỂM ĐẾN, MẸO HAY) */
        .badge-category {
            background: rgba(212,163,115,0.14); /* Màu nền vàng Gold siêu nhạt và trong suốt */
            color: var(--gold-dark); /* Màu chữ vàng Gold đậm để tương phản tốt trên nền nhạt */
            font-family: 'Inter', sans-serif;
            font-weight: 600; /* Chữ hơi đậm để tăng tính nhận diện */
            font-size: 0.72rem; /* Kích thước chữ nhỏ tinh tế */
            padding: 0.3rem 0.85rem; /* Khoảng đệm bên trong bo khít chữ */
            border-radius: var(--radius-full); /* Bo tròn xoe 100% hai đầu nhãn */
            border: 1px solid rgba(212,163,115,0.3); /* Đường viền mảnh màu vàng Gold mờ */
            display: inline-block; /* Chỉ chiếm diện tích vừa đủ bằng nội dung chữ bên trong */
            letter-spacing: 0.04em; /* Giãn cách nhẹ giữa các chữ cái */
            text-transform: uppercase; /* Tự động viết hoa toàn bộ ký tự (Tạo nét thiết kế báo chí chuyên nghiệp) */
        }
        
        /* ── CẤU HÌNH THÔNG TIN BỔ TRỢ (META INFO) ── */
        /* Hiển thị các thông số nhỏ đi kèm bài viết như: Ngày đăng, Lượt xem, Người viết */
        .meta-info {
            display: flex; /* Kích hoạt Flexbox để sắp xếp icon và chữ nằm trên một hàng ngang */
            align-items: center; /* Căn giữa các thành phần theo chiều dọc */
            gap: 0.75rem; /* Tạo khoảng cách 12px đều nhau giữa các cụm thông tin */
            color: var(--text-muted); /* Chữ màu xám nhạt để làm dịu và phân cấp thông tin */
            font-size: 0.82rem; /* Kích thước chữ nhỏ */
            font-family: 'Inter', sans-serif;
        }
        /* Định dạng riêng cho các icon FontAwesome nằm trong cụm thông tin bổ trợ */
        .meta-info i { color: var(--gold); } /* Tất cả icon (như hình đồng hồ, con mắt) đổi sang màu vàng Gold làm điểm nhấn */
        
        /* ── CẤU HÌNH ĐÁNH GIÁ SAO (STARS) ── */
        /* Thường dùng cho các bài viết đánh giá chất lượng địa điểm, khách sạn */
        .stars { color: #D4A373; } /* Các ngôi sao được tô màu vàng Gold lấp lánh */
        .stars .empty { color: #d6d3d1; } /* Những ngôi sao trống (chưa được đánh giá) đổi sang màu xám xi măng nhạt */
        
        /* ── CẤU HÌNH TIÊU ĐỀ PHÂN ĐOẠN (SECTION HEADERS) ── */
        /* Định dạng các khối tiêu đề lớn phân chia các vùng trên trang chủ (Ví dụ: "Điểm Đến Nổi Bật", "Cẩm Nang Mới Nhất") */
        .section-header { margin-bottom: 2.5rem; } /* Khoảng cách từ khối tiêu đề xuống nội dung bên dưới là 40px */
        
        /* Thẻ h2 nằm trong khối tiêu đề phân đoạn */
        .section-header h2 {
            font-family: 'Playfair Display', serif; /* Font chữ có chân cổ điển, đẳng cấp */
            font-weight: 700; /* In đậm rõ nét */
            font-size: 2rem; /* Kích thước chữ lớn nổi bật hẳn lên */
            margin-bottom: 0.5rem; /* Cách dòng mô tả ngắn phía dưới một khoảng nhỏ */
            color: var(--navy); /* Chữ màu xanh đen chủ đạo */
            letter-spacing: -0.01em; /* Co khít khoảng cách chữ cái để tiêu đề trông gãy gọn, tinh tế hơn */
        }
        /* Class bổ trợ giúp đổi màu một từ hoặc cụm từ quan trọng trong tiêu đề sang màu vàng Gold */
        .section-header .gradient-text { color: var(--gold); }
        
        /* Thẻ mô tả ngắn nằm dưới tiêu đề h2 */
        .section-header p { 
            color: var(--text-secondary); /* Chữ màu xám đá */
            font-size: 1.05rem; /* Kích thước chữ hơi lớn một chút để làm lời dẫn nhập */
        }
        
        /* Class dùng chung toàn hệ thống để nhuộm màu vàng Gold cho văn bản */
        .gradient-text { color: var(--gold); }
        
        /* ── CẤU HÌNH CHÂN TRANG (FOOTER) ── */
        /* Toàn bộ khu vực chân trang web chứa bản quyền, thông tin liên hệ và các liên kết nhanh */
        .footer {
            background: var(--navy); /* Nền màu xanh đen Luxury tạo cảm giác vững chãi, khép lại trang web */
            border-top: 1px solid rgba(212,163,115,0.2); /* Đường viền gạch ngang mảnh màu vàng mờ ở ranh giới trên */
            padding: 4rem 0 1.5rem; /* Đệm phía trên rất rộng (64px) để tạo khoảng thoáng, đệm đáy 24px */
            margin-top: 5rem; /* Đẩy toàn bộ khối Footer cách xa nội dung phía trên là 80px */
            color: #94a3b8; /* Chữ mặc định ở footer có màu xám xanh Slate nhã nhặn, dịu mắt */
        }
        
        /* Thẻ tiêu đề danh mục trong footer (Ví dụ: VỀ CHÚNG TÔI, ĐIỀU KHOẢN, LIÊN HỆ) */
        .footer h5 {
            font-family: 'Playfair Display', serif; /* Font chữ thương hiệu */
            font-weight: 600; /* Độ đậm chữ lớn */
            margin-bottom: 1.25rem; /* Khoảng cách đến các đường link bên dưới */
            color: var(--gold); /* Tiêu đề đổi sang màu vàng Gold sang trọng để nổi bật trên nền tối */
            font-size: 1.05rem;
        }
        
        /* Các đường link menu liệt kê dưới chân trang */
        .footer a {
            color: #94a3b8; /* Màu xám xanh mặc định */
            text-decoration: none; /* Khử gạch chân mặc định */
            transition: color 0.3s ease; /* Hiệu ứng chuyển màu chữ mượt khi hover */
            display: block; /* Ép mỗi đường link chiếm trọn 1 hàng độc lập để tự động xếp dọc xuống dưới */
            padding: 0.25rem 0; /* Khoảng bấm đệm trên dưới giúp người dùng dễ click bằng điện thoại */
            font-size: 0.9rem;
        }
        
        /* Hiệu ứng khi di chuột vào các đường link dưới footer */
        .footer a:hover { color: var(--gold); } /* Chữ bừng sáng sang màu vàng Gold */
        
        /* Khối hiển thị Logo thương hiệu chữ nằm dưới Footer */
        .footer .footer-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.4rem;
            font-weight: 700;
            color: #ffffff; /* Tên thương hiệu được thắp sáng bằng màu trắng tinh khôi tuyệt đối */
        }
/* ── NÚT MẠNG XÃ HỘI Ở CHÂN TRANG (SOCIAL ICONS) ── */
        /* Định dạng các nút liên kết Facebook, Instagram... dưới Footer */
        .footer .social-icon {
            display: inline-flex; /* Kích hoạt Flexbox dạng inline để các nút tự xếp hàng ngang nhưng vẫn co giãn nội dung bên trong */
            align-items: center; /* Căn giữa icon theo chiều dọc */
            justify-content: center; /* Căn giữa icon theo chiều ngang */
            width: 38px; height: 38px; /* Khóa cứng kích thước tạo thành một khối vuông 38x38px */
            border-radius: 50%; /* Bo tròn tuyệt đối 100% biến khối vuông thành hình tròn hoàn hảo */
            border: 1px solid rgba(212,163,115,0.3); /* Đường viền mảnh màu vàng Gold mờ */
            color: #94a3b8; /* Biểu tượng icon mặc định có màu xám xanh Slate dịu mắt */
            font-size: 0.95rem; /* Kích thước icon vừa vặn */
            transition: all 0.3s ease; /* Tạo hiệu ứng chuyển động mượt mà khi di chuột vào */
            text-decoration: none; /* Khử đường gạch chân mặc định của thẻ liên kết <a> */
        }
        
        /* Hiệu ứng khi người dùng rê chuột vào nút mạng xã hội */
        .footer .social-icon:hover {
            background: var(--gold); /* Nền trong suốt chuyển sang màu vàng Gold đầy đặc */
            border-color: var(--gold); /* Đường viền chuyển đồng bộ sang màu vàng Gold */
            color: white; /* Biểu tượng icon bên trong tự động đổi sang màu trắng tinh khôi */
            transform: translateY(-2px); /* Nút bấm hơi nảy nhẹ lên trên 2px tạo cảm giác phản hồi sinh động */
        }
        
        /* ── HỘP THÔNG BÁO HỆ THỐNG (ALERTS) ── */
        /* Khung thông báo chung (Ví dụ: "Đăng nhập thành công", "Mật khẩu sai") */
        .alert-custom {
            border: none; /* Khử bỏ đường viền mặc định thô kệch của Bootstrap */
            border-radius: var(--radius-sm); /* Bo góc nhẹ 10px tạo nét tinh tế, hiện đại */
            padding: 1rem 1.5rem; /* Khoảng đệm rộng rãi giúp thông báo dễ đọc, thoáng đãng */
            font-weight: 500; /* Chữ hơi đậm để tăng tính chú ý */
            font-family: 'Inter', sans-serif;
        }
        
        /* 1. Hộp thông báo THÀNH CÔNG (Success Alert - Màu xanh lá pastel) */
        .alert-success-custom {
            background: rgba(26,58,42,0.08); /* Màu nền xanh lá rừng siêu nhạt, dịu mắt và không bị chói */
            color: #1a3a2a; /* Chữ màu xanh lá rừng đậm để tạo độ tương phản sắc nét, dễ đọc */
            border: 1px solid rgba(26,58,42,0.2) !important; /* Đường viền xanh lá mờ bọc nhẹ xung quanh (Ép phê duyệt tối cao bằng !important) */
        }
        
        /* 2. Hộp thông báo THẤT BẠI/LỖI (Error Alert - Màu đỏ pastel) */
        .alert-error-custom {
            background: rgba(220,38,38,0.07); /* Màu nền đỏ nhạt tạo tín hiệu cảnh báo nhưng không gây nhức mắt */
            color: #dc2626; /* Chữ màu đỏ đậm (Red-600) giúp nhận diện lỗi ngay lập tức */
            border: 1px solid rgba(220,38,38,0.18) !important; /* Đường viền đỏ mờ bọc quanh khung */
        }
        
        /* ── Ô NHẬP LIỆU TÙY BIẾN (FORM CONTROLS) ── */
        /* Áp dụng cho các ô input nhập tài khoản, mật khẩu, tìm kiếm... */
        .form-control-dark {
            background: #fdfcfa !important; /* Màu nền trắng sữa siêu nhẹ, tạo cảm giác cao cấp hơn trắng tinh */
            border: 1px solid var(--beige) !important; /* Viền màu be trung tính, mảnh dẻ */
            color: var(--text-primary) !important; /* Chữ người dùng gõ vào có màu xanh đen chủ đạo */
            border-radius: var(--radius-sm); /* Bo góc 10px đồng bộ hệ thống */
            padding: 0.7rem 1rem; /* Chiều cao ô nhập liệu thông thoáng, dễ bấm */
            transition: all 0.3s ease; /* Hiệu ứng chuyển cảnh mượt mà khi nhấn vào ô */
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
        }
        
        /* Trạng thái FOCUS: Khi người dùng nhấp chuột vào ô để bắt đầu gõ chữ */
        .form-control-dark:focus {
            border-color: var(--gold) !important; /* Đường viền lập tức đổi sang màu vàng Gold nổi bật */
            
            /* Tạo một lớp hào quang (Glow effect) mờ rộng 3px màu vàng Gold nhạt bao quanh ô input */
            box-shadow: 0 0 0 3px rgba(212,163,115,0.15) !important; 
            background: #ffffff !important; /* Nền chuyển hẳn sang màu trắng tinh khôi để tối ưu độ tương phản khi gõ chữ */
        }
        
        /* Định dạng riêng cho dòng chữ gợi ý ẩn ngầm bên dưới (chữ mờ khi chưa gõ gì) */
        .form-control-dark::placeholder { color: var(--text-muted); } /* Chữ gợi ý mang màu xám nhạt */
        
        /* Tiêu đề nằm trên mỗi ô nhập liệu (Ví dụ: "Tên đăng nhập", "Mật khẩu") */
        .form-label-custom {
            color: var(--text-secondary); /* Chữ màu xám đá */
            font-weight: 600; /* In chữ hơi đậm để phân tách rõ ràng với ô nhập phía dưới */
            font-size: 0.875rem; /* Kích thước chữ nhỏ gọn tiêu chuẩn */
            margin-bottom: 0.5rem; /* Cách ô input phía dưới một khoảng nhỏ 8px */
            display: block; /* Ép thẻ label chiếm trọn một hàng để đẩy ô input tự động xuống dòng */
            font-family: 'Inter', sans-serif;
        }
        
        /* ── NÚT YÊU THÍCH BÀI VIẾT (FAVORITE BUTTON) ── */
        /* Nút bấm hình trái tim để người dùng lưu lại bài viết cẩm nang du lịch yêu thích */
        .btn-favorite {
            border: 2px solid rgba(239,68,68,0.25); /* Đường viền dày 2px màu đỏ mờ */
            color: #94a3b8; /* Biểu tượng trái tim mặc định có màu xám xanh (chưa yêu thích) */
            background: transparent; /* Nền trong suốt xuyên thấu */
            border-radius: var(--radius-full); /* Bo tròn xoe dạng viên thuốc mềm mại */
            padding: 0.5rem 1.25rem; /* Khoảng đệm cân đối, thông thoáng */
            transition: all 0.3s ease;
            font-weight: 500;
            font-family: 'Inter', sans-serif;
        }
/* Hiệu ứng khi nút Yêu thích được kích hoạt (active) hoặc khi người dùng di chuột vào (hover) */
        .btn-favorite.active, .btn-favorite:hover {
            background: rgba(239,68,68,0.08); /* Đổi sang nền màu đỏ hồng cánh sen siêu nhạt và trong suốt */
            border-color: #ef4444; /* Đường viền chuyển sang màu đỏ rực (Red-500) */
            color: #ef4444; /* Biểu tượng trái tim và chữ chuyển đồng bộ sang màu đỏ để báo hiệu trạng thái */
        }
        
        /* ── THANH PHÂN TRANG (PAGINATION) ── */
        /* Dùng để chia danh sách cẩm nang du lịch thành nhiều trang (Ví dụ: Trang 1, 2, 3...) */
        .pagination .page-link {
            background: var(--bg-card); /* Màu nền trắng mặc định */
            border: 1px solid var(--glass-border); /* Đường viền mờ hiệu ứng kính đồng bộ hệ thống */
            color: var(--text-secondary); /* Số trang hiển thị bằng màu xám đá */
            transition: all 0.3s ease; /* Tạo hiệu ứng mượt mà khi hover hoặc chuyển trang */
            border-radius: var(--radius-sm) !important; /* Thiết lập góc bo 10px cho từng ô số (Ép phê duyệt tối cao bằng !important) */
            margin: 0 2px; /* Tạo khoảng cách giãn cách nhỏ 4px giữa các ô số để không bị dính vào nhau */
            font-family: 'Inter', sans-serif;
        }
        
        /* Hiệu ứng khi người dùng rê chuột vào một ô số trang */
        .pagination .page-link:hover {
            background: var(--gold); /* Nền ô số bừng sáng sang màu vàng Gold */
            border-color: var(--gold); /* Viền ô số đổi sang màu vàng Gold */
            color: white; /* Số hiển thị tự động chuyển sang màu trắng nổi bật */
        }
        
        /* Định dạng riêng cho ô số trang HIỆN TẠI mà người dùng đang đứng xem (Active Page) */
        .pagination .page-item.active .page-link {
            background: var(--gradient-primary); /* Đổ dải màu chuyển sắc Gradient vàng Gold Luxury cực kỳ nổi bật */
            border-color: transparent; /* Khử bỏ đường viền để khối màu mượt mà */
            color: white !important; /* Chữ số bắt buộc đổi sang màu trắng tinh khôi */
        }
        
        /* ── ẢNH ĐẠI DIỆN THÀNH VIÊN (USER AVATAR) ── */
        /* Hiển thị trên góc phải thanh điều hướng sau khi thành viên đăng nhập thành công */
        .user-avatar {
            width: 36px; height: 36px; /* Khóa cứng kích thước ảnh theo chuẩn mini 36x36px */
            border-radius: 50%; /* Bo tròn tuyệt đối 100% để tạo khung ảnh đại diện hình tròn */
            object-fit: cover; /* Tự động cắt cúp, giữ nguyên tỷ lệ khuôn mặt không bị bóp méo dù ảnh gốc của user là ảnh dọc hay ảnh ngang */
            border: 2px solid var(--gold); /* Đường viền dày 2px màu vàng Gold bao quanh như một chiếc khung tranh sang trọng */
        }
        
        /* ── NÚT MENU TRÊN THIẾT BỊ DI ĐỘNG (NAVBAR TOGGLER) ── */
        /* Nút biểu tượng "3 dấu gạch ngang" (Hamburger Button) xuất hiện khi xem web bằng điện thoại */
        .navbar-toggler {
            border: 1px solid var(--glass-border); /* Đường viền mảnh mờ xung quanh nút */
            padding: 0.4rem 0.6rem; /* Khoảng đệm nhỏ gọn, vừa khít ngón tay bấm */
        }
        
        /* Mã hóa trực tiếp icon 3 dấu gạch ngang bằng chuỗi SVG vector để đảm bảo biểu tượng luôn sắc nét trên màn hình Retina */
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%2851,65,85,1%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }
        
        /* ── HIỆU ỨNG HOẠT HỌA TỰ ĐỘNG (ANIMATIONS KEYFRAMES) ── */
        /* Định nghĩa thuật toán hoạt họa: Chuyển động vừa trồi lên trên vừa hiện hình mượt mà (Fade In Up) */
        @keyframes fadeInUp {
            from { 
                opacity: 0; /* Giai đoạn xuất phát: Trong suốt hoàn toàn (ẩn ngầm) */
                transform: translateY(24px); /* Nằm tụt thấp xuống dưới vị trí gốc 24px */
            }
            to { 
                opacity: 1; /* Giai đoạn kết thúc: Hiện hình rõ nét 100% */
                transform: translateY(0); /* Trồi lên vị trí gốc ban đầu */
            }
        }
        
        /* Class bọc ngoài để gán hiệu ứng fadeInUp vào các phần tử HTML mong muốn */
        .animate-fade-in-up { 
            animation: fadeInUp 0.6s ease forwards; /* Thực hiện hoạt họa trong 0.6 giây, chuyển động êm ái và giữ nguyên trạng thái kết thúc */
        }
        
        /* Kỹ thuật Staggered Delay: Tạo hiệu ứng lướt sóng nhịp nhàng (Phần tử sau xuất hiện trễ hơn phần tử trước) */
        .delay-100 { animation-delay: 0.1s; } /* Trì hoãn 0.1 giây mới chạy hoạt họa */
        .delay-200 { animation-delay: 0.2s; } /* Trì hoãn 0.2 giây mới chạy hoạt họa */
        .delay-300 { animation-delay: 0.3s; } /* Trì hoãn 0.3 giây mới chạy hoạt họa */
        
        /* ── MENU THẢ XUỐNG (DROPDOWN MENU) ── */
        /* Hộp tùy chọn đổ xuống khi click vào Avatar hoặc Danh mục (Ví dụ: "Thông tin cá nhân", "Đăng xuất") */
        .dropdown-menu {
            background: var(--bg-card); /* Màu nền trắng tinh tế */
            border: 1px solid var(--glass-border); /* Đường viền kính mờ */
            box-shadow: var(--shadow-md); /* Đổ bóng mức độ vừa phải tạo chiều sâu tách biệt khỏi nền web */
            border-radius: var(--radius-md); /* Bo góc hộp menu thả xuống 16px vô cùng hiện đại */
            padding: 0.5rem; /* Khoảng đệm an toàn bao quanh các nút bên trong */
        }
        
        /* Từng dòng liên kết lựa chọn bên trong menu thả xuống */
        .dropdown-item {
            border-radius: var(--radius-sm); /* Bo góc nhẹ 10px cho từng dòng để khi hover tạo thành khối bo đẹp mắt */
            padding: 0.5rem 1rem; /* Khoảng đệm thông thoáng dễ di chuột hoặc chạm bằng điện thoại */
            color: var(--text-primary); /* Chữ màu xanh đen chủ đạo */
            font-weight: 500; /* Chữ có độ đậm vừa phải */
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease; /* Hiệu ứng đổi màu siêu nhanh 0.2 giây khi đổi mục lựa chọn */
        }
        
        /* Hiệu ứng khi di chuột vào từng dòng lựa chọn của menu dropdown */
        .dropdown-item:hover {
            background: rgba(212,163,115,0.1); /* Nền dòng đổi sang màu vàng Gold siêu nhạt (Độ trong suốt 10%) */
            color: var(--gold-dark); /* Chữ chuyển sang màu vàng Gold đậm để đánh dấu dòng đang chọn */
        }
        
        /* ── TRANG NỘI DUNG CHI TIẾT BÀI VIẾT (POST CONTENT) ── */
        /* Class bọc toàn bộ văn bản nội dung chi tiết của một bài viết cẩm nang du lịch từ Database ra */
        .post-content h1, .post-content h2, .post-content h3, .post-content h4 {
            font-family: 'Playfair Display', serif; /* Ép toàn bộ tiêu đề bài viết con tuân thủ font chữ thương hiệu có chân sang trọng */
            color: var(--navy); /* Toàn bộ tiêu đề lớn nhỏ có màu xanh đen quyền lực */
            margin-top: 2rem; /* Tạo khoảng cách an toàn 32px với đoạn văn phía trên nhằm phân đoạn mạch lạc */
            margin-bottom: 1rem; /* Cách đoạn văn giải thích phía dưới 16px */
        }
/* Định dạng các đoạn văn (<p>) trong bài viết chi tiết */
        .post-content p { 
            margin-bottom: 1.4rem; /* Khoảng cách an toàn giữa các đoạn văn là 22.4px giúp văn bản không bị dính cục */
            line-height: 1.9; /* Giãn dòng rộng rãi (1.9), tỷ lệ vàng tối ưu cho mắt khi đọc bài viết dài */
        }
        
        /* Định dạng hình ảnh minh họa chèn trong nội dung bài viết */
        .post-content img { 
            max-width: 100%; /* Đảm bảo hình ảnh không bao giờ bị tràn, tự động co nhỏ lại vừa vặn với khung chứa */
            border-radius: var(--radius-md); /* Bo góc ảnh 16px đồng bộ với thiết kế Card cao cấp */
            margin: 1.5rem 0; /* Tạo khoảng cách trống 24px ở cả phía trên và phía dưới bức ảnh */
        }
        
        /* Định dạng khối trích dẫn câu chữ nổi bật (Blockquote) */
        .post-content blockquote {
            border-left: 4px solid var(--gold); /* Tạo một thanh gạch dọc dày 4px màu vàng Gold ở bên mép trái */
            padding: 1rem 1.5rem; /* Tạo khoảng đệm thông thoáng bao quanh nội dung trích dẫn */
            background: rgba(212,163,115,0.07); /* Nền màu vàng thổ siêu nhạt, tăng tính nhận diện */
            /* Bo góc tinh tế: Giữ nguyên 2 góc bên trái vuông góc để khớp với thanh dọc, bo tròn 10px cho 2 góc bên phải */
            border-radius: 0 var(--radius-sm) var(--radius-sm) 0; 
            font-style: italic; /* Nghiêng toàn bộ chữ bên trong (chuẩn thiết kế báo chí) */
            color: var(--slate); /* Chữ màu xám đá dịu mắt */
            margin: 1.5rem 0; /* Cách đoạn văn trên và dưới 24px */
        }
        
        /* KỸ THUẬT DROPCAP CAO CẤP: Phóng to chữ cái đầu tiên của đoạn văn đầu tiên trong bài viết */
        .post-content p:first-of-type::first-letter {
            font-family: 'Playfair Display', serif; /* Sử dụng font chữ có chân cổ điển */
            font-size: 3.5rem; /* Phóng to kích thước chữ cái đầu lên gấp hơn 3 lần bình thường */
            font-weight: 700; /* Thiết lập chữ siêu đậm */
            float: left; /* Đẩy chữ cái này áp sát về bên trái để các dòng văn bản tiếp theo tự động bao quanh nó */
            line-height: 0.85; /* Ép chặt khoảng cách dòng để chữ phóng to không đẩy dòng 2 xuống quá sâu */
            margin-right: 0.12em; /* Tạo một khoảng cách nhỏ vừa vặn ở bên phải để không bị dính vào từ tiếp theo */
            color: var(--gold); /* Nhuộm chữ cái đầu tiên này bằng màu vàng Gold chủ đạo */
        }
        
        /* ── ĐÁP ỨNG ĐA MÀN HÌNH (MOBILE RESPONSIVE MEDIABLE QUERIES) ── */
        /* Toàn bộ luật CSS bên dưới sẽ tự động kích hoạt khi người dùng dùng thiết bị có màn hình từ 768px trở xuống (Mobile, Máy tính bảng dọc) */
        @media (max-width: 768px) {
            .section-header h2 { font-size: 1.5rem; } /* Thu nhỏ tiêu đề phân đoạn từ 2rem xuống 1.5rem để không bị tràn dòng trên điện thoại */
            .card-glass .card-img-top { height: 200px; } /* Hạ chiều cao ảnh bài viết xuống 200px (mặc định là 240px) giúp bố cục gọn gàng */
            .navbar-brand { font-size: 1.2rem; } /* Thu nhỏ kích thước chữ Logo thương hiệu trên di động */
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

    <!-- Content -->
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
    <!-- Footer -->
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
                    {{-- Mở khối lệnh PHP: Vào bảng Categories trong DB -> Lấy ra 5 bản ghi -> Lưu vào biến $footerCategories --}}
                    @php $footerCategories = \App\Models\Category::take(5)->get(); @endphp

                    {{-- Khởi động vòng lặp: Duyệt qua 5 danh mục vừa lấy được, gán mỗi danh mục là biến tạm $cat --}}
                    @foreach($footerCategories as $cat)
                        {{-- In ra thẻ liên kết: Truyền chuỗi 'slug' của danh mục lên URL để lọc bài viết theo danh mục đó --}}
                        <a href="{{ route('posts.index', ['category' => $cat->slug]) }}">
                            {{ $cat->name }} {{-- Hiển thị tên của danh mục ra ngoài màn hình (Ví dụ: Vũng Tàu, Đà Lạt...) --}}
                        </a>
                    @endforeach {{-- Kết thúc vòng lặp dữ liệu danh mục --}}
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
        // 1. KHỞI TẠO HIỆU ỨNG CUỘN TRANG AOS
        // duration: 800ms (0.8 giây) | once: true (chỉ chạy hiệu ứng 1 lần khi cuộn xuống) | offset: 100px (cách mép màn hình 100px là kích hoạt)
        AOS.init({ duration: 800, once: true, offset: 100 });
        
        // 2. XỬ LÝ ĐỔI NỀN THANH ĐIỀU HƯỚNG (NAVBAR SCROLL EFFECT)
        window.addEventListener('scroll', function() { // Lắng nghe hành vi cuộn chuột của người dùng trên trình duyệt
            const nav = document.getElementById('mainNav'); // Tìm và lấy ra thanh menu thông qua id="mainNav"
            
            if (window.scrollY > 50) { // Nếu người dùng cuộn màn hình xuống phía dưới vượt quá 50px
                nav.classList.add('scrolled'); // Tự động đắp thêm class "scrolled" để chuyển màu nền từ trong suốt sang đục/đổ bóng
            } else { // Ngược lại, nếu người dùng cuộn ngược hẳn lên trên đỉnh đầu trang
                nav.classList.remove('scrolled'); // Gỡ bỏ class "scrolled" để trả menu về trạng thái thanh mảnh, trong suốt như ban đầu
            }
        });
        
        // 3. TỰ ĐỘNG XÓA HỘP THÔNG BÁO SAU 5 GIÂY (AUTO-CLOSE ALERTS INTERACTION)
        setTimeout(function() { // Thiết lập bộ đếm thời gian trì hoãn (Hàm bất đồng bộ)
            // Tìm kiếm tất cả các hộp thông báo có tính năng tắt (class .alert-dismissible) đang hiển thị trên màn hình
            document.querySelectorAll('.alert-dismissible').forEach(function(alert) { 
                // Sử dụng hàm gốc của Bootstrap JS để tự động kích hoạt hành vi đóng (close) hộp thông báo đó lại
                new bootstrap.Alert(alert).close(); 
            });
        }, 5000); // 5000 mili-giây tương đương với đúng 5 giây sẽ kích hoạt lệnh xóa này
    </script>
    
    @stack('scripts') //một "hộp chứa trống" đặt ở đáy file giao diện tổng (Layout), dùng để gom toàn bộ các đoạn mã hoặc tệp JavaScript (JS) riêng biệt từ các trang con và nạp tập trung về một chỗ.
    {{-- ═══════════════════════════════════════════════
         TRAVELBOT — AI Chatbot Widget
         ═══════════════════════════════════════════════ --}}
    <style>
    /* ── Chatbot Launcher Button ── */
    #chatbot-launcher {
        position: fixed;
        bottom: 28px;
        right: 28px;
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: linear-gradient(135deg, #D4A373, #b8864e);
        border: none;
        cursor: pointer;
        box-shadow: 0 8px 28px rgba(212,163,115,0.55);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        color: white;
        font-size: 1.4rem;
    }
    #chatbot-launcher:hover {
        transform: scale(1.1) translateY(-3px);
        box-shadow: 0 14px 36px rgba(212,163,115,0.65);
    }
    #chatbot-launcher .badge-unread {
        position: absolute;
        top: -4px;
        right: -4px;
        width: 20px;
        height: 20px;
        background: #ef4444;
        border-radius: 50%;
        font-size: 0.65rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 2px solid white;
        display: none;
    }

    /* ── Chatbot Window ── */
    #chatbot-window {
        position: fixed;
        bottom: 100px;
        right: 28px;
        width: 380px;
        max-height: 560px;
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 24px 64px rgba(15,23,42,0.18), 0 8px 24px rgba(15,23,42,0.1);
        border: 1px solid rgba(212,163,115,0.2);
        display: flex;
        flex-direction: column;
        z-index: 9998;
        overflow: hidden;
        transform: scale(0.85) translateY(20px);
        opacity: 0;
        pointer-events: none;
        transition: all 0.3s cubic-bezier(0.4,0,0.2,1);
        transform-origin: bottom right;
    }
    #chatbot-window.open {
        transform: scale(1) translateY(0);
        opacity: 1;
        pointer-events: all;
    }

    /* ── Header ── */
    .chatbot-header {
        background: linear-gradient(135deg, #0F172A 0%, #1a3a2a 100%);
        padding: 1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
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
        flex-shrink: 0;
    }
    .chatbot-header-info h6 {
        font-family: 'Playfair Display', serif;
        font-weight: 700;
        color: #ffffff;
        margin: 0;
        font-size: 0.95rem;
    }
    .chatbot-header-info span {
        font-size: 0.72rem;
        color: rgba(255,255,255,0.6);
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .chatbot-header-info .online-dot {
        width: 7px;
        height: 7px;
        background: #22c55e;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-dot 2s infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.4; }
    }
    .chatbot-close {
        margin-left: auto;
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

    /* ── Messages Area ── */
    .chatbot-messages {
        flex: 1;
        overflow-y: auto;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        background: #FAF7F2;
        scroll-behavior: smooth;
    }
    .chatbot-messages::-webkit-scrollbar { width: 4px; }
    .chatbot-messages::-webkit-scrollbar-thumb { background: #E7D7C9; border-radius: 2px; }

    /* ── Message Bubbles ── */
    .msg-row {
        display: flex;
        gap: 0.6rem;
        align-items: flex-end;
    }
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

    .msg-bubble {
        max-width: 78%;
        padding: 0.65rem 1rem;
        border-radius: 16px;
        font-size: 0.875rem;
        line-height: 1.6;
        font-family: 'Inter', sans-serif;
        word-break: break-word;
    }
    .msg-bubble.bot {
        background: #ffffff;
        color: #0F172A;
        border: 1px solid rgba(212,163,115,0.2);
        border-bottom-left-radius: 4px;
        box-shadow: 0 2px 8px rgba(15,23,42,0.06);
    }
    .msg-bubble.user {
        background: linear-gradient(135deg, #D4A373, #b8864e);
        color: white;
        border-bottom-right-radius: 4px;
    }
    .msg-time {
        font-size: 0.65rem;
        color: #94a3b8;
        margin-top: 3px;
        text-align: right;
    }
    .msg-row.user .msg-time { text-align: left; }

    /* ── Typing Indicator ── */
    .typing-indicator {
        display: flex;
        gap: 4px;
        align-items: center;
        padding: 0.65rem 1rem;
        background: #ffffff;
        border-radius: 16px;
        border-bottom-left-radius: 4px;
        border: 1px solid rgba(212,163,115,0.2);
        width: fit-content;
    }
    .typing-dot {
        width: 7px;
        height: 7px;
        background: #D4A373;
        border-radius: 50%;
        animation: typing-bounce 1.2s infinite;
    }
    .typing-dot:nth-child(2) { animation-delay: 0.2s; }
    .typing-dot:nth-child(3) { animation-delay: 0.4s; }
    @keyframes typing-bounce {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
        30% { transform: translateY(-6px); opacity: 1; }
    }

    /* ── Quick Suggestions ── */
    .chatbot-suggestions {
        padding: 0.75rem 1.25rem 0;
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
        background: #FAF7F2;
    }
    .suggestion-chip {
        background: rgba(212,163,115,0.12);
        border: 1px solid rgba(212,163,115,0.3);
        color: #b8864e;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Inter', sans-serif;
        white-space: nowrap;
    }
    .suggestion-chip:hover {
        background: #D4A373;
        color: white;
        border-color: #D4A373;
    }

    /* ── Input Area ── */
    .chatbot-input-area {
        padding: 0.85rem 1.25rem 1rem;
        background: #ffffff;
        border-top: 1px solid rgba(212,163,115,0.15);
        display: flex;
        gap: 0.6rem;
        align-items: flex-end;
        flex-shrink: 0;
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
        resize: none;
        outline: none;
        transition: border-color 0.2s;
        max-height: 100px;
        line-height: 1.5;
    }
    .chatbot-input:focus { border-color: #D4A373; background: #ffffff; }
    .chatbot-input::placeholder { color: #94a3b8; }
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
    .chatbot-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }

    /* ── Footer note ── */
    .chatbot-footer-note {
        text-align: center;
        font-size: 0.65rem;
        color: #94a3b8;
        padding: 0 1.25rem 0.6rem;
        background: #ffffff;
        font-family: 'Inter', sans-serif;
    }

    /* ── Mobile responsive ── */
    @media (max-width: 480px) {
        #chatbot-window {
            width: calc(100vw - 24px);
            right: 12px;
            bottom: 90px;
            max-height: 70vh;
        }
        #chatbot-launcher { right: 16px; bottom: 20px; }
    }
    </style>

    <!-- Chatbot Launcher Button -->
    <button id="chatbot-launcher" onclick="toggleChatbot()" title="Chat với TravelBot AI">
        <i class="fas fa-robot" id="chatbot-icon"></i>
        <span class="badge-unread" id="chatbot-badge">1</span>
    </button>

    <!-- Chatbot Window -->
    <div id="chatbot-window">
        <!-- Header -->
        <div class="chatbot-header">
            <div class="chatbot-avatar">
                <i class="fas fa-robot"></i>
            </div>
            <div class="chatbot-header-info">
                <h6>TravelBot AI</h6>
                <span><span class="online-dot"></span> Trực tuyến • Hỗ trợ du lịch 24/7</span>
            </div>
            <button class="chatbot-close" onclick="toggleChatbot()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Messages -->
        <div class="chatbot-messages" id="chatbot-messages">
            <!-- Welcome message -->
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

        <!-- Quick Suggestions -->
        <div class="chatbot-suggestions" id="chatbot-suggestions">
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🏖️ Biển đẹp nhất VN</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🍜 Ẩm thực Hà Nội</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">💰 Du lịch tiết kiệm</button>
            <button class="suggestion-chip" onclick="sendSuggestion(this)">🏔️ Trekking Sapa</button>
        </div>

        <!-- Input Area -->
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

    let chatHistory = [];   // Lưu lịch sử hội thoại
    let isTyping    = false; // Trạng thái đang gửi
    let isOpen      = false; // Trạng thái cửa sổ

    // ── ĐỊNH NGHĨA HÀM XỬ LÝ SỰ KIỆN MỞ HOẶC ĐÓNG CỬA SỔ CHATBOT ──
    function toggleChatbot() {
        
        isOpen = !isOpen; 
        // Đảo ngược trạng thái hiện tại của chatbot (Nếu đang true/mở thì đổi thành false/đóng, và ngược lại).

        const win   = document.getElementById('chatbot-window'); 
        // Tìm và lấy ra thẻ HTML đại diện cho toàn bộ giao diện khung chat dựa vào ID là 'chatbot-window'.

        const icon  = document.getElementById('chatbot-icon'); 
        // Tìm và lấy ra thẻ icon (biểu tượng) nằm trên nút bấm chat dựa vào ID là 'chatbot-icon'.

        const badge = document.getElementById('chatbot-badge'); 
        // Tìm và lấy ra thẻ thông báo nhỏ (badge đỏ/tin nhắn mới) dựa vào ID là 'chatbot-badge'.

        if (isOpen) { 
            // BIỂU THỨC ĐIỀU KIỆN: Nếu biến isOpen bằng true (tức là người dùng muốn MỞ cửa sổ chatbot).

            win.classList.add('open'); 
            // Thêm class tên là 'open' vào thẻ khung chat để CSS kích hoạt hiệu ứng hiển thị bung khung chat lên.

            icon.className = 'fas fa-times'; 
            // Thay đổi font icon thành biểu tượng dấu "X" (dấu nhân) để người dùng bấm vào khi muốn đóng lại.

            badge.style.display = 'none'; 
            // Ẩn hoàn toàn cục thông báo đỏ đi vì người dùng đã mở khung chat lên xem rồi.

            setTimeout(() => document.getElementById('chatbot-input').focus(), 300); 
            // Hẹn giờ chờ 300 miligiây (đợi khung chat mở xong) rồi tự động đặt con trỏ chuột vào ô nhập liệu (input).

        } else { 
            // TRƯỜNG HỢP NGƯỢC LẠI: Nếu biến isOpen bằng false (tức là người dùng muốn ĐÓNG cửa sổ chatbot).

            win.classList.remove('open'); 
            // Xóa class 'open' khỏi thẻ khung chat để CSS kích hoạt hiệu ứng thu nhỏ/ẩn khung chat đi.

            icon.className = 'fas fa-robot'; 
            // Thay đổi font icon quay trở lại thành hình con robot thân thiện như trạng thái ban đầu.
        }
    }

    // Gửi tin nhắn từ suggestion chip
    function sendSuggestion(btn) {
        const text = btn.textContent.replace(/^[^\w\s]+\s*/, '').trim(); // bỏ emoji đầu
        document.getElementById('chatbot-input').value = btn.textContent.trim();
        sendMessage();
        // Ẩn suggestions sau lần đầu dùng
        document.getElementById('chatbot-suggestions').style.display = 'none';
    }

    // ── HÀM TỰ ĐỘNG THAY ĐỔI CHIỀU CAO CỦA Ô NHẬP LIỆU (TEXTAREA) THEO ĐỘ DÀI VĂN BẢN ──
    function autoResizeTextarea(el) {
        
        el.style.height = 'auto';
        // Đặt lại chiều cao của ô nhập liệu về trạng thái 'auto' (mặc định) 
        // nhằm thu nhỏ ô lại khi người dùng xóa bớt chữ, giúp tính toán lại chiều cao chính xác ở dòng sau.

        el.style.height = Math.min(el.scrollHeight, 100) + 'px';
        // el.scrollHeight: Lấy toàn bộ độ cao thực tế của vùng chứa nội dung văn bản bên trong ô.
        // Math.min(..., 100): So sánh độ cao thực tế với mốc tối đa là 100 pixel rồi lấy giá trị nhỏ hơn.
        // Ý nghĩa cả dòng: Cho phép ô tự giãn cao lên theo nội dung nhưng chỉ cao tối đa đến 100px (quá 100px sẽ xuất hiện thanh cuộn scrollbar chứ không giãn thêm nữa).
    }


    // ── HÀM XỬ LÝ SỰ KIỆN KHI NGƯỜI DÙNG NHẤN PHÍM TRÊN BÀN PHÍM (TRONG Ô CHAT) ──
    function handleChatKey(e) {
        
        if (e.key === 'Enter' && !e.shiftKey) {
            // BIỂU THỨC ĐIỀU KIỆN LỒNG NHAU: 
            // e.key === 'Enter': Kiểm tra xem phím vừa nhấn có phải là phím Enter hay không.
            // !e.shiftKey: Đảm bảo rằng người dùng ĐANG KHÔNG GIỮ phím Shift (tức là chỉ nhấn một mình phím Enter).

            e.preventDefault();
            // Ngăn chặn hành vi mặc định của phím Enter trong ô textarea (hành vi mặc định là xuống dòng mới).

            sendMessage();
            // Thực thi ngay lập tức hàm sendMessage() đã viết sẵn trong mã nguồn để gửi tin nhắn đi luôn.
        }
        // Ghi chú thêm: Nếu người dùng nhấn tổ hợp phím "Shift + Enter", điều kiện trên sẽ sai, 
        // lệnh gửi tin nhắn không chạy và ô chat sẽ thực hiện xuống dòng bình thường để họ viết tiếp đoạn văn mới.
    }

    // ── HÀM BẤT ĐỒNG BỘ (ASYNC) XỬ LÝ QUÁ TRÌNH GỬI TIN NHẮN CỦA NGƯỜI DÙNG ──
async function sendMessage() {
    
    const input = document.getElementById('chatbot-input');
    // Tìm và lấy ra thẻ HTML của ô nhập liệu dựa vào ID là 'chatbot-input'.

    const text  = input.value.trim();
    // Lấy nội dung chữ trong ô nhập, đồng thời dùng hàm .trim() để cắt bỏ toàn bộ dấu cách thừa ở đầu và cuối chuỗi.

    if (!text || isTyping) return;
    // BIỂU THỨC ĐIỀU KIỆN CHẶN LỖI: Nếu chuỗi text rỗng (người dùng không gõ gì) HOẶC chatbot đang trong trạng thái bận xử lý/đang gõ (isTyping bằng true) 
    // thì ngay lập tức thoát khỏi hàm (return) và không chạy các lệnh bên dưới nữa.

    // ── BƯỚC 1: HIỂN THỊ TIN NHẮN CỦA USER LÊN KHUNG CHAT ──
    appendMessage('user', text);
    // Gọi hàm appendMessage() để đẩy tin nhắn của người dùng (với vai trò 'user') hiển thị lên giao diện màn hình chat.

    input.value = '';
    // Xóa sạch nội dung trong ô nhập liệu sau khi tin nhắn đã được đẩy lên màn hình chat thành công.

    input.style.height = 'auto';
    // Đặt lại chiều cao ô nhập liệu về mức mặc định ban đầu (phục vụ cho tính năng auto-resize vừa reset).

    // ── BƯỚC 2: CẬP NHẬT TRẠNG THÁI GIAO DIỆN KHUNG CHAT ──
    document.getElementById('chatbot-suggestions').style.display = 'none';
    // Tìm phần tử chứa danh sách các câu hỏi gợi ý (suggestions) và ẩn nó đi (display = 'none') vì người dùng đã bắt đầu cuộc hội thoại thực tế.

    // ── BƯỚC 3: KÍCH HOẠT HIỆU ỨNG CHỜ BOT TRẢ LỜI ──
    showTyping();
    // Gọi hàm showTyping() để hiển thị hiệu ứng dấu 3 chấm nhấp nháy động, báo cho người dùng biết bot đang xử lý thông tin.

    isTyping = true;
    // Chuyển biến cờ trạng thái isTyping thành true để khóa hệ thống lại, không cho người dùng bấm gửi liên tục khi bot chưa phản hồi xong.

    document.getElementById('chatbot-send-btn').disabled = true;
    // Tìm nút bấm gửi (mũi tên/gửi tin nhắn) và tạm thời vô hiệu hóa nó (disabled = true) nhằm ngăn chặn hành vi click đúp hoặc click nhiều lần của người dùng.
}

        try {
            // 1. Gửi một yêu cầu bất đồng bộ (Async Request) bằng hàm fetch() đến URL định tuyến 'chatbot.chat' của Laravel
            const response = await fetch('{{ route("chatbot.chat") }}', {
                method: 'POST', // Sử dụng phương thức POST để bảo mật và gửi được lượng dữ liệu lớn

                // Thiết lập các thuộc tính Header bắt buộc cho Request
                headers: {
                    'Content-Type': 'application/json', // Báo cho máy chủ biết dữ liệu gửi lên là định dạng JSON
                    
                    // Lấy mã token bảo mật từ thẻ meta của trang web để chống tấn công giả mạo CSRF (Laravel bắt buộc)
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 
                    
                    'Accept': 'application/json', // Yêu cầu máy chủ Laravel phản hồi lại dữ liệu dạng JSON
                },

                // Chuyển đổi đối tượng dữ liệu (Object) thành chuỗi JSON sạch để truyền đi qua Internet
                body: JSON.stringify({
                    message: text, // Gửi nội dung câu hỏi hiện tại mà người dùng vừa gõ
                    history: chatHistory.slice(-10), // Cắt lấy tối đa 10 lượt hội thoại gần nhất gửi kèm để Bot nhớ ngữ cảnh
                }),
            });

            // 2. Chờ máy chủ xử lý xong và chuyển đổi dữ liệu phản hồi (Response) thô về thành đối tượng JSON
            const data = await response.json();
            
            // Tắt hiệu ứng "ba dấu chấm nhấp nháy" (Typing indicator) để chuẩn bị hiển thị câu trả lời
            hideTyping();

            // 3. Kiểm tra nếu máy chủ trả về trạng thái xử lý thành công (success == true)
            if (data.success) {
                // Hiển thị câu trả lời của Bot lên màn hình giao diện, kèm danh sách bài viết gợi ý (nếu có)
                appendMessage('bot', data.message, data.posts || []);
                
                // Lưu câu hỏi của người dùng vào mảng lịch sử (Bộ nhớ tạm của trình duyệt)
                chatHistory.push({ role: 'user',  content: text });
                
                // Lưu câu trả lời của Bot vào mảng lịch sử để làm ngữ cảnh cho các câu hỏi sau
                chatHistory.push({ role: 'model', content: data.message });
                
                // Nếu mảng lịch sử vượt quá 20 dòng, tiến hành cắt bớt chỉ giữ lại 20 dòng mới nhất để tránh nặng bộ nhớ
                if (chatHistory.length > 20) chatHistory = chatHistory.slice(-20);

            } else {
                // Nếu máy chủ báo thất bại, hiển thị câu lỗi của hệ thống hoặc câu mặc định xin lỗi khách
                appendMessage('bot', data.message || 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại! 🙏', []);
            }

        } catch (err) {
            // Khối lệnh này sẽ chạy nếu có lỗi thình lình xảy ra trong quá trình truyền tải (ví dụ: máy chủ sập, mất mạng)
            hideTyping(); // Ẩn hiệu ứng ba chấm nhấp nháy
            
            // Hiển thị thông báo mất kết nối mạng cho người dùng biết
            appendMessage('bot', 'Mất kết nối mạng. Vui lòng kiểm tra internet và thử lại! 📶', []);

        } finally {
            // Khối này luôn luôn được thực thi cuối cùng, bất kể luồng trên chạy THÀNH CÔNG hay THẤT BẠI
            isTyping = false; // Đặt lại trạng thái "Bot đang gõ" về false để người dùng có thể hỏi tiếp câu mới
            
            document.getElementById('chatbot-send-btn').disabled = false; // Mở khóa (Kích hoạt lại) nút Gửi tin nhắn
            
            document.getElementById('chatbot-input').focus(); // Tự động đưa con trỏ chuột nhấp nháy vào ô nhập liệu
        }

    // Thêm một tin nhắn mới (của Bot hoặc của User) vào khung chat (UI)
    function appendMessage(role, text, posts) {
        // Nếu mảng bài viết 'posts' bị rỗng hoặc không truyền vào, mặc định gán thành một mảng rỗng []
        posts = posts || [];
        
        // Lấy thẻ div chứa toàn bộ danh sách các tin nhắn hiển thị trên giao diện
        const container = document.getElementById('chatbot-messages');
        
        // Lấy thời gian hiện tại theo định dạng của Việt Nam (Ví dụ: "14:30")
        const now = new Date().toLocaleTimeString('vi-VN', { hour: '2-digit', minute: '2-digit' });

        // --- KHỞI TẠO CÁC BIẾN PHÂN BIỆT GIỮA BOT VÀ USER ---
        const isBot       = role === 'bot'; // Trả về true nếu là tin nhắn của Bot, false nếu là User
        const avatarClass = isBot ? 'bot' : 'user-av'; // Gán class CSS cho khung ảnh đại diện tương ứng
        const avatarIcon  = isBot ? 'fa-robot' : 'fa-user'; // Chọn icon FontAwesome: robot cho Bot, user cho Người dùng
        const bubbleClass = isBot ? 'bot' : 'user'; // Gán class CSS cho bong bóng chứa nội dung tin nhắn
        const rowClass    = isBot ? '' : 'user'; // Gán class CSS cho hàng tin nhắn (để căn lề trái/phải trên UI)

        // Gọi hàm định dạng chuỗi văn bản (ví dụ: chuyển các ký tự Markdown thành thẻ HTML như chữ đậm, xuống dòng)
        const formatted = formatMessage(text);

        // --- XỬ LÝ RENDER DANH SÁCH BÀI VIẾT ĐI KÈM (POST CARDS) ---
        let postsHtml = ''; // Khởi tạo chuỗi rỗng để chứa mã HTML của các bài viết gợi ý
        
        // Chỉ tạo danh sách bài viết nếu đây là tin nhắn từ Bot VÀ mảng bài viết có chứa dữ liệu
        if (isBot && posts.length > 0) {
            // Mở thẻ div bọc ngoài cùng của danh sách bài viết, sử dụng Flexbox căn dọc, cách nhau 0.5rem
            postsHtml = '<div style="margin-top:0.75rem;display:flex;flex-direction:column;gap:0.5rem;">';
            
            // Duyệt qua từng bài viết trong mảng bằng hàm forEach (post: đối tượng bài viết, idx: vị trí 0, 1, 2...)
            posts.forEach(function(post, idx) {
                // Khởi tạo đường dẫn động đến bài viết bằng helper url() của Laravel kết hợp với slug của bài viết
                const postUrl = '{{ url("/bai-viet") }}/' + post.slug;
                
                // Nếu bài viết có dữ liệu địa điểm (location), render thẻ span chứa icon bản đồ và tên địa điểm
                const location = post.location
                    ? '<span style="color:#94a3b8;font-size:0.7rem;"><i class="fas fa-map-marker-alt" style="color:#D4A373;margin-right:3px;"></i>' + escapeHtml(post.location) + '</span>'
                    : '';
                    
                // Nếu bài viết có đoạn trích ngắn (excerpt), render thẻ p mô tả và ép mã độc (XSS) bằng hàm escapeHtml
                const excerpt = post.excerpt
                    ? '<p style="color:#64748b;font-size:0.75rem;margin:0.2rem 0 0;line-height:1.4;">' + escapeHtml(post.excerpt) + '</p>'
                    : '';
                    
                // Cộng dồn mã HTML của từng thẻ bài viết (Sử dụng Template Literals dấu `` để viết chuỗi nhiều dòng)
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
                    /* Hiệu ứng rê chuột vào (Hover): Đổi nền đậm hơn một chút và đổi màu viền sang màu vàng Gold */
                    onmouseover="this.style.background='rgba(212,163,115,0.12)';this.style.borderColor='#D4A373';"
                    /* Hiệu ứng rê chuột ra (Leave): Khôi phục lại màu nền và màu viền mặc định ban đầu */
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
            postsHtml += '</div>'; // Đóng thẻ div bọc ngoài cùng của danh sách bài viết
        }
        // Khởi tạo cấu trúc chuỗi HTML để hiển thị một dòng tin nhắn hoàn chỉnh
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

        // Chèn chuỗi mã HTML vừa tạo vào vị trí cuối cùng (ngay trước thẻ đóng) bên trong khung chứa tin nhắn
        container.insertAdjacentHTML('beforeend', html);
        
        // Tự động cuộn thanh cuộn của khung chat xuống dưới cùng để tin nhắn mới luôn xuất hiện trong tầm mắt
        container.scrollTop = container.scrollHeight;
    }

    // --- 2. HÀM CHỐNG TẤN CÔNG BẢO MẬT (ANTI-XSS) ---
    // Khử độc các ký tự đặc biệt để ngăn chặn hacker chèn mã script độc hại từ server vào giao diện người dùng
    function escapeHtml(str) {
        if (!str) return ''; // Nếu chuỗi rỗng thì trả về chuỗi rỗng luôn
        return String(str)
            .replace(/&/g, '&amp;')   // Thay thế ký tự & thành thực thể an toàn
            .replace(/</g, '&lt;')    // Thay thế ký tự < thành &lt; để trình duyệt không hiểu nhầm là thẻ mở HTML
            .replace(/>/g, '&gt;')    // Thay thế ký tự > thành &gt; để trình duyệt không hiểu nhầm là thẻ đóng HTML
            .replace(/"/g, '&quot;'); // Thay thế dấu nháy kép thành thực thể an toàn
    }

    // --- 3. HÀM CHUYỂN ĐỔI ĐỊNH DẠNG VĂN BẢN (FORMAT MESSAGE) ---
    // Biên dịch các cú pháp định dạng văn bản thô (giống Markdown) thành các thẻ HTML hiển thị đẹp mắt
    function formatMessage(text) {
        return text
            .replace(/&/g, '&amp;') // Vừa vào hàm là mã hóa an toàn ký tự & trước
            .replace(/</g, '&lt;')  // Mã hóa an toàn ký tự < để chuẩn bị gán thẻ riêng
            .replace(/>/g, '&gt;')  // Mã hóa an toàn ký tự >
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>') // Chuyển đổi cú pháp **chữ** thành thẻ in đậm <strong>
            .replace(/\*(.*?)\*/g, '<em>$1</em>')             // Chuyển đổi cú pháp *chữ* thành thẻ in nghiêng <em>
            .replace(/\n/g, '<br>');                           // Thay thế ký tự xuống dòng xuống dòng thực tế bằng thẻ <br>
    }

    // --- 4. HÀM HIỂN THỊ HIỆU ỨNG BA DẤU CHẤM (SHOW TYPING INDICATOR) ---
    // Tạo cảm giác chân thật cho người dùng rằng "Bot đang suy nghĩ và gõ câu trả lời"
    function showTyping() {
        const container = document.getElementById('chatbot-messages');
        
        // Khởi tạo mã HTML của khung ba dấu chấm chuyển động nhấp nháy, gán id="typing-row" để dễ tìm và xóa sau này
        const html = `
            <div class="msg-row" id="typing-row">
                <div class="msg-avatar bot"><i class="fas fa-robot" style="font-size:0.7rem;"></i></div>
                <div class="typing-indicator">
                    <div class="typing-dot"></div> <div class="typing-dot"></div> <div class="typing-dot"></div> </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html); // Chèn hiệu ứng vào cuối khung chat
        container.scrollTop = container.scrollHeight;     // Cuộn màn hình xuống dưới cùng
    }

    // --- 5. HÀM ẨN HIỆU ỨNG BA DẤU CHẤM (HIDE TYPING INDICATOR) ---
    // Gọi hàm này ngay khi nhận được dữ liệu trả về từ Server để xóa hàng ba dấu chấm đi trước khi hiện tin nhắn thật
    function hideTyping() {
        const row = document.getElementById('typing-row'); // Tìm đúng hàng tin nhắn đang hiển thị hiệu ứng ba chấm
        if (row) row.remove(); // Nếu tồn tại thì thực hiện xóa bỏ nó khỏi cây cấu hình DOM (giao diện)
    }

    // --- 6. HÀM TỰ ĐỘNG GỢI Ý MỞ CHATBOT (SETTIMEOUT BADGE) ---
    // Sau khi người dùng vào trang web được 3 giây (3000ms), nếu họ chưa mở khung chat, hiển thị một thông báo bong bóng nhỏ gợi ý
    setTimeout(() => {
        if (!isOpen) { // Nếu trạng thái cửa sổ chat đang đóng (isOpen == false)
            document.getElementById('chatbot-badge').style.display = 'flex'; // Hiện bong bóng nhỏ nhắc nhở lên màn hình
        }
    }, 3000);

// --- KHỐI TỰ ĐỘNG BẬT CHATBOT SAU KHI ĐĂNG NHẬP THÀNH CÔNG ---
    // Khi máy chủ render file này, nó sẽ kiểm tra xem trong Flash Session có dữ liệu mang tên 'chatbot_open' hay không.
    // Nếu Controller KHÔNG gửi biến này (người dùng chỉ lướt web bình thường), toàn bộ khối code JS bên trong sẽ BỊ XÓA BỎ hoàn toàn khi trả về trình duyệt.
    // Nếu Controller CÓ gửi biến này (vừa đăng nhập thành công), đoạn JS dưới đây mới được giữ lại để trình duyệt thực thi.
    @if(session('chatbot_open'))
    
    // Đặt thời gian chờ 0.5 giây (500ms) sau khi trang web tải xong để giao diện ổn định, tránh bị lag hiệu ứng
    setTimeout(() => {
        
        // Kiểm tra xem hiện tại khung chat đang đóng hay không (biến cờ !isOpen tương đương isOpen == false)
        if (!isOpen) {
            
            // Gọi hàm toggleChatbot() để kích hoạt hiệu ứng CSS Slide-up hoặc Fade-in, mở bung cửa sổ Chatbot lên
            toggleChatbot();
            
            // Tiếp tục đặt một thời gian chờ phụ 0.6 giây (600ms) để chờ hiệu ứng mở khung chat chạy xong xuui
            setTimeout(() => {
                
                // Kỹ thuật đan xen mã PHP Blade vào biến JavaScript:
                // Hệ thống check ngầm: Nếu User đã xác thực (auth()->check() là true) thì in ra Tên của User đó.
                // Ngược lại nếu là khách vãng lai (hoặc lỗi session), trả về chuỗi rỗng "".
                // Khi chạy trên trình duyệt, dòng này sẽ biến thành một chuỗi tĩnh, ví dụ: const userName = 'Nguyễn Văn A';
                const userName = '{{ auth()->check() ? auth()->user()->name : "" }}';
                
                // Gọi hàm appendMessage để ném tin nhắn chào mừng cá nhân hóa từ Bot lên màn hình chat
                appendMessage('bot',
                    'Chào mừng **' + escapeHtml(userName) + '** đã đăng nhập! 🎉\n'
                    + 'Tôi là TravelBot, sẵn sàng tư vấn du lịch cho bạn.\n'
                    + 'Bạn muốn khám phá điểm đến nào hôm nay? ✈️',
                    [] // Mảng bài viết gợi ý 'posts' truyền vào là rỗng [] vì đây chỉ là câu chào tổng quan ban đầu
                );
            }, 600); // 600ms này đảm bảo chữ xuất hiện SAU KHI cái hộp chat đã mở ra hoàn toàn
        }
    }, 500);
    @endif
    </script>
</body>
</html>