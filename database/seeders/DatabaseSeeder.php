<?php

namespace Database\Seeders;

use App\Models\User;        // Nhúng Model User để tương tác với bảng 'users'
use App\Models\Category;    // Nhúng Model Category để tương tác với bảng 'categories'
use App\Models\Post;        // Nhúng Model Post để tương tác với bảng 'posts'
use App\Models\Comment;     // Nhúng Model Comment để tương tác với bảng 'comments'
use App\Models\Rating;      // Nhúng Model Rating để tương tác với bảng 'ratings'
use App\Models\Favorite;    // Nhúng Model Favorite để tương tác với bảng 'favorites'
use Illuminate\Database\Seeder; // Lớp Seeder gốc của Laravel
use Illuminate\Support\Facades\Hash; // Thư viện mã hóa mật khẩu
use Illuminate\Support\Facades\DB;   // Facade DB để chạy các câu lệnh SQL thuần
use Illuminate\Support\Str;          // Thư viện xử lý chuỗi (Helper)

class DatabaseSeeder extends Seeder
{
    /**
     * Hàm run(): Tự động thực thi toàn bộ logic đổ dữ liệu mẫu khi gõ "php artisan db:seed"
     */
    public function run(): void
    {
        // 1. DỌN DẸP VÀ LÀM SẠCH DATABASE TRƯỚC KHI NẠP DỮ LIỆU
        
        // Tắt cơ chế kiểm tra khóa ngoại để tránh việc hệ thống báo lỗi không cho xóa bảng vì đang có bảng khác liên kết
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate(): Lệnh dọn sạch bách dữ liệu cũ trong các bảng, đồng thời reset cột ID tự tăng quay về số 1
        Rating::truncate();
        Favorite::truncate();
        Comment::truncate();
        Post::truncate();
        Category::truncate();
        User::truncate();
        
        // Bật lại cơ chế kiểm tra khóa ngoại sau khi đã dọn dẹp xong để đảm bảo an toàn logic dữ liệu
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // ===================== KHỞI TẠO TÀI KHOẢN (USERS) =====================
        
        // Tạo duy nhất 1 tài khoản tối cao Admin để kiểm thử tính năng của quản trị viên
        $admin = User::create([
            'name'     => 'Admin',
            'email'    => 'admin@dulich.com',
            'password' => Hash::make('password'),   // Mã hóa bảo mật mật khẩu bằng thuật toán Bcrypt một chiều
            'role'     => 'admin',                  // Phân quyền 'admin'
        ]);

        // Tạo danh sách 7 người dùng thông thường bằng cơ chế Collection của Laravel
        $users = collect([
            ['name' => 'Nguyễn Văn An',   'email' => 'user1@dulich.com'],
            ['name' => 'Trần Thị Bình',   'email' => 'user2@dulich.com'],
            ['name' => 'Lê Hoàng Cường',  'email' => 'user3@dulich.com'],
            ['name' => 'Phạm Minh Duy',   'email' => 'user4@dulich.com'],
            ['name' => 'Hoàng Thu Hà',    'email' => 'user5@dulich.com'],
            ['name' => 'Vũ Thị Lan',      'email' => 'user6@dulich.com'],
            ['name' => 'Đặng Quốc Tuấn',  'email' => 'user7@dulich.com'],
        ])->map(fn($u) => User::create([ // Dùng hàm map() vòng lặp qua từng phần tử để tạo bản ghi hàng loạt
            'name'     => $u['name'],
            'email'    => $u['email'],
            'password' => Hash::make('password'), // Tất cả tài khoản dùng chung mật khẩu 'password' để tiện đăng nhập test
            'role'     => 'user',                 // Phân quyền 'user' thường
        ]));
        // ===================== CATEGORIES =====================
        $catsData = [
            ['name' => 'Ẩm thực',     'description' => 'Khám phá ẩm thực đặc sắc các vùng miền Việt Nam và thế giới'],
            ['name' => 'Điểm đến',    'description' => 'Giới thiệu các địa điểm du lịch hấp dẫn trong và ngoài nước'],
            ['name' => 'Checkin',     'description' => 'Những địa điểm checkin sống ảo cực chất, góc chụp đẹp'],
            ['name' => 'Kinh nghiệm', 'description' => 'Chia sẻ kinh nghiệm du lịch thực tế từ cộng đồng'],
            ['name' => 'Khách sạn',   'description' => 'Review khách sạn, resort, homestay chất lượng'],
            ['name' => 'Cẩm nang',    'description' => 'Cẩm nang du lịch chi tiết từ A đến Z'],
        ];
        // Khởi tạo một mảng rỗng để lưu trữ danh sách các Object danh mục sau khi tạo thành công
        $cats = [];
        
        // Vòng lặp foreach: Duyệt qua mảng dữ liệu mẫu $catsData (mảng chứa tên và mô tả danh mục)
        foreach ($catsData as $c) {
            
            // Category::create($c): Ghi mới danh mục vào Database dựa trên mảng con $c
            // $cats[$c['name']] = ...: Lưu Object danh mục vừa tạo vào mảng $cats với "Key" chính là tên danh mục.
            // (Mục đích: Để lát nữa dễ dàng lấy nhanh ID danh mục bằng cú pháp $cats['Tên danh mục']->id mà không cần truy vấn lại DB)
            $cats[$c['name']] = Category::create($c);
        }

        // $users->prepend($admin): Sử dụng phương thức prepend() của Laravel Collection 
        // để chèn tài khoản $admin vào vị trí ĐẦU TIÊN của danh sách người dùng.
        // Kết quả: Biến $allUsers sẽ chứa đầy đủ tất cả tài khoản hệ thống bao gồm 1 Admin ở đầu và 7 User ở sau.
        $allUsers = $users->prepend($admin);
        // ===================== POSTS DATA =====================
        $postsData = [

            // ==================== ĐIỂM ĐẾN (4 bài) ====================
            [
                'title'      => 'Top 10 điểm đến không thể bỏ qua tại Đà Nẵng',
                'category'   => 'Điểm đến',
                'location'   => 'Đà Nẵng',
                'image'      => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=1200&q=85',
                'views'      => 3420,
                'user_index' => 0,
                'excerpt'    => 'Khám phá 10 địa điểm du lịch tuyệt vời nhất tại thành phố đáng sống Đà Nẵng, từ Bà Nà Hills đến bãi biển Mỹ Khê.',
                'content'    => '
<h2>Đà Nẵng – Thành phố đáng sống nhất Việt Nam</h2>
<p>Đà Nẵng không chỉ nổi tiếng với những bãi biển xanh trong vắt mà còn sở hữu hàng loạt điểm tham quan độc đáo, ẩm thực phong phú và con người thân thiện. Dưới đây là 10 địa điểm bạn nhất định phải ghé khi đến thành phố biển này.</p>

<h3>1. Bà Nà Hills – Cầu Vàng huyền thoại</h3>
<p>Nằm ở độ cao 1.487m so với mực nước biển, Bà Nà Hills là khu du lịch phức hợp nổi tiếng nhất miền Trung. Cầu Vàng với hai bàn tay khổng lồ đỡ cây cầu đã trở thành biểu tượng du lịch Việt Nam, thu hút hàng triệu lượt khách mỗi năm.</p>
<img src="https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=900&q=80" alt="Cầu Vàng Bà Nà Hills" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><strong>Giờ mở cửa:</strong> 7:00 – 22:00 hàng ngày. <strong>Giá vé:</strong> 750.000đ/người (bao gồm cáp treo)</p>

<h3>2. Bãi biển Mỹ Khê – Top 6 bãi biển đẹp nhất hành tinh</h3>
<p>Được tạp chí Forbes bình chọn là một trong 6 bãi biển quyến rũ nhất hành tinh, Mỹ Khê trải dài hơn 30km với cát trắng mịn, nước biển trong xanh. Đây là thiên đường cho các hoạt động lướt sóng, dù lượn và tắm biển.</p>
<img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&q=80" alt="Bãi biển Mỹ Khê" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Ngũ Hành Sơn – Núi đá huyền bí</h3>
<p>Quần thể 5 ngọn núi đá cẩm thạch mang tên Kim, Mộc, Thủy, Hỏa, Thổ ẩn chứa nhiều hang động, chùa chiền linh thiêng. Đặc biệt, làng nghề điêu khắc đá Non Nước ngay dưới chân núi là nơi bạn có thể mua những món quà lưu niệm độc đáo.</p>

<h3>4. Bán đảo Sơn Trà – Lá phổi xanh của thành phố</h3>
<p>Rừng nguyên sinh Sơn Trà là nơi sinh sống của loài voọc chà vá chân nâu quý hiếm. Từ đỉnh Bàn Cờ, bạn có thể ngắm toàn cảnh thành phố Đà Nẵng và vịnh Đà Nẵng tuyệt đẹp.</p>
<img src="https://images.unsplash.com/photo-1528127269322-539801943592?w=900&q=80" alt="Sơn Trà Đà Nẵng" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>5. Cầu Rồng – Biểu tượng của thành phố</h3>
<p>Cây cầu hình rồng dài 666m bắc qua sông Hàn là công trình kiến trúc độc đáo nhất Đà Nẵng. Vào tối thứ 7 và Chủ nhật, rồng phun lửa và nước tạo nên màn trình diễn ngoạn mục.</p>

<h2>Lưu ý khi du lịch Đà Nẵng</h2>
<ul>
<li>Thời điểm đẹp nhất: tháng 2 – 8 (mùa khô, ít mưa)</li>
<li>Di chuyển: thuê xe máy hoặc xe đạp điện để tự do khám phá</li>
<li>Ăn uống: thử mì Quảng, bánh mì Đà Nẵng, bún chả cá</li>
<li>Lưu trú: khu vực biển Mỹ Khê có nhiều lựa chọn từ bình dân đến cao cấp</li>
</ul>',
            ],
            [
                'title'      => 'Hướng dẫn du lịch Phú Quốc tự túc 4 ngày 3 đêm',
                'category'   => 'Điểm đến',
                'location'   => 'Phú Quốc',
                'image'      => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=1200&q=85',
                'views'      => 2890,
                'user_index' => 1,
                'excerpt'    => 'Lịch trình chi tiết du lịch Phú Quốc 4N3Đ với chi phí tiết kiệm, bao gồm ăn uống, đi lại và các hoạt động vui chơi.',
                'content'    => '
<h2>Phú Quốc – Đảo Ngọc của Việt Nam</h2>
<p>Phú Quốc là hòn đảo lớn nhất Việt Nam với diện tích 574km², nổi tiếng với những bãi biển hoang sơ, rừng nguyên sinh và hải sản tươi ngon. Đây là điểm đến lý tưởng cho cả gia đình, cặp đôi và nhóm bạn.</p>
<img src="https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=900&q=80" alt="Phú Quốc nhìn từ trên cao" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Lịch trình 4 ngày 3 đêm</h2>

<h3>Ngày 1: Khám phá Nam đảo</h3>
<p><strong>Sáng:</strong> Đến sân bay Phú Quốc, nhận phòng khách sạn. Ăn sáng với bánh mì trứng và cà phê Phú Quốc thơm ngon.</p>
<p><strong>Trưa:</strong> Ghé thăm Dinh Cậu – ngôi đền linh thiêng nhìn ra biển. Ăn trưa tại chợ đêm Phú Quốc với bún quậy, gỏi cá trích.</p>
<p><strong>Chiều:</strong> Tắm biển tại Bãi Sao – một trong những bãi biển đẹp nhất Đông Nam Á với cát trắng mịn như bột và nước biển xanh ngọc.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Bãi Sao Phú Quốc" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Ngày 2: Khám phá Bắc đảo</h3>
<p>Thuê xe máy (120.000đ/ngày) khám phá Bắc đảo. Ghé thăm Vườn Quốc gia Phú Quốc – khu rừng nguyên sinh với hệ sinh thái đa dạng. Ăn trưa tại làng chài Rạch Vẹm, thưởng thức nhum biển và cầu gai nướng.</p>

<h3>Ngày 3: Tour 3 đảo</h3>
<p>Tham gia tour 3 đảo (Hòn Thơm, Hòn Dừa, Hòn Mây Rút) với giá khoảng 350.000đ/người. Lặn ngắm san hô, câu cá, tắm biển và thưởng thức bữa trưa hải sản trên thuyền.</p>
<img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=900&q=80" alt="Lặn ngắm san hô Phú Quốc" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Ngày 4: Vinpearl Land và về</h3>
<p>Vui chơi tại Vinpearl Land Phú Quốc – công viên giải trí lớn nhất Việt Nam với hơn 100 trò chơi, công viên nước và thủy cung. Chiều mua sắm đặc sản rồi ra sân bay.</p>

<h2>Chi phí tham khảo (1 người)</h2>
<ul>
<li>Vé máy bay khứ hồi: 2.000.000 – 4.000.000đ</li>
<li>Khách sạn 3 sao: 600.000 – 1.200.000đ/đêm</li>
<li>Ăn uống: 300.000 – 500.000đ/ngày</li>
<li>Tour 3 đảo: 350.000đ</li>
<li><strong>Tổng ước tính: 8 – 15 triệu đồng</strong></li>
</ul>',
            ],
            [
                'title'      => 'Khám phá Hội An – Phố cổ đèn lồng lung linh nhất Việt Nam',
                'category'   => 'Điểm đến',
                'location'   => 'Hội An, Quảng Nam',
                'image'      => 'https://images.unsplash.com/photo-1559592413-7cec4d0cae2b?w=1200&q=85',
                'views'      => 2100,
                'user_index' => 2,
                'excerpt'    => 'Hội An – Di sản văn hóa thế giới với những con phố cổ rêu phong, đèn lồng rực rỡ và ẩm thực đặc sắc không nơi nào có được.',
                'content'    => '
<h2>Hội An – Viên ngọc của miền Trung</h2>
<p>Hội An được UNESCO công nhận là Di sản Văn hóa Thế giới năm 1999. Phố cổ với những ngôi nhà gỗ hàng trăm năm tuổi, đèn lồng rực rỡ và dòng sông Thu Bồn thơ mộng tạo nên một không gian như bước ra từ trang sách cổ tích.</p>
<img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=900&q=80" alt="Phố cổ Hội An về đêm" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Những điểm không thể bỏ qua</h3>
<p><strong>Chùa Cầu Nhật Bản:</strong> Biểu tượng của Hội An, cây cầu gỗ 400 năm tuổi bắc qua con lạch nhỏ. Đây là hình ảnh xuất hiện trên tờ tiền 20.000đ của Việt Nam.</p>
<p><strong>Phố đèn lồng:</strong> Mỗi tối, hàng nghìn chiếc đèn lồng đủ màu sắc thắp sáng phố cổ, tạo nên khung cảnh huyền ảo như trong truyện cổ tích.</p>
<img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80" alt="Đèn lồng Hội An" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Ẩm thực Hội An</h3>
<p>Cao lầu – món mì đặc trưng chỉ có ở Hội An, sợi mì vàng dai ăn với thịt xá xíu và rau sống. Bánh mì Phượng – được Anthony Bourdain gọi là "bánh mì ngon nhất thế giới". Cơm gà Hội An – cơm trắng dẻo ăn với gà xé phay và nước dùng thơm ngon.</p>
<img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80" alt="Ẩm thực Hội An" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Kinh nghiệm tham quan</h3>
<ul>
<li>Mua vé tham quan phố cổ: 120.000đ/người (vào 5 điểm)</li>
<li>Thuê xe đạp để dạo quanh: 50.000đ/ngày</li>
<li>Đêm rằm hàng tháng: phố cổ tắt điện, thắp đèn lồng – đẹp nhất</li>
<li>Mua đèn lồng làm quà: 30.000 – 100.000đ/chiếc</li>
</ul>',
            ],
            [
                'title'      => 'Cẩm nang du lịch Hạ Long: Tất cả những gì bạn cần biết từ A đến Z',
                'category'   => 'Điểm đến',
                'location'   => 'Hạ Long, Quảng Ninh',
                'image'      => 'https://images.unsplash.com/photo-1583417319070-4a69db38a482?w=1200&q=85',
                'views'      => 3560,
                'user_index' => 3,
                'excerpt'    => 'Hướng dẫn du lịch Hạ Long chi tiết từ A-Z, bao gồm cách đi, chỗ ở, ăn gì, chơi gì, chi phí bao nhiêu.',
                'content'    => '
<h2>Vịnh Hạ Long – Kỳ quan thiên nhiên thế giới</h2>
<p>Vịnh Hạ Long được UNESCO công nhận là Di sản Thiên nhiên Thế giới hai lần (1994 và 2000), với hơn 1.600 hòn đảo đá vôi nhô lên từ mặt biển xanh ngọc. Đây là một trong những điểm đến không thể bỏ qua khi đến Việt Nam.</p>
<img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=900&q=80" alt="Vịnh Hạ Long" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Cách di chuyển đến Hạ Long</h2>
<ul>
<li><strong>Xe khách từ Hà Nội:</strong> 150.000 – 250.000đ, 3-4 tiếng</li>
<li><strong>Xe limousine:</strong> 250.000 – 350.000đ, 2.5-3 tiếng, thoải mái hơn</li>
<li><strong>Tự lái xe:</strong> Cao tốc Hà Nội – Hạ Long mới, chỉ 2.5 tiếng</li>
<li><strong>Thủy phi cơ:</strong> 2.400.000đ/chiều, 45 phút, trải nghiệm độc đáo</li>
</ul>

<h2>Các tour du thuyền</h2>
<p><strong>Tour 2 ngày 1 đêm:</strong> Giá 1.500.000 – 5.000.000đ/người. Ngủ đêm trên du thuyền, thăm nhiều điểm hơn, có bữa tối và bữa sáng. Đây là lựa chọn phổ biến nhất.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Du thuyền Hạ Long" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Top điểm tham quan</h2>
<ul>
<li><strong>Hang Sửng Sốt:</strong> Hang động lớn nhất vịnh Hạ Long, nhũ đá kỳ ảo</li>
<li><strong>Đảo Ti Tốp:</strong> Leo 400 bậc thang lên đỉnh ngắm toàn cảnh vịnh</li>
<li><strong>Làng chài Cửa Vạn:</strong> Làng chài nổi trên biển, cuộc sống ngư dân độc đáo</li>
<li><strong>Hang Đầu Gỗ:</strong> Hang động đẹp nhất vịnh với nhiều nhũ đá hình thù kỳ lạ</li>
</ul>

<h2>Lưu ý quan trọng</h2>
<ul>
<li>Tránh mùa bão (tháng 7-9), thời điểm đẹp nhất là tháng 10-4</li>
<li>Chọn du thuyền có giấy phép hoạt động hợp lệ</li>
<li>Mặc áo phao khi chèo kayak</li>
<li>Không xả rác xuống biển – bảo vệ kỳ quan thiên nhiên</li>
</ul>',
            ],


            // ==================== ẨM THỰC (4 bài) ====================
            [
                'title'      => 'Đặc sản Huế: 15 món ăn bạn nhất định phải thử một lần trong đời',
                'category'   => 'Ẩm thực',
                'location'   => 'Huế',
                'image'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=85',
                'views'      => 2156,
                'user_index' => 4,
                'excerpt'    => 'Tổng hợp 15 món ăn đặc sản Huế nổi tiếng nhất, từ bún bò Huế đến bánh bèo, bánh nậm, cơm hến.',
                'content'    => '
<h2>Ẩm thực Huế – Tinh hoa ẩm thực cung đình</h2>
<p>Huế không chỉ nổi tiếng với những cung điện, lăng tẩm cổ kính mà còn được biết đến là kinh đô ẩm thực của Việt Nam. Ẩm thực Huế mang đậm dấu ấn cung đình với sự tinh tế trong chế biến, trình bày và hương vị đặc trưng cay nồng, đậm đà.</p>
<img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=900&q=80" alt="Ẩm thực Huế" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Bún bò Huế – Linh hồn ẩm thực xứ Huế</h3>
<p>Khác với phở Hà Nội hay bún bò Nam Bộ, bún bò Huế có nước dùng đậm đà từ xương bò hầm lâu với sả, mắm ruốc và ớt. Tô bún đầy đủ với bắp bò, giò heo, chả cua và rau sống. Giá chỉ từ 30.000 – 50.000đ/tô.</p>
<p><em>Địa chỉ ngon: Bún bò Mụ Rớt (2 Nguyễn Bỉnh Khiêm), Bún bò O Xuân (Nguyễn Công Trứ)</em></p>

<h3>2. Bánh bèo – Tinh tế và thanh tao</h3>
<p>Những chiếc bánh bèo trắng mịn đựng trong chén nhỏ, phủ tôm chấy, mỡ hành và nước mắm chua ngọt. Ăn một lần là nhớ mãi. Một đĩa 10 chén chỉ khoảng 20.000đ.</p>
<img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=900&q=80" alt="Bánh bèo Huế" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Bánh nậm – Mỏng manh như lụa</h3>
<p>Bánh nậm được gói trong lá chuối, làm từ bột gạo mỏng với nhân tôm thịt. Khi ăn, bánh tan ngay trong miệng, để lại vị ngọt thanh khó quên.</p>

<h3>4. Cơm hến – Bữa sáng bình dân mà ngon</h3>
<p>Cơm nguội trộn với hến xào, rau sống, đậu phộng rang, bánh tráng nướng và nước hến. Món ăn dân dã nhưng đầy đủ hương vị, chỉ 15.000 – 25.000đ/bát.</p>

<h3>5. Bánh khoái – Giòn tan hấp dẫn</h3>
<p>Bánh khoái Huế giòn rụm với nhân tôm, thịt, giá đỗ và trứng, ăn kèm rau sống và nước chấm đặc biệt từ gan heo. Khác hoàn toàn với bánh xèo miền Nam.</p>

<h3>6. Chè Huế – Ngọt ngào đa dạng</h3>
<p>Huế có hơn 30 loại chè khác nhau: chè đậu ván, chè bắp, chè hạt sen, chè khoai tía... Phố chè Trịnh Công Sơn là thiên đường cho những ai mê ngọt.</p>
<img src="https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=900&q=80" alt="Chè Huế" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Kinh nghiệm ăn uống tại Huế</h2>
<ul>
<li>Ăn sáng sớm (6-8h) để thưởng thức đồ ăn tươi ngon nhất</li>
<li>Ghé chợ Đông Ba để tìm đủ loại đặc sản</li>
<li>Đừng ngại thử các quán vỉa hè – thường ngon hơn nhà hàng</li>
<li>Mang về: mè xửng, bánh in, mứt gừng Huế</li>
</ul>',
            ],
            [
                'title'      => 'Ẩm thực đường phố Sài Gòn: 20 quán ăn vỉa hè ngon nhất không thể bỏ qua',
                'category'   => 'Ẩm thực',
                'location'   => 'TP. Hồ Chí Minh',
                'image'      => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=1200&q=85',
                'views'      => 3100,
                'user_index' => 5,
                'excerpt'    => 'Tổng hợp những quán ăn vỉa hè nổi tiếng nhất Sài Gòn mà bất kỳ food tour nào cũng phải ghé.',
                'content'    => '
<h2>Sài Gòn – Thiên đường ẩm thực đường phố</h2>
<p>Sài Gòn là thành phố không bao giờ ngủ, và ẩm thực đường phố chính là linh hồn của nơi này. Từ những tô phở bốc khói lúc 5 giờ sáng đến những xe bánh mì giòn rụm lúc nửa đêm, Sài Gòn luôn có thứ gì đó để ăn.</p>
<img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80" alt="Ẩm thực đường phố Sài Gòn" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Phở Hòa Pasteur – Huyền thoại 70 năm</h3>
<p>Quán phở lâu đời nhất Sài Gòn tại 260C Pasteur, Q3. Nước dùng trong vắt, ngọt thanh từ xương bò hầm 12 tiếng. Mở từ 6h sáng, thường xuyên có hàng dài chờ đợi. Giá: 80.000 – 120.000đ/tô.</p>

<h3>2. Bánh mì Huỳnh Hoa – Bánh mì ngon nhất Sài Gòn</h3>
<p>Tại 26 Lê Thị Riêng, Q1. Ổ bánh mì to đùng nhồi đầy thịt nguội, pate, chả lụa và rau. Luôn có hàng dài từ sáng đến tối. Giá: 45.000đ/ổ.</p>
<img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=900&q=80" alt="Bánh mì Sài Gòn" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Hủ tiếu Nam Vang – Đặc sản gốc Hoa</h3>
<p>Sợi hủ tiếu dai, nước dùng ngọt từ xương heo và tôm khô, ăn kèm thịt bằm, tôm, gan heo. Quán Hồng Phát (Nguyễn Trãi, Q5) là địa chỉ quen thuộc của dân Sài Gòn.</p>

<h3>4. Cơm tấm Thuận Kiều</h3>
<p>Cơm tấm Sài Gòn với sườn nướng thơm lừng, bì, chả trứng và nước mắm chua ngọt. Ăn lúc nào cũng ngon, đặc biệt là bữa sáng sớm.</p>

<h3>5. Bánh tráng trộn – Snack đường phố hot nhất</h3>
<p>Bánh tráng cắt nhỏ trộn với xoài xanh, tôm khô, trứng cút, sa tế và tương. Món ăn vặt không thể thiếu khi dạo phố Sài Gòn, giá chỉ 15.000 – 25.000đ.</p>
<img src="https://images.unsplash.com/photo-1563805042-7684c019e1cb?w=900&q=80" alt="Ăn vặt Sài Gòn" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Bản đồ ẩm thực theo quận</h2>
<ul>
<li><strong>Quận 1:</strong> Phở, bánh mì, cà phê sân thượng</li>
<li><strong>Quận 3:</strong> Bún bò, bánh cuốn, chè</li>
<li><strong>Quận 5 (Chợ Lớn):</strong> Dimsum, hủ tiếu, bánh bao</li>
<li><strong>Quận 10:</strong> Chè, bánh tráng trộn, ốc</li>
<li><strong>Bình Thạnh:</strong> Lẩu, nướng, hải sản</li>
</ul>',
            ],
            [
                'title'      => 'Khám phá ẩm thực Hà Nội: Những món ăn sáng không thể bỏ qua',
                'category'   => 'Ẩm thực',
                'location'   => 'Hà Nội',
                'image'      => 'https://images.unsplash.com/photo-1569050467447-ce54b3bbc37d?w=1200&q=85',
                'views'      => 1890,
                'user_index' => 6,
                'excerpt'    => 'Hà Nội có nền ẩm thực sáng phong phú bậc nhất Việt Nam. Từ phở, bún chả đến bánh cuốn, mỗi món đều mang hương vị riêng không thể nhầm lẫn.',
                'content'    => '
<h2>Ẩm thực sáng Hà Nội – Bắt đầu ngày mới thật ngon</h2>
<p>Người Hà Nội rất coi trọng bữa sáng. Họ sẵn sàng dậy sớm, xếp hàng chờ đợi để được thưởng thức tô phở nóng hổi hay đĩa bánh cuốn mỏng tang. Đây là nét văn hóa ẩm thực đặc trưng của thủ đô ngàn năm văn hiến.</p>
<img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80" alt="Phở Hà Nội" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Phở Hà Nội – Quốc hồn quốc túy</h3>
<p>Phở Hà Nội khác phở Sài Gòn ở chỗ nước dùng trong hơn, ít ngọt hơn và không ăn kèm giá đỗ. Bánh phở mỏng, mềm. Phở Thìn (13 Lò Đúc) và Phở Bát Đàn là hai địa chỉ huyền thoại không thể bỏ qua.</p>

<h3>2. Bún chả – Đặc sản được Obama thưởng thức</h3>
<p>Bún chả Hà Nội với chả viên và chả miếng nướng than hoa, ăn kèm bún tươi, rau sống và nước chấm chua ngọt. Quán Hương Liên (24 Lê Văn Hưu) nổi tiếng sau khi cố Tổng thống Obama ghé thăm năm 2016.</p>
<img src="https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=900&q=80" alt="Bún chả Hà Nội" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Bánh cuốn Thanh Trì</h3>
<p>Bánh cuốn Hà Nội mỏng như tờ giấy, trong suốt, ăn kèm chả quế và nước mắm pha. Bánh cuốn Thanh Trì (làng Thanh Trì) là ngon nhất, nhưng trong phố có thể tìm ở Bánh cuốn Bà Hoành (66 Tô Hiến Thành).</p>

<h3>4. Xôi Yến – Xôi ngon nhất Hà Nội</h3>
<p>Xôi Yến (35B Nguyễn Hữu Huân) nổi tiếng với xôi xéo, xôi gấc, xôi lạc... Mỗi sáng có hàng dài người xếp hàng từ 6h. Giá 20.000 – 40.000đ/suất.</p>

<h3>5. Bánh mì trứng vỉa hè</h3>
<p>Những xe bánh mì trứng vỉa hè Hà Nội với ổ bánh mì nóng giòn, trứng ốp la và pate. Bữa sáng nhanh gọn, ngon miệng chỉ 15.000 – 20.000đ.</p>
<img src="https://images.unsplash.com/photo-1567620905732-2d1ec7ab7445?w=900&q=80" alt="Ẩm thực sáng Hà Nội" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Địa chỉ ăn sáng theo khu vực</h2>
<ul>
<li><strong>Phố cổ (Hoàn Kiếm):</strong> Phở, bún chả, bánh cuốn, cà phê trứng</li>
<li><strong>Hai Bà Trưng:</strong> Bún chả, phở, xôi</li>
<li><strong>Đống Đa:</strong> Bánh mì, bún riêu, miến lươn</li>
<li><strong>Tây Hồ:</strong> Bánh tôm Hồ Tây, bún ốc, chả cá Lã Vọng</li>
</ul>',
            ],
            [
                'title'      => 'Review nhà hàng hải sản tươi sống tại Nha Trang – Ăn ngon không lo về giá',
                'category'   => 'Ẩm thực',
                'location'   => 'Nha Trang, Khánh Hòa',
                'image'      => 'https://images.unsplash.com/photo-1559339352-11d035aa65de?w=1200&q=85',
                'views'      => 1650,
                'user_index' => 0,
                'excerpt'    => 'Nha Trang là thiên đường hải sản tươi sống với giá cả phải chăng. Tổng hợp những nhà hàng ngon nhất và kinh nghiệm ăn hải sản không bị chặt chém.',
                'content'    => '
<h2>Nha Trang – Thiên đường hải sản miền Trung</h2>
<p>Nha Trang nổi tiếng không chỉ với những bãi biển đẹp mà còn là thiên đường hải sản tươi sống. Tôm hùm, cua, mực, ốc... tất cả đều được đánh bắt trực tiếp từ biển, đảm bảo độ tươi ngon tuyệt đối.</p>
<img src="https://images.unsplash.com/photo-1559339352-11d035aa65de?w=900&q=80" alt="Hải sản Nha Trang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Top nhà hàng hải sản ngon tại Nha Trang</h3>

<h4>1. Nhà hàng Trúc Linh – Hải sản tươi sống số 1</h4>
<p>Địa chỉ: 11 Bến Chợ, Nha Trang. Nổi tiếng với tôm hùm nướng phô mai, cua rang muối và mực một nắng. Giá tầm trung, phục vụ nhanh. Nên đặt bàn trước vào buổi tối.</p>

<h4>2. Nhà hàng Hải Sản Phú Quý</h4>
<p>Địa chỉ: 79 Trần Phú. View biển đẹp, hải sản tươi sống chọn trực tiếp từ bể. Đặc biệt là món ghẹ hấp bia và tôm sú nướng muối ớt.</p>
<img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=900&q=80" alt="Nhà hàng hải sản Nha Trang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Kinh nghiệm ăn hải sản không bị chặt chém</h3>
<ul>
<li>Hỏi giá trước khi gọi món, đặc biệt với tôm hùm và cua</li>
<li>Chọn hải sản sống trực tiếp từ bể, cân trước mặt</li>
<li>Tránh các nhà hàng ngay mặt tiền biển – thường đắt hơn 30-50%</li>
<li>Ăn tại chợ Đầm hoặc chợ Xóm Mới để có giá tốt nhất</li>
<li>Thời điểm ngon nhất: 17h-20h khi hải sản vừa được đưa vào bờ</li>
</ul>

<h3>Giá tham khảo (2026)</h3>
<ul>
<li>Tôm hùm: 800.000 – 1.500.000đ/kg</li>
<li>Cua biển: 300.000 – 500.000đ/kg</li>
<li>Mực ống: 150.000 – 250.000đ/kg</li>
<li>Ốc hương: 200.000 – 350.000đ/kg</li>
<li>Ghẹ: 200.000 – 400.000đ/kg</li>
</ul>',
            ],


            // ==================== CHECKIN (4 bài) ====================
            [
                'title'      => 'Khám phá Sapa mùa lúa chín – Khi nào đi và góc chụp đẹp nhất',
                'category'   => 'Checkin',
                'location'   => 'Sapa, Lào Cai',
                'image'      => 'https://images.unsplash.com/photo-1528127269322-539801943592?w=1200&q=85',
                'views'      => 2750,
                'user_index' => 1,
                'excerpt'    => 'Hướng dẫn thời điểm lý tưởng ngắm ruộng bậc thang mùa lúa chín vàng ở Sapa và những góc chụp ảnh đẹp nhất.',
                'content'    => '
<h2>Sapa mùa lúa chín – Bức tranh vàng của núi rừng Tây Bắc</h2>
<p>Mỗi năm một lần, vào khoảng tháng 9 đến đầu tháng 10, những thửa ruộng bậc thang ở Sapa khoác lên mình tấm áo vàng óng ả, tạo nên khung cảnh đẹp đến nghẹt thở. Đây là thời điểm Sapa đẹp nhất trong năm.</p>
<img src="https://images.unsplash.com/photo-1528127269322-539801943592?w=900&q=80" alt="Ruộng bậc thang Sapa mùa lúa chín" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Thời điểm lúa chín đẹp nhất</h2>
<ul>
<li><strong>Cuối tháng 9 – đầu tháng 10:</strong> Lúa chín vàng rực, đẹp nhất năm</li>
<li><strong>Tháng 5 – 6:</strong> Mùa nước đổ, ruộng bậc thang phản chiếu bầu trời</li>
<li><strong>Tháng 12 – 2:</strong> Mùa đông, có thể có tuyết rơi trên đỉnh Fansipan</li>
</ul>

<h3>1. Mù Cang Chải – Vựa lúa vàng huyền thoại</h3>
<p>Cách Sapa 100km, Mù Cang Chải (Yên Bái) có những thửa ruộng bậc thang đẹp nhất Việt Nam. Đèo Khau Phạ – một trong tứ đại đỉnh đèo – là điểm dừng chân không thể bỏ qua.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Mù Cang Chải" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>2. Bản Lao Chải – Tả Van</h3>
<p>Con đường trekking từ Sapa xuống Lao Chải – Tả Van dài 10km đi qua những thửa ruộng bậc thang tuyệt đẹp. Dọc đường gặp người H\'Mông, Dao đỏ trong trang phục truyền thống rực rỡ.</p>

<h3>3. Đỉnh Fansipan – Nóc nhà Đông Dương</h3>
<p>Ở độ cao 3.143m, Fansipan cho bạn cảm giác đứng trên mây. Cáp treo Fansipan dài nhất thế giới đưa bạn lên đỉnh chỉ trong 15 phút. Giá vé: 800.000đ/người.</p>

<h2>Kinh nghiệm chụp ảnh đẹp tại Sapa</h2>
<ul>
<li><strong>Giờ vàng:</strong> 6-8h sáng và 16-18h chiều – ánh sáng đẹp nhất</li>
<li><strong>Thiết bị:</strong> Ống kính góc rộng để chụp toàn cảnh ruộng bậc thang</li>
<li><strong>Trang phục:</strong> Mặc màu sắc nổi bật để tạo điểm nhấn trong ảnh</li>
<li><strong>Thời tiết:</strong> Kiểm tra dự báo thời tiết, tránh ngày mưa và sương mù dày</li>
</ul>',
            ],
            [
                'title'      => 'Review top 5 homestay view đẹp nhất Đà Lạt – Sống ảo cực chất',
                'category'   => 'Checkin',
                'location'   => 'Đà Lạt',
                'image'      => 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1200&q=85',
                'views'      => 1980,
                'user_index' => 2,
                'excerpt'    => 'Review chi tiết 5 homestay view đẹp, giá hợp lý tại Đà Lạt dành cho các cặp đôi và nhóm bạn.',
                'content'    => '
<h2>Đà Lạt – Thành phố ngàn hoa và những homestay đẹp như mơ</h2>
<p>Đà Lạt không chỉ nổi tiếng với khí hậu mát mẻ quanh năm mà còn là thiên đường của những homestay độc đáo, view đẹp và giá cả phải chăng. Dưới đây là 5 homestay được giới trẻ check-in nhiều nhất.</p>
<img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&q=80" alt="Homestay Đà Lạt" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. The Dreamy House – Nhà trong rừng thông</h3>
<p><strong>Giá:</strong> 800.000 – 1.500.000đ/đêm | <strong>Vị trí:</strong> Đường Vạn Thành, cách trung tâm 5km</p>
<p>Ngôi nhà gỗ nằm giữa rừng thông xanh mát, có bếp lửa ấm cúng và ban công nhìn ra thung lũng. Buổi sáng thức dậy trong tiếng chim hót và sương mù bảng lảng – cảm giác như đang ở châu Âu.</p>

<h3>2. Mây Trên Đồi – Sống trên mây</h3>
<p><strong>Giá:</strong> 600.000 – 1.200.000đ/đêm | <strong>Vị trí:</strong> Gần hồ Tuyền Lâm</p>
<p>Homestay nằm trên đồi cao, view nhìn ra hồ Tuyền Lâm và rừng thông bạt ngàn. Có bể bơi ngoài trời và khu vườn hoa đẹp như tranh vẽ.</p>
<img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80" alt="Homestay view đẹp Đà Lạt" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Pine Forest Homestay – Giữa rừng thông nguyên sinh</h3>
<p><strong>Giá:</strong> 1.000.000 – 2.000.000đ/đêm | <strong>Vị trí:</strong> Đường Khe Sanh</p>
<p>Các cabin gỗ riêng biệt nằm rải rác trong rừng thông, có bếp nướng BBQ và khu vực cắm trại. Đêm ngủ nghe tiếng gió thổi qua rừng thông – trải nghiệm khó quên.</p>

<h3>4. Flower Garden Homestay – Vườn hoa bốn mùa</h3>
<p><strong>Giá:</strong> 500.000 – 900.000đ/đêm | <strong>Vị trí:</strong> Đường Nguyên Tử Lực</p>
<p>Khuôn viên rộng với hàng trăm loài hoa đua nở quanh năm. Chủ nhà thân thiện, bữa sáng ngon với bánh mì và cà phê Đà Lạt.</p>

<h3>5. Chill House Dalat – Boho Chic</h3>
<p><strong>Giá:</strong> 400.000 – 800.000đ/đêm | <strong>Vị trí:</strong> Trung tâm thành phố</p>
<p>Phong cách Boho với nhiều góc chụp ảnh đẹp, cây xanh và hoa tươi khắp nơi. Có phòng tập yoga và khu vực đọc sách yên tĩnh.</p>

<h2>Lưu ý khi đặt homestay Đà Lạt</h2>
<ul>
<li>Đặt trước ít nhất 2 tuần vào mùa cao điểm (lễ, Tết, hè)</li>
<li>Kiểm tra kỹ ảnh thực tế và đánh giá trên Booking/Airbnb</li>
<li>Mang áo ấm vì Đà Lạt lạnh về đêm, đặc biệt mùa đông</li>
</ul>',
            ],
            [
                'title'      => 'Những góc check-in đẹp nhất tại Hà Giang – Thiên đường của dân phượt',
                'category'   => 'Checkin',
                'location'   => 'Hà Giang',
                'image'      => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=85',
                'views'      => 2200,
                'user_index' => 3,
                'excerpt'    => 'Hà Giang với cao nguyên đá Đồng Văn, đèo Mã Pí Lèng hùng vĩ và những bản làng dân tộc thiểu số là điểm đến trong mơ của mọi tín đồ du lịch.',
                'content'    => '
<h2>Hà Giang – Cực Bắc hùng vĩ của Tổ quốc</h2>
<p>Hà Giang là tỉnh địa đầu Tổ quốc với địa hình núi non hùng vĩ, cao nguyên đá Đồng Văn được UNESCO công nhận là Công viên Địa chất Toàn cầu. Đây là điểm đến không thể bỏ qua cho những ai yêu thích khám phá và chụp ảnh phong cảnh.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Cao nguyên đá Hà Giang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Đèo Mã Pí Lèng – Vua của các đèo</h3>
<p>Đèo Mã Pí Lèng dài 20km, nằm ở độ cao 1.200-2.000m, là một trong tứ đại đỉnh đèo của Việt Nam. Từ đỉnh đèo nhìn xuống hẻm vực Tu Sản và dòng sông Nho Quế xanh biếc – khung cảnh đẹp đến nghẹt thở.</p>
<p><strong>Góc chụp đẹp nhất:</strong> Điểm dừng chân giữa đèo, chụp vào buổi sáng sớm khi sương mù còn bảng lảng.</p>

<h3>2. Cột cờ Lũng Cú – Điểm cực Bắc Tổ quốc</h3>
<p>Leo 389 bậc thang lên đỉnh núi Rồng để đến cột cờ Lũng Cú – điểm cực Bắc của Việt Nam. Từ đây có thể nhìn sang đất Trung Quốc và ngắm toàn cảnh cao nguyên đá hùng vĩ.</p>
<img src="https://images.unsplash.com/photo-1528127269322-539801943592?w=900&q=80" alt="Cột cờ Lũng Cú" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Phố cổ Đồng Văn</h3>
<p>Phố cổ Đồng Văn với những ngôi nhà trình tường đất cổ kính, chợ phiên họp vào Chủ nhật với người dân tộc H\'Mông, Lô Lô, Giáy trong trang phục truyền thống rực rỡ.</p>

<h3>4. Ruộng bậc thang Hoàng Su Phì</h3>
<p>Ít người biết đến hơn Sapa nhưng ruộng bậc thang Hoàng Su Phì không kém phần đẹp. Mùa lúa chín (tháng 9-10) là thời điểm đẹp nhất để ghé thăm.</p>

<h2>Lịch trình loop Hà Giang 4 ngày</h2>
<p><strong>Ngày 1:</strong> Hà Nội → Hà Giang (xe khách đêm)<br>
<strong>Ngày 2:</strong> Hà Giang → Đồng Văn (qua Quản Bạ, Yên Minh)<br>
<strong>Ngày 3:</strong> Đồng Văn → Mèo Vạc (đèo Mã Pí Lèng)<br>
<strong>Ngày 4:</strong> Mèo Vạc → Hà Giang → Hà Nội</p>',
            ],
            [
                'title'      => 'Cung đường ven biển Ninh Thuận – Bình Thuận: Đẹp như trời Tây',
                'category'   => 'Checkin',
                'location'   => 'Ninh Thuận – Bình Thuận',
                'image'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=85',
                'views'      => 1560,
                'user_index' => 4,
                'excerpt'    => 'Cung đường ven biển từ Ninh Thuận đến Bình Thuận với đồi cát đỏ, biển xanh và những ngôi làng chài bình yên là thiên đường check-in ít người biết.',
                'content'    => '
<h2>Ninh Thuận – Bình Thuận: Vùng đất nắng gió và biển xanh</h2>
<p>Nếu bạn đang tìm kiếm một cung đường ven biển đẹp như trời Tây mà không cần bay ra nước ngoài, thì Ninh Thuận – Bình Thuận chính là câu trả lời. Đồi cát đỏ, biển xanh ngọc, làng chài bình yên và những cánh đồng nho xanh mướt.</p>
<img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&q=80" alt="Biển Ninh Thuận" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Đồi cát Mũi Né – Sa mạc giữa lòng Việt Nam</h3>
<p>Đồi cát đỏ và đồi cát trắng Mũi Né là điểm check-in nổi tiếng nhất Bình Thuận. Chụp ảnh đẹp nhất vào lúc bình minh (5-7h) hoặc hoàng hôn (17-19h) khi ánh sáng vàng óng chiếu lên những đụn cát.</p>

<h3>2. Vườn nho Ninh Thuận</h3>
<p>Ninh Thuận là vùng trồng nho lớn nhất Việt Nam. Tham quan vườn nho xanh mướt, chụp ảnh giữa những giàn nho trĩu quả và thưởng thức rượu vang nho địa phương.</p>
<img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&q=80" alt="Vườn nho Ninh Thuận" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Bãi biển Bình Tiên – Hoang sơ và trong vắt</h3>
<p>Bãi biển Bình Tiên (Ninh Thuận) là một trong những bãi biển hoang sơ đẹp nhất Việt Nam, ít người biết đến. Nước biển trong xanh như pha lê, cát trắng mịn và không có hàng quán chen chúc.</p>

<h3>4. Tháp Chăm Pô Klong Garai</h3>
<p>Quần thể tháp Chăm hơn 700 năm tuổi, là di tích kiến trúc Chăm Pa đẹp nhất còn lại ở Việt Nam. Đặc biệt đẹp vào buổi chiều khi ánh nắng vàng chiếu lên những viên gạch đỏ cổ kính.</p>

<h2>Lịch trình 3 ngày khám phá</h2>
<p><strong>Ngày 1:</strong> Phan Rang – Tháp Chàm, vườn nho, biển Ninh Chữ<br>
<strong>Ngày 2:</strong> Bình Tiên, Vĩnh Hy, Mũi Dinh<br>
<strong>Ngày 3:</strong> Mũi Né, đồi cát, làng chài Mũi Né</p>',
            ],


            // ==================== KINH NGHIỆM (3 bài) ====================
            [
                'title'      => '10 mẹo tiết kiệm chi phí khi đi du lịch mà dân phượt nào cũng biết',
                'category'   => 'Kinh nghiệm',
                'location'   => 'Việt Nam',
                'image'      => 'https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=1200&q=85',
                'views'      => 4200,
                'user_index' => 5,
                'excerpt'    => 'Bí kíp du lịch tiết kiệm nhưng vẫn trải nghiệm đầy đủ, từ đặt vé máy bay rẻ đến chọn chỗ ăn ngon giá tốt.',
                'content'    => '
<h2>Du lịch tiết kiệm – Đi nhiều hơn với chi phí ít hơn</h2>
<p>Nhiều người nghĩ du lịch tốn kém, nhưng thực ra với những mẹo đúng đắn, bạn hoàn toàn có thể khám phá nhiều nơi mà không cần phải "đốt" hết tiền tiết kiệm. Dưới đây là 10 bí kíp từ những người đã đi hàng chục chuyến.</p>
<img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=900&q=80" alt="Du lịch tiết kiệm" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Đặt vé máy bay trước 2-3 tháng</h3>
<p>Giá vé máy bay thường rẻ nhất khi đặt trước 6-8 tuần. Theo dõi giá trên Google Flights, Skyscanner hoặc đăng ký nhận thông báo giảm giá. Bay vào thứ 3, thứ 4 thường rẻ hơn thứ 6, thứ 7 đến 30%.</p>

<h3>2. Chọn thời điểm du lịch thấp điểm</h3>
<p>Tránh các dịp lễ lớn (30/4, 2/9, Tết) khi giá tăng gấp đôi. Du lịch vào tháng 3, tháng 10-11 thường có giá tốt nhất và ít đông đúc hơn.</p>

<h3>3. Ở hostel hoặc homestay thay vì khách sạn</h3>
<p>Hostel giường tầng chỉ 100.000 – 200.000đ/đêm, trong khi homestay gia đình thường rẻ hơn khách sạn 30-50% và cho trải nghiệm văn hóa địa phương thú vị hơn.</p>
<img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80" alt="Hostel du lịch" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>4. Ăn tại chợ và quán địa phương</h3>
<p>Một bữa ăn tại chợ hoặc quán vỉa hè chỉ 30.000 – 50.000đ, trong khi nhà hàng du lịch có thể tốn 200.000 – 500.000đ cho cùng một món. Hỏi người dân địa phương về quán ngon thay vì tin vào biển quảng cáo.</p>

<h3>5. Thuê xe máy thay vì taxi</h3>
<p>Thuê xe máy 100.000 – 150.000đ/ngày, trong khi taxi có thể tốn 500.000đ chỉ cho một chuyến đi. Xe máy còn cho bạn tự do khám phá những con đường nhỏ mà taxi không vào được.</p>

<h3>6. Sử dụng ứng dụng du lịch thông minh</h3>
<p>Google Maps (offline), Grab (di chuyển), Agoda/Booking (khách sạn), Foody (ăn uống). Những app này giúp bạn tiết kiệm đáng kể.</p>

<h3>7. Đi theo nhóm để chia sẻ chi phí</h3>
<p>Thuê xe, phòng khách sạn, tour... đều rẻ hơn khi đi nhóm. Một phòng 4 người chia ra chỉ bằng 1/4 so với đặt phòng đơn.</p>

<h2>Bảng chi phí tham khảo cho chuyến đi 3 ngày</h2>
<ul>
<li>Lưu trú tiết kiệm: 300.000đ/đêm | Trung bình: 800.000đ/đêm</li>
<li>Ăn uống tiết kiệm: 200.000đ/ngày | Trung bình: 500.000đ/ngày</li>
<li>Di chuyển tiết kiệm: 150.000đ/ngày | Trung bình: 400.000đ/ngày</li>
<li><strong>Tổng tiết kiệm: ~750.000đ/ngày | Trung bình: ~2.000.000đ/ngày</strong></li>
</ul>',
            ],
            [
                'title'      => 'Kinh nghiệm leo Tà Năng – Phan Dũng: Cung đường trekking đẹp nhất Việt Nam',
                'category'   => 'Kinh nghiệm',
                'location'   => 'Lâm Đồng – Bình Thuận',
                'image'      => 'https://images.unsplash.com/photo-1551632811-561732d1e306?w=1200&q=85',
                'views'      => 1870,
                'user_index' => 6,
                'excerpt'    => 'Chia sẻ kinh nghiệm chinh phục cung trekking Tà Năng – Phan Dũng, lưu ý an toàn và chuẩn bị hành lý.',
                'content'    => '
<h2>Tà Năng – Phan Dũng: Cung đường trekking huyền thoại</h2>
<p>Tà Năng – Phan Dũng là cung đường trekking dài nhất và đẹp nhất Việt Nam, trải dài 60km qua 3 tỉnh Lâm Đồng, Ninh Thuận và Bình Thuận. Đây là thử thách dành cho những người yêu thiên nhiên và muốn trải nghiệm vẻ đẹp hoang sơ của núi rừng Tây Nguyên.</p>
<img src="https://images.unsplash.com/photo-1551632811-561732d1e306?w=900&q=80" alt="Trekking Tà Năng Phan Dũng" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Thông tin cơ bản</h2>
<ul>
<li><strong>Độ dài:</strong> 60km, đi trong 3 ngày 2 đêm</li>
<li><strong>Độ khó:</strong> Trung bình – Khó (cần thể lực tốt)</li>
<li><strong>Thời điểm đẹp nhất:</strong> Tháng 11 – 4 (mùa khô)</li>
<li><strong>Chi phí:</strong> 1.500.000 – 2.500.000đ/người (tự túc)</li>
</ul>

<h3>Ngày 1: Tà Năng – Suối Vàng (20km)</h3>
<p>Xuất phát từ thị trấn Tà Năng (Lâm Đồng) lúc 7h sáng. Đường đi qua những đồng cỏ xanh mướt, rừng thông và suối trong vắt. Điểm cắm trại đêm đầu tại Suối Vàng.</p>
<img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&q=80" alt="Cắm trại trekking" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Ngày 2: Suối Vàng – Bàu Cạn (25km)</h3>
<p>Ngày khó nhất của hành trình. Đường đi qua nhiều dốc cao, rừng rậm và suối cần lội qua. Phong cảnh thay đổi từ rừng thông sang rừng lá rộng nhiệt đới.</p>

<h3>Ngày 3: Bàu Cạn – Phan Dũng (15km)</h3>
<p>Đoạn cuối đi qua những đồng cỏ tranh vàng óng và rừng cây thưa. Kết thúc hành trình tại thôn Phan Dũng (Bình Thuận).</p>

<h2>Lưu ý an toàn quan trọng</h2>
<ul>
<li>Không đi một mình, tối thiểu 4-5 người</li>
<li>Thuê porter địa phương nếu lần đầu đi</li>
<li>Báo cáo với kiểm lâm trước khi vào rừng</li>
<li>Tuyệt đối không đi vào mùa mưa (tháng 5-10)</li>
</ul>',
            ],
            [
                'title'      => 'Kinh nghiệm du lịch một mình lần đầu – Những điều cần biết trước khi đi',
                'category'   => 'Kinh nghiệm',
                'location'   => 'Việt Nam',
                'image'      => 'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=1200&q=85',
                'views'      => 2450,
                'user_index' => 0,
                'excerpt'    => 'Du lịch một mình (solo travel) đang ngày càng phổ biến. Đây là những kinh nghiệm thực tế giúp bạn tự tin bước ra khỏi vùng an toàn.',
                'content'    => '
<h2>Solo Travel – Hành trình khám phá bản thân</h2>
<p>Du lịch một mình không chỉ là đi du lịch – đó là hành trình khám phá bản thân, vượt qua giới hạn và tạo ra những kỷ niệm không thể chia sẻ với ai khác. Nếu bạn đang do dự, bài viết này sẽ giúp bạn tự tin hơn.</p>
<img src="https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=900&q=80" alt="Du lịch một mình" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>Tại sao nên thử du lịch một mình?</h3>
<p>Bạn hoàn toàn tự do quyết định lịch trình, không cần thỏa hiệp với ai. Bạn sẽ gặp gỡ nhiều người thú vị hơn khi đi một mình. Bạn sẽ tự tin và độc lập hơn sau mỗi chuyến đi.</p>

<h3>Chuẩn bị trước khi đi</h3>
<p><strong>Nghiên cứu kỹ điểm đến:</strong> Đọc review, xem bản đồ, biết các điểm tham quan chính và cách di chuyển giữa chúng.</p>
<p><strong>Đặt chỗ ở trước:</strong> Ít nhất đêm đầu tiên, để không bị lúng túng khi vừa đến nơi lạ.</p>
<p><strong>Thông báo cho người thân:</strong> Chia sẻ lịch trình và địa chỉ lưu trú với ít nhất một người thân.</p>
<img src="https://images.unsplash.com/photo-1436491865332-7a61a109cc05?w=900&q=80" alt="Chuẩn bị hành lý" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>An toàn khi đi một mình</h3>
<ul>
<li>Luôn giữ điện thoại đầy pin và có internet</li>
<li>Không chia sẻ thông tin phòng khách sạn với người lạ</li>
<li>Tin vào trực giác – nếu cảm thấy không an toàn, hãy rời đi</li>
<li>Giữ bản sao hộ chiếu và thẻ ngân hàng ở nơi khác</li>
<li>Mua bảo hiểm du lịch trước khi đi</li>
</ul>

<h3>Điểm đến phù hợp cho solo traveler lần đầu</h3>
<ul>
<li><strong>Đà Lạt:</strong> An toàn, nhiều hostel, cộng đồng du lịch sôi động</li>
<li><strong>Hội An:</strong> Thân thiện, dễ đi bộ, nhiều hoạt động</li>
<li><strong>Đà Nẵng:</strong> Cơ sở hạ tầng tốt, nhiều lựa chọn ăn uống</li>
<li><strong>Phú Quốc:</strong> Bãi biển đẹp, nhiều resort và hostel</li>
</ul>',
            ],


            // ==================== KHÁCH SẠN (3 bài) ====================
            [
                'title'      => 'Review 5 resort Phú Quốc sang chảnh nhất – Xứng đáng từng đồng',
                'category'   => 'Khách sạn',
                'location'   => 'Phú Quốc',
                'image'      => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=1200&q=85',
                'views'      => 2340,
                'user_index' => 1,
                'excerpt'    => 'Review chi tiết 5 resort 5 sao tại Phú Quốc với hồ bơi vô cực, bãi biển riêng và dịch vụ đẳng cấp quốc tế.',
                'content'    => '
<h2>Phú Quốc – Thiên đường resort của Đông Nam Á</h2>
<p>Phú Quốc đang nhanh chóng trở thành điểm đến resort hàng đầu Đông Nam Á với hàng loạt khu nghỉ dưỡng 5 sao đẳng cấp quốc tế. Dưới đây là review chi tiết 5 resort tốt nhất mà tôi đã trực tiếp trải nghiệm.</p>
<img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80" alt="Resort Phú Quốc" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. JW Marriott Phú Quốc Emerald Bay ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 8.000.000đ/đêm | <strong>Vị trí:</strong> Bãi Khem, Nam đảo</p>
<p>Được thiết kế theo phong cách đại học cổ điển châu Âu, JW Marriott là resort đẹp nhất Phú Quốc. Hồ bơi vô cực nhìn ra biển, bãi biển riêng dài 1km, 8 nhà hàng và bar, spa đẳng cấp thế giới.</p>
<p><strong>Điểm nổi bật:</strong> Bữa sáng buffet với hơn 100 món, butler service 24/7, khu vui chơi trẻ em rộng lớn.</p>

<h3>2. Vinpearl Resort & Spa Phú Quốc ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 4.500.000đ/đêm | <strong>Vị trí:</strong> Bãi Dài, Bắc đảo</p>
<p>Khu resort rộng lớn với 750 phòng và villa, 5 hồ bơi, bãi biển riêng và kết nối trực tiếp với Vinpearl Land. Lý tưởng cho gia đình có trẻ em.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Hồ bơi resort Phú Quốc" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Premier Village Phú Quốc Resort ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 6.000.000đ/đêm | <strong>Vị trí:</strong> Mũi Ông Đội, Nam đảo</p>
<p>Resort villa độc đáo nằm trên mũi đất nhô ra biển, mỗi villa đều có hồ bơi riêng và tầm nhìn 180 độ ra biển. Đây là lựa chọn hoàn hảo cho tuần trăng mật.</p>

<h3>4. Fusion Resort Phú Quốc ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 5.000.000đ/đêm | <strong>Vị trí:</strong> Vũng Bầu, Bắc đảo</p>
<p>Điểm đặc biệt: tất cả các gói đều bao gồm spa không giới hạn. Kiến trúc độc đáo kết hợp giữa hiện đại và truyền thống, nằm trên bãi biển hoang sơ ít người biết đến.</p>

<h3>5. Salinda Resort Phú Quốc ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 3.500.000đ/đêm | <strong>Vị trí:</strong> Bãi Trường</p>
<p>Resort boutique với 62 phòng và villa, không khí yên tĩnh và riêng tư. Nhà hàng Ombra với đầu bếp người Ý phục vụ những món ăn tuyệt vời.</p>',
            ],
            [
                'title'      => 'Top 5 khách sạn boutique Hà Nội – Sang trọng giữa lòng phố cổ',
                'category'   => 'Khách sạn',
                'location'   => 'Hà Nội',
                'image'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=85',
                'views'      => 1780,
                'user_index' => 2,
                'excerpt'    => 'Những khách sạn boutique Hà Nội kết hợp hoàn hảo giữa kiến trúc Đông Dương cổ điển và tiện nghi hiện đại, mang đến trải nghiệm lưu trú độc đáo.',
                'content'    => '
<h2>Khách sạn boutique Hà Nội – Nơi lịch sử gặp gỡ hiện đại</h2>
<p>Hà Nội có một nét đặc biệt mà ít thành phố nào có được: những khách sạn boutique nằm trong các biệt thự Pháp cổ hoặc nhà ống phố cổ được cải tạo tinh tế, mang đến trải nghiệm lưu trú vừa sang trọng vừa đậm chất văn hóa.</p>
<img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&q=80" alt="Khách sạn boutique Hà Nội" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Sofitel Legend Metropole Hanoi ⭐⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 6.000.000đ/đêm | <strong>Vị trí:</strong> 15 Ngô Quyền, Hoàn Kiếm</p>
<p>Khách sạn lịch sử nhất Hà Nội, xây dựng từ năm 1901. Nơi đây từng đón tiếp Charlie Chaplin, Graham Greene và nhiều nguyên thủ quốc gia. Kiến trúc Pháp cổ điển, dịch vụ đẳng cấp thế giới.</p>

<h3>2. La Siesta Premium Hang Be ⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 1.800.000đ/đêm | <strong>Vị trí:</strong> Phố cổ Hà Nội</p>
<p>Khách sạn boutique 4 sao nằm ngay trong lòng phố cổ. Phòng ốc thiết kế tinh tế với đồ nội thất gỗ thủ công, nhân viên phục vụ chu đáo. Bữa sáng ngon với view nhìn ra phố cổ.</p>
<img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=900&q=80" alt="Phòng khách sạn boutique" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Hanoi La Castela Hotel ⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 1.200.000đ/đêm | <strong>Vị trí:</strong> Hoàn Kiếm</p>
<p>Khách sạn nhỏ xinh với 20 phòng, thiết kế theo phong cách Đông Dương. Rooftop bar với view nhìn ra Hồ Hoàn Kiếm là điểm cộng lớn.</p>

<h3>4. Essence Palace Hotel ⭐⭐⭐⭐</h3>
<p><strong>Giá:</strong> từ 900.000đ/đêm | <strong>Vị trí:</strong> Phố cổ</p>
<p>Tỷ lệ giá/chất lượng tốt nhất trong danh sách. Phòng rộng rãi, sạch sẽ, nhân viên thân thiện và vị trí đắc địa ngay trung tâm phố cổ.</p>

<h2>Lưu ý khi đặt khách sạn Hà Nội</h2>
<ul>
<li>Đặt phòng trực tiếp qua website khách sạn thường rẻ hơn OTA 10-15%</li>
<li>Khu phố cổ (Hoàn Kiếm) là vị trí tốt nhất để đi bộ khám phá</li>
<li>Tránh đặt phòng gần phố Tạ Hiện vào cuối tuần nếu bạn cần yên tĩnh</li>
<li>Kiểm tra chính sách hủy phòng – nên chọn loại miễn phí hủy</li>
</ul>',
            ],
            [
                'title'      => 'Review homestay Đà Lạt dưới 500k – Rẻ mà vẫn đẹp, vẫn chất',
                'category'   => 'Khách sạn',
                'location'   => 'Đà Lạt',
                'image'      => 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=1200&q=85',
                'views'      => 1920,
                'user_index' => 3,
                'excerpt'    => 'Không cần chi nhiều tiền vẫn có thể tìm được homestay đẹp ở Đà Lạt. Tổng hợp những homestay dưới 500k/đêm nhưng view đẹp, sạch sẽ và tiện nghi.',
                'content'    => '
<h2>Homestay Đà Lạt dưới 500k – Có thật không?</h2>
<p>Nhiều người nghĩ homestay đẹp ở Đà Lạt phải tốn cả triệu đồng mỗi đêm. Nhưng thực tế, nếu biết tìm đúng chỗ và đặt đúng thời điểm, bạn hoàn toàn có thể tìm được những nơi lưu trú tuyệt vời với giá dưới 500.000đ/đêm.</p>
<img src="https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?w=900&q=80" alt="Homestay Đà Lạt" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>1. Cozy Nest Dalat – 280.000đ/đêm</h3>
<p>Phòng đôi nhỏ xinh với cửa sổ nhìn ra vườn hoa. Chủ nhà thân thiện, có bếp chung để tự nấu ăn. Vị trí gần chợ Đà Lạt, đi bộ 5 phút.</p>

<h3>2. The Little House – 350.000đ/đêm</h3>
<p>Ngôi nhà nhỏ ấm cúng với nội thất gỗ vintage. Có ban công nhỏ nhìn ra đường phố yên tĩnh. Bữa sáng đơn giản được bao gồm trong giá phòng.</p>
<img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&q=80" alt="Phòng homestay Đà Lạt" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h3>3. Green Hill Homestay – 420.000đ/đêm</h3>
<p>Nằm trên đồi nhỏ, view nhìn ra thung lũng xanh mướt. Phòng rộng rãi với giường đôi lớn và phòng tắm riêng. Có bãi đỗ xe miễn phí.</p>

<h3>4. Dalat Backpacker Hostel – 120.000đ/giường</h3>
<p>Hostel dành cho solo traveler với giường tầng sạch sẽ, tủ khóa riêng và khu vực sinh hoạt chung rộng rãi. Nơi tuyệt vời để gặp gỡ bạn đồng hành.</p>

<h2>Mẹo tìm homestay rẻ đẹp ở Đà Lạt</h2>
<ul>
<li>Đặt qua Airbnb hoặc Booking vào ngày thường (thứ 2-5) – rẻ hơn 20-30%</li>
<li>Tránh đặt vào dịp lễ, Tết, hè – giá tăng gấp đôi</li>
<li>Đọc kỹ đánh giá, đặc biệt chú ý đến sự sạch sẽ và thái độ chủ nhà</li>
<li>Hỏi về chính sách check-in sớm/check-out muộn</li>
<li>Khu vực Trần Hưng Đạo và Nguyên Tử Lực có nhiều homestay đẹp giá tốt</li>
</ul>',
            ],


            // ==================== CẨM NANG (3 bài) ====================
            [
                'title'      => 'Cẩm nang du lịch Đà Lạt từ A đến Z – Tất cả những gì bạn cần biết',
                'category'   => 'Cẩm nang',
                'location'   => 'Đà Lạt',
                'image'      => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=85',
                'views'      => 3200,
                'user_index' => 4,
                'excerpt'    => 'Hướng dẫn du lịch Đà Lạt toàn diện: cách đi, chỗ ở, ăn gì, chơi gì, mua gì và những lưu ý quan trọng cho chuyến đi hoàn hảo.',
                'content'    => '
<h2>Đà Lạt – Thành phố ngàn hoa bốn mùa</h2>
<p>Đà Lạt nằm ở độ cao 1.500m so với mực nước biển, có khí hậu mát mẻ quanh năm với nhiệt độ trung bình 18-22°C. Thành phố hoa này là điểm đến yêu thích của người Việt Nam và du khách quốc tế với phong cảnh thiên nhiên tuyệt đẹp, ẩm thực đặc sắc và không khí lãng mạn.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Đà Lạt toàn cảnh" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Cách di chuyển đến Đà Lạt</h2>
<ul>
<li><strong>Máy bay:</strong> Sân bay Liên Khương cách trung tâm 30km. Bay từ Hà Nội/TP.HCM: 45-90 phút</li>
<li><strong>Xe khách từ TP.HCM:</strong> 7-8 tiếng, giá 150.000 – 250.000đ</li>
<li><strong>Xe khách từ Nha Trang:</strong> 4-5 tiếng, giá 120.000 – 180.000đ</li>
<li><strong>Tự lái xe:</strong> Từ TP.HCM theo QL20, khoảng 300km</li>
</ul>

<h2>Điểm tham quan nổi bật</h2>
<p><strong>Hồ Xuân Hương:</strong> Hồ nhân tạo nằm giữa lòng thành phố, lý tưởng để đi dạo buổi sáng sớm hoặc chiều tối.</p>
<p><strong>Thung lũng Tình Yêu:</strong> Khu du lịch sinh thái với hồ nước xanh, vườn hoa và nhiều hoạt động vui chơi.</p>
<p><strong>Đồi Mộng Mơ:</strong> Khu vườn hoa rộng lớn với nhiều loài hoa đặc trưng của Đà Lạt.</p>
<img src="https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=900&q=80" alt="Hoa Đà Lạt" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Ẩm thực Đà Lạt</h2>
<ul>
<li><strong>Bánh mì xíu mại:</strong> Đặc sản không thể bỏ qua, giá 15.000đ/ổ</li>
<li><strong>Bánh tráng nướng:</strong> Ăn vặt phổ biến nhất Đà Lạt</li>
<li><strong>Sữa đậu nành nóng:</strong> Uống buổi sáng sớm khi trời lạnh</li>
<li><strong>Lẩu bò Đà Lạt:</strong> Ăn tối ấm bụng trong tiết trời se lạnh</li>
<li><strong>Dâu tây tươi:</strong> Mua tại vườn hoặc chợ Đà Lạt</li>
</ul>

<h2>Mua gì về làm quà</h2>
<ul>
<li>Mứt dâu tây, mứt atiso</li>
<li>Cà phê Đà Lạt (Arabica)</li>
<li>Rượu vang Đà Lạt</li>
<li>Hoa tươi (hồng, cúc, ly)</li>
<li>Áo len thổ cẩm</li>
</ul>

<h2>Thời điểm đẹp nhất để đến Đà Lạt</h2>
<p>Đà Lạt đẹp quanh năm, nhưng đẹp nhất vào tháng 11-12 khi hoa dã quỳ nở vàng rực khắp nơi, và tháng 1-3 khi hoa anh đào nở. Tránh tháng 7-9 vì mưa nhiều.</p>',
            ],
            [
                'title'      => 'Cẩm nang du lịch Nha Trang: Biển xanh, nắng vàng và hải sản tươi ngon',
                'category'   => 'Cẩm nang',
                'location'   => 'Nha Trang, Khánh Hòa',
                'image'      => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=85',
                'views'      => 2680,
                'user_index' => 5,
                'excerpt'    => 'Nha Trang – thành phố biển đẹp nhất Việt Nam với bãi biển dài 6km, hải sản tươi ngon và hàng loạt hoạt động giải trí hấp dẫn.',
                'content'    => '
<h2>Nha Trang – Viên ngọc biển của Việt Nam</h2>
<p>Nha Trang sở hữu bãi biển dài 6km ngay trung tâm thành phố, nước biển trong xanh và khí hậu nắng ấm quanh năm. Đây là điểm đến lý tưởng cho những ai muốn nghỉ dưỡng biển kết hợp với ẩm thực hải sản tươi ngon.</p>
<img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=900&q=80" alt="Bãi biển Nha Trang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Cách di chuyển đến Nha Trang</h2>
<ul>
<li><strong>Máy bay:</strong> Sân bay Cam Ranh cách trung tâm 35km. Bay từ Hà Nội/TP.HCM: 1.5-2 tiếng</li>
<li><strong>Tàu hỏa:</strong> Từ Hà Nội: 24-26 tiếng | Từ TP.HCM: 7-8 tiếng</li>
<li><strong>Xe khách từ TP.HCM:</strong> 9-10 tiếng, giá 200.000 – 350.000đ</li>
</ul>

<h2>Hoạt động không thể bỏ qua</h2>
<p><strong>Tour 4 đảo:</strong> Hòn Mun, Hòn Tằm, Hòn Miễu, Hòn Một – lặn ngắm san hô, tắm biển và ăn hải sản. Giá: 200.000 – 350.000đ/người.</p>
<p><strong>Vinpearl Land:</strong> Công viên giải trí trên đảo, đi cáp treo qua biển. Giá: 900.000đ/người.</p>
<p><strong>Tắm bùn khoáng:</strong> Trải nghiệm độc đáo tại I-Resort hoặc Tháp Bà. Giá: 200.000 – 400.000đ/người.</p>
<img src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?w=900&q=80" alt="Lặn biển Nha Trang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Điểm tham quan văn hóa</h2>
<ul>
<li><strong>Tháp Bà Ponagar:</strong> Quần thể tháp Chăm hơn 1.000 năm tuổi</li>
<li><strong>Chùa Long Sơn:</strong> Chùa lớn nhất Nha Trang với tượng Phật trắng khổng lồ</li>
<li><strong>Viện Hải dương học:</strong> Bảo tàng sinh vật biển thú vị cho cả gia đình</li>
</ul>

<h2>Thời điểm đẹp nhất</h2>
<p>Tháng 1-8 là mùa khô, biển đẹp và ít mưa. Tháng 9-12 là mùa mưa bão, nên tránh. Đẹp nhất là tháng 3-6 khi nước biển trong nhất.</p>',
            ],
            [
                'title'      => 'Cẩm nang du lịch Hội An: Phố cổ, làng quê và biển An Bàng',
                'category'   => 'Cẩm nang',
                'location'   => 'Hội An, Quảng Nam',
                'image'      => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1200&q=85',
                'views'      => 2890,
                'user_index' => 6,
                'excerpt'    => 'Hội An không chỉ có phố cổ – còn có làng rau Trà Quế, làng gốm Thanh Hà, bãi biển An Bàng và ẩm thực đặc sắc không nơi nào có được.',
                'content'    => '
<h2>Hội An – Nhiều hơn một phố cổ</h2>
<p>Hội An được nhiều du khách quốc tế bình chọn là thành phố đẹp nhất châu Á. Nhưng Hội An không chỉ có phố cổ – đây còn là nơi có những làng nghề truyền thống, bãi biển hoang sơ và ẩm thực đặc sắc nhất miền Trung.</p>
<img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=900&q=80" alt="Phố cổ Hội An" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Cách di chuyển đến Hội An</h2>
<ul>
<li><strong>Từ Đà Nẵng:</strong> Taxi/Grab 30km, khoảng 200.000 – 300.000đ | Xe buýt: 30.000đ</li>
<li><strong>Từ Huế:</strong> Xe khách 120km, khoảng 3 tiếng</li>
<li><strong>Sân bay gần nhất:</strong> Đà Nẵng (30km)</li>
</ul>

<h2>Phố cổ Hội An</h2>
<p>Vé tham quan phố cổ: 120.000đ/người (vào 5 điểm tham quan). Nên đi bộ hoặc thuê xe đạp (50.000đ/ngày) để khám phá. Đẹp nhất vào buổi tối khi đèn lồng thắp sáng.</p>
<img src="https://images.unsplash.com/photo-1555400038-63f5ba517a47?w=900&q=80" alt="Đèn lồng Hội An" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">

<h2>Những trải nghiệm độc đáo</h2>
<p><strong>Làng rau Trà Quế:</strong> Tham gia trồng rau cùng nông dân, học nấu ăn với rau tươi hái từ vườn. Tour nửa ngày: 200.000 – 350.000đ.</p>
<p><strong>Làng gốm Thanh Hà:</strong> Tự tay nặn gốm trên bàn xoay, mang về sản phẩm tự làm. Giá: 50.000đ/người.</p>
<p><strong>Bãi biển An Bàng:</strong> Cách phố cổ 4km, bãi biển đẹp và ít đông hơn Cửa Đại. Nhiều quán cà phê view biển đẹp.</p>

<h2>Ẩm thực Hội An</h2>
<ul>
<li><strong>Cao lầu:</strong> Đặc sản chỉ có ở Hội An, 40.000 – 60.000đ/tô</li>
<li><strong>Bánh mì Phượng:</strong> Nổi tiếng thế giới, 25.000đ/ổ</li>
<li><strong>Cơm gà Hội An:</strong> Cơm trắng dẻo với gà xé phay, 40.000đ/suất</li>
<li><strong>Hoành thánh chiên:</strong> Bánh chiên giòn ăn với nước sốt cà chua</li>
</ul>',
            ],

        ]; // end $postsData


       // ===================== TẠO POSTS =====================
        
        // Khởi tạo một tập hợp Collection rỗng để chứa các Object bài viết sau khi tạo thành công ngoài Database
        $createdPosts = collect();
        
        // Vòng lặp foreach: Duyệt qua mảng dữ liệu cấu trúc bài viết mẫu $postsData
        foreach ($postsData as $data) {
            
            // Tìm kiếm người dùng làm tác giả: Dựa vào chỉ số 'user_index' trong mảng dữ liệu 
            // để bốc trúng Object User tương ứng từ tập hợp $users đã tạo ở phía trên
            $author = $users[$data['user_index']];
            
            // Tạo chuỗi đường dẫn (Slug) định danh duy nhất cho bài viết:
            // Lấy tiêu đề chuyển thành chuỗi không dấu (Str::slug) nối với một chuỗi ngẫu nhiên 5 ký tự (Str::random)
            // (Mục đích: Đảm bảo URL không bao giờ bị trùng lặp ngay cả khi hai bài viết có cùng tiêu đề)
            $slug   = Str::slug($data['title']) . '-' . Str::random(5);

            // Khởi tạo một thực thể (Instance) mới của Model Post
            $post = new Post();
            
            // setRawAttributes(): Hàm nâng cao dùng để đổ thẳng toàn bộ mảng dữ liệu thô vào các cột trong DB.
            // Phương thức này chạy cực nhanh vì nó đi xuyên qua (bỏ qua) cơ chế kiểm tra thuộc tính $fillable trong Model.
            $post->setRawAttributes([
                'user_id'     => $author->id,                    // ID của người dùng viết bài này (Khóa ngoại kết nối bảng users)
                'category_id' => $cats[$data['category']]->id,   // Lấy ID danh mục dựa vào Tên danh mục từ mảng bản đồ ánh xạ $cats
                'title'       => $data['title'],                 // Tiêu đề bài viết du lịch
                'slug'        => $slug,                          // Đường dẫn URL chuẩn SEO vừa tạo ở trên
                'excerpt'     => $data['excerpt'],               // Đoạn mô tả/trích dẫn ngắn của bài viết hiển thị ở danh sách ngoài trang chủ
                'content'     => $data['content'],               // Nội dung bài viết chi tiết
                'image'       => $data['image'],                 // Đường dẫn ảnh bìa bài viết
                'location'    => $data['location'],              // Tên địa danh/địa điểm du lịch (Ví dụ: Đà Lạt, Hạ Long)
                'views_count' => $data['views'],                 // Số lượt xem ban đầu phục vụ chức năng bài viết phổ biến
                'status'      => 'published',                    // Cố định trạng thái xuất bản công khai để web hiển thị và Chatbot quét được ngay
            ]);
            
            // Lưu thực thể bài viết này xuống cơ sở dữ liệu (Database)
            $post->save();
            
            // Đẩy Object bài viết vừa lưu thành công vào tập hợp chung $createdPosts bằng hàm push()
            // (Mục đích: Để lát nữa dùng mảng này đi gán ngẫu nhiên bình luận, đánh giá ở đoạn code phía dưới)
            $createdPosts->push($post);
        }

        // ===================== COMMENTS =====================
        $commentsPool = [
            'Bài viết rất hay và chi tiết! Mình vừa đi về và thấy đúng y chang những gì bạn mô tả. Cảm ơn bạn nhiều nhé!',
            'Thông tin rất hữu ích, mình đang lên kế hoạch cho chuyến đi tháng sau. Cho mình hỏi thêm về chi phí di chuyển nội địa được không?',
            'Ảnh đẹp quá! Bạn chụp bằng máy gì vậy? Mình cũng muốn có những bức ảnh đẹp như thế này.',
            'Mình đã đến đây 2 lần rồi và lần nào cũng không muốn về. Bài viết của bạn đã tóm tắt được hết những điểm hay nhất!',
            'Cảm ơn bạn đã chia sẻ kinh nghiệm thực tế. Mình thấy nhiều blog khác viết không đúng với thực tế, nhưng bài này rất chân thực.',
            'Bài viết bổ ích lắm! Mình bookmark lại để tham khảo khi đi. Bạn có thể chia sẻ thêm về thời tiết không?',
            'Tuyệt vời! Mình vừa đặt vé xong sau khi đọc bài này. Hy vọng chuyến đi sẽ đẹp như bạn mô tả.',
            'Thông tin về giá cả rất cập nhật và chính xác. Mình đã check và đúng như bạn nói. Cảm ơn!',
            'Bài viết hay nhưng mình nghĩ bạn nên thêm thông tin về phương tiện di chuyển công cộng nữa thì hoàn hảo hơn.',
            'Đọc bài này mà thèm đi du lịch quá! Mình đang tiết kiệm tiền để thực hiện chuyến đi này. Cảm ơn bạn đã chia sẻ!',
            'Mình đã đến đây theo gợi ý của bài viết này và không hối hận chút nào. Mọi thứ đều đúng như mô tả!',
            'Bài viết rất chi tiết và dễ đọc. Mình thích cách bạn chia nhỏ thông tin theo từng mục, rất dễ theo dõi.',
            'Cảm ơn bạn đã chia sẻ những mẹo tiết kiệm này! Mình áp dụng và tiết kiệm được khá nhiều so với dự kiến ban đầu.',
            'Hình ảnh trong bài đẹp quá, nhìn là muốn xách ba lô đi ngay. Bạn có thể chia sẻ thêm về mùa nào đẹp nhất không?',
            'Mình đã đọc nhiều bài về chủ đề này nhưng bài của bạn là đầy đủ và thực tế nhất. Rất đáng để bookmark!',
            'Lần đầu đến đây mình cũng lo lắng lắm nhưng nhờ bài viết này mà mình tự tin hơn nhiều. Cảm ơn tác giả!',
            'Bài viết giúp mình tiết kiệm được rất nhiều thời gian research. Thông tin đầy đủ và cập nhật, rất đáng tin cậy.',
            'Mình đã chia sẻ bài này cho cả nhóm bạn để cùng lên kế hoạch. Ai cũng thích và muốn đi ngay!',
        ];

        // VÒNG LẶP LỚN: Duyệt qua từng bài viết ($post) vừa được tạo thành công trong hệ thống
        foreach ($createdPosts as $post) {
            
            // rand(4, 8): Quyết định ngẫu nhiên số lượng bình luận cho bài viết này (tối thiểu 4 và tối đa 8 bình luận)
            $numComments = rand(4, 8);
            
            // $allUsers->shuffle(): Đảo trộn ngẫu nhiên vị trí danh sách tất cả người dùng (bao gồm cả Admin và User)
            // (Mục đích: Để mỗi bài viết sẽ có những người vào bình luận khác nhau, không bài nào giống bài nào)
            $shuffled    = $allUsers->shuffle();
            
            // Biến đếm để theo dõi xem bài viết hiện tại đã nhận đủ số lượng bình luận ngẫu nhiên hay chưa
            $count       = 0;
            
            // VÒNG LẶP NHỎ: Duyệt qua danh sách người dùng đã được xáo trộn để tiến hành viết bình luận
            foreach ($shuffled as $commenter) {
                
                // ĐIỀU KIỆN DỪNG: Nếu số lượng bình luận đã tạo đạt mức giới hạn ngẫu nhiên ($numComments) thì thoát vòng lặp nhỏ
                if ($count >= $numComments) break;
                
                // ĐIỀU KIỆN CHỐNG LOGIC SAI: Nếu ID người định bình luận trùng với ID tác giả bài viết ($post->user_id) 
                // thì bỏ qua (continue) để chuyển sang người tiếp theo (Tác giả không tự vào seeding bình luận cho chính mình)
                if ($commenter->id === $post->user_id) continue;
                
                // Tiến hành tạo bản ghi bình luận mới vào Database
                Comment::create([
                    'post_id'     => $post->id,       // Gán ID bài viết nhận bình luận (Khóa ngoại)
                    'user_id'     => $commenter->id,  // Gán ID người viết bình luận (Khóa ngoại)
                    
                    // array_rand(): Lấy ngẫu nhiên một câu bình luận từ kho nội dung chuẩn bị sẵn ($commentsPool)
                    'content'     => $commentsPool[array_rand($commentsPool)],
                    
                    'is_approved' => true,            // Cố định trạng thái đã kiểm duyệt để bình luận hiển thị ngay ngoài giao diện
                    
                    // Giả lập thời gian tạo (created_at) lùi về quá khứ ngẫu nhiên từ 1 đến 30 ngày và vài giờ trước
                    'created_at'  => now()->subDays(rand(1, 30))->subHours(rand(0, 23)),
                    
                    // Giả lập thời gian cập nhật (updated_at) lùi về quá khứ ngẫu nhiên từ 0 đến 5 ngày trước
                    'updated_at'  => now()->subDays(rand(0, 5)),
                ]);
                
                // Tăng biến đếm lên 1 đơn vị sau khi tạo thành công một bình luận
                $count++;
            } // Kết thúc vòng lặp người bình luận (Vòng lặp nhỏ)
        } // Kết thúc vòng lặp bài viết (Vòng lặp lớn)

        // ===================== RATINGS =====================
        // VÒNG LẶP LỚN: Duyệt qua từng bài viết ($post) có trong hệ thống để tiến hành nạp điểm đánh giá (Rating)
        foreach ($createdPosts as $post) {
            
            // rand(5, 9): Quyết định ngẫu nhiên số lượng người sẽ vào chấm điểm cho bài viết này (từ 5 đến 9 người)
            $numRatings = rand(5, 9);
            
            // shuffle(): Xáo trộn ngẫu nhiên danh sách toàn bộ người dùng để tạo sự khách quan (không bài nào có nhóm người chấm giống bài nào)
            $shuffled   = $allUsers->shuffle();
            
            // Biến đếm số lượng đánh giá đã nạp thành công cho bài viết hiện tại
            $count      = 0;
            
            // VÒNG LẶP NHỎ: Duyệt qua danh sách người dùng đã xáo trộn để bốc người ra chấm điểm
            foreach ($shuffled as $rater) {
                
                // ĐIỀU KIỆN DỪNG: Nếu số lượt đánh giá đã đạt đủ số lượng ngẫu nhiên định sẵn ($numRatings) thì dừng vòng lặp nhỏ
                if ($count >= $numRatings) break;
                
                // ĐIỀU KIỆN RÀNG BUỘC: Nếu ID người chấm trùng với ID tác giả bài viết thì bỏ qua (Tác giả không được tự chấm điểm bài mình)
                if ($rater->id === $post->user_id) continue;
                
                // MẸO TÂM LÝ DỮ LIỆU: Khởi tạo một tập hợp điểm số với tỷ lệ xuất hiện điểm 4 và 5 sao cực kỳ dày đặc, 
                // sau đó dùng random() để bốc ngẫu nhiên. (Giúp điểm trung bình của các bài viết du lịch luôn đẹp, từ 4.0 trở lên)
                $score = collect([5, 5, 5, 4, 4, 4, 3, 5, 4])->random();
                
                // Sử dụng khối try-catch để bao bọc lệnh ghi dữ liệu nhằm phòng chống lỗi hệ thống (Crash)
                try {
                    // Tiến hành tạo bản ghi đánh giá sao mới vào Database
                    Rating::create([
                        'post_id'    => $post->id,       // ID bài viết được đánh giá (Khóa ngoại)
                        'user_id'    => $rater->id,      // ID người thực hiện chấm điểm (Khóa ngoại)
                        'score'      => $score,          // Số sao ngẫu nhiên bốc từ tập hợp phía trên (1 đến 5 sao)
                        
                        // Giả lập thời gian tạo lùi về quá khứ ngẫu nhiên từ 1 đến 20 ngày trước
                        'created_at' => now()->subDays(rand(1, 20)),
                        
                        // Giả lập thời gian cập nhật lùi về quá khứ ngẫu nhiên từ 0 đến 5 ngày trước
                        'updated_at' => now()->subDays(rand(0, 5)),
                    ]);
                    
                    // Tăng biến đếm lên 1 đơn vị sau khi nạp thành công một bản ghi Rating
                    $count++;
                    
                } catch (\Exception $e) { 
                    /* BẰNG CHỨNG GIẢI THÍCH: Khối catch này sẽ bắt tất cả các ngoại lệ (Exception). 
                       Nếu một người dùng vô tình bị hệ thống bốc trúng 2 lần để đánh giá cùng 1 bài viết, 
                       Database sẽ chặn lại vì dính khóa Unique(user_id, post_id). 
                       Lệnh này sẽ âm thầm bỏ qua bản ghi lỗi đó để file Seeder tiếp tục chạy trơn tru mà không bị dừng đột ngột. */
                }
            } // Kết thúc vòng lặp người đánh giá (Vòng lặp nhỏ)
        } // Kết thúc vòng lặp bài viết (Vòng lặp lớn)

        // ===================== FAVORITES =====================
        // VÒNG LẶP LỚN: Duyệt qua từng bài viết ($post) hiện có để tiến hành nạp dữ liệu người dùng yêu thích (Favorite)
        foreach ($createdPosts as $post) {
            
            // rand(3, 6): Quyết định ngẫu nhiên số lượng người sẽ bấm "Lưu bài viết" này vào danh sách yêu thích (từ 3 đến 6 người)
            $numFavs  = rand(3, 6);
            
            // shuffle(): Đảo trộn ngẫu nhiên thứ tự toàn bộ người dùng để tạo sự tự nhiên cho dữ liệu hệ thống
            $shuffled = $allUsers->shuffle();
            
            // Biến đếm số lượng bản ghi yêu thích đã được gán thành công cho bài viết hiện tại
            $count    = 0;
            
            // VÒNG LẶP NHỎ: Duyệt qua danh sách người dùng đã được xáo trộn để bốc người ra bấm "Yêu thích"
            foreach ($shuffled as $faver) {
                
                // ĐIỀU KIỆN DỪNG: Nếu số lượng người lưu bài viết đã đạt đủ giới hạn ngẫu nhiên ($numFavs) thì thoát vòng lặp nhỏ
                if ($count >= $numFavs) break;
                
                // ĐIỀU KIỆN RÀNG BUỘC: Nếu ID người dùng trùng với ID tác giả bài viết thì bỏ qua (Tác giả không tự lưu bài viết của chính mình)
                if ($faver->id === $post->user_id) continue;
                
                // Sử dụng khối try-catch để bao bọc quá trình ghi dữ liệu nhằm phòng chống lỗi trùng lặp bản ghi
                try {
                    // Tiến hành tạo bản ghi yêu thích mới vào Database
                    Favorite::create([
                        'post_id'    => $post->id,       // ID bài viết được bấm yêu thích (Khóa ngoại kết nối bảng posts)
                        'user_id'    => $faver->id,      // ID người bấm nút yêu thích bài viết này (Khóa ngoại kết nối bảng users)
                        
                        // Giả lập thời gian bấm nút "Lưu" lùi về quá khứ ngẫu nhiên từ 1 đến 15 ngày trước
                        'created_at' => now()->subDays(rand(1, 15)),
                        
                        // Giả lập thời gian cập nhật lùi về quá khứ ngẫu nhiên từ 0 đến 3 ngày trước
                        'updated_at' => now()->subDays(rand(0, 3)),
                    ]);
                    
                    // Tăng biến đếm lên 1 đơn vị sau khi nạp thành công một bản ghi Favorite
                    $count++;
                    
                } catch (\Exception $e) { 
                    /* BỎ QUA TRÙNG LẶP: Khối catch này sẽ bắt ngoại lệ nếu người dùng này vô tình bị bốc trúng 2 lần 
                       để yêu thích cùng một bài viết. Database sẽ chặn lại vì dính ràng buộc Unique(user_id, post_id). 
                       Lệnh này sẽ âm thầm bỏ qua để file Seeder tiếp tục chạy trơn tru mà không bị sập hệ thống nửa chừng. */
                }
            } // Kết thúc vòng lặp người yêu thích bài viết (Vòng lặp nhỏ)
        } // Kết thúc vòng lặp bài viết (Vòng lặp lớn)

        // IN THÔNG BÁO THÀNH CÔNG RA TERMINAL:
        // Sử dụng thuộc tính $this->command->info() của Laravel Seeder để in ra một dòng chữ màu xanh lá cây 
        // báo cáo chính xác tổng số lượng bài viết đã được nạp kèm đầy đủ ảnh, bình luận, đánh giá và lượt thích thành công!
        $this->command->info('✅ Seeded: ' . $createdPosts->count() . ' bài viết published với ảnh, comments, ratings và favorites!');
        // ===================== BÀI VIẾT NHÁP (draft) =====================
        $draftPostsData = [
            [
                'title'      => '[NHÁP] Khám phá Côn Đảo – Hòn đảo thiêng liêng và hoang sơ nhất Việt Nam',
                'category'   => 'Điểm đến',
                'location'   => 'Côn Đảo, Bà Rịa – Vũng Tàu',
                'image'      => 'https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=1200&q=85',
                'views'      => 0,
                'user_index' => 2,
                'excerpt'    => 'Côn Đảo – nơi lịch sử bi hùng gặp gỡ thiên nhiên hoang sơ tuyệt đẹp. Bài viết đang được hoàn thiện.',
                'content'    => '
<h2>Côn Đảo – Viên ngọc ẩn của Việt Nam</h2>
<p>Côn Đảo là quần đảo gồm 16 hòn đảo lớn nhỏ nằm ngoài khơi tỉnh Bà Rịa – Vũng Tàu. Nơi đây nổi tiếng với lịch sử bi hùng của nhà tù Côn Đảo thời thực dân Pháp và đế quốc Mỹ, đồng thời sở hữu thiên nhiên hoang sơ tuyệt đẹp với những bãi biển trong vắt và rừng nguyên sinh.</p>
<img src="https://images.unsplash.com/photo-1537956965359-7573183d1f57?w=900&q=80" alt="Côn Đảo" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><em>[Bài viết đang được hoàn thiện – sẽ cập nhật thêm thông tin về lịch trình, chi phí và kinh nghiệm di chuyển...]</em></p>
<h3>Những điểm không thể bỏ qua</h3>
<ul>
<li>Nhà tù Côn Đảo – Di tích lịch sử quốc gia đặc biệt</li>
<li>Nghĩa trang Hàng Dương – Nơi an nghỉ của hàng nghìn liệt sĩ</li>
<li>Bãi Đầm Trầu – Bãi biển đẹp nhất Côn Đảo</li>
<li>Vườn Quốc gia Côn Đảo – Rùa biển đẻ trứng</li>
</ul>',
            ],
            [
                'title'      => '[NHÁP] Ẩm thực Tây Nguyên: Những món ăn độc đáo của đồng bào dân tộc',
                'category'   => 'Ẩm thực',
                'location'   => 'Tây Nguyên',
                'image'      => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=1200&q=85',
                'views'      => 0,
                'user_index' => 4,
                'excerpt'    => 'Ẩm thực Tây Nguyên mang đậm bản sắc văn hóa của các dân tộc Ê Đê, Ba Na, Gia Rai với những nguyên liệu độc đáo từ rừng núi.',
                'content'    => '
<h2>Ẩm thực Tây Nguyên – Hương vị của đại ngàn</h2>
<p>Tây Nguyên không chỉ nổi tiếng với cà phê và cao nguyên đất đỏ mà còn có nền ẩm thực phong phú, độc đáo của các dân tộc thiểu số. Mỗi món ăn đều gắn liền với văn hóa, tín ngưỡng và cuộc sống của người dân bản địa.</p>
<img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=900&q=80" alt="Ẩm thực Tây Nguyên" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><em>[Đang bổ sung thêm nội dung về các món ăn đặc trưng và địa chỉ thưởng thức...]</em></p>
<h3>Các món ăn đặc trưng</h3>
<ul>
<li>Cơm lam – Cơm nấu trong ống tre</li>
<li>Gà nướng đất sét</li>
<li>Rượu cần – Thức uống truyền thống</li>
<li>Canh thụt – Đặc sản của người Ê Đê</li>
</ul>',
            ],
            [
                'title'      => '[NHÁP] Kinh nghiệm xin visa và du lịch Nhật Bản tự túc',
                'category'   => 'Cẩm nang',
                'location'   => 'Nhật Bản',
                'image'      => 'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=1200&q=85',
                'views'      => 0,
                'user_index' => 0,
                'excerpt'    => 'Hướng dẫn xin visa Nhật Bản và kinh nghiệm du lịch tự túc tại xứ sở hoa anh đào. Bài viết đang được hoàn thiện.',
                'content'    => '
<h2>Du lịch Nhật Bản tự túc – Giấc mơ trong tầm tay</h2>
<p>Nhật Bản từ lâu đã là điểm đến mơ ước của nhiều người Việt Nam. Với hệ thống giao thông công cộng hiện đại, an toàn tuyệt đối và văn hóa độc đáo, Nhật Bản là điểm đến hoàn hảo cho chuyến du lịch tự túc.</p>
<img src="https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=900&q=80" alt="Nhật Bản" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><em>[Đang hoàn thiện phần hướng dẫn xin visa và lịch trình chi tiết...]</em></p>
<h3>Thủ tục xin visa Nhật Bản</h3>
<ul>
<li>Hồ sơ cần chuẩn bị: hộ chiếu, ảnh, giấy tờ tài chính</li>
<li>Nộp hồ sơ tại Đại sứ quán Nhật Bản hoặc trung tâm visa</li>
<li>Thời gian xử lý: 5-7 ngày làm việc</li>
<li>Lệ phí: miễn phí (visa du lịch)</li>
</ul>',
            ],
            [
                'title'      => '[NHÁP] Review khách sạn capsule Hà Nội – Trải nghiệm ngủ kiểu Nhật giữa lòng thủ đô',
                'category'   => 'Khách sạn',
                'location'   => 'Hà Nội',
                'image'      => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1200&q=85',
                'views'      => 0,
                'user_index' => 3,
                'excerpt'    => 'Khách sạn capsule – xu hướng lưu trú mới tại Hà Nội với giá rẻ, tiện nghi và trải nghiệm độc đáo kiểu Nhật Bản.',
                'content'    => '
<h2>Khách sạn Capsule – Xu hướng lưu trú mới tại Hà Nội</h2>
<p>Capsule hotel – loại hình khách sạn với những "khoang ngủ" nhỏ gọn kiểu Nhật Bản đang dần xuất hiện tại Hà Nội. Đây là lựa chọn lý tưởng cho solo traveler muốn tiết kiệm chi phí nhưng vẫn có không gian riêng tư.</p>
<img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=900&q=80" alt="Capsule hotel" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><em>[Đang bổ sung thêm review chi tiết và so sánh các capsule hotel tại Hà Nội...]</em></p>
<h3>Ưu điểm của capsule hotel</h3>
<ul>
<li>Giá rẻ: 200.000 – 400.000đ/đêm</li>
<li>Có không gian riêng tư với rèm che</li>
<li>Tiện nghi đầy đủ: ổ cắm, đèn đọc sách, wifi</li>
<li>Khu vực sinh hoạt chung rộng rãi</li>
</ul>',
            ],
            [
                'title'      => '[NHÁP] Checkin mùa hoa tam giác mạch Hà Giang – Tím ngắt cả trời',
                'category'   => 'Checkin',
                'location'   => 'Hà Giang',
                'image'      => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1200&q=85',
                'views'      => 0,
                'user_index' => 5,
                'excerpt'    => 'Mùa hoa tam giác mạch Hà Giang (tháng 10-12) là thời điểm đẹp nhất để check-in với những cánh đồng hoa tím ngắt trải dài.',
                'content'    => '
<h2>Hoa tam giác mạch Hà Giang – Mùa hoa đẹp nhất năm</h2>
<p>Mỗi năm vào khoảng tháng 10 đến tháng 12, cao nguyên đá Đồng Văn lại khoác lên mình tấm áo tím hồng của hoa tam giác mạch. Đây là thời điểm Hà Giang đẹp nhất và thu hút đông đảo du khách nhất trong năm.</p>
<img src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=900&q=80" alt="Hoa tam giác mạch Hà Giang" style="width:100%;border-radius:12px;margin:1.5rem 0;object-fit:cover;max-height:420px;">
<p><em>[Đang bổ sung thêm thông tin về địa điểm ngắm hoa đẹp nhất và kinh nghiệm di chuyển...]</em></p>
<h3>Thời điểm hoa nở đẹp nhất</h3>
<ul>
<li>Đầu tháng 10: Hoa bắt đầu nở ở vùng thấp</li>
<li>Giữa tháng 10 – đầu tháng 11: Đẹp nhất, hoa nở rộ khắp nơi</li>
<li>Tháng 11 – 12: Hoa tàn dần nhưng vẫn còn đẹp</li>
</ul>',
            ],
        ];

        // VÒNG LẶP FOREACH: Duyệt qua mảng dữ liệu mẫu chứa các bài viết nháp ($draftPostsData)
        foreach ($draftPostsData as $data) {
            
            // Xác định tác giả bài nháp: Dựa vào 'user_index' để bốc đúng Object User trong tập hợp $users
            $author = $users[$data['user_index']];
            
            // Khởi tạo chuỗi đường dẫn (Slug) đặc trưng cho bài nháp:
            // Tiêu đề không dấu + chèn thêm chữ '-draft-' + chuỗi ngẫu nhiên 5 ký tự để phân biệt hoàn toàn với bài đã xuất bản
            $slug   = Str::slug($data['title']) . '-draft-' . Str::random(5);
            
            // Khởi tạo một thực thể (Instance) mới của Model Post
            $post   = new Post();
            
            // setRawAttributes(): Nạp thẳng dữ liệu thô vào đối tượng để bỏ qua bộ lọc $fillable, giúp tăng tốc độ ghi DB
            $post->setRawAttributes([
                'user_id'     => $author->id,                    // ID người tạo bài nháp
                'category_id' => $cats[$data['category']]->id,   // ID danh mục được ánh xạ từ tên danh mục
                'title'       => $data['title'],                 // Tiêu đề bài viết nháp
                'slug'        => $slug,                          // Đường dẫn URL duy nhất dành riêng cho bài nháp
                'excerpt'     => $data['excerpt'],               // Mô tả ngắn của bài viết
                'content'     => $data['content'],               // Nội dung chi tiết của bài viết
                'image'       => $data['image'],                 // Đường dẫn ảnh minh họa
                'location'    => $data['location'],              // Địa danh du lịch gắn liền với bài viết
                'views_count' => 0,                              // Đặt số lượt xem mặc định bằng 0 (Vì bài nháp chưa ai được đọc công khai)
                'status'      => 'draft',                        // ĐẶT TRẠNG THÁI LÀ 'draft' (Bài nháp - Chờ kiểm duyệt hoặc chưa xuất bản)
            ]);
            
            // Lưu bài viết nháp này xuống cơ sở dữ liệu
            $post->save();
        } // Kết thúc vòng lặp tạo bài viết nháp

        // IN THÔNG BÁO THÀNH CÔNG RA TERMINAL:
        // Sử dụng hàm count() thuần của PHP để đếm tổng số phần tử trong mảng dữ liệu mẫu $draftPostsData
        // và in ra dòng thông báo màu xanh lá cây báo hiệu đã nạp xong các bài viết nháp thành công!
        $this->command->info('✅ Seeded: ' . count($draftPostsData) . ' bài viết nháp (draft)!');

        // ===================== BÌNH LUẬN CHƯA DUYỆT =====================
        $pendingComments = [
            'Bài viết hay lắm! Mình muốn hỏi thêm về vấn đề đặt phòng trước bao lâu thì tốt nhất?',
            'Mình vừa đi về hôm qua, thực tế có một số điểm khác với bài viết nhưng nhìn chung vẫn đúng. Cảm ơn tác giả!',
            'Giá cả trong bài có vẻ hơi cũ rồi, hiện tại đã tăng khá nhiều so với những gì bạn đề cập.',
            'Bài viết rất chi tiết! Nhưng mình thấy thiếu thông tin về chỗ đổi tiền và ATM ở khu vực này.',
            'Mình đã chia sẻ bài này lên group du lịch và được rất nhiều người like. Cảm ơn bạn đã viết bài chất lượng!',
            'Lần đầu đọc blog này, thấy nội dung rất hay và hữu ích. Mình sẽ theo dõi thêm các bài viết khác.',
            'Bạn có thể cho mình biết thêm về cách di chuyển từ sân bay về trung tâm không? Bài chưa đề cập đến phần này.',
            'Mình đã đặt tour theo gợi ý của bài viết và rất hài lòng. Hướng dẫn viên nhiệt tình, cảnh đẹp như mô tả!',
            'Thông tin rất hữu ích nhưng mình nghĩ bạn nên cập nhật lại giá vé vì đã thay đổi từ đầu năm nay.',
            'Cảm ơn bài viết! Mình có một câu hỏi: nếu đi vào mùa mưa thì có nên đi không hay chờ mùa khô?',
            'Bài viết quá hay! Mình đã lưu lại để tham khảo cho chuyến đi sắp tới. Cảm ơn tác giả rất nhiều!',
            'Mình thấy bài viết này rất thực tế, không phóng đại như nhiều blog khác. Đáng tin cậy!',
        ];

        // 1. TRUY VẤN DỮ LIỆU: Lấy tất cả các bài viết đã xuất bản công khai (status = published) từ Database
        $publishedPosts = Post::where('status', 'published')->get();
        
        // Khởi tạo biến đếm để theo dõi tổng số bình luận chờ duyệt được tạo ra thành công
        $pendingCount   = 0;

        // VÒNG LẶP FOREACH: Sử dụng hàm random() kết hợp hàm min() để bốc ngẫu nhiên tối đa 12 bài viết 
        // (Nếu tổng số bài viết ít hơn 12, hàm min() sẽ tự lấy toàn bộ số lượng bài viết hiện có để tránh lỗi hệ thống)
        foreach ($publishedPosts->random(min(12, $publishedPosts->count())) as $i => $post) {
            
            // Bốc ngẫu nhiên người dùng đầu tiên từ danh sách đã xáo trộn để đóng vai trò người bình luận
            $commenter = $allUsers->shuffle()->first();
            
            // VÒNG LẶP WHILE (THUẬT TOÁN CHẶN LỖI): Đảm bảo người bình luận không được trùng với tác giả bài viết ($post->user_id).
            // Nếu vô tình trùng, vòng lặp while sẽ tiếp tục bắt ép hệ thống xáo trộn và bốc lại người khác cho đến khi khác ID tác giả mới chịu dừng.
            while ($commenter->id === $post->user_id) {
                $commenter = $allUsers->shuffle()->first();
            }
            
            // Tiến hành ghi mới bản ghi bình luận chờ duyệt vào Database
            Comment::create([
                'post_id'     => $post->id,       // Gán ID bài viết nhận bình luận (Khóa ngoại)
                'user_id'     => $commenter->id,  // Gán ID người viết bình luận (Khóa ngoại)
                
                // THUẬT TOÁN TOÁN HỌC PHÉP CHIA LẤY DƯ (%): Lấy chỉ số vòng lặp $i chia lấy dư cho tổng số câu mẫu có trong mảng $pendingComments.
                // (Mục đích: Đảm bảo rải đều các mẫu câu bình luận khác nhau cho các bài viết mà không lo bị vượt quá chỉ số phần tử của mảng)
                'content'     => $pendingComments[$i % count($pendingComments)],
                
                'is_approved' => false,           // ĐẶT TRẠNG THÁI LÀ FALSE: Nghĩa là bình luận này đang ở trạng thái CHỜ DUYỆT (Ẩn ngoài giao diện)
                
                // Giả lập thời gian tạo lùi về quá khứ rất gần, chỉ từ 1 đến 48 giờ trước (Tạo cảm giác bình luận vừa mới được gửi lên)
                'created_at'  => now()->subHours(rand(1, 48)),
                
                // Giả lập thời gian cập nhật lùi về quá khứ ngẫu nhiên từ 0 đến 12 giờ trước
                'updated_at'  => now()->subHours(rand(0, 12)),
            ]);
            
            // Tăng biến đếm lên 1 đơn vị sau khi tạo thành công một bình luận chờ duyệt
            $pendingCount++;
        } // Kết thúc vòng lặp tạo bình luận chờ duyệt

        // IN THÔNG BÁO THÀNH CÔNG RA TERMINAL:
        // Sử dụng thuộc tính $this->command->info() để in ra dòng chữ thông báo màu xanh lá cây 
        // báo cáo chính xác số lượng bình luận chờ duyệt vừa được nạp vào hệ thống để Admin test.
        $this->command->info('✅ Seeded: ' . $pendingCount . ' bình luận chờ duyệt (is_approved = false)!');
    }
}