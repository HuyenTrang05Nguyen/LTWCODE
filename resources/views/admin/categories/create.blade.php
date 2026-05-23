{{-- Kế thừa file giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Truyền tiêu đề trang vào vị trí cấu hình thẻ <title> của layout gốc --}}
@section('title', 'Thêm danh mục')

{{-- Bắt đầu định nghĩa vùng nội dung chính để nhúng vào layout gốc --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-plus-circle me-2" style="color:var(--primary);"></i>Thêm danh mục</h2>
    {{-- Thẻ điều hướng quay lại danh sách danh mục bằng cách gọi tên Route hệ thống --}}
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
</div>

{{-- Kiểm tra nếu có bất kỳ lỗi xác thực dữ liệu (Validation Errors) nào được đẩy từ Form Request về --}}
@if($errors->any())
    <div class="alert alert-custom alert-error-custom mb-3">
        {{-- Vòng lặp duyệt qua tất cả các câu thông báo lỗi để hiển thị ra màn hình --}}
        @foreach($errors->all() as $e)
            <div>{{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="card-glass p-4">
    {{-- Form gửi dữ liệu lên Server:
         - method="POST": Gửi dữ liệu ẩn bảo mật.
         - action: Trỏ tới hàm lưu dữ liệu (store).
         - enctype="multipart/form-data": Bắt buộc phải có thuộc tính này thì mới upload được file ảnh lên server. --}}
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        {{-- Token bảo mật CSRF: Bắt buộc phải có để Laravel chặn đứng các cuộc tấn công giả mạo yêu cầu từ trang web khác --}}
        @csrf
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Tên danh mục *</label>
                {{-- value="{{ old('name') }}": Hàm old() giữ lại chữ người dùng đã nhập nếu form bị lỗi validation, giúp họ không phải gõ lại --}}
                <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name') }}" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                {{-- accept="image/*": Giới hạn bộ lọc ở cửa sổ chọn file, chỉ cho phép chọn các định dạng tệp là hình ảnh --}}
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            <div class="col-12">
                <label class="form-label fw-bold text-secondary">Mô tả</label>
                <textarea name="description" class="form-control form-control-dark" rows="3">{{ old('description') }}</textarea>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-save me-1"></i>Lưu
                </button>
            </div>
        </div>
    </form>
</div>
@endsection {{-- Kết thúc vùng định nghĩa nội dung chính --}}