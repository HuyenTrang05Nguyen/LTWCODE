<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo hoặc Cập nhật Tài khoản Admin
        User::updateOrCreate(
            ['email' => 'admin@dulich.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // 2. Tạo hoặc Cập nhật Tài khoản Người dùng thường
        $userNames = ['Nguyễn Văn An', 'Trần Thị Bình', 'Lê Hoàng Cường', 'Phạm Minh Duy', 'Hoàng Thu Hà'];
        foreach ($userNames as $i => $name) {
            User::updateOrCreate(
                ['email' => 'user' . ($i + 1) . '@dulich.com'],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]
            );
        }

        // 3. Tạo Danh mục (Sử dụng firstOrCreate để tránh trùng tên)
        $categoriesData = [
            ['name' => 'Ẩm thực',     'description' => 'Khám phá ẩm thực đặc sắc các vùng miền'],
            ['name' => 'Điểm đến',    'description' => 'Giới thiệu các địa điểm du lịch hấp dẫn'],
            ['name' => 'Checkin',     'description' => 'Những địa điểm checkin sống ảo cực chất'],
            ['name' => 'Kinh nghiệm', 'description' => 'Chia sẻ kinh nghiệm du lịch thực tế từ cộng đồng'],
            ['name' => 'Khách sạn',   'description' => 'Review khách sạn, resort, homestay chất lượng'],
            ['name' => 'Cẩm nang',    'description' => 'Cẩm nang du lịch chi tiết từ A đến Z'],
        ];

        foreach ($categoriesData as $cat) {
            Category::firstOrCreate(
                ['name' => $cat['name']],
                ['description' => $cat['description']]
            );
        }

        // Lấy ID thực tế sau khi tạo để map chính xác vào bài viết
        $catAmThuc   = Category::where('name', 'Ẩm thực')->first()->id;
        $catDiemDen  = Category::where('name', 'Điểm đến')->first()->id;
        $catCheckin  = Category::where('name', 'Checkin')->first()->id;

        // 4. Tạo Bài viết (Dùng firstOrCreate dựa trên Tiêu đề bài viết)
        $postsData = [
            [
                'title' => 'Top 10 điểm đến không thể bỏ qua tại Đà Nẵng',
                'excerpt' => 'Khám phá 10 địa điểm du lịch tuyệt vời nhất tại thành phố đáng sống Đà Nẵng, từ Bà Nà Hills đến bãi biển Mỹ Khê.',
                'content' => '<h2>1. Bà Nà Hills - Cầu Vàng</h2><p>Bà Nà Hills nằm ở độ cao 1.487m...</p>',
                'category_id' => $catDiemDen,
                'location' => 'Đà Nẵng',
                'image' => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=800&q=80',
                'views_count' => 1250,
            ],
            [
                'title' => 'Hướng dẫn du lịch Phú Quốc tự túc 4 ngày 3 đêm',
                'excerpt' => 'Lịch trình chi tiết du lịch Phú Quốc 4N3Đ với chi phí tiết kiệm, bao gồm ăn uống, đi lại và các hoạt động vui chơi.',
                'content' => '<h2>Ngày 1: Khám phá Nam đảo</h2><p>Sáng đến Phú Quốc...</p>',
                'category_id' => $catDiemDen,
                'location' => 'Phú Quốc',
                'image' => 'https://images.unsplash.com/photo-1598090842581-c94b8e1e4bfb?w=800&q=80',
                'views_count' => 980,
            ],
            [
                'title' => 'Đặc sản Huế: 15 món ăn bạn nhất định phải thử',
                'excerpt' => 'Tổng hợp 15 món ăn đặc sản Huế nổi tiếng nhất, từ bún bò Huế đến bánh bèo, bánh nậm, com hến.',
                'content' => '<h2>1. Bún bò Huế</h2><p>Món ăn biểu tượng của Huế...</p>',
                'category_id' => $catAmThuc,
                'location' => 'Huế',
                'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=800&q=80',
                'views_count' => 756,
            ],
            [
                'title' => 'Review top 5 homestay đẹp nhất Đà Lạt 2024',
                'excerpt' => 'Review chi tiết 5 homestay view đẹp, giá hợp lý tại Đà Lạt dành cho các cặp đôi và nhóm bạn.',
                'content' => '<h2>1. The Kupid Homestay</h2><p>Nằm trên đồi thông...</p>',
                'category_id' => $catCheckin,
                'location' => 'Đà Lạt',
                'image' => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=800&q=80',
                'views_count' => 623,
            ],
            [
                'title' => '10 mẹo tiết kiệm chi phí khi đi du lịch',
                'excerpt' => 'Bí kíp du lịch tiết kiệm nhưng vẫn trải nghiệm đầy đủ, từ đặt vé máy bay rẻ đến chọn chỗ ăn ngon giá tốt.',
                'content' => '<h2>1. Đặt vé máy bay trước 2-3 tháng</h2><p>Giá vé rẻ nhất...</p>',
                'category_id' => $catDiemDen,
                'location' => 'Việt Nam',
                'image' => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=800&q=80',
                'views_count' => 1432,
            ],
            [
                'title' => 'Khám phá Sapa mùa lúa chín - Khi nào đi đẹp nhất?',
                'excerpt' => 'Hướng dẫn thời điểm lý tưởng ngắm ruộng bậc thang mùa lúa chín vàng ở Sapa và lịch trình 3 ngày 2 đêm.',
                'content' => '<h2>Thời điểm đẹp nhất</h2><p>Mùa lúa chín ở Sapa...</p>',
                'category_id' => $catCheckin,
                'location' => 'Sapa, Lào Cai',
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=800&q=80',
                'views_count' => 875,
            ],
            [
                'title' => 'Ẩm thực đường phố Sài Gòn: Những quán ăn vỉa hè ngon nhất',
                'excerpt' => 'Tổng hợp những quán ăn vỉa hè nổi tiếng nhất Sài Gòn mà bất kỳ food tour nào cũng phải ghé.',
                'content' => '<h2>1. Phở Hòa Pasteur</h2><p>Quán phở nổi tiếng nhất...</p>',
                'category_id' => $catAmThuc,
                'location' => 'TP. Hồ Chí Minh',
                'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=800&q=80',
                'views_count' => 1100,
            ],
            [
                'title' => 'Cẩm nang du lịch Hạ Long: Tất cả những gì bạn cần biết',
                'excerpt' => 'Hướng dẫn du lịch Hạ Long chi tiết từ A-Z, bao gồm cách đi, chỗ ở, ăn gì, chơi gì, chi phí bao nhiêu.',
                'content' => '<h2>Di chuyển đến Hạ Long</h2><p>Từ Hà Nội có thể đi...</p>',
                'category_id' => $catDiemDen,
                'location' => 'Hạ Long, Quảng Ninh',
                'image' => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=800&q=80',
                'views_count' => 920,
            ],
            [
                'title' => 'Kinh nghiệm leo Tà Năng - Phan Dũng: Cung đường trekking đẹp nhất Việt Nam',
                'excerpt' => 'Chia sẻ kinh nghiệm chinh phục cung trekking Tà Năng - Phan Dũng, lưu ý an toàn và chuẩn bị hành lý.',
                'content' => '<h2>Giới thiệu cung đường</h2><p>Tà Năng - Phan Dũng dài...</p>',
                'category_id' => $catCheckin,
                'location' => 'Lâm Đồng - Bình Thuận',
                'image' => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=800&q=80',
                'views_count' => 670,
            ]
        ];

        foreach ($postsData as $post) {
            Post::firstOrCreate(
                ['title' => $post['title']],
                [
                    'excerpt'     => $post['excerpt'],
                    'content'     => $post['content'],
                    'category_id' => $post['category_id'],
                    'location'    => $post['location'],
                    'image'       => $post['image'],
                    'views_count' => $post['views_count'],
                    'slug'        => \Illuminate\Support\Str::slug($post['title']),
                    'status'      => 'published' // Đảm bảo trạng thái để chatbot quét ra dữ liệu
                ]
            );
        }
    }
}
