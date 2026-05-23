<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Xử lý và kiểm tra yêu cầu (Request) trước khi cho phép đi tiếp vào các trang quản trị
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra quyền hạn: 
        // Lọc 1: (!auth()->check()) -> Nếu người dùng chưa đăng nhập
        // Lọc 2: (!auth()->user()->isAdmin()) -> Hoặc đã đăng nhập nhưng không phải là Tài khoản Quản trị (Admin)
        if (!auth()->check() || !auth()->user()->isAdmin()) {
            // Ngay lập tức chặn lại và trả về trang lỗi 403 (Forbidden - Từ chối truy cập) kèm thông báo
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Nếu vượt qua vòng kiểm tra trên, cho phép Request tiếp tục đi tiếp vào Controller xử lý
        return $next($request);
    }
}