<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// 1. ĐÁNH DẤU THỜI GIAN: Lưu lại lúc hệ thống bắt đầu chạy để đo tốc độ load trang
define('LARAVEL_START', microtime(true));

// 2. KIỂM TRA BẢO TRÌ: Nếu web đang bật chế độ bảo trì, dừng lại và hiện trang thông báo ngay
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) { 
    require $maintenance;
}

// 3. NẠP THƯ VIỆN (AUTOLOADER): Giúp Laravel tự động nhận diện và gọi được tất cả các file, các Class trong dự án mà không cần include thủ công
require __DIR__.'/../vendor/autoload.php';

// 4. KHỞI ĐỘNG BỘ NÃO (BOOTSTRAP): Nạp các cấu hình cốt lõi để tạo ra thực thể ứng dụng ($app) sẵn sàng làm việc
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. XỬ LÝ YÊU CẦU (REQUEST): 
// - Request::capture(): "Bắt" lấy thông tin người dùng gửi lên (họ vào URL nào, gửi data gì...)
// - handleRequest(): Giao thông tin đó cho Laravel xử lý (chạy qua Route -> Controller) rồi trả về giao diện cho người dùng
$app->handleRequest(Request::capture());