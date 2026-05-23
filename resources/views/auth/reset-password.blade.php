{{-- Kế thừa cấu trúc giao diện layout hệ thống chung bên ngoài Client (layouts.app) --}}
@extends('layouts.app')

{{-- Đặt tiêu đề cho tab trình duyệt --}}
@section('title', 'Quên mật khẩu')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')

{{-- Khối bao ngoài biểu diễn giao diện toàn màn hình:
     - min-height: calc(100vh - 76px): Đảm bảo chiều cao tối thiểu luôn chiếm trọn phần màn hình còn lại sau khi trừ đi thanh Navbar (76px), giúp chân trang không bị co rúm.
     - display: flex; align-items: center;: Sử dụng mô hình Flexbox để căn chỉnh khung form luôn lọt vào chính giữa màn hình theo chiều dọc.
     - background: Hiệu ứng chuyển dải màu mượt mà (Gradient) từ màu kem sang màu be tạo cảm giác dễ chịu. --}}
<section style="min-height:calc(100vh - 76px);display:flex;align-items:center;background:linear-gradient(135deg,var(--cream) 0%,var(--beige) 100%);">
    <div class="container">
        <div class="row justify-content-center">
            {{-- Giới hạn chiều rộng tối đa của khung đăng nhập (440px) để đảm bảo tính cân đối thẩm mỹ --}}
            <div class="col-md-5" style="max-width:440px;">
                {{-- Thêm hiệu ứng hoạt họa trượt lên nhẹ nhàng khi tải trang bằng thuộc tính data-aos="fade-up" --}}
                <div data-aos="fade-up" style="background:#fff;border-radius:24px;padding:2.5rem;box-shadow:0 20px 60px rgba(15,23,42,0.1);border:1px solid rgba(212,163,115,0.18);">
                    
                    {{-- Khối tiêu đề trang --}}
                    <div class="text-center mb-4">
                        {{-- Tạo vòng tròn bao bọc biểu tượng chiếc chìa khóa bằng dải màu Gradient sang trọng --}}
                        <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#D4A373,#b8864e);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 8px 24px rgba(212,163,115,0.35);">
                            <i class="fas fa-key fa-lg text-white"></i>
                        </div>
                        <h3 style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.7rem;color:var(--navy);margin-bottom:0.25rem;">Quên mật khẩu</h3>
                        <p style="color:var(--text-secondary);font-size:0.9rem;">Nhập email để nhận link đặt lại mật khẩu</p>
                    </div>

                    {{-- HIỂN THỊ THÔNG BÁO THÀNH CÔNG (Session Flash Data):
                         Khi Backend Controller xử lý gửi email thành công, biến session('success') này sẽ sinh ra 
                         để thông báo cho người dùng check hộp thư. Trạng thái này sẽ tự biến mất nếu nhấn F5 tải lại trang. --}}
                    @if(session('success'))
                    <div class="alert alert-custom alert-success-custom mb-4">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    @endif

                    {{-- HIỂN THỊ LỖI XÁC THỰC (Validation Errors):
                         Bật lên khi email nhập vào bị sai định dạng hoặc không tìm thấy tài khoản nào khớp với email này trong DB. --}}
                    @if($errors->any())
                    <div class="alert alert-custom alert-error-custom mb-4">
                        @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Form Yêu cầu cấp lại mật khẩu:
                         - method="POST": Phương thức gửi dữ liệu bảo mật lên hệ thống.
                         - action: Điều hướng request tới route mặc định chịu trách nhiệm quản lý logic quên mật khẩu của Laravel. --}}
                    <form method="POST" action="{{ route('password.email') }}">
                        {{-- Token bắt buộc phòng chống lỗ hổng bảo mật tấn công chéo người dùng (CSRF Protection) --}}
                        @csrf
                        
                        {{-- Ô nhập dữ liệu Email xác nhận --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Email đăng ký</label>
                            {{-- - value="{{ old('email') }}": Giữ lại ký tự email vừa nhập nếu form gặp lỗi validation trả về.
                                 - autofocus: Tự động nháy con trỏ chuột vào ô này ngay khi vừa mở trang. --}}
                            <input type="email" name="email" class="form-control form-control-dark"
                                   placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        
                        {{-- Nút bấm submit form --}}
                        <button type="submit" class="btn btn-primary-custom w-100 mb-4" style="padding:0.8rem;font-size:1rem;font-weight:600;border-radius:var(--radius-full);">
                            <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại mật khẩu
                        </button>
                    </form>
                    
                    {{-- Liên kết điều hướng quay ngược lại màn hình Đăng nhập --}}
                    <p class="text-center mb-0" style="color:var(--text-secondary);font-size:0.9rem;">
                        <a href="{{ route('login') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;">
                            <i class="fas fa-arrow-left me-1"></i>Quay lại đăng nhập
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection