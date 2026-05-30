{{-- Layout ứng dụng --}}
@extends('layouts.app')

{{-- Tiêu đề trang --}}
@section('title', 'Quên mật khẩu')

{{-- Nội dung chính --}}
@section('content')
<section class="py-5" style="min-height: 80vh; display: flex; align-items: center;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                {{-- Thẻ giao diện hiệu ứng kính mờ --}}
                <div class="card-glass p-4 p-md-5 animate-fade-in-up">
                    
                    {{-- Tiêu đề --}}
                    <div class="text-center mb-4">
                        <div style="width:70px;height:70px;border-radius:20px;background:var(--gradient-primary);display:inline-flex;align-items:center;justify-content:center;margin-bottom:1rem;">
                            <i class="fas fa-key fa-2x text-white"></i>
                        </div>
                        <h3 class="fw-bold">Quên mật khẩu</h3>
                        <p class="text-secondary">Nhập email để nhận link đặt lại mật khẩu</p>
                    </div>

                    {{-- Thông báo thành công --}}
                    @if(session('success'))
                    <div class="alert alert-custom alert-success-custom mb-3">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    </div>
                    @endif

                    {{-- Thông báo lỗi xác thực --}}
                    @if($errors->any())
                    <div class="alert alert-custom alert-error-custom mb-3">
                        @foreach($errors->all() as $error)
                        <div><i class="fas fa-exclamation-circle me-1"></i>{{ $error }}</div>
                        @endforeach
                    </div>
                    @endif

                    {{-- Form gửi yêu cầu --}}
                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf
                        
                        {{-- Email --}}
                        <div class="mb-4">
                            <label class="form-label-custom">Email đăng ký</label>
                            <input type="email" name="email" class="form-control form-control-dark"
                                   placeholder="your@email.com" value="{{ old('email') }}" required autofocus>
                        </div>
                        
                        {{-- Nút gửi --}}
                        <button type="submit" class="btn btn-primary-custom w-100 py-2 mb-3">
                            <i class="fas fa-paper-plane me-2"></i>Gửi link đặt lại mật khẩu
                        </button>
                    </form>
                    
                    {{-- Quay lại đăng nhập --}}
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