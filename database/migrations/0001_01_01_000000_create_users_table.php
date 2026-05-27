<?php

// NẠP CÁC THƯ VIỆN HẠ TẦNG CỦA LARAVEL ĐỂ THAO TÁC SCHEMA DATABASE
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ý NGHĨA HỆ THỐNG:
 * Đây là một "Khởi tạo di trú" (Migration File) dưới dạng lớp ẩn danh kế thừa từ lớp Migration gốc.
 * Nó đóng vai trò như hệ thống Quản lý phiên bản (Version Control) cho cấu trúc Database của Website.
 * Giúp lập trình viên định nghĩa các bảng dữ liệu bằng mã PHP thuần mà không cần viết lệnh SQL thô,
 * đảm bảo hệ thống có thể chạy đồng bộ trên mọi loại hệ quản trị CSDL (MySQL, PostgreSQL, SQL Server...).
 */
return new class extends Migration
{
    /**
     * HÀM UP(): THỰC THI DI TRÚ (Chạy lệnh tạo bảng khi dùng lệnh `php artisan migrate`)
     */
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // BẢNG 1: USERS - LƯU TRỮ THÔNG TIN TÀI KHOẢN THÀNH VIÊN & ADMIN
        // ---------------------------------------------------------------------
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Khởi tạo cột 'id': Khóa chính (Primary Key), kiểu số nguyên lớn tự động tăng (BigIncrement)
            $table->string('name'); // Cột 'name': Lưu họ tên người dùng, kiểu chuỗi ký tự (VARCHAR)
            
            // Cột 'email': Lưu địa chỉ email đăng nhập, kiểu chuỗi ký tự. 
            // Ràng buộc duy nhất (unique), cấm tuyệt đối tình trạng 2 tài khoản trùng email nhau trong Database.
            $table->string('email')->unique(); 
            
            // Cột 'email_verified_at': Lưu mốc thời gian xác thực Email. 
            // Cho phép rỗng (nullable) vì khi mới đăng ký tài khoản thành viên chưa kịp kích hoạt email.
            $table->timestamp('email_verified_at')->nullable(); 
            
            $table->string('password'); // Cột 'password': Lưu chuỗi mật khẩu đã được HASH mã hóa một chiều
            $table->rememberToken(); // Tự động tạo cột 'remember_token' (VARCHAR 100) phục vụ tính năng "Ghi nhớ đăng nhập"
            $table->timestamps(); // Tự động sinh ra 2 cột: 'created_at' (Ngày tạo) và 'updated_at' (Ngày cập nhật mới nhất)
        });

        // ---------------------------------------------------------------------
        // BẢNG 2: PASSWORD_RESET_TOKENS - PHỤC VỤ LUỒNG 3 TRONG BÁO CÁO (QUÊN MẬT KHẨU)
        // ---------------------------------------------------------------------
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            // Cột 'email': Lưu email yêu cầu cấp lại mật khẩu. 
            // Thiết lập làm Khóa chính (primary) để đảm bảo tại một thời điểm chỉ có duy nhất 1 yêu cầu cấp lại trên 1 email.
            $table->string('email')->primary(); 
            $table->string('token'); // Cột 'token': Lưu mã bảo mật ngẫu nhiên được gửi kèm vào link trong Email người dùng
            $table->timestamp('created_at')->nullable(); // Lưu mốc thời gian tạo mã Token để tính thời gian hết hạn (ví dụ sau 60 phút)
        });

        // ---------------------------------------------------------------------
        // BẢNG 3: SESSIONS - QUẢN LÝ PHIÊN LÀM VIỆC AN TOÀN TRÊN HỆ THỐNG
        // ---------------------------------------------------------------------
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary(); // Cột 'id': Lưu chuỗi mã Session ngẫu nhiên duy nhất, làm Khóa chính.
            
            // Cột 'user_id': Liên kết đến ID của bảng users để biết session này thuộc về ai.
            // Đặt nullable() vì nếu là khách vãng lai chưa đăng nhập thì ID sẽ bằng Rỗng.
            // Đặt index() để đánh chỉ mục, giúp database truy vấn nhận diện người dùng nhanh hơn gấp nhiều lần.
            $table->foreignId('user_id')->nullable()->index(); 
            
            // Cột 'ip_address': Lưu địa chỉ IP mạng của người dùng. Độ dài tối đa 45 ký tự để hỗ trợ toàn diện cho cả IPv4 và IPv6.
            $table->string('ip_address', 45)->nullable(); 
            $table->text('user_agent')->nullable(); // Cột 'user_agent': Lưu thông tin cấu hình Trình duyệt và Thiết bị của khách truy cập.
            $table->longText('payload'); // Cột 'payload': Kiểu văn bản cực lớn để lưu trữ toàn bộ biến môi trường đã được mã hóa của phiên làm việc.
            
            // Cột 'last_activity': Lưu mốc thời gian Unix timestamp tương tác cuối cùng của Session.
            // Đặt index() để hệ thống dọn dẹp (Garbage Collection) các session hết hạn/bỏ hoang một cách thần tốc.
            $table->integer('last_activity')->index(); 
        });
    }

    /**
     * HÀM DOWN(): HỦY BỎ DI TRÚ (Chạy lệnh xóa bảng khi dùng lệnh `php artisan migrate:rollback` hoặc `migrate:refresh`)
     */
    public function down(): void
    {
        // Chạy lệnh xóa các bảng theo cơ chế "Nếu bảng tồn tại thì mới xóa" để ngăn chặn lỗi hệ thống
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};