@extends('layouts.admin')
@section('title', 'Quản lý bài viết')
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-newspaper me-2" style="color:var(--primary);"></i>Quản lý bài viết</h2>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary-custom"><i class="fas fa-plus me-1"></i>Thêm bài viết</a>
</div>

{{-- Khối lọc: Dùng method="GET" để gửi tham số tìm kiếm lên URL --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        {{-- request('key') giúp giữ lại giá trị đã lọc trên thanh URL --}}
        <div class="col-md-4"><input type="text" name="search" class="form-control form-control-dark" placeholder="Tìm kiếm..." value="{{ request('search') }}"></div>
        <div class="col-md-3">
            <select name="status" class="form-control form-control-dark">
                <option value="">Tất cả trạng thái</option>
                <option value="published" {{ request('status')=='published'?'selected':'' }}>Đã đăng</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Nháp</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-control form-control-dark">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $c)<option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach
            </select>
        </div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-search"></i></button></div>
    </form>
</div>

<div class="table-dark-custom">
    <table class="table"><thead><tr><th>#</th><th>Tiêu đề</th><th>Danh mục</th><th>Tác giả</th><th>Trạng thái</th><th>Lượt xem</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
    <tbody>
    @forelse($posts as $post)
    <tr>
        <td>{{ $post->id }}</td>
        {{-- Truy cập trực tiếp qua Route model binding --}}
        <td><a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none" target="_blank">{{ Str::limit($post->title, 40) }}</a></td>
        {{-- $post->category: truy cập dữ liệu từ bảng liên kết (phải có quan hệ belongsTo) --}}
        <td><span class="badge-status badge-published">{{ $post->category->name }}</span></td>
        <td>{{ $post->user->name }}</td>
        <td><span class="badge-status {{ $post->status==='published'?'badge-published':'badge-draft' }}">{{ $post->status==='published'?'Đã đăng':'Nháp' }}</span></td>
        <td>{{ number_format($post->views_count) }}</td>
        <td>{{ $post->created_at->format('d/m/Y') }}</td>
        <td>
            <div class="d-flex gap-1">
                <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Xóa bài viết này?')">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="8" class="text-center text-secondary py-4">Không có bài viết nào.</td></tr>
    @endforelse
    </tbody></table>
</div>
{{-- Hiển thị các nút chuyển trang (Phân trang tự động) --}}
<div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
@endsection