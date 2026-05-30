@extends('layouts.admin')
@section('title', 'Quản lý người dùng')
@section('content')
<div class="admin-header"><h2><i class="fas fa-users me-2" style="color:var(--primary);"></i>Quản lý người dùng</h2></div>

{{-- Khối lọc người dùng --}}
<div class="card-glass p-3 mb-4">
    <form method="GET" class="row g-2 align-items-end">
        <div class="col-md-6"><input type="text" name="search" class="form-control form-control-dark" placeholder="Tìm theo tên, email..." value="{{ request('search') }}"></div>
        <div class="col-md-4"><select name="role" class="form-control form-control-dark"><option value="">Tất cả vai trò</option><option value="admin" {{ request('role')=='admin'?'selected':'' }}>Admin</option><option value="user" {{ request('role')=='user'?'selected':'' }}>User</option></select></div>
        <div class="col-md-2"><button type="submit" class="btn btn-primary-custom w-100"><i class="fas fa-search"></i></button></div>
    </form>
</div>

<div class="table-dark-custom">
    <table class="table"><thead><tr><th>#</th><th>Tên</th><th>Email</th><th>Vai trò</th><th>Bài viết</th><th>Ngày tạo</th><th>Hành động</th></tr></thead>
    <tbody>
    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td><div class="d-flex align-items-center gap-2"><img src="{{ $user->avatar_url }}" alt="" style="width:32px;height:32px;border-radius:50%;object-fit:cover;"><span class="fw-bold">{{ $user->name }}</span></div></td>
        <td>{{ $user->email }}</td>
        {{-- Badge hiển thị vai trò khác nhau tùy theo giá trị role --}}
        <td><span class="badge-status {{ $user->role==='admin'?'badge-admin':'badge-user' }}">{{ $user->role==='admin'?'Admin':'User' }}</span></td>
        <td>{{ $user->posts_count }}</td>
        <td>{{ $user->created_at->format('d/m/Y') }}</td>
        <td>
            <div class="d-flex gap-1">
                {{-- Toggle vai trò (PATCH) --}}
                <form method="POST" action="{{ route('admin.users.toggleRole', $user) }}">@csrf @method('PATCH')
                    <button type="submit" class="btn btn-sm btn-outline-info" title="Đổi vai trò"><i class="fas fa-exchange-alt"></i></button>
                </form>
                
                {{-- Bảo mật: Kiểm tra nếu không phải chính mình thì mới cho hiển thị nút Xóa --}}
                @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Xóa người dùng này?')">@csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                </form>
                @endif
            </div>
        </td>
    </tr>
    @endforeach
    </tbody></table>
</div>
<div class="d-flex justify-content-center mt-3">{{ $users->links() }}</div>
@endsection