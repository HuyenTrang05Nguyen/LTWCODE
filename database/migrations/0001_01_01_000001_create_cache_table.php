<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để định nghĩa các kiểu dữ liệu của cột trong bảng
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database (Tạo, sửa, xóa bảng)

return new class extends Migration
{
    /**
     * Hàm up(): Tự động chạy khi gõ lệnh "php artisan migrate" để khởi tạo các bảng vào Database.
     */
    public function up(): void
    {
        // 1. KHỞI TẠO BẢNG 'cache': Dùng để lưu trữ dữ liệu đệm (Ví dụ: số lượt xem, danh mục tải chậm...)
        Schema::create('cache', function (Blueprint $table) {
            // Định nghĩa cột 'key' kiểu chuỗi và gán làm Khóa chính (Primary Key). Dùng để định danh duy nhất cho dữ liệu cache.
            $table->string('key')->primary();
            
            // Định nghĩa cột 'value' kiểu văn bản trung bình (chứa được khoảng 16MB dữ liệu), dùng để lưu trữ nội dung dữ liệu được cache.
            $table->mediumText('value');
            
            // Định nghĩa cột 'expiration' kiểu số nguyên để lưu mốc thời gian hết hạn (Timestamp), đồng thời đánh chỉ mục (Index) giúp tìm kiếm, xóa dữ liệu hết hạn siêu nhanh.
            $table->integer('expiration')->index();
        });

        // 2. KHỞI TẠO BẢNG 'cache_locks': Dùng để xử lý cơ chế "Khóa đồng bộ dữ liệu" (Tránh việc nhiều luồng xử lý cùng ghi đè 1 dữ liệu tại 1 thời điểm)
        Schema::create('cache_locks', function (Blueprint $table) {
            // Định nghĩa cột 'key' kiểu chuỗi và gán làm Khóa chính (Primary Key) để quản lý tên của lệnh khóa.
            $table->string('key')->primary();
            
            // Định nghĩa cột 'owner' kiểu chuỗi để lưu vết danh tính/tiến trình đang nắm giữ chiếc khóa này (Token của Job hoặc Request).
            $table->string('owner');
            
            // Định nghĩa cột 'expiration' kiểu số nguyên lưu thời gian khóa tự động mở, có đánh chỉ mục (Index) để tối ưu hóa truy vấn kiểm tra thời hạn.
            $table->integer('expiration')->index();
        });
    }

    /**
     * Hàm down(): Tự động chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa bỏ các bảng khỏi Database.
     */
    public function down(): void
    {
        Schema::dropIfExists('cache');       // Xóa bảng 'cache' nếu bảng này đang tồn tại trong hệ thống
        Schema::dropIfExists('cache_locks'); // Xóa bảng 'cache_locks' nếu bảng này đang tồn tại trong hệ thống
    }
};