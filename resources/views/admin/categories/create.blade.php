@extends('layouts.admin') {{-- Kế thừa giao diện cha --}}
@section('title', 'Thêm danh mục') {{-- Tiêu đề trang --}}
@section('content')
<div class="admin-header"><h2><i class="fas fa-plus-circle me-2" style="color:var(--primary);"></i>Thêm danh mục</h2><a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-1"></i>Quay lại</a></div>

{{-- Hiển thị lỗi validation nếu có --}}
@if($errors->any())<div class="alert alert-custom alert-error-custom mb-3">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif

<div class="card-glass p-4">
    {{-- enctype="multipart/form-data" để upload file --}}
    <form method="POST" action="{{ route('admin.categories.store') }}" enctype="multipart/form-data">
        @csrf {{-- Bảo mật form (bắt buộc) --}}
        <div class="row g-3">
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Tên danh mục *</label><input type="text" name="name" class="form-control form-control-dark" value="{{ old('name') }}" required></div> {{-- old('name') giữ lại dữ liệu khi lỗi --}}
            <div class="col-md-6"><label class="form-label fw-bold text-secondary">Hình ảnh</label><input type="file" name="image" class="form-control form-control-dark" accept="image/*"></div>
            <div class="col-12"><label class="form-label fw-bold text-secondary">Mô tả</label><textarea name="description" class="form-control form-control-dark" rows="3">{{ old('description') }}</textarea></div>
            <div class="col-12"><button type="submit" class="btn btn-primary-custom"><i class="fas fa-save me-1"></i>Lưu</button></div>
        </div>
    </form>
</div>
@endsection