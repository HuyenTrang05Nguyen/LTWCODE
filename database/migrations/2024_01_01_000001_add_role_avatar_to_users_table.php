<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để thiết lập kiểu dữ liệu cho các cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để bổ sung các cột mới vào bảng 'users'.
     */
    public function up(): void
    {
        // Schema::table: Ra lệnh chỉnh sửa/cập nhật bảng 'users' đã tồn tại sẵn (thay vì Schema::create là tạo mới)
        Schema::table('users', function (Blueprint $table) {
            
            // 1. BỔ SUNG CỘT PHÂN QUYỀN 'role'
            // Kiểu ENUM: Chỉ cho phép cột này nhận 1 trong 2 giá trị cố định là 'admin' hoặc 'user' (tránh nhập dữ liệu lung tung)
            // default('user'): Nếu khi đăng ký không chọn quyền, hệ thống tự động gán là tài khoản thường ('user')
            // after('email'): Vị trí sắp xếp cột này trong Database sẽ nằm ngay sau cột 'email' để dễ quản lý dữ liệu
            $table->enum('role', ['admin', 'user'])->default('user')->after('email');
            
            // 2. BỔ SUNG CỘT ẢNH ĐẠI DIỆN 'avatar'
            // Kiểu string: Lưu chuỗi đường dẫn tệp tin ảnh (Ví dụ: uploads/avatars/user1.jpg)
            // nullable(): Cho phép cột này được quyền để TRỐNG (null) vì khi mới đăng ký tài khoản, user chưa kịp cập nhật ảnh
            // after('role'): Vị trí cột này trong Database sẽ xếp ngay sau cột 'role' vừa tạo ở trên
            $table->string('avatar')->nullable()->after('role');
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa bỏ các cột vừa thêm, trả bảng về nguyên trạng.
     */
    public function down(): void
    {
        // Tiếp tục gọi lệnh chỉnh sửa bảng 'users'
        Schema::table('users', function (Blueprint $table) {
            
            // dropColumn: Câu lệnh xóa bỏ hoàn toàn các cột được chỉ định ra khỏi cấu trúc bảng trong Database
            // Truyền vào một mảng gồm 2 cột ['role', 'avatar'] để xóa chúng đi cùng một lúc
            $table->dropColumn(['role', 'avatar']);
        });
    }
};