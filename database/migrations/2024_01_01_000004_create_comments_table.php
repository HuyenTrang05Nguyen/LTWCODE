<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để định nghĩa các kiểu dữ liệu của cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để tạo mới bảng 'comments' vào Database.
     */
    public function up(): void
    {
        // Schema::create: Ra lệnh tạo một bảng hoàn toàn mới có tên là 'comments'
        Schema::create('comments', function (Blueprint $table) {
            
            // 1. CỘT KHÓA CHÍNH 'id'
            // Tạo cột id tự động tăng, kiểu số nguyên lớn (BigInteger) làm khóa chính cho mỗi bình luận
            $table->id();
            
            // 2. MỐI QUAN HỆ KHÓA NGOẠI: AI LÀ NGƯỜI BÌNH LUẬN?
            // foreignId('user_id'): Tạo cột khóa ngoại liên kết sang bảng 'users'
            // constrained(): Ràng buộc logic bắt buộc ID này phải tồn tại thật bên bảng users
            // onDelete('cascade'): Xóa liên hoàn - Nếu tài khoản người dùng bị xóa, toàn bộ bình luận của họ cũng tự động bị xóa sạch theo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 3. MỐI QUAN HỆ KHÓA NGOẠI: BÌNH LUẬN NÀY NẰM Ở BÀI VIẾT NÀO?
            // Liên kết sang bảng 'posts'. Nếu bài viết cẩm nang bị xóa, toàn bộ các bình luận dưới bài viết đó cũng tự động bay màu theo để tránh rác cơ sở dữ liệu
            $table->foreignId('post_id')->constrained()->onDelete('cascade');
            
            // 4. CỘT NỘI DUNG BÌNH LUẬN 'content'
            // Kiểu text: Chứa chuỗi văn bản dài, thoải mái cho người dùng viết đánh giá, nhận xét về chuyến đi
            $table->text('content');
            
            // 5. CỘT TRẠNG THÁI KIỂM DUYỆT 'is_approved'
            // Kiểu boolean: Chỉ nhận giá trị đúng/sai (1 hoặc 0 / true hoặc false)
            // default(true): Mặc định khi người dùng bấm gửi bình luận thì hệ thống cho phép hiển thị công khai ngay lập tức (true)
            $table->boolean('is_approved')->default(true);
            
            // 6. CỘT THỜI GIAN 'timestamps'
            // Tự động sinh ra 2 cột: 'created_at' (ngày giờ viết bình luận) và 'updated_at' (ngày giờ chỉnh sửa gần nhất)
            $table->timestamps();
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa sổ bảng này khỏi Database.
     */
    public function down(): void
    {
        // Xóa bảng 'comments' nếu tồn tại để giải phóng cấu trúc Database
        Schema::dropIfExists('comments');
    }
};