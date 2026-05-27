<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Khởi tạo và cấu hình instance Application (  )
return Application::configure(basePath: dirname(__DIR__))
    
    /**
     * Cấu hình Hệ thống Định tuyến (Routing) cho toàn bộ ứng dụng
     */
    ->withRouting(
        web: __DIR__.'/../routes/web.php',       // Nạp file định tuyến giao diện Web (chứa các trang chính, bộ lọc...)
        commands: __DIR__.'/../routes/console.php', // Nạp các câu lệnh chạy bằng Terminal (Artisan Commands) nếu có
        health: '/up',                            // Tạo đường dẫn tự động kiểm tra trạng thái hoạt động (Health Check) của server
    )

    /**
     * Cấu hình các lớp Trung gian (Middleware)
     */
    ->withMiddleware(function (Middleware $middleware): void {
        // Đăng ký bí danh (Alias) cho Middleware tự viết
        $middleware->alias([
            // Đặt tên bí danh là 'admin' đại diện cho file lớp AdminMiddleware để gọi ngắn gọn trong file routes/web.php
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })

    /**
     * Cấu hình hệ thống xử lý Ngoại lệ/Lỗi (Exception Handling)
     */
    ->withExceptions(function (Exceptions $exceptions): void {
        // Nơi tùy biến cách hệ thống hiển thị hoặc ghi log khi gặp lỗi (Ví dụ: Lỗi 404, 500...)
    })->create(); // Chính thức tạo và trả về instance ứng dụng