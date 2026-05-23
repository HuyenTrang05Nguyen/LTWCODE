{{-- Kế thừa cấu trúc giao diện layout hệ thống chung bên ngoài Client (layouts.app) --}}
@extends('layouts.app')

{{-- Đặt tiêu đề cho tab trình duyệt --}}
@section('title', 'Đăng nhập')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')

<style>
/* CSS Tùy biến cục bộ dành riêng cho trang Đăng nhập */
.auth-page {
    /* Đảm bảo chiều cao trang bao phủ toàn màn hình trừ đi chiều cao của thanh Navbar (76px) */
    min-height: calc(100vh - 76px);
    display: flex;
    align-items: stretch;
}
.auth-left {
    /* Gọi ảnh nền thiên nhiên/du lịch từ Unsplash làm hình nền quảng bá bên trái */
    background: url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=80') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: flex-end;
    padding: 3rem;
}
.auth-left::before {
    /* Lớp phủ mờ màu tối kết hợp hiệu ứng dải màu (Gradient) giúp phần chữ màu trắng hiển thị rõ nét, không bị lóa */
    content:'';
    position:absolute;inset:0;
    background: linear-gradient(135deg, rgba(15,23,42,0.65) 0%, rgba(26,58,42,0.55) 100%);
}
.auth-right {
    background: var(--cream);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3rem 2rem;
}
.auth-card {
    background: #fff;
    border-radius: 24px;
    padding: 2.5rem;
    /* Hiệu ứng bóng đổ (Box Shadow) giúp khung form đăng nhập nổi bật trên nền kem */
    box-shadow: 0 20px 60px rgba(15,23,42,0.1);
    border: 1px solid rgba(212,163,115,0.18);
    width: 100%;
    max-width: 420px;
}
</style>

<div class="auth-page">
    <div class="auth-left col-lg-6 d-none d-lg-flex">
        <div class="position-relative">
            <span style="display:inline-block;background:rgba(212,163,115,0.25);border:1px solid rgba(212,163,115,0.5);color:var(--gold);padding:0.3rem 1rem;border-radius:9999px;font-size:0.78rem;font-weight:600;letter-spacing:0.08em;text-transform:uppercase;margin-bottom:1rem;">
                ✈ TravelGuide
            </span>
            <h2 style="font-family:'Playfair Display',serif;font-size:2.2rem;font-weight:700;color:#fff;line-height:1.25;margin-bottom:1rem;">
                Khám phá thế giới<br><span style="color:var(--gold);">cùng chúng tôi</span>
            </h2>
            <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;max-width:380px;">
                Hàng nghìn bài viết du lịch, kinh nghiệm thực tế và cảm hứng cho chuyến đi tiếp theo của bạn.
            </p>
        </div>
    </div>

    <div class="auth-right col-lg-6 col-12">
        {{-- Thêm hiệu ứng hoạt họa trượt lên nhẹ nhàng bằng thuộc tính data-aos --}}
        <div class="auth-card" data-aos="fade-up">
            <div class="text-center mb-4">
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#D4A373,#b8864e);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 8px 24px rgba(212,163,115,0.35);">
                    <i class="fas fa-paper-plane fa-lg text-white"></i>
                </div>
                <h3 style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.7rem;color:var(--navy);margin-bottom:0.25rem;">Đăng nhập</h3>
                <p style="color:var(--text-secondary);font-size:0.9rem;">Chào mừng bạn trở lại!</p>
            </div>

            {{-- HIỂN THỊ THÔNG BÁO LỖI:
                 In ra màn hình nếu người dùng nhập sai tài khoản, sai mật khẩu, hoặc không đúng định dạng email --}}
            @if($errors->any())
            <div class="alert alert-custom alert-error-custom mb-4">
                @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- Form Đăng nhập: Gửi request phương thức POST tới route xử lý xác thực đăng nhập mặc định --}}
            <form method="POST" action="{{ route('login') }}">
                {{-- Token bắt buộc bảo vệ ứng dụng khỏi lỗ hổng bảo mật tấn công giả mạo (Cross-Site Request Forgery) --}}
                @csrf
                
                {{-- Ô nhập địa chỉ Email --}}
                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    {{-- - value="{{ old('email') }}": Giữ lại email cũ đã nhập nếu chẳng may gõ sai mật khẩu ở dưới.
                         - autofocus: Tự động nhảy con trỏ chuột vào ô Email ngay khi tải trang xong. --}}
                    <input type="email" name="email" class="form-control form-control-dark" placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                </div>
                
                {{-- Ô nhập Mật khẩu bảo mật --}}
                <div class="mb-3">
                    <label class="form-label-custom">Mật khẩu</label>
                    <input type="password" name="password" class="form-control form-control-dark" placeholder="••••••••" required>
                </div>
                
                <div class="mb-4 d-flex justify-content-between align-items-center">
                    {{-- Tính năng Ghi nhớ đăng nhập: 
                         Nếu được tích chọn, Laravel sẽ lưu một cookie mang chuỗi mã hóa đặc biệt (remember_token) trên máy người dùng, 
                         giúp duy trì phiên đăng nhập kể cả khi họ tắt hẳn trình duyệt và mở lại sau nhiều ngày --}}
                    <div class="form-check">
                        <input type="checkbox" name="remember" class="form-check-input" id="remember" style="border-color:var(--beige);">
                        <label class="form-check-label" style="color:var(--text-secondary);font-size:0.875rem;" for="remember">Ghi nhớ đăng nhập</label>
                    </div>
                    {{-- Đường dẫn liên kết nhảy sang trang Yêu cầu cấp lại mật khẩu mới --}}
                    <a href="{{ route('password.request') }}" style="color:var(--gold-dark);font-weight:500;font-size:0.875rem;text-decoration:none;">Quên mật khẩu?</a>
                </div>
                
                {{-- Nút bấm thực thi lệnh gửi form đăng nhập --}}
                <button type="submit" class="btn btn-primary-custom w-100 mb-4" style="padding:0.8rem;font-size:1rem;font-weight:600;border-radius:var(--radius-full);">
                    <i class="fas fa-sign-in-alt me-2"></i> Đăng nhập
                </button>
            </form>
            
            {{-- Đường dẫn hỗ trợ thành viên mới chưa có tài khoản di chuyển sang trang Đăng ký --}}
            <p class="text-center mb-0" style="color:var(--text-secondary);font-size:0.9rem;">
                Chưa có tài khoản? <a href="{{ route('register') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;">Đăng ký ngay</a>
            </p>
        </div>
    </div>
</div>

@endsection