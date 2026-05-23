{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Đẩy tiêu đề trang vào vị trí yield('title') ở layout gốc --}}
@section('title', 'Sửa danh mục')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-edit me-2" style="color:var(--primary);"></i>Sửa danh mục</h2>
    {{-- Nút điều hướng quay lại danh sách quản lý danh mục --}}
    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
</div>

{{-- Hiển thị danh sách các lỗi nếu dữ liệu sửa đổi vi phạm các quy tắc của Form Request --}}
@if($errors->any())
    <div class="alert alert-custom alert-error-custom mb-3">
        @foreach($errors->all() as $e)
            <div>{{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="card-glass p-4">
    {{-- Form cập nhật dữ liệu:
         - action: Gọi đường dẫn update kèm theo biến $category (hoặc $category->id) để hệ thống biết đang sửa bản ghi nào.
         - enctype="multipart/form-data": Bắt buộc phải có để hệ thống tiếp nhận file ảnh mới nếu người dùng thay đổi. --}}
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        {{-- Token bảo mật bắt buộc của Laravel để chống tấn công giả mạo CSRF --}}
        @csrf 
        
        {{-- Phương thức giả lập HTTP: Form HTML mặc định chỉ hỗ trợ GET và POST. 
             Chỉ thị @method('PUT') sẽ sinh một input ẩn báo cho Laravel biết cần xử lý Form này theo chuẩn RESTful API bằng hàm update() trong Controller --}}
        @method('PUT')
        
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Tên danh mục *</label>
                {{-- old('name', $category->name): Ưu tiên hiển thị dữ liệu vừa gõ lỗi (nếu có); nếu không có lỗi, mặc định đổ dữ liệu cũ của danh mục từ DB lên ô input --}}
                <input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $category->name) }}" required>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                {{-- Kiểm tra nếu danh mục hiện tại đã có ảnh trong Database, hiển thị một khung ảnh nhỏ (thumbnail) để người quản trị xem trước --}}
                @if($category->image)
                    <div class="mb-2">
                        {{-- Gọi thuộc tính ảo image_url (Accessor) đã được xử lý chuẩn hóa đường dẫn ở Model Category --}}
                        <img src="{{ $category->image_url }}" alt="" style="height:60px;border-radius:8px;">
                    </div>
                @endif
                {{-- Ô chọn file ảnh mới (nếu người dùng không chọn file mới, hệ thống sẽ giữ nguyên ảnh cũ) --}}
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            <div class="col-12">
                <label class="form-label fw-bold text-secondary">Mô tả</label>
                <textarea name="description" class="form-control form-control-dark" rows="3">{{ old('description', $category->description) }}</textarea>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-save me-1"></i>Cập nhật
                </button>
            </div>
        </div>
    </form>
</div>
@endsection {{-- Kết thúc định nghĩa nội dung chính --}}