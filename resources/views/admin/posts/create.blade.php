{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Truyền tiêu đề trang vào vị trí cấu hình thẻ <title> của layout gốc --}}
@section('title', 'Thêm bài viết')

{{-- Bắt đầu định nghĩa vùng nội dung chính để nhúng vào biệt khu yield('content') ở layout gốc --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-plus-circle me-2" style="color:var(--primary);"></i>Thêm bài viết</h2>
    {{-- Nút quay lại danh sách quản lý bài viết --}}
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i>Quay lại
    </a>
</div>

{{-- Kiểm tra nếu biểu mẫu gửi lên bị lỗi xác thực (Validation Errors) từ PostRequest --}}
@if($errors->any())
    <div class="alert alert-custom alert-error-custom mb-3">
        {{-- Duyệt qua mảng lỗi toàn cục để in từng câu thông báo tiếng Việt ra màn hình --}}
        @foreach($errors->all() as $e)
            <div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>
        @endforeach
    </div>
@endif

<div class="card-glass p-4">
    {{-- Form thêm mới bài viết:
         - method="POST": Phương thức bảo mật để gửi dữ liệu bài viết và nội dung dài.
         - action: Trỏ tới route xử lý lưu trữ bài viết (store).
         - enctype="multipart/form-data": Bắt buộc phải có để đóng gói và upload file ảnh đại diện bài viết lên server. --}}
    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">
        {{-- Mã token CSRF bảo mật bắt buộc của Laravel để phòng chống lỗi bảo mật giả mạo 419 --}}
        @csrf
        
        <div class="row g-3">
            {{-- Ô nhập tiêu đề bài viết: Dùng old('title') để giữ lại chuỗi văn bản nếu form bị roll back (trả về do lỗi) --}}
            <div class="col-md-8">
                <label class="form-label fw-bold text-secondary">Tiêu đề *</label>
                <input type="text" name="title" class="form-control form-control-dark" value="{{ old('title') }}" required>
            </div>
            
            {{-- Ô chọn Danh mục (Category):
                 - Dùng @foreach để duyệt danh sách các danh mục ($categories) được truyền từ Controller sang.
                 - Toán tử ba ngôi `old('category_id') == $c->id ? 'selected' : ''` đảm bảo giữ đúng danh mục người dùng đã chọn trước đó nếu form bị báo lỗi. --}}
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Danh mục *</label>
                <select name="category_id" class="form-control form-control-dark" required>
                    <option value="">Chọn danh mục</option>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            
            {{-- Ô nhập mô tả ngắn cho bài viết (Sử dụng thẻ textarea) --}}
            <div class="col-md-8">
                <label class="form-label fw-bold text-secondary">Mô tả ngắn</label>
                <textarea name="excerpt" class="form-control form-control-dark" rows="2">{{ old('excerpt') }}</textarea>
            </div>
            
            {{-- Ô nhập địa điểm, tọa độ du lịch gắn liền với bài viết review --}}
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Địa điểm</label>
                <input type="text" name="location" class="form-control form-control-dark" value="{{ old('location') }}">
            </div>
            
            {{-- Khung nhập nội dung chi tiết bài viết (Thường được tích hợp với trình soạn thảo như CKEditor/TinyMCE ngoài view) --}}
            <div class="col-12">
                <label class="form-label fw-bold text-secondary">Nội dung *</label>
                <textarea name="content" class="form-control form-control-dark" rows="12" required>{{ old('content') }}</textarea>
            </div>
            
            {{-- Ô chọn tải ảnh đại diện lên: Giới hạn bộ lọc tệp chỉ hiển thị ảnh qua accept="image/*" --}}
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            {{-- Khối chọn trạng thái xuất bản bài viết:
                 - published: Cho hiển thị ngay lên trang chủ ngoài client.
                 - draft: Lưu ở chế độ nháp bí mật, chỉ có quản trị viên nhìn thấy trong admin. --}}
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Trạng thái *</label>
                <select name="status" class="form-control form-control-dark">
                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Đã đăng</option>
                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Nháp</option>
                </select>
            </div>
            
            {{-- Nút bấm submit biểu mẫu để kích hoạt tiến trình kiểm tra dữ liệu và lưu bài viết --}}
            <div class="col-12">
                <button type="submit" class="btn btn-primary-custom">
                    <i class="fas fa-save me-1"></i>Lưu bài viết
                </button>
            </div>
        </div>
    </form>
</div>
@endsection {{-- Kết thúc vùng định nghĩa nội dung chính --}}