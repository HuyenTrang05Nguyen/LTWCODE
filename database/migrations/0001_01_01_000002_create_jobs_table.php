<?php

// NẠP CÁC THƯ VIỆN HẠ TẦNG CỦA LARAVEL ĐỂ THAO TÁC CẤU TRÚC DATABASE
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Ý NGHĨA KIẾN TRÚC:
 * Đây là file cấu hình khởi tạo hệ thống Hàng đợi bằng Database (Database Queue Driver).
 * Giúp giải quyết bài toán nghẽn mạng (Bottleneck) bằng cách chuyển các tác vụ tốn thời gian 
 * (như gửi email khôi phục mật khẩu, xử lý ảnh, quét dữ liệu) xuống chạy ngầm dưới nền Server.
 */
return new class extends Migration
{
    /**
     * HÀM UP(): THỰC THI DI TRÚ (Tạo các bảng hệ thống hàng đợi)
     */
    public function up(): void
    {
        // ---------------------------------------------------------------------
        // BẢNG 1: JOBS - LƯU TRỮ CÁC TÁC VỤ ĐANG XẾP HÀNG CHỜ XỬ LÝ NGẦM
        // ---------------------------------------------------------------------
        Schema::create('jobs', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng, định danh cho từng công việc gửi vào hàng đợi
            
            // Cột 'queue': Tên của nhánh hàng đợi (ví dụ: 'default', 'emails').
            // Đặt index() để đánh chỉ mục, giúp Worker của Laravel tìm kiếm và bốc tác vụ ra chạy nhanh nhất.
            $table->string('queue')->index(); 
            
            // Cột 'payload': Lưu trữ toàn bộ thông tin mã hóa (Serialized) của Job dưới dạng chuỗi văn bản cực lớn.
            // Chứa thông tin về Class xử lý, tham số truyền vào (ví dụ: ID của người nhận email).
            $table->longText('payload'); 
            
            // Cột 'attempts': Số lần hệ thống đã thử chạy lại tác vụ này nếu xảy ra lỗi (kiểu số nguyên siêu nhỏ, không âm).
            $table->unsignedTinyInteger('attempts'); 
            
            // Cột 'reserved_at': Đánh dấu mốc thời gian Unix khi có một Worker bốc Job này lên xử lý (Locking mechanism),
            // nhằm ngăn chặn tình trạng 2 Worker cùng chạy trùng 1 công việc tại một thời điểm. Cho phép rỗng (nullable).
            $table->unsignedInteger('reserved_at')->nullable(); 
            
            $table->unsignedInteger('available_at'); // Mốc thời gian công việc này được phép bắt đầu chạy (Dùng khi muốn hẹn giờ gửi mail/tác vụ)
            $table->unsignedInteger('created_at'); // Mốc thời gian tác vụ này được tạo ra và đẩy vào hàng đợi
        });

        // ---------------------------------------------------------------------
        // BẢNG 2: JOB_BATCHES - QUẢN LÝ CÁC NHÓM CÔNG VIỆC CHẠY SONG SONG (BATCHING)
        // ---------------------------------------------------------------------
        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary(); // Mã ID ngẫu nhiên của nhóm (Chuỗi kí tự), đặt làm Khóa chính
            $table->string('name'); // Tên định danh của nhóm các công việc (ví dụ: 'Gửi Email Bản Tin')
            $table->integer('total_jobs'); // Tổng số lượng công việc con nằm trong nhóm này
            $table->integer('pending_jobs'); // Số lượng công việc con còn lại đang chờ xử lý
            $table->integer('failed_jobs'); // Số lượng công việc con đã bị thất bại trong nhóm
            $table->longText('failed_job_ids'); // Danh sách mảng các ID của các công việc bị lỗi để Admin dễ theo dõi
            $table->mediumText('options')->nullable(); // Lưu cấu hình tùy chọn mở rộng khi chạy Batch (dạng văn bản vừa)
            $table->integer('cancelled_at')->nullable(); // Mốc thời gian nếu Admin chủ động ấn Hủy bỏ không cho cụm tác vụ này chạy tiếp
            $table->integer('created_at'); // Mốc thời gian khởi tạo nhóm công việc
            $table->integer('finished_at')->nullable(); // Mốc thời gian hoàn thành toàn bộ các công việc trong nhóm
        });

        // ---------------------------------------------------------------------
        // BẢNG 3: FAILED_JOBS - HỘP ĐEN LƯU TRỮ LỖI (LOGGING) KHI CÔNG VIỆC THẤT BẠI
        // ---------------------------------------------------------------------
        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id(); // Khóa chính tự tăng của bảng log lỗi
            
            // Cột 'uuid': Mã định danh chuỗi duy nhất trên toàn cầu (Universal Unique Identifier),
            // Đặt ràng buộc unique() để phục vụ lệnh chạy lại tác vụ lỗi một cách chính xác (`php artisan queue:retry {uuid}`).
            $table->string('uuid')->unique(); 
            
            $table->text('connection'); // Lưu tên kết nối hàng đợi đang dùng (ví dụ: 'database' hoặc 'redis')
            $table->text('queue'); // Lưu tên nhánh hàng đợi xảy ra lỗi
            $table->longText('payload'); // Lưu lại toàn bộ dữ liệu gốc của tác vụ bị hỏng tại thời điểm đó
            $table->longText('exception'); // Cột quan trọng nhất: Lưu chi tiết mã lỗi và thông báo sập hệ thống (StackTrace) để lập trình viên sửa lỗi
            $table->timestamp('failed_at')->useCurrent(); // Tự động ghi nhận mốc thời gian tác vụ bị sập bằng thời gian thực của Server
        });
    }

    /**
     * HÀM DOWN(): HỦY BỎ DI TRÚ (Xóa sạch các bảng khi rollback)
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('failed_jobs');
    }
};