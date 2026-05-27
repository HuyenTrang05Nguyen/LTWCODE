<?php
use Illuminate\Database\Migrations\Migration;   // 📌 Import class cha — mọi migration đều phải kế thừa class này
use Illuminate\Database\Schema\Blueprint;        // 📌 Blueprint = "bản vẽ" cấu trúc bảng, dùng để định nghĩa các cột
use Illuminate\Support\Facades\Schema;           // 📌 Facade Schema = công cụ tạo/xóa bảng trong database

return new class extends Migration               // 📌 Dùng anonymous class (PHP 8+), không cần đặt tên class
{
    public function up(): void                   // 📌 up() = chạy khi migrate (tạo bảng). Ngược lại là down()
    {
        Schema::create('users', function (Blueprint $table) {  // 📌 Tạo bảng tên 'users'
            $table->id();                        // 📌 Tạo cột 'id' kiểu BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->string('name');              // 📌 Cột 'name' kiểu VARCHAR(255) — lưu họ tên người dùng
            $table->string('email')->unique();   // 📌 Cột 'email' VARCHAR(255) + ràng buộc UNIQUE (không được trùng)
            $table->timestamp('email_verified_at')->nullable(); // 📌 Lưu thời điểm xác thực email, nullable = có thể NULL (chưa xác thực)
            $table->string('password');          // 📌 Lưu mật khẩu đã mã hóa bcrypt (KHÔNG bao giờ lưu plain text)
            $table->rememberToken();             // 📌 Tạo cột 'remember_token' VARCHAR(100) — dùng cho tính năng "Ghi nhớ đăng nhập"
            $table->timestamps();               // 📌 Tự động tạo 2 cột: created_at và updated_at kiểu TIMESTAMP
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) { // 📌 Bảng lưu token đặt lại mật khẩu
            $table->string('email')->primary();  // 📌 Email làm khóa chính (PRIMARY KEY) — mỗi email chỉ có 1 token
            $table->string('token');             // 📌 Chuỗi token bảo mật ngẫu nhiên gửi qua email
            $table->timestamp('created_at')->nullable(); // 📌 Thời điểm tạo token — dùng để kiểm tra token có hết hạn chưa
        });

        Schema::create('sessions', function (Blueprint $table) { // 📌 Bảng lưu phiên đăng nhập (nếu dùng database session driver)
            $table->string('id')->primary();     // 📌 Session ID dạng chuỗi ngẫu nhiên làm khóa chính
            $table->foreignId('user_id')->nullable()->index(); // 📌 Liên kết user (nullable vì khách vãng lai cũng có session)
            $table->string('ip_address', 45)->nullable(); // 📌 Lưu IP người dùng (45 ký tự để hỗ trợ cả IPv6)
            $table->text('user_agent')->nullable(); // 📌 Lưu thông tin trình duyệt (Chrome, Firefox...)
            $table->longText('payload');         // 📌 Dữ liệu session được serialize (mã hóa base64)
            $table->integer('last_activity')->index(); // 📌 Unix timestamp lần hoạt động cuối — dùng để dọn session cũ
        });
    }

    public function down(): void                 // 📌 down() = chạy khi rollback (xóa bảng). Thứ tự xóa ngược với tạo
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
