{{-- Kế thừa layout giao diện chung --}}
@extends('layouts.app')

{{-- Tiêu đề trang --}}
@section('title', 'Đăng ký')

{{-- Khối nội dung chính --}}
@section('content')

<style>
.auth-page {
    min-height: calc(100vh - 76px);
    display: flex;
    align-items: stretch;
}
.auth-left {
    background: url('https://images.unsplash.com/photo-1528127269322-539801943592?w=1200&q=80') center/cover no-repeat;
    position: relative;
    display: flex;
    align-items: flex-end;
    padding: 3rem;
}
.auth-left::before {
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
    box-shadow: 0 20px 60px rgba(15,23,42,0.1);
    border: 1px solid rgba(212,163,115,0.18);
    width: 100%;
    max-width: 420px;
}
</style>

<div class="auth-page">
    {{-- Cột trái: Ảnh nền tạo cảm hứng --}}
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

    {{-- Cột phải: Form đăng ký --}}
    <div class="auth-right col-lg-6 col-12">
        <div class="auth-card" data-aos="fade-up">
            <div class="text-center mb-4">
                <div style="width:64px;height:64px;border-radius:16px;background:linear-gradient(135deg,#D4A373,#b8864e);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;box-shadow:0 8px 24px rgba(212,163,115,0.35);">
                    <i class="fas fa-user-plus fa-lg text-white"></i>
                </div>
                <h3 style="font-family:'Playfair Display',serif;font-weight:700;font-size:1.7rem;color:var(--navy);margin-bottom:0.25rem;">Đăng ký tài khoản</h3>
                <p style="color:var(--text-secondary);font-size:0.9rem;">Tham gia cộng đồng du lịch!</p>
            </div>

            @if($errors->any())
            <div class="alert alert-custom alert-error-custom mb-4">
                @foreach($errors->all() as $error)
                <div><i class="fas fa-exclamation-circle me-1"></i> {{ $error }}</div>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf 
                <div class="mb-3">
                    <label class="form-label-custom">Họ và tên</label>
                    <input type="text" name="name" class="form-control form-control-dark" placeholder="Nguyễn Văn A" value="{{ old('name') }}" required autofocus>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Email</label>
                    <input type="email" name="email" class="form-control form-control-dark" placeholder="your@email.com" value="{{ old('email') }}" required>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-custom">Mật khẩu</label>
                    <input type="password" name="password" class="form-control form-control-dark" placeholder="Ít nhất 8 ký tự" required>
                </div>
                
                <div class="mb-4">
                    <label class="form-label-custom">Xác nhận mật khẩu</label>
                    <input type="password" name="password_confirmation" class="form-control form-control-dark" placeholder="Nhập lại mật khẩu" required>
                </div>
                
                <button type="submit" class="btn btn-primary-custom w-100 mb-4" style="padding:0.8rem;font-size:1rem;font-weight:600;border-radius:var(--radius-full);">
                    <i class="fas fa-user-plus me-2"></i> Đăng ký
                </button>
            </form>
            
            <p class="text-center mb-0" style="color:var(--text-secondary);font-size:0.9rem;">
                Đã có tài khoản? <a href="{{ route('login') }}" style="color:var(--gold-dark);font-weight:600;text-decoration:none;">Đăng nhập</a>
            </p>
        </div>
    </div>
</div>

@endsection