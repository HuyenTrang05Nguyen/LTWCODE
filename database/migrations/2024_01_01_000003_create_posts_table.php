<?php

use Illuminate\Database\Migrations\Migration; // Nhúng lớp Migration gốc của framework Laravel
use Illuminate\Database\Schema\Blueprint;    // Nhúng lớp Blueprint để thiết lập các kiểu dữ liệu cho cột
use Illuminate\Support\Facades\Schema;       // Nhúng Facade Schema để điều khiển cấu trúc Database

return new class extends Migration
{
    /**
     * Hàm up(): Chạy khi gõ lệnh "php artisan migrate" để tạo mới bảng 'posts' vào Database.
     */
    public function up(): void
    {
        // Schema::create: Ra lệnh tạo bảng mới có tên là 'posts' (chứa thông tin bài viết)
        Schema::create('posts', function (Blueprint $table) {
            
            // 1. CỘT KHÓA CHÍNH 'id'
            // Tạo cột id tự động tăng, kiểu số nguyên lớn (BigInteger) làm khóa chính cho mỗi bài viết
            $table->id();
            
            // 2. MỐI QUAN HỆ KHÓA NGOẠI: BÀI VIẾT THUỘC VỀ THÀNH VIÊN NÀO (Tác giả)
            // foreignId('user_id'): Tạo cột khóa ngoại liên kết sang bảng 'users'
            // constrained(): Ràng buộc chặt chẽ logic, ID lưu ở đây bắt buộc phải tồn tại bên bảng users
            // onDelete('cascade'): Khóa liên hoàn - Nếu tài khoản User bị xóa, toàn bộ bài viết của User đó sẽ tự động bị xóa sạch theo
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // 3. MỐI QUAN HỆ KHÓA NGOẠI: BÀI VIẾT THUỘC VỀ DANH MỤC NÀO (Điểm đến/Chủ đề)
            // Liên kết sang bảng 'categories'. Nếu danh mục đó bị xóa (Ví dụ: Xóa danh mục Đà Lạt), toàn bộ bài viết thuộc về Đà Lạt tự động bay màu theo nhờ 'cascade'
            $table->foreignId('category_id')->constrained()->onDelete('cascade');
            
            // 4. CỘT TIÊU ĐỀ BÀI VIẾT 'title'
            // Kiểu string (VARCHAR 255): Lưu tiêu đề chính của bài viết cẩm nang du lịch
            $table->string('title');
            
            // 5. CỘT ĐƯỜNG DẪN THÂN THIỆN 'slug'
            // Biến tiêu đề thành chuỗi không dấu, cách nhau bằng gạch ngang để làm URL đẹp (unique: bắt buộc không được trùng nhau)
            $table->string('slug')->unique();
            
            // 6. CỘT TÓM TẮT NGẮN 'excerpt'
            // Kiểu text: Lưu mô tả ngắn khoảng 2-3 câu hiển thị ở trang danh sách bài viết (nullable: có thể để trống không điền)
            $table->text('excerpt')->nullable();
            
            // 7. CỘT NỘI DUNG CHI TIẾT 'content'
            // Kiểu longText: Lưu nội dung cực lớn (lên tới 4GB), thoải mái chứa văn bản bài viết kèm mã HTML, mã chèn ảnh của bộ soạn thảo tin tức
            $table->longText('content');
            
            // 8. CỘT ẢNH BÌA BÀI VIẾT 'image'
            // Kiểu string: Lưu đường dẫn tệp ảnh đại diện hiển thị cho bài viết (nullable: cho phép bài viết không có ảnh bìa)
            $table->string('image')->nullable();
            
            // 9. CỘT TỌA ĐỘ / ĐỊA DANH 'location'
            // Kiểu string: Lưu tên địa danh cụ thể của bài viết (Ví dụ: "Hồ Xuân Hương, Đà Lạt") phục vụ tìm kiếm địa lý
            $table->string('location')->nullable();
            
            // 10. CỘT ĐẾM LƯỢT XEM 'views_count'
            // unsignedInteger: Kiểu số nguyên dương (không có dấu âm) để đếm lượt xem bài viết, giá trị mặc định lúc tạo bài là 0 lượt xem
            $table->unsignedInteger('views_count')->default(0);
            
            // 11. CỘT TRẠNG THÁI BÀI VIẾT 'status'
            // Kiểu ENUM: Chỉ nhận 1 trong 2 trạng thái là 'draft' (bản nháp) hoặc 'published' (xuất bản công khai). Mặc định là 'published'
            $table->enum('status', ['draft', 'published'])->default('published');
            
            // 12. CỘT THỜI GIAN 'timestamps'
            // Tự động sinh ra 2 cột 'created_at' (ngày giờ đăng bài) và 'updated_at' (ngày giờ sửa bài gần nhất)
            $table->timestamps();
        });
    }

    /**
     * Hàm down(): Chạy khi gõ lệnh phục hồi "php artisan migrate:rollback" để xóa sổ hoàn toàn bảng này.
     */
    public function down(): void
    {
        // Xóa bảng 'posts' nếu tồn tại để giải phóng cấu trúc Database
        Schema::dropIfExists('posts');
    }
};