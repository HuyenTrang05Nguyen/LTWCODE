{{-- Kế thừa cấu trúc giao diện layout chung của trang Client/App thay vì trang Admin --}}
@extends('layouts.app')

{{-- Cấu hình tiêu đề trang hiển thị trên thanh tab trình duyệt --}}
@section('title', 'Quên mật khẩu')

{{-- Bắt đầu định nghĩa vùng nội dung chính để nhúng vào layout gốc --}}
@section('content')
<section class="py-5" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card-glass p-4 p-md-5 animate-fade-in-up">
                    <div class="text-center mb-4">
                        <div style="width:70px;height:70px;border-radius:20px;background:var(--gradient-primary);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                            <i class="fas fa-key fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold">Quên mật khẩu</h3>
                        <p class="text-secondary">Nhập email để nhận link đặt lại mật khẩu</p>
                    </div>

                    {{-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG (Session Flash):
                         Nếu hệ thống gửi email thành công, biến session('success') hoặc session('status') sẽ tồn tại 
                         và in ra thông báo màu xanh (Ví dụ: "Chúng tôi đã gửi link đặt lại mật khẩu vào email của bạn") --}}
                    @if(session('success'))
                    <div class="alert alert-custom alert-success-custom mb-3">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    @endif

                    {{-- HIỂN THỊ THÔNG BÁO LỖI (Validation Errors):
                         Xảy ra khi Email không tồn tại trong hệ thống hoặc định dạng nhập vào không đúng chuẩn Email --}}
                    @if($errors->any())
                    <div class="alert alert-custom alert-error-custom mb-3">
                        @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Form gửi yêu cầu quên mật khẩu:
                         - method="POST": Phương thức bảo mật bắt buộc khi làm form gửi dữ liệu lên server.
                         - action: Gọi đến route mặc định của Laravel (hoặc tùy biến) chuyên trách xử lý gửi email xác nhận. --}}
                    <form method="POST" action="{{ route('password.email') }}">
                        {{-- Token phòng chống tấn công giả mạo CSRF bắt buộc để Laravel chấp nhận request --}}
                        @csrf
                        
                        <div class="mb-4">
                            <label class="form-label-custom">Email đăng ký</label>
                            {{-- - value="{{ old('email') }}": Giữ lại email người dùng vừa gõ nếu hệ thống báo lỗi.
                                 - autofocus: Tự động đưa con trỏ chuột nhấp nháy vào ô này ngay khi vừa tải xong trang. --}}
                            <input type="email" name="email" class="form-control form-control-dark"
                                   placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        
                        <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại mật khẩu
                        </button>
                    </form>
                    
                    {{-- Nút điều hướng quay về trang Đăng nhập nếu người dùng chợt nhớ ra mật khẩu --}}
                    <p class="text-center text-secondary mb-0">
                        <a href="{{ route('login') }}" style="color:var(--primary);">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection