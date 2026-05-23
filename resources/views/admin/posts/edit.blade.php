{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Đẩy tiêu đề trang vào vị trí cấu hình thẻ <title> của layout gốc --}}
@section('title', 'Sửa bài viết')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-edit me-2" style="color:var(--primary);"></i>Sửa bài viết</h2>
    {{-- Nút điều hướng quay lại danh sách quản lý bài viết --}}
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
</div>

{{-- Hiển thị danh sách các lỗi nếu dữ liệu sửa đổi vi phạm các quy tắc kiểm tra (Validation) từ phía Server --}}
@if($errors->any())
    <div class="alert alert-custom alert-error-custom mb-3">
        @foreach($errors->all() as $e)
            <div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="card-glass p-4">
    {{-- Form cập nhật dữ liệu bài viết:
         - action: Gọi đường dẫn update kèm theo đối tượng $post (Laravel tự hiểu là lấy $post->id) để biết cần sửa bài nào.
         - enctype="multipart/form-data": Bắt buộc phải có để hệ thống tiếp nhận file ảnh đại diện mới nếu người dùng thay đổi. --}}
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">
        {{-- Token bảo mật bắt buộc của Laravel để chống tấn công giả mạo CSRF --}}
        @csrf 
        
        {{-- Phương thức giả lập HTTP: Do form HTML chỉ hỗ trợ GET/POST, chỉ thị này ép Laravel xử lý theo chuẩn phương thức PUT (Cập nhật) --}}
        @method('PUT')
        
        <div class="row g-3">
            {{-- Ô nhập tiêu đề bài viết: old('title', $post->title) đổ dữ liệu cũ của bài viết từ DB lên; nếu sửa bị lỗi validation thì giữ lại chữ vừa gõ hụt --}}
            <div class="col-md-8">
                <label class="form-label fw-bold text-secondary">Tiêu đề *</label>
                <input type="text" name="title" class="form-control form-control-dark" value="{{ old('title', $post->title) }}" required>
            </div>
            
            {{-- Ô chọn Danh mục bài viết:
                 - Duyệt qua toàn bộ danh sách danh mục ($categories) từ Controller truyền sang.
                 - So khớp: Nếu danh mục nào trùng với danh mục hiện tại của bài viết (hoặc danh mục vừa chọn lại bị lỗi), gán thuộc tính 'selected' để chọn sẵn --}}
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Danh mục *</label>
                <select name="category_id" class="form-control form-control-dark" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $post->category_id) == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Ô nhập mô tả ngắn cho bài viết --}}
            <div class="col-md-8">
                <label class="form-label fw-bold text-secondary">Mô tả ngắn</label>
                <textarea name="excerpt" class="form-control form-control-dark" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea>
            </div>
            
            {{-- Ô nhập thông tin địa điểm của bài viết --}}
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Địa điểm</label>
                <input type="text" name="location" class="form-control form-control-dark" value="{{ old('location', $post->location) }}">
            </div>
            
            {{-- Ô nhập nội dung chi tiết bài viết --}}
            <div class="col-12">
                <label class="form-label fw-bold text-secondary">Nội dung *</label>
                <textarea name="content" class="form-control form-control-dark" rows="12" required>{{ old('content', $post->content) }}</textarea>
            </div>
            
            {{-- Khối quản lý hình ảnh: --}}
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                {{-- Kiểm tra nếu bài viết đang có ảnh, gọi thuộc tính ảo image_url (Accessor) từ Model Post để hiển thị ảnh thumbnail xem trước --}}
                @if($post->image)
                    <div class="mb-2">
                        <img src="{{ $post->image_url }}" alt="" style="height:80px;border-radius:8px;">
                    </div>
                @endif
                {{-- Ô chọn file ảnh mới (nếu để trống, Controller sẽ tự hiểu là giữ nguyên ảnh cũ không thay đổi) --}}
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            {{-- Khối chọn trạng thái xuất bản bài viết --}}
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Trạng thái *</label>
                <select name="status" class="form-control form-control-dark">
                    <option value="published" {{ old('status', $post->status) == 'published' ? 'selected' : '' }}>Đã đăng</option>
                    <option value="draft" {{ old('status', $post->status) == 'draft' ? 'selected' : '' }}>Nháp</option>
                </select>
            </div>
            
            {{-- Nút bấm thực thi gửi form để tiến hành cập nhật bài viết --}}
            <div class="col-12">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-save me-1"></i>Cập nhật
                </button>
            </div>
        </div>
    </form>
</div>
@endsection {{-- Kết thúc định nghĩa nội dung chính --}}