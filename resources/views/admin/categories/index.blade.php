{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Định nghĩa tiêu đề cho trang danh sách danh mục --}}
@section('title', 'Quản lý danh mục')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-folder me-2" style="color:var(--primary);"></i>Quản lý danh mục</h2>
    {{-- Nút điều hướng dẫn tới trang Thêm mới danh mục --}}
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary-custom">
        <i class="fas fa-plus me-1"></i>Thêm danh mục
    </a>
</div>

<div class="table-dark-custom">
    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Tên</th>
                <th>Slug</th>
                <th>Mô tả</th>
                <th>Số bài viết</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        {{-- Vòng lặp đặc biệt @forelse của Blade: 
             - Nếu mảng $categories có dữ liệu, nó hoạt động giống như @foreach để duyệt dữ liệu.
             - Nếu mảng $categories rỗng (không có bản ghi nào), nó sẽ tự động nhảy vào nhánh @empty ở dưới --}}
        @forelse($categories as $cat)
        <tr>
            {{-- Hiển thị ID của danh mục --}}
            <td>{{ $cat->id }}</td>
            
            {{-- Hiển thị tên danh mục --}}
            <td class="fw-bold">{{ $cat->name }}</td>
            
            {{-- Hiển thị slug (đường dẫn thân thiện) của danh mục --}}
            <td><code class="text-secondary">{{ $cat->slug }}</code></td>
            
            {{-- Hiển thị mô tả ngắn: Dùng Str::limit để cắt bớt chữ nếu mô tả quá dài (quá 50 ký tự), tránh làm vỡ khung hàng của bảng --}}
            <td>{{ Str::limit($cat->description, 50) }}</td>
            
            {{-- Hiển thị số lượng bài viết thuộc danh mục này (Được nạp thông qua hàm withCount('posts') từ Controller) --}}
            <td><span class="badge-status badge-published">{{ $cat->posts_count }}</span></td>
            
            <td>
                <div class="d-flex gap-1">
                    {{-- Nút Sửa: Chuyển hướng tới trang edit kèm theo ID của danh mục hiện tại --}}
                    <a href="{{ route('admin.categories.edit', $cat) }}" class="btn btn-sm btn-outline-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    
                    {{-- Form Xóa: Hành động xóa dữ liệu bắt buộc phải nằm trong một thẻ <form> gửi phương thức ẩn để đảm bảo an toàn hệ thống
                         - onsubmit: Sử dụng sự kiện Javascript sinh hộp thoại confirm hỏi lại người dùng nhằm tránh việc bấm nhầm --}}
                    <form method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Xóa danh mục này?')">
                        {{-- Token bảo mật CSRF bắt buộc để Laravel xác thực biểu mẫu gửi lên hợp pháp --}}
                        @csrf 
                        
                        {{-- Giả lập phương thức HTTP DELETE để định tuyến Laravel hiểu và kích hoạt hàm destroy() trong Controller --}}
                        @method('DELETE')
                        
                        <button type="submit" class="btn btn-sm btn-outline-danger">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        {{-- Kịch bản xảy ra khi cơ sở dữ liệu trống rỗng, chưa có danh mục nào được tạo --}}
        <tr>
            <td colspan="6" class="text-center text-secondary py-4">Không có danh mục nào.</td>
        </tr>
        @endforelse
        </tbody>
    </table>
</div>

{{-- Hiển thị thanh điều hướng phân trang (Pagination Links) tự động sinh bởi Laravel (Ví dụ: các nút Trang 1, 2, Tiếp theo)
     - Khối căn giữa d-flex justify-content-center giúp thanh phân trang luôn nằm cân đối ở giữa màn hình --}}
<div class="d-flex justify-content-center mt-3">
    {{ $categories->links() }}
</div>
@endsection {{-- Kết thúc định nghĩa nội dung chính --}}