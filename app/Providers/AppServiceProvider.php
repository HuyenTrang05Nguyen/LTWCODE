<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
// Khai báo Facade URL để cấu hình và thao tác với các đường dẫn định tuyến trong ứng dụng
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Đăng ký các dịch vụ bổ sung vào Container của ứng dụng (Chạy đầu tiên)
     */
    public function register(): void
    {
        //
    }

    /**
     * Khởi động các dịch vụ (Bootstrap) sau khi tất cả các Service khác đã được đăng ký xong
     */
    public function boot(): void
    {
        // Cấu hình Laravel sử dụng giao diện của Bootstrap 5 làm giao diện phân trang mặc định thay vì Tailwind CSS
        \Illuminate\Pagination\Paginator::useBootstrapFive();

        // Xử lý ép cấu hình giao thức bảo mật HTTPS khi chạy dự án thực tế trên Server (Deploy)
        // Điều kiện: Nếu môi trường chạy (app.env) là 'production' HOẶC nhận diện được Header chuyển tiếp bảo mật từ Proxy (Cloudflare, Load Balancer...) là HTTPS
        if (config('app.env') === 'production' || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            // Ép tất cả các đường dẫn (URL), tài nguyên (CSS, JS, Ảnh) và Form trong hệ thống luôn tự động sinh ra dưới dạng link bảo mật https://
            URL::forceScheme('https');
        }
    }
}