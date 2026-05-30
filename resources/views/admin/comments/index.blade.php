@extends('layouts.admin')
@section('title', 'Quản lý bình luận')
@section('content')
<div class="admin-header"><h2><i class="fas fa-comments me-2" style="color:var(--primary);"></i>Quản lý bình luận</h2></div>

{{-- Form lọc bình luận theo trạng thái --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-8">
            <select name="status" class="form-control form-control-dark">
                <option value="">Tất cả</option>
                {{-- request('status') giúp giữ giá trị lọc trên URL sau khi reload trang --}}
                <option value="approved" {{ request('status')=='approved'?'selected':'' }}>Đã duyệt</option>
                <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Chờ duyệt</option>
            </select>
        </div>
        <div class="col-md-4"><button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-filter me-1"></i>Lọc</button></div>
    </form>
</div>

<div class="table-dark-custom">
    <table class="table"><thead><tr><th>#</th><th>Người dùng</th><th>Bài viết</th><th>Nội dung</th><th>Trạng thái</th><th>Ngày</th><th>Hành động</th></tr></thead>
    <tbody>
    @forelse($comments as $c)
    <tr>
        <td>{{ $c->id }}</td>
        <td>{{ $c->user->name }}</td>
        <td><a href="{{ route('posts.show', $c->post->slug) }}" class="text-info text-decoration-none" target="_blank">{{ Str::limit($c->post->title, 30) }}</a></td>
        <td>{{ Str::limit($c->content, 60) }}</td>
        {{-- Dùng ternary để đổi màu badge: is_approved = true thì xanh, ngược lại thì cam --}}
        <td><span class="badge-status {{ $c->is_approved?'badge-published':'badge-draft' }}">{{ $c->is_approved?'Đã duyệt':'Chờ duyệt' }}</span></td>
        <td>{{ $c->created_at->format('d/m/Y') }}</td>
        <td>
            <div class="d-flex gap-1">
                @if(!$c->is_approved)
                {{-- PATCH: Phương thức dùng để cập nhật một phần dữ liệu (duyệt bình luận) --}}
                <form method="POST" action="{{ route('admin.comments.approve', $c) }}">@csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt"><i class="fas fa-check"></i></button>
                </form>
                @else
                {{-- PATCH: Ẩn bình luận --}}
                <form method="POST" action="{{ route('admin.comments.reject', $c) }}">@csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-warning" title="Ẩn"><i class="fas fa-eye-slash"></i></button>
                </form>
                @endif
                {{-- DELETE: Xóa bình luận --}}
                <form method="POST" action="{{ route('admin.comments.destroy', $c) }}" onsubmit="return confirm('Xóa bình luận?')">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
            </div>
        </td>
    </tr>
    @empty
    <tr><td colspan="7" class="text-center text-secondary py-4">Không có bình luận.</td></tr>
    @endforelse
    </tbody></table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $comments->links() }}</div>
@endsection