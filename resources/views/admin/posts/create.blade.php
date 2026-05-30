@extends('layouts.admin')
@section('title', 'Thêm bài viết')
@section('content')
<div class="admin-header"><h2><i class="fas fa-plus-circle me-2" style="color:var(--primary);"></i>Thêm bài viết</h2><a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a></div>

{{-- Hiển thị thông báo lỗi chi tiết nếu validation thất bại --}}
@if($errors->any())<div class="alert alert-custom alert-error-custom mb-3">@foreach($errors->all() as $e)<div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach</div>@endif

<div class="card-glass p-4">
    {{-- Form có upload file nên bắt buộc dùng enctype --}}
    <form method="POST" action="{{ route('admin.posts.store') }}" enctype="multipart/form-data">@csrf
        <div class="row g-3">
            <div class="col-md-8"><label class="form-label fw-bold text-secondary">Tiêu đề *</label><input type="text" name="title" class="form-control form-control-dark" value="{{ old('title') }}" required></div>
            
            {{-- Select danh mục: Lấy dữ liệu từ $categories đã truyền từ Controller --}}
            <div class="col-md-4"><label class="form-label fw-bold text-secondary">Danh mục *</label><select name="category_id" class="form-control form-control-dark" required><option value="">Chọn danh mục</option>@foreach($categories as $c)<option value="{{ $c->id }}" {{ old('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select></div>
            
            <div class="col-md-8"><label class="form-label fw-bold text-secondary">Mô tả ngắn</label><textarea name="excerpt" class="form-control form-control-dark" rows="2">{{ old('excerpt') }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold text-secondary">Địa điểm</label><input type="text" name="location" class="form-control form-control-dark" value="{{ old('location') }}"></div>
            <div class="col-12"><label class="form-label fw-bold text-secondary">Nội dung *</label><textarea name="content" class="form-control form-control-dark" rows="12" required>{{ old('content') }}</textarea></div>
            
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Hình ảnh</label><input type="file" name="image" class="form-control form-control-dark" accept="image/*"></div>
            
            {{-- Chọn trạng thái để quản lý bài viết --}}
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Trạng thái *</label><select name="status" class="form-control form-control-dark"><option value="published" {{ old('status')=='published'?'selected':'' }}>Đã đăng</option><option value="draft" {{ old('status')=='draft'?'selected':'' }}>Nháp</option></select></div>
            
            <div class="col-12"><button type="submit" class="btn btn-primary-custom"><i class="fas fa-save me-1"></i>Lưu bài viết</button></div>
        </div>
    </form>
</div>
@endsection