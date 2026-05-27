<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để định nghĩa các kiểu dữ liệu của cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để tạo mới bảng 'ratings' vào Database.
     */
    public function up(): void
    {
        // Schema::create: Ra lệnh tạo một bảng hoàn toàn mới có tên là 'ratings'
        Schema::create('ratings', function (Blueprint $table) {
            
            // 1. CỘT KHÓA CHÍNH 'id'
            // Tạo cột id tự động tăng, kiểu số nguyên lớn (BigInteger) làm khóa chính cho bảng ghi đánh giá
            $table->id();
            
            // 2. MỐI QUAN HỆ KHÓA NGOẠI: AI LÀ NGƯỜI ĐÁNH GIÁ?
            // foreignId('user_id'): Tạo cột khóa ngoại liên kết sang bảng 'users'
            // constrained(): Ràng buộc logic bắt buộc ID này phải tồn tại thật bên bảng users
            // onDelete('cascade'): Xóa liên hoàn - Nếu tài khoản người dùng bị xóa, toàn bộ đánh giá của họ cũng tự động bị xóa theo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 3. MỐI QUAN HỆ KHÓA NGOẠI: BÀI VIẾT NÀO ĐƯỢC ĐÁNH GIÁ?
            // Liên kết sang bảng 'posts'. Nếu bài viết cẩm nang bị xóa, các lượt chấm điểm của bài viết này cũng tự động bị xóa sạch theo
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // 4. CỘT ĐIỂM SỐ ĐÁNH GIÁ 'score'
            // tinyInteger: Kiểu số nguyên siêu nhỏ (chỉ tốn 1 byte lưu trữ), cực kỳ tối ưu để lưu số sao (Ví dụ: từ 1 đến 5 sao)
            // unsigned(): Ép buộc chỉ nhận số nguyên dương, loại bỏ hoàn toàn các giá trị âm (không thể đánh giá âm sao)
            $table->tinyInteger('score')->unsigned();
            
            // 5. CỘT THỜI GIAN 'timestamps'
            // Tự động sinh ra 2 cột: 'created_at' (ngày giờ đánh giá) và 'updated_at' (ngày giờ sửa điểm số gần nhất)
            $table->timestamps();
            
            // 6. RÀNG BUỘC DUY NHẤT HỢP THỂ (COMPOSITE UNIQUE INDEX)
            // Đảm bảo cặp giá trị (user_id và post_id) kết hợp lại là DUY NHẤT.
            // Quy định mỗi người dùng chỉ được phép đánh giá điểm số DUY NHẤT 1 LẦN cho cùng 1 bài viết.
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa sổ bảng này khỏi Database.
     */
    public function down(): void
    {
        // Xóa bảng 'ratings' nếu tồn tại để giải phóng cấu trúc Database
        Schema::dropIfExists('ratings');
    }
};