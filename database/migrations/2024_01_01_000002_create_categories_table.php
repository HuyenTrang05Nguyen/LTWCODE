<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để thiết lập các kiểu dữ liệu cho cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để tạo mới bảng 'categories' vào Database.
     */
    public function up(): void
    {
        // Schema::create: Ra lệnh tạo một bảng hoàn toàn mới có tên là 'categories'
        Schema::create('categories', function (Blueprint $table) {
            
            // 1. CỘT KHÓA CHÍNH 'id'
            // Tự động tạo cột 'id' kiểu số nguyên lớn (BigInteger), tự động tăng (Auto-increment) và gán làm Khóa chính
            $table->id();
            
            // 2. CỘT TÊN DANH MỤC 'name'
            // Kiểu string (VARCHAR 255 ký tự): Lưu tên của danh mục du lịch (Ví dụ: Cẩm nang ẩm thực, Địa điểm Hot...)
            $table->string('name');
            
            // 3. CỘT ĐƯỜNG DẪN THÂN THIỆN 'slug'
            // Kiểu string: Lưu chuỗi chữ thường không dấu nối nhau bằng gạch ngang phục vụ đường dẫn URL đẹp (Ví dụ: cam-nang-am-thuc)
            // unique(): Đặt ràng buộc DUY NHẤT, không được phép trùng lặp slug giữa các danh mục để tránh lỗi định tuyến URL
            $table->string('slug')->unique();
            
            // 4. CỘT MÔ TẢ 'description'
            // Kiểu text: Lưu văn bản dài giới thiệu tóm tắt về danh mục này
            // nullable(): Cho phép để trống (null) nếu lúc tạo danh mục chưa kịp viết mô tả
            $table->text('description')->nullable();
            
            // 5. CỘT ẢNH ĐẠI DIỆN DANH MỤC 'image'
            // Kiểu string: Lưu chuỗi đường dẫn tệp tin ảnh minh họa của danh mục (Ví dụ: uploads/categories/dalat.jpg)
            // nullable(): Cho phép để trống nếu danh mục này không dùng ảnh đại diện
            $table->string('image')->nullable();
            
            // 6. CỘT MỐC THỜI GIAN TRUY VẾT 'timestamps'
            // Tự động sinh ra 2 cột: 'created_at' (ngày giờ tạo) và 'updated_at' (ngày giờ cập nhật gần nhất) của bản ghi
            $table->timestamps();
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa sổ bảng này khỏi Database.
     */
    public function down(): void
    {
        // dropIfExists: Kiểm tra nếu bảng 'categories' đang tồn tại thì xóa sạch hoàn toàn bảng này và dữ liệu bên trong
        Schema::dropIfExists('categories');
    }
};