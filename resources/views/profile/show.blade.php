{{-- Kế thừa cấu trúc layout nền tảng từ file resources/views/layouts/app.blade.php --}}
@extends('layouts.app')

{{-- Thiết lập tiêu đề động cho thẻ <title> trên trình duyệt --}}
@section('title', 'Hồ sơ cá nhân')

{{-- Bắt đầu định nghĩa khối nội dung chính để nhúng vào layout master --}}
@section('content')

<style>
/* Khối bao quát thông tin tài khoản ở đầu trang: Sử dụng dải màu gradient chéo (135 độ) từ xanh Navy sang xanh Forest */
.profile-hero {
    background: linear-gradient(135deg, var(--navy) 0%, var(--forest) 100%);
    padding: 4rem 0 6rem;
    position: relative;
    overflow: hidden;
}
/* Lớp giả chèn ảnh phong cảnh từ Unsplash làm mờ (opacity 0.07) hòa trộn vào dải màu nền để tạo chiều sâu nghệ thuật */
.profile-hero::before {
    content:'';
    position:absolute; inset:0;
    background: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=1200&q=60') center/cover;
    opacity: 0.07;
}
/* Thẻ hộp màu trắng (Card) bo tròn góc để chứa các nhóm nội dung bên dưới */
.profile-card {
    background: #fff;
    border-radius: 24px;
    border: 1px solid rgba(212,163,115,0.2);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}
/* Ô nhộng thống kê số liệu (Pill Box) được chia đều không gian nhờ thuộc tính flex: 1 */
.stat-pill {
    flex: 1;
    padding: 1rem;
    text-align: center;
    border-radius: 12px;
    background: rgba(212,163,115,0.07);
    border: 1px solid rgba(212,163,115,0.15);
}
</style>

<div class="profile-hero">
    <div class="container position-relative text-center">
        <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
             style="width:100px; height:100px; border-radius:50%; object-fit:cover; border:4px solid var(--gold); box-shadow:0 8px 32px rgba(212,163,115,0.4); margin-bottom:1rem;">
        
        <h2 style="font-family:'Playfair Display',serif; font-weight:700; color:#fff; font-size:1.8rem; margin-bottom:0.25rem;">{{ $user->name }}</h2>
        <p style="color:rgba(255,255,255,0.65); font-size:0.9rem; margin-bottom:0.75rem;">{{ $user->email }}</p>
        
        <span style="display:inline-block; background:rgba(212,163,115,0.25); border:1px solid rgba(212,163,115,0.5); color:var(--gold); padding:0.3rem 1rem; border-radius:9999px; font-size:0.78rem; font-weight:600; letter-spacing:0.06em; text-transform:uppercase;">
            {{ $user->role === 'admin' ? '👑 Quản trị viên' : '✈ Thành viên' }}
        </span>
    </div>
</div>

{{-- margin-top: -3rem: Kéo ngược toàn bộ khối nội dung lên phía trên để đè lên một phần của khối Hero che đi khoảng trống --}}
<section class="py-5" style="margin-top:-3rem;">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-4" data-aos="fade-right">
                
                <div class="profile-card p-4 mb-4">
                    <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1.25rem; font-size:1rem;">
                        <i class="fas fa-chart-bar me-2" style="color:var(--gold);"></i>Thống kê
                    </h5>
                    <div class="d-flex gap-2">
                        <div class="stat-pill">
                            <div style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700; color:var(--gold); line-height:1.1;">{{ $user->posts->count() }}</div>
                            <small style="color:var(--text-muted); font-weight:500; font-size:0.78rem;">Bài viết</small>
                        </div>
                        <div class="stat-pill">
                            <div style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700; color:var(--forest); line-height:1.1;">{{ $user->comments->count() }}</div>
                            <small style="color:var(--text-muted); font-weight:500; font-size:0.78rem;">Bình luận</small>
                        </div>
                        <div class="stat-pill">
                            <div style="font-family:'Playfair Display',serif; font-size:1.8rem; font-weight:700; color:#ef4444; line-height:1.1;">{{ $user->favorites->count() }}</div>
                            <small style="color:var(--text-muted); font-weight:500; font-size:0.78rem;">Yêu thích</small>
                        </div>
                    </div>
                </div>

                <div class="profile-card p-4">
                    <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1rem; font-size:1rem;">
                        <i class="fas fa-link me-2" style="color:var(--gold);"></i>Liên kết nhanh
                    </h5>
                    <a href="{{ route('posts.favorites') }}" class="d-flex align-items-center gap-3 py-2 text-decoration-none" style="border-bottom:1px solid rgba(212,163,115,0.12); color:var(--text-secondary); transition:color 0.2s;" onmouseover="this.style.color='var(--gold-dark)'" onmouseout="this.style.color='var(--text-secondary)'">
                        <i class="fas fa-heart" style="color:var(--gold); width:20px; text-align:center;"></i>
                        <span style="font-size:0.9rem;">Bài viết yêu thích</span>
                    </a>
                    <a href="{{ route('posts.index') }}" class="d-flex align-items-center gap-3 py-2 text-decoration-none" style="color:var(--text-secondary); transition:color 0.2s;" onmouseover="this.style.color='var(--gold-dark)'" onmouseout="this.style.color='var(--text-secondary)'">
                        <i class="fas fa-compass" style="color:var(--gold); width:20px; text-align:center;"></i>
                        <span style="font-size:0.9rem;">Khám phá bài viết</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-8" data-aos="fade-left">
                <div class="profile-card p-4 p-md-5">
                    <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1.5rem; font-size:1.1rem;">
                        <i class="fas fa-edit me-2" style="color:var(--gold);"></i>Cập nhật thông tin
                    </h5>
                    
                    @if($errors->any())
                    <div class="alert alert-custom alert-error-custom mb-4">
                        @foreach($errors->all() as $e)
                            <div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
                        @endforeach
                    </div>
                    @endif
                    
                    <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
                        @csrf {{-- Khởi tạo token chống tấn công giả mạo yêu cầu chéo CSRF --}}
                        @method('PUT') {{-- Khai báo ghi đè phương thức HTTP của Form thành PUT theo chuẩn RESTful cho hành động cập nhật --}}
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-custom">Họ tên</label>
                                {{-- old('name', $user->name): Nếu form lỗi và tải lại, giữ lại giá trị vừa gõ mới (old), nếu không có thì lấy mặc định từ database --}}
                                <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $user->name) }}" required>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Email</label>
                                <input type="email" name="email" class="form-control form-control-dark" value="{{ old('email', $user->email) }}" required>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-custom">Avatar</label>
                                <input type="file" name="avatar" class="form-control form-control-dark" accept="image/*">
                                <small style="color:var(--text-muted); font-size:0.8rem; margin-top:0.3rem; display:block;">Để trống nếu không muốn thay đổi ảnh đại diện</small>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Mật khẩu mới <span style="color:var(--text-muted); font-weight:400;">(để trống nếu không đổi)</span></label>
                                <input type="password" name="password" class="form-control form-control-dark">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-custom">Xác nhận mật khẩu</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-dark">
                            </div>
                            
                            <div class="col-12 mt-2">
                                <button type="submit" class="btn btn-primary-custom" style="border-radius:var(--radius-full); padding:0.65rem 2.5rem; font-size:0.95rem;">
                                    <i class="fas fa-save me-2"></i>Lưu thay đổi
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection