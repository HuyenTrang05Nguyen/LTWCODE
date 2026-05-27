<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để định nghĩa các kiểu dữ liệu của cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để tạo mới bảng 'favorites' vào Database.
     */
    public function up(): void
    {
        // Schema::create: Ra lệnh tạo một bảng hoàn toàn mới có tên là 'favorites'
        Schema::create('favorites', function (Blueprint $table) {
            
            // 1. CỘT KHÓA CHÍNH 'id'
            // Tạo cột id tự động tăng, kiểu số nguyên lớn (BigInteger) làm khóa chính cho bảng ghi lưu yêu thích
            $table->id();
            
            // 2. MỐI QUAN HỆ KHÓA NGOẠI: AI LÀ NGƯỜI BẤM THÍCH BÀI VIẾT?
            // foreignId('user_id'): Tạo cột khóa ngoại liên kết sang bảng 'users'
            // constrained(): Ràng buộc logic bắt buộc ID này phải tồn tại thật bên bảng users
            // onDelete('cascade'): Xóa liên hoàn - Nếu tài khoản người dùng bị xóa, toàn bộ danh sách yêu thích của họ cũng tự động bị xóa theo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 3. MỐI QUAN HỆ KHÓA NGOẠI: BÀI VIẾT NÀO ĐƯỢC BẤM THÍCH?
            // Liên kết sang bảng 'posts'. Nếu bài viết cẩm nang bị xóa, các lượt lưu bài viết này cũng tự động biến mất theo để sạch dữ liệu
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // 4. CỘT THỜI GIAN 'timestamps'
            // Tự động sinh ra 2 cột: 'created_at' (ngày giờ bấm thích) và 'updated_at' (ngày giờ cập nhật gần nhất)
            $table->timestamps();
            
            // 5. RÀNG BUỘC DUY NHẤT HỢP THỂ (COMPOSITE UNIQUE INDEX)
            // Đảm bảo cặp giá trị (user_id và post_id) kết hợp lại là DUY NHẤT. 
            // Ngăn chặn việc 1 người dùng bấm Thích trùng lặp 2 lần cho cùng 1 bài viết trong cơ sở dữ liệu.
            $table->unique(['user_id', 'post_id']);
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa sổ bảng này khỏi Database.
     */
    public function down(): void
    {
        // Xóa bảng 'favorites' nếu tồn tại để giải phóng cấu trúc Database
        Schema::dropIfExists('favorites');
    }
};