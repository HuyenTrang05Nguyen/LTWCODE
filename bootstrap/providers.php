<?php

use App\Providers\AppServiceProvider;

// Trả về mảng danh sách tất cả các Service Providers được kích hoạt trong ứng dụng Laravel
return [
    // Đăng ký AppServiceProvider - Nơi cấu hình toàn cục hệ thống (như phân trang Bootstrap 5, ép link HTTPS...)
    AppServiceProvider::class,
];