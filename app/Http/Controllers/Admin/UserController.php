<?php
// Mở thẻ PHP

namespace App\Http\Controllers\Admin;
// Namespace của controller
// File nằm tại:
// app/Http/Controllers/Admin

use App\Http\Controllers\Controller;
// Import Controller gốc Laravel

use App\Models\User;
// Import model User
// Dùng thao tác bảng users

use Illuminate\Http\Request;
// Import Request
// Dùng lấy dữ liệu từ URL, form, query string,...

class UserController extends Controller
// Tạo UserController
{
    public function index(Request $request)
    // Hàm hiển thị danh sách user
    {
        $query = User::withCount('posts');
        // Tạo query lấy users

        // withCount('posts')
        // -> đếm số lượng bài viết của mỗi user

        // Laravel tự tạo field:
        // posts_count

        // Ví dụ:
        // {
        //    name: "Phuong",
        //    posts_count: 15
        // }

        if ($request->filled('search')) {
        // Kiểm tra có nhập search không

            $query->where(function ($q) use ($request) {
            // Tạo nested query

            // use ($request)
            // -> truyền biến request vào closure

                $q->where('name', 'like', "%{$request->search}%")
                // Search theo tên

                  ->orWhere('email', 'like', "%{$request->search}%");
                // HOẶC search theo email

                // SQL tương đương:
                /*
                WHERE (
                    name LIKE '%abc%'
                    OR email LIKE '%abc%'
                )
                */
            });
        }

        if ($request->filled('role')) {
        // Kiểm tra có filter role không

            $query->where('role', $request->role);
            // Lọc theo role

            // Ví dụ:
            // admin
            // user
        }

        $users = $query->latest()->paginate(10)->withQueryString();
        // latest()
        // -> user mới nhất lên đầu

        // paginate(10)
        // -> phân trang 10 user/trang

        // withQueryString()
        // -> giữ query filter/search khi chuyển trang

        return view('admin.users.index', compact('users'));
        // Trả về view:
        // resources/views/admin/users/index.blade.php

        // compact('users')
        // -> truyền biến users sang view
    }

    public function toggleRole(User $user)
    // Hàm đổi role user
    {
        if ($user->id === auth()->id()) {
        // Kiểm tra có phải chính tài khoản hiện tại không

            return back()->with(
                'error',
                'Không thể thay đổi role của chính mình!'
            );

            // Chặn admin tự đổi quyền chính mình

            // Nếu không:
            // admin có thể tự biến mình thành user
            // rồi mất quyền admin luôn
        }

        $user->role = $user->role === 'admin'
            ? 'user'
            : 'admin';

        // Toán tử ternary operator

        // Nếu role hiện tại là admin
        // -> đổi thành user

        // Ngược lại:
        // -> đổi thành admin

        $user->save();
        // Lưu thay đổi vào database

        return back()->with(
            'success',
            "Đã cập nhật quyền cho {$user->name}!"
        );

        // {$user->name}
        // -> string interpolation

        // Ví dụ:
        // Đã cập nhật quyền cho Phuong!
    }

    public function destroy(User $user)
    // Hàm xóa user
    {
        if ($user->id === auth()->id()) {
        // Kiểm tra có phải chính tài khoản đang login không

            return back()->with(
                'error',
                'Không thể xóa tài khoản của chính mình!'
            );

            // Chặn tự xóa chính mình

            // Nếu không:
            // admin tự xóa tài khoản
            // -> mất toàn bộ quyền truy cập hệ thống
        }

        $user->delete();
        // Xóa user khỏi database

        return back()->with('success', 'Đã xóa người dùng!');
        // Quay lại + thông báo thành công
    }
}
