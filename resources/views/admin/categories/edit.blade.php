@extends('layouts.admin') {{-- Kế thừa giao diện --}}
@section('title', 'Sửa danh mục')
@section('content')
<div class="admin-header"><h2><i class="fas fa-edit me-2" style="color:var(--primary);"></i>Sửa danh mục</h2><a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a></div>

{{-- Hiển thị thông báo lỗi nếu có --}}
@if($errors->any())<div class="alert alert-custom alert-error-custom mb-3">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

<div class="card-glass p-4">
    {{-- Route cần truyền biến $category để Laravel biết sửa dòng nào --}}
    <form method="POST" action="{{ route('admin.categories.update', $category) }}" enctype="multipart/form-data">
        @csrf @method('PUT') {{-- @method('PUT') là bắt buộc để cập nhật dữ liệu --}}
        
        <div class="row g-3">
            {{-- value={{ old('name', $category->name) }} ưu tiên giá trị nhập lỗi (old) trước, nếu không thì lấy từ database --}}
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Tên danh mục *</label><input type="text" name="name" class="form-control form-control-dark" value="{{ old('name', $category->name) }}" required></div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold text-secondary">Hình ảnh</label>
                {{-- Kiểm tra nếu có ảnh cũ thì hiển thị ảnh preview --}}
                @if($category->image)<div class="mb-2"><img src="{{ $category->image_url }}" alt="" style="height:60px;border-radius:8px;"></div>@endif
                <input type="file" name="image" class="form-control form-control-dark" accept="image/*">
            </div>
            
            <div class="col-12"><label class="form-label fw-bold text-secondary">Mô tả</label><textarea name="description" class="form-control form-control-dark" rows="3">{{ old('description', $category->description) }}</textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-primary-custom"><i class="fas fa-save me-1"></i>Cập nhật</button></div>
        </div>
    </form>
</div>
@endsection