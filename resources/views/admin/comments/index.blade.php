{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Khai báo tiêu đề hiển thị cho trang quản lý bình luận --}}
@section('title', 'Quản lý bình luận')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-comments me-2" style="color:var(--primary);"></i>Quản lý bình luận</h2>
</div>

{{-- Khối form lọc (Filter) dữ liệu theo trạng thái:
     - Dùng method="GET": Khi bấm lọc, tham số lọc sẽ đính lên thanh URL (Ví dụ: ?status=approved) --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-8">
            <select name="status" class="form-control form-control-dark">
                <option value="">Tất cả</option>
                {{-- request('status') == 'approved' ? 'selected' : '': Đoạn toán tử ba ngôi giúp kiểm tra trên URL hiện tại, 
                     nếu đang lọc trạng thái nào thì giữ lại trạng thái đó hiển thị trên ô select sau khi tải lại trang --}}
                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Đã duyệt</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ duyệt</option>
            </select>
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-primary-custom w-100">
                <i class="fas fa-filter me-1"></i>Lọc
            </button>
        </div>
    </form>
</div>

<div class="table-dark-custom">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Người dùng</th>
                <th>Bài viết</th>
                <th>Nội dung</th>
                <th>Trạng thái</th>
                <th>Ngày</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        {{-- Vòng lặp kiểm tra và duyệt qua danh sách bình luận --}}
        @forelse($comments as $c)
        <tr>
            <td>{{ $c->id }}</td>
            
            {{-- Sử dụng quan hệ liên kết bảng $c->user để bốc tên người dùng đã viết bình luận --}}
            <td>{{ $c->user->name }}</td>
            
            {{-- Hiển thị tiêu đề bài viết được bình luận (và đặt link dẫn ra trang chi tiết ngoài client bằng slug)
                 - Str::limit: Cắt ngắn tiêu đề bài viết chỉ lấy 30 ký tự để tránh làm vỡ giao diện bảng --}}
            <td>
                <a href="{{ route('posts.show', $c->post->slug) }}" class="text-info text-decoration-none" target="_blank">
                    {{ Str::limit($c->post->title, 30) }}
                </a>
            </td>
            
            {{-- Cắt ngắn nội dung bình luận chỉ hiển thị tối đa 60 ký tự để bảng trông gọn gàng hơn --}}
            <td>{{ Str::limit($c->content, 60) }}</td>
            
            {{-- Sử dụng toán tử ba ngôi để đổi class CSS màu sắc và chuỗi văn bản tương ứng với trạng thái ẩn/hiện --}}
            <td>
                <span class="badge-status {{ $c->is_approved ? 'badge-published' : 'badge-draft' }}">
                    {{ $c->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                </span>
            </td>
            
            {{-- Định dạng lại hiển thị ngày tháng năm tạo bình luận theo kiểu Việt Nam (Ngày/Tháng/Năm) --}}
            <td>{{ $c->created_at->format('d/m/Y') }}</td>
            
            <td>
                <div class="d-flex gap-1">
                    {{-- KIỂM TRA ĐIỀU KIỆN ĐỂ HIỂN THỊ NÚT CHỨC NĂNG PHÙ HỢP:
                         Nếu bình luận CHƯA ĐƯỢC DUYỆT (is_approved = false): Hiển thị form cho phép bấm Duyệt --}}
                    @if(!$c->is_approved)
                        <form method="POST" action="{{ route('admin.comments.approve', $c) }}">
                            @csrf 
                            {{-- Giả lập phương thức PATCH dùng để cập nhật một phần dữ liệu (chuyển trạng thái từ false sang true) --}}
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-success" title="Duyệt">
                                <i class="fas fa-check"></i>
                            </button>
                        </form>
                    {{-- Ngược lại nếu bình luận ĐÃ DUYỆT: Hiển thị form cho phép bấm Ẩn đi --}}
                    @else
                        <form method="POST" action="{{ route('admin.comments.reject', $c) }}">
                            @csrf 
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm btn-outline-warning" title="Ẩn">
                                <i class="fas fa-eye-slash"></i>
                            </button>
                        </form>
                    @endif
                    
                    {{-- Form thực hiện chức năng XÓA hoàn toàn bình luận khỏi hệ thống --}}
                    <form method="POST" action="{{ route('admin.comments.destroy', $c) }}" onsubmit="return confirm('Xóa bình luận?')">
                        @csrf 
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        {{-- Nhánh hiển thị nếu không tìm thấy bình luận nào thỏa mãn điều kiện --}}
        <tr>
            <td colspan="7" class="text-center text-secondary py-4">Không có bình luận.</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Hiển thị các nút phân trang điều hướng --}}
<div class="d-flex justify-content-center mt-3">
    {{ $comments->links() }}
</div>
@endsection