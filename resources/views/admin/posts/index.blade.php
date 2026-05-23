{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Khai báo tiêu đề hiển thị cho trang quản lý bài viết --}}
@section('title', 'Quản lý bài viết')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-newspaper me-2" style="color:var(--primary);"></i>Quản lý bài viết</h2>
    {{-- Nút điều hướng dẫn tới trang Thêm mới bài viết --}}
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary-custom"><i class="fas fa-plus me-1"></i>Thêm bài viết</a>
</div>

{{-- Khối thanh tìm kiếm và bộ lọc nâng cao (Filter Form):
     - Dùng method="GET": Đính các tham số lọc lên thanh URL (Ví dụ: ?search=da+nang&status=published&category_id=2) --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        {{-- Tìm kiếm theo từ khóa (Tiêu đề bài viết) --}}
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-dark" placeholder="Tìm kiếm..." value="{{ request('search') }}">
        </div>
        
        {{-- Lọc bài viết theo Trạng thái (Đã đăng / Nháp) --}}
        <div class="col-md-3">
            <select name="status" class="form-control form-control-dark">
                <option value="">Tất cả trạng thái</option>
                <option value="published" {{ request('status')=='published'?'selected':'' }}>Đã đăng</option>
                <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Nháp</option>
            </select>
        </div>
        
        {{-- Lọc bài viết theo Danh mục du lịch --}}
        <div class="col-md-3">
            <select name="category_id" class="form-control form-control-dark">
                <option value="">Tất cả danh mục</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}" {{ request('category_id')==$c->id?'selected':'' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        
        {{-- Nút kích hoạt hành động tìm kiếm --}}
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-search"></i></button>
        </div>
    </form>
</div>

<div class="table-dark-custom">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tiêu đề</th>
                <th>Danh mục</th>
                <th>Tác giả</th>
                <th>Trạng thái</th>
                <th>Lượt xem</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        {{-- Sử dụng vòng lặp @forelse để duyệt danh sách bài viết, tự động nhảy vào nhánh @empty nếu kết quả lọc/tìm kiếm trống rỗng --}}
        @forelse($posts as $post)
        <tr>
            {{-- Hiển thị mã ID bài viết --}}
            <td>{{ $post->id }}</td>
            
            {{-- Hiển thị tiêu đề bài viết (Cắt ngắn tối đa 40 ký tự) và tạo link mở tab mới xem chi tiết ngoài Client --}}
            <td>
                <a href="{{ route('posts.show', $post->slug) }}" class="text-dark text-decoration-none" target="_blank">
                    {{ Str::limit($post->title, 40) }}
                </a>
            </td>
            
            {{-- Gọi quan hệ liên kết bảng lấy tên danh mục thuộc bài viết này ($post->category) --}}
            <td><span class="badge-status badge-published">{{ $post->category->name }}</span></td>
            
            {{-- Gọi quan hệ liên kết bảng lấy tên tác giả viết bài viết này ($post->user) --}}
            <td>{{ $post->user->name }}</td>
            
            {{-- Đổi màu Badge CSS động dựa theo trạng thái bài viết là 'published' (Đã đăng) hay 'draft' (Nháp) --}}
            <td>
                <span class="badge-status {{ $post->status==='published'?'badge-published':'badge-draft' }}">
                    {{ $post->status==='published'?'Đã đăng':'Nháp' }}
                </span>
            </td>
            
            {{-- Định dạng số hiển thị lượt xem: Thêm dấu phẩy phân tách hàng nghìn (Ví dụ: 1,500 lượt xem) --}}
            <td>{{ number_format($post->views_count) }}</td>
            
            {{-- Định dạng ngày tạo bài viết theo chuẩn hiển thị Việt Nam (Ngày/Tháng/Năm) --}}
            <td>{{ $post->created_at->format('d/m/Y') }}</td>
            
            <td>
                <div class="d-flex gap-1">
                    {{-- Nút điều hướng sang trang Sửa (Sử dụng liên kết GET) --}}
                    <a href="{{ route('admin.posts.edit', $post) }}" class="btn btn-sm btn-outline-warning"><i class="fas fa-edit"></i></a>
                    
                    {{-- Form thực hiện hành động Xóa bản ghi:
                         - onsubmit: Tạo hộp thoại cảnh báo Javascript ngăn ngừa quản trị viên bấm nhầm nút xóa
                         - @method('DELETE'): Ép trình duyệt truyền yêu cầu theo phương thức ẩn DELETE đúng chuẩn RESTful API --}}
                    <form method="POST" action="{{ route('admin.posts.destroy', $post) }}" onsubmit="return confirm('Xóa bài viết này?')">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        {{-- Kịch bản xảy ra khi không tìm thấy bài viết nào phù hợp với từ khóa hoặc bộ lọc --}}
        <tr>
            <td colspan="8" class="text-center text-secondary py-4">Không có bài viết nào.</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Hiển thị thanh nút bấm phân trang tự động dưới chân bảng dữ liệu --}}
<div class="d-flex justify-content-center mt-3">{{ $posts->links() }}</div>
@endsection