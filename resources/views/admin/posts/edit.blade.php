@extends('layouts.admin')
@section('title', 'Sửa bài viết')
@section('content')
<div class="admin-header"><h2><i class="fas fa-edit me-2" style="color:var(--primary);"></i>Sửa bài viết</h2><a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a></div>

@if($errors->any())<div class="alert alert-custom alert-error-custom mb-3">@foreach($errors->all() as $e)<div><i class="fas fa-exclamation-circle me-1"></i>{{ $e }}</div>@endforeach</div>@endif

<div class="card-glass p-4">
    {{-- Truyền biến $post vào route để Laravel biết bạn đang sửa bài nào --}}
    {{-- @method('PUT') là bắt buộc để thực hiện hành động cập nhật (Update) --}}
    <form method="POST" action="{{ route('admin.posts.update', $post) }}" enctype="multipart/form-data">@csrf @method('PUT')
        <div class="row g-3">
            {{-- Dùng old('tên', $post->trường) để ưu tiên giá trị lỗi (nếu có), còn không thì lấy từ DB --}}
            <div class="col-md-8"><label class="form-label fw-bold text-secondary">Tiêu đề *</label><input type="text" name="title" class="form-control form-control-dark" value="{{ old('title', $post->title) }}" required></div>
            
            <div class="col-md-4"><label class="form-label fw-bold text-secondary">Danh mục *</label>
                <select name="category_id" class="form-control form-control-dark" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}" {{ old('category_id', $post->category_id)==$c->id?'selected':'' }}>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="col-md-8"><label class="form-label fw-bold text-secondary">Mô tả ngắn</label><textarea name="excerpt" class="form-control form-control-dark" rows="2">{{ old('excerpt', $post->excerpt) }}</textarea></div>
            <div class="col-md-4"><label class="form-label fw-bold text-secondary">Địa điểm</label><input type="text" name="location" class="form-control form-control-dark" value="{{ old('location', $post->location) }}"></div>
            <div class="col-12"><label class="form-label fw-bold text-secondary">Nội dung *</label><textarea name="content" class="form-control form-control-dark" rows="12" required>{{ old('content', $post->content) }}</textarea></div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                {{-- Kiểm tra ảnh cũ để hiển thị preview trước khi người dùng thay ảnh mới --}}
                @if($post->image)<div class="mb-2"><img src="{{ $post->image_url }}" alt="" style="height:80px;border-radius:8px;"></div>@endif
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Trạng thái *</label>
                <select name="status" class="form-control form-control-dark">
                    <option value="published" {{ old('status',$post->status)=='published'?'selected':'' }}>Đã đăng</option>
                    <option value="draft" {{ old('status',$post->status)=='draft'?'selected':'' }}>Nháp</option>
                </select>
            </div>
            
            <div class="col-12"><button type="submit" class="btn btn-primary-custom"><i class="fas fa-save me-1"></i>Cập nhật</button></div>
        </div>
    </form>
</div>
@endsection