{{-- Kế thừa cấu trúc giao diện layout hệ thống chung bên ngoài Client (layouts.app) --}}
@extends('layouts.app')

{{-- Đặt tiêu đề cho tab trình duyệt --}}
@section('title', 'Đăng ký')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')

<style>
/* CSS Tùy biến cục bộ dành riêng cho trang Đăng ký */
.auth-page {
    /* Đảm bảo chiều cao trang bao phủ toàn màn hình trừ đi chiều cao của thanh Navbar (76px) */
    min-height: calc(100vh - 76px);
    display: flex;
    align-items: stretch;
}
.auth-left {
    /* Gọi ảnh nền thiên nhiên/du lịch từ Unsplash làm hình nền quảng bá bên trái */
    background: url('https://images.unsplash.com/photo-1528127269322-539801943592?w=1200&q=80') center/cover no-repeat;
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
    /* Hiệu ứng bóng đổ (Box Shadow) giúp khung form đăng ký nổi bật trên nền kem */
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
                Tham gia cộng đồng<br><span style="color:var(--gold);">du lịch Việt Nam</span>
            </h2>
            <p style="color:rgba(255,255,255,0.75);font-size:0.95rem;line-height:1.7;max-width:380px;">
                Chia sẻ kinh nghiệm, kết nối với những người yêu du lịch và khám phá những điểm đến tuyệt vời.
            </p>
        </div>
    </div>

    <div class="auth-right col-lg-6 col-12">
        {{-- Thêm hiệu ứng hoạt họa trượt lên nhẹ nhàng bằng thuộc tính data-aos --}}
        <div class="auth-card" data-aos="fade-up">
            <div class="text-center mb-4">
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#D4A373,#b8864e);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 8px 24px rgba(212,163,115,0.35);">
                    <i class="fas fa-user-plus fa-lg text-white"></i>
                </div>
                <h3 style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.7rem;color:var(--navy);margin-bottom:0.25rem;">Đăng ký tài khoản</h3>
                <p style="color:var(--text-secondary);font-size:0.9rem;">Tham gia cộng đồng du lịch!</p>
            </div>

            {{-- HIỂN THỊ THÔNG BÁO LỖI VALIDATION:
                 In ra lỗi nếu vi phạm ràng buộc (Ví dụ: Trùng Email, Mật khẩu ngắn hơn 8 ký tự, hoặc Nhập lại mật khẩu không khớp) --}}
            @if($errors->any())
            <div class="alert alert-custom alert-error-custom mb-4">
                @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
            @endif

            {{-- Form Đăng ký: Gửi request phương thức POST tới hành động xử lý đăng ký thành viên của hệ thống --}}
            <form method="POST" action="{{ route('register') }}">
                {{-- Mã hóa Token bảo mật CSRF bắt buộc để bảo vệ form chống lại các cuộc tấn công giả mạo --}}
                @csrf
                
                {{-- Trường nhập Họ và tên --}}
                <div class="mb-3">
                    <label class="form-label-custom">Họ và tên</label>
                    {{-- value="{{ old('name') }}": Giữ lại tên người dùng đã gõ nếu form bị lỗi validation ở các ô bên dưới --}}
                    <input type="text" name="name" class="form-control form-control-dark" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                </div>
                
                {{-- Trường nhập địa chỉ Email --}}
                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <input type="email" name="email" class="form-control form-control-dark" placeholder="your@email.com" value="{{ old('email') }}" required>
                </div>
                
                {{-- Trường nhập Mật khẩu bảo mật --}}
                <div class="mb-3">
                    <label class="form-label-custom">Mật khẩu</label>
                    <input type="password" name="password" class="form-control form-control-dark" placeholder="Ít nhất 8 ký tự" required>
                </div>
                
                {{-- Trường nhập lại mật khẩu để kiểm tra:
                     - QUY TẮC RÀNG BUỘC: Name bắt buộc phải đặt tên là 'password_confirmation' để kích hoạt tính năng 
                       xác thực tự động 'confirmed' trong luật Validation của Laravel --}}
                <div class="mb-4">
                    <label class="form-label-custom">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-dark" placeholder="Nhập lại mật khẩu" required>
                </div>
                
                {{-- Nút bấm thực thi gửi form đăng ký --}}
                <button type="submit" class="btn btn-primary-custom w-100 mb-4" style="padding:0.8rem;font-size:1rem;font-weight:600;border-radius:var(--radius-full);">
                    <i class="fas fa-user-plus me-2"></i> Đăng ký
                </button>
            </form>
            
            {{-- Đường dẫn hỗ trợ thành viên đã có tài khoản quay lại trang Đăng nhập --}}
            <p class="text-center mb-0" style="color:var(--text-secondary);font-size:0.9rem;">
                Đã có tài khoản? <a href="{{ route('login') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

@endsection