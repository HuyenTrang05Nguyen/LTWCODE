{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Khai báo tiêu đề hiển thị cho trang quản lý người dùng --}}
@section('title', 'Quản lý người dùng')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')
<div class="admin-header">
    <h2><i class="fas fa-users me-2" style="color:var(--primary);"></i>Quản lý người dùng</h2>
</div>

{{-- Khối thanh tìm kiếm và bộ lọc vai trò (Filter Form):
     - Dùng method="GET": Đính các tham số lọc trực tiếp lên URL để thuận tiện cho việc phân trang (Ví dụ: ?search=nguyen+van+a&role=user) --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        {{-- Ô tìm kiếm người dùng theo Tên hoặc Email --}}
        <div class="col-md-6">
            <input type="text" name="search" class="form-control form-control-dark" placeholder="Tìm theo tên, email..." value="{{ request('search') }}">
        </div>
        
        {{-- Ô lọc người dùng theo Vai trò (Phân quyền Admin hoặc User) --}}
        <div class="col-md-4">
            <select name="role" class="form-control form-control-dark">
                <option value="">Tất cả vai trò</option>
                <option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option>
                <option value="user" {{ request('role')=='user'?'selected':'' }}>User</option>
            </select>
        </div>
        
        {{-- Nút bấm thực thi lệnh lọc/tìm kiếm --}}
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
                <th>Tên</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Bài viết</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
        {{-- Vòng lặp duyệt qua danh sách người dùng được truyền từ UserController sang --}}
        @foreach($users as $user)
        <tr>
            {{-- Hiển thị mã ID tài khoản --}}
            <td>{{ $user->id }}</td>
            
            <td>
                <div class="d-flex align-items-center gap-2">
                    {{-- Gọi thuộc tính ảo $user->avatar_url (Accessor định nghĩa trong Model User) để lấy đường dẫn ảnh đại diện chuẩn chỉnh --}}
                    <img src="{{ $user->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                    <span class="fw-bold">{{ $user->name }}</span>
                </div>
            </td>
            
            {{-- Hiển thị email của người dùng --}}
            <td>{{ $user->email }}</td>
            
            {{-- Đổi class CSS màu sắc của Badge dựa trên quyền tài khoản là Admin hay Người dùng thông thường --}}
            <td>
                <span class="badge-status {{ $user->role==='admin'?'badge-admin':'badge-user' }}">
                    {{ $user->role==='admin'?'Admin':'User' }}
                </span>
            </td>
            
            {{-- Hiển thị tổng số bài viết thành viên này đã đăng (Nạp tự động qua hàm withCount('posts') trong Controller) --}}
            <td>{{ $user->posts_count }}</td>
            
            {{-- Định dạng lại hiển thị ngày đăng ký tài khoản thành Ngày/Tháng/Năm của Việt Nam --}}
            <td>{{ $user->created_at->format('d/m/Y') }}</td>
            
            <td>
                <div class="d-flex gap-1">
                    {{-- Form xử lý Thay đổi vai trò (Chuyển nhanh từ Admin <-> User):
                         - @method('PATCH'): Giả lập phương thức PATCH vì ta chỉ cập nhật duy nhất cột 'role' của bản ghi --}}
                    <form method="POST" action="{{ route('admin.users.toggleRole', $user) }}">
                        @csrf 
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-outline-info" title="Đổi vai trò"><i class="fas fa-exchange-alt"></i></button>
                    </form>
                    
                    {{-- ĐIỀU KIỆN BẢO VỆ HỆ THỐNG: Khóa không cho phép người quản trị tự thực hiện hành động xóa chính tài khoản mà họ đang đăng nhập --}}
                    @if($user->id !== auth()->id())
                        {{-- Form thực hiện hành động XÓA tài khoản thành viên ra khỏi hệ thống --}}
                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa người dùng này?')">
                            @csrf 
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    @endif
                </div>
            </td>
        </tr>
        @endforeach