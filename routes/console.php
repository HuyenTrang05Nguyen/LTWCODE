<?php

// Import class Inspiring - chứa danh sách các câu danh ngôn/châm ngôn có sẵn của Laravel
use Illuminate\Foundation\Inspiring;
// Import Facade Artisan - dùng để định nghĩa hoặc tương tác với các lệnh command line
use Illuminate\Support\Facades\Artisan;

/**
 * Định nghĩa một Artisan Command mới bằng Closure (Closure Command)
 * - Tên lệnh kích hoạt trên Terminal: php artisan inspire
 */
Artisan::command('inspire', function () {
    
    // $this->comment() dùng để in ra text có màu vàng (với định dạng comment) trên Terminal
    // Inspiring::quote() sẽ lấy ngẫu nhiên một câu châm ngôn từ thư viện của Laravel
    $this->comment(Inspiring::quote());

})->purpose('Display an inspiring quote'); // purpose(): Định nghĩa mô tả cho lệnh này khi bạn gõ "php artisan list"