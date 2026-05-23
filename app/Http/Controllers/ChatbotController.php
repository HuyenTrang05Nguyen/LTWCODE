<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use App\Models\Post;
use App\Models\Category;

class ChatbotController extends Controller
{
    /**
     * POST /chatbot — Xử lý tin nhắn từ Client gửi lên
     */
    public function chat(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào: Tin nhắn không được để trống và tối đa 500 ký tự
        $request->validate([
            'message' => 'required|string|min:1|max:500',
        ]);

        // Loại bỏ khoảng trắng thừa ở hai đầu tin nhắn
        $message = trim($request->input('message'));

        // Cấu hình giới hạn tần suất gửi (Rate Limiting) dựa trên IP người dùng để tránh bị spam tin nhắn
        $ip       = $request->ip();
        $cacheKey = 'chatbot_rate_' . md5($ip); // Tạo một mã khóa duy nhất trong bộ nhớ Cache cho mỗi IP

        try {
            // Lấy số lần đã nhắn trong vòng 1 phút qua (mặc định bằng 0 nếu chưa nhắn lần nào)
            $count = Cache::get($cacheKey, 0);
            
            // Nếu nhắn quá 30 lần trong 1 phút, chặn lại và trả về mã lỗi 429 (Too Many Requests)
            if ($count >= 30) {
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn gửi quá nhiều tin nhắn. Vui lòng thử lại sau 1 phút! ⏳',
                    'posts'   => [],
                ], 429);
            }
            // Nếu chưa vượt giới hạn, tăng số lần nhắn lên 1 và lưu lại trong Cache với thời hạn 60 giây (1 phút)
            Cache::put($cacheKey, $count + 1, 60);
        } catch (\Exception $e) {
            // Ghi log cảnh báo nếu bộ nhớ Cache gặp sự cố (ví dụ khi deploy lên các hosting free bị giới hạn ghi file)
            Log::warning('Chatbot Cache Driver Warning: ' . $e->getMessage());
        }

        // Gọi hàm nội bộ để phân tích tin nhắn và tìm câu trả lời phù hợp
        $result = $this->processMessage($message);

        // Trả kết quả về cho giao diện (gồm văn bản phản hồi và mảng các bài viết gợi ý đi kèm) dưới dạng JSON
        return response()->json([
            'success' => true,
            'message' => $result['text'],
            'posts'   => $result['posts'],
        ]);
    }

    /**
     * ĐIỀU HƯỚNG VÀ PHÂN TÍCH TIN NHẮN THEO TỪ KHÓA ĐỂ TRẢ LỜI
     */
    private function processMessage(string $message): array
    {
        // Chuyển toàn bộ tin nhắn thành chữ thường để không phân biệt chữ hoa, chữ thường khi so khớp
        $msg = mb_strtolower($message, 'UTF-8');

        // Nhánh 1: Nếu chứa từ khóa chào hỏi
        if ($this->contains($msg, ['xin chào', 'chào', 'hello', 'hi', 'hey', 'alo'])) {
            return ['text' => $this->greet(), 'posts' => []];
        }
        // Nhánh 2: Nếu chứa từ khóa cảm ơn
        if ($this->contains($msg, ['cảm ơn', 'cám ơn', 'thanks', 'thank you', 'tks'])) {
            return ['text' => 'Không có gì! Tôi luôn sẵn sàng hỗ trợ bạn. Bạn cần tư vấn thêm gì không? 😊', 'posts' => []];
        }
        // Nhánh 3: Nếu chứa từ khóa tạm biệt
        if ($this->contains($msg, ['tạm biệt', 'bye', 'goodbye', 'hẹn gặp lại'])) {
            return ['text' => 'Tạm biệt! Chúc bạn có chuyến du lịch thật vui vẻ! ✈️🌏', 'posts' => []];
        }
        // Nhánh 4: Kiểm tra và tìm kiếm bài viết theo Địa điểm (Hà Nội, Đà Nẵng...)
        $loc = $this->searchByLocation($msg);
        if ($loc) return $loc;
        
        // Nhánh 5: Kiểm tra và tìm kiếm bài viết theo Danh mục (Ẩm thực, Điểm đến...)
        $cat = $this->searchByCategory($msg);
        if ($cat) return $cat;
        
        // Nhánh 6: Tư vấn khi người dùng hỏi về Chi phí / Giá cả / Ngân sách
        if ($this->contains($msg, ['chi phí', 'bao nhiêu tiền', 'giá', 'ngân sách', 'tiết kiệm', 'rẻ', 'budget'])) {
            return $this->budgetAdvice($msg);
        }
        // Nhánh 7: Tư vấn khi hỏi về Thời gian / Mùa / Thời tiết lý tưởng để đi du lịch
        if ($this->contains($msg, ['khi nào', 'tháng mấy', 'mùa nào', 'thời điểm', 'thời tiết', 'mùa'])) {
            return ['text' => $this->seasonAdvice($msg), 'posts' => []];
        }
        // Nhánh 8: Tư vấn khi hỏi về Ẩm thực / Đặc sản / Quán ăn ngon
        if ($this->contains($msg, ['ăn gì', 'món ăn', 'đặc sản', 'ẩm thực', 'quán ăn', 'nhà hàng', 'food'])) {
            return $this->foodAdvice($msg);
        }
        // Nhánh 9: Tư vấn khi tìm Chỗ ở / Khách sạn / Homestay
        if ($this->contains($msg, ['khách sạn', 'homestay', 'resort', 'ở đâu', 'lưu trú', 'phòng', 'hotel'])) {
            return $this->hotelAdvice($msg);
        }
        // Nhánh 10: Tư vấn về Cách thức di chuyển / Phương tiện đi lại
        if ($this->contains($msg, ['đi bằng gì', 'phương tiện', 'xe', 'máy bay', 'tàu', 'di chuyển', 'đường đi'])) {
            return ['text' => $this->transportAdvice($msg), 'posts' => []];
        }
        // Nhánh 11: Giới thiệu thông tin tổng quan của hệ thống website TravelGuide
        if ($this->contains($msg, ['website', 'trang web', 'travelguide', 'bài viết', 'danh mục', 'tìm kiếm'])) {
            return ['text' => $this->websiteInfo(), 'posts' => []];
        }
        // Nhánh 12: Nếu không khớp các từ khóa trên, tiến hành tách từ để tìm kiếm tự do trong database
        $search = $this->searchPosts($message);
        if ($search) return $search;

        // Nhánh 13: Trường hợp bot hoàn toàn không hiểu từ khóa nào, đưa ra câu trả lời mặc định hướng dẫn người dùng
        return ['text' => $this->fallback($message), 'posts' => []];
    }

    // Trả về câu chào mừng và thống kê nhanh số lượng bài viết, danh mục đang có trên hệ thống
    private function greet(): string
    {
        $totalPosts = Post::where('status', 'published')->count();
        $totalCats  = Category::count();
        return "Xin chào! Tôi là **TravelBot** 🌏\n"
             . "Tôi có thể giúp bạn:\n"
             . "• 🗺️ Tư vấn địa điểm du lịch\n"
             . "• 🍜 Gợi ý ẩm thực đặc sản\n"
             . "• 🏨 Tìm khách sạn, homestay\n"
             . "• 💰 Lập kế hoạch chi phí\n"
             . "• 📖 Tìm bài viết (hiện có **{$totalPosts} bài** trong **{$totalCats} danh mục**)\n\n"
             . "Bạn muốn khám phá đâu hôm nay? ✈️";
    }

    // Nhận diện địa danh trong câu hỏi và lấy ra tối đa 3 bài viết nổi bật nhất thuộc địa danh đó
    private function searchByLocation(string $msg): ?array
    {
        // Bản đồ từ khóa tiếng Việt có dấu tương ứng với các biến thể tìm kiếm trong database
        $locations = [
            'đà nẵng'     => ['Da Nang', 'Đà Nẵng'],
            'hà nội'      => ['Ha Noi', 'Hà Nội'],
            'hội an'      => ['Hoi An', 'Hội An'],
            'phú quốc'    => ['Phu Quoc', 'Phú Quốc'],
            'đà lạt'      => ['Da Lat', 'Đà Lạt'],
            'nha trang'   => ['Nha Trang'],
            'sapa'        => ['Sapa', 'Sa Pa'],
            'hạ long'     => ['Ha Long', 'Hạ Long'],
            'huế'         => ['Hue', 'Huế'],
            'hà giang'    => ['Ha Giang', 'Hà Giang'],
            'ninh bình'   => ['Ninh Binh', 'Ninh Bình'],
            'sài gòn'     => ['Sai Gon', 'TP. Hồ Chí Minh', 'HCM'],
            'hồ chí minh' => ['Ho Chi Minh', 'TP.HCM'],
            'mũi né'      => ['Mui Ne', 'Mũi Né'],
            'phong nha'   => ['Phong Nha'],
            'cần thơ'     => ['Can Tho', 'Cần Thơ'],
        ];

        foreach ($locations as $keyword => $searchTerms) {
            // Nếu phát hiện tin nhắn chứa từ khóa địa danh
            if (mb_strpos($msg, $keyword, 0, 'UTF-8') !== false) {
                // Truy vấn tìm bài viết khớp tên vị trí hoặc tiêu đề, sắp xếp theo lượt xem nhiều nhất
                $posts = Post::where('status', 'published')
                    ->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->orWhere('location', 'like', "%{$term}%")
                              ->orWhere('title', 'like', "%{$term}%");
                        }
                    })
                    ->orderBy('views_count', 'desc')
                    ->take(3)
                    ->get();

                $locationName = ucwords($keyword);
                // Nếu địa danh này hợp lệ nhưng database chưa viết bài nào
                if ($posts->isEmpty()) {
                    return [
                        'text'  => "Tôi chưa có bài viết về **{$locationName}** nhưng đây là điểm đến tuyệt vời! 🌟\nBạn có thể tìm kiếm thêm tại trang Bài viết nhé.",
                        'posts' => [],
                    ];
                }

                // Nếu tìm thấy, chuẩn bị dữ liệu mảng bài viết để gửi về cho giao diện hiển thị thành các Card
                $text = "Tôi tìm thấy **{$posts->count()} bài viết** về **{$locationName}** 📍\n";
                $postData = [];
                foreach ($posts as $post) {
                    $excerpt = $post->excerpt ? mb_substr(strip_tags($post->excerpt), 0, 70, 'UTF-8') . '...' : '';
                    $postData[] = [
                        'title'    => $post->title,
                        'slug'     => $post->slug,
                        'location' => $post->location,
                        'views'    => number_format($post->views_count),
                        'excerpt'  => $excerpt,
                    ];
                }
                return ['text' => $text, 'posts' => $postData];
            }
        }
        return null;
    }

    // Nhận diện từ khóa để tìm kiếm bài viết dựa theo Danh mục (Categories) có trong database
    private function searchByCategory(string $msg): ?array
    {
        $categoryMap = [
            'ẩm thực'     => ['ăn', 'món', 'food', 'ẩm thực', 'đặc sản', 'quán'],
            'điểm đến'    => ['điểm đến', 'địa điểm', 'tham quan', 'du lịch', 'khám phá'],
            'checkin'     => ['checkin', 'check-in', 'sống ảo', 'chụp ảnh', 'instagrammable'],
            'kinh nghiệm' => ['kinh nghiệm', 'mẹo', 'tips', 'bí kíp', 'lưu ý'],
            'khách sạn'   => ['khách sạn', 'resort', 'homestay', 'lưu trú', 'ở đâu'],
            'cẩm nang'    => ['cẩm nang', 'hướng dẫn', 'guide', 'lịch trình'],
        ];

        foreach ($categoryMap as $catName => $keywords) {
            foreach ($keywords as $kw) {
                if (mb_strpos($msg, $kw, 0, 'UTF-8') !== false) {
                    // Tìm bản ghi danh mục tương ứng trong bảng categories
                    $category = Category::where('name', 'like', "%{$catName}%")->first();
                    if (!$category) continue;

                    // Lấy ra 3 bài viết có lượt xem cao nhất thuộc danh mục này
                    $posts = Post::where('status', 'published')
                        ->where('category_id', $category->id)
                        ->orderBy('views_count', 'desc')
                        ->take(3)
                        ->get();

                    if ($posts->isEmpty()) {
                        return ['text' => "Danh mục **{$catName}** hiện chưa có bài viết. Hãy quay lại sau nhé! 😊", 'posts' => []];
                    }

                    $text = "📂 Danh mục **{$catName}** — Top bài viết nổi bật:\n";
                    $postData = [];
                    foreach ($posts as $post) {
                        $postData[] = [
                            'title'    => $post->title,
                            'slug'     => $post->slug,
                            'location' => $post->location,
                            'views'    => number_format($post->views_count),
                            'excerpt'  => '',
                        ];
                    }
                    return ['text' => $text, 'posts' => $postData];
                }
            }
        }
        return null;
    }

    // Trả về các mẹo tiết kiệm chi phí và đính kèm các bài viết cẩm nang chi phí rẻ
    private function budgetAdvice(string $msg): array
    {
        $posts = Post::where('status', 'published')
            ->where(function ($q) {
                $q->where('title', 'like', '%tiết kiệm%')
                  ->orWhere('title', 'like', '%chi phí%')
                  ->orWhere('title', 'like', '%budget%')
                  ->orWhere('title', 'like', '%rẻ%');
            })
            ->orderBy('views_count', 'desc')
            ->take(2)
            ->get();

        $text = "💰 **Mẹo du lịch tiết kiệm:**\n\n"
              . "• ✈️ Đặt vé máy bay trước 1-2 tháng, thứ 3-4 thường rẻ hơn\n"
              . "• 🏨 Chọn homestay hoặc hostel thay vì khách sạn (tiết kiệm 50-70%)\n"
              . "• 🍜 Ăn ở quán địa phương, tránh nhà hàng gần khu du lịch\n"
              . "• 🛵 Thuê xe máy thay vì taxi (150-200k/ngày)\n"
              . "• 📅 Đi vào mùa thấp điểm (tháng 3-4, 9-10)\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    // Đưa ra lời khuyên về mùa/thời tiết đi du lịch lý tưởng cho từng vùng miền
    private function seasonAdvice(string $msg): string
    {
        $seasons = [
            'đà nẵng'  => "Đà Nẵng đẹp nhất tháng 3-8 (mùa khô). Tránh tháng 9-11 (mưa bão).",
            'hà nội'   => "Hà Nội đẹp nhất tháng 9-11 (mùa thu) và tháng 3-4 (mùa xuân).",
            'phú quốc' => "Phú Quốc đẹp nhất tháng 11-4 (mùa khô). Tránh tháng 6-9 (mưa nhiều).",
            'đà lạt'   => "Đà Lạt đẹp quanh năm. Tháng 11-12 có hoa dã quỳ vàng rực.",
            'sapa'     => "Sapa đẹp nhất tháng 9-10 (lúa chín vàng) và tháng 3-4 (hoa đào).",
            'hạ long'  => "Hạ Long đẹp nhất tháng 4-8. Tránh tháng 11-3 (sương mù, lạnh).",
        ];

        foreach ($seasons as $location => $advice) {
            if (mb_strpos($msg, $location, 0, 'UTF-8') !== false) {
                return "🗓️ **Thời điểm lý tưởng — " . ucwords($location) . ":**\n\n"
                     . $advice . "\n\n"
                     . "💡 Tip: Đặt phòng trước ít nhất 2 tuần vào mùa cao điểm!";
            }
        }

        return "🗓️ **Thời điểm du lịch tốt nhất theo vùng:**\n\n"
             . "• 🌞 **Miền Bắc** (Hà Nội, Sapa): Tháng 9-11 và 3-4\n"
             . "• ☀️ **Miền Trung** (Đà Nẵng, Huế): Tháng 3-8\n"
             . "• 🌴 **Miền Nam** (Phú Quốc, Cần Thơ): Tháng 11-4\n"
             . "• 🏔️ **Tây Nguyên** (Đà Lạt): Quanh năm đều đẹp\n\n"
             . "Bạn muốn biết thêm về địa điểm cụ thể nào không?";
    }

    // Đưa ra danh sách món ăn đặc sản tiêu biểu khi phát hiện tên địa danh trong câu hỏi ẩm thực
    private function foodAdvice(string $msg): array
    {
        $foodByLocation = [
            'hà nội'   => "Phở Bát Đàn, Bún chả Obama, Bánh cuốn Thanh Vân, Chả cá Lã Vọng, Kem Tràng Tiền",
            'đà nẵng'  => "Mì Quảng, Bánh tráng cuốn thịt heo, Bún mắm nêm, Bánh xèo, Hải sản tươi sống",
            'hội an'   => "Cao lầu, Mì Quảng, Bánh mì Phượng, Cơm gà Bà Buội, Chè bắp",
            'huế'      => "Bún bò Huế, Bánh bèo, Cơm hến, Bánh khoái, Chè Huế",
            'sài gòn'  => "Phở Hòa Pasteur, Bánh mì Huỳnh Hoa, Cơm tấm, Hủ tiếu Nam Vang",
            'phú quốc' => "Hải sản tươi sống, Nước mắm Phú Quốc, Nhum biển, Gỏi cá trích",
        ];

        foreach ($foodByLocation as $location => $foods) {
            if (mb_strpos($msg, $location, 0, 'UTF-8') !== false) {
                return [
                    'text'  => "🍜 **Ẩm thực " . ucwords($location) . " không thể bỏ qua:**\n\n" . $foods . "\n\n💡 Tip: Ăn ở chợ địa phương để thưởng thức đúng vị!",
                    'posts' => [],
                ];
            }
        }

        // Nếu không ghi địa danh cụ thể, quét tìm các bài viết thuộc danh mục ẩm thực trong database để gợi ý
        $posts = Post::where('status', 'published')
            ->whereHas('category', function ($q) { $q->where('name', 'like', '%ẩm thực%'); })
            ->orderBy('views_count', 'desc')->take(3)->get();

        $text = "🍜 **Ẩm thực Việt Nam nổi tiếng:**\n\n"
              . "• Phở, Bún bò, Bánh mì — món ăn đường phố huyền thoại\n"
              . "• Mỗi vùng có đặc sản riêng, hãy thử đồ ăn địa phương!\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    // Đưa ra lời khuyên phân khúc lưu trú và đính kèm danh sách bài viết review khách sạn
    private function hotelAdvice(string $msg): array
    {
        $posts = Post::where('status', 'published')
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%khách sạn%')->orWhere('name', 'like', '%checkin%');
            })
            ->orderBy('views_count', 'desc')->take(3)->get();

        $text = "🏨 **Lời khuyên chọn chỗ ở:**\n\n"
              . "• 💰 **Tiết kiệm**: Hostel, Homestay (200-400k/đêm)\n"
              . "• 🌟 **Tầm trung**: Khách sạn 3 sao (400-800k/đêm)\n"
              . "• 👑 **Cao cấp**: Resort, Boutique Hotel (1-5 triệu/đêm)\n\n"
              . "💡 Đặt qua Agoda, Booking.com để so sánh giá tốt nhất!\n";

        $postData = [];
        foreach ($posts as $post) {
            $postData[] = ['title' => $post->title, 'slug' => $post->slug, 'location' => $post->location, 'views' => number_format($post->views_count), 'excerpt' => ''];
        }
        return ['text' => $text, 'posts' => $postData];
    }

    // Trả về cẩm nang thông tin về các loại phương tiện di chuyển phổ biến hiện nay
    private function transportAdvice(string $msg): string
    {
        return "🚗 **Phương tiện di chuyển phổ biến:**\n\n"
             . "✈️ **Máy bay**: Nhanh nhất, đặt sớm giá rẻ (Vietjet, Bamboo, Vietnam Airlines)\n"
             . "🚂 **Tàu hỏa**: Ngắm cảnh đẹp, phù hợp Hà Nội-Đà Nẵng-Sài Gòn\n"
             . "🚌 **Xe khách**: Rẻ nhất, nhiều tuyến, limousine thoải mái hơn\n"
             . "🛵 **Xe máy**: Tự do khám phá, thuê 150-200k/ngày\n"
             . "🚕 **Grab**: Tiện lợi trong thành phố, giá cố định\n\n"
             . "💡 **Tip**: Đặt vé máy bay thứ 3-4 thường rẻ hơn 20-30%!";
    }

    // Trả về báo cáo thống kê danh mục và hướng dẫn cách thức tra cứu dữ liệu trên website
    private function websiteInfo(): string
    {
        $totalPosts = Post::where('status', 'published')->count();
        // Eager loading đếm số lượng bài viết thuộc từng danh mục
        $categories = Category::withCount(['posts' => function ($q) {
            $q->where('status', 'published');
        }])->get();

        $catList = '';
        foreach ($categories as $cat) {
            $catList .= "• **{$cat->name}**: {$cat->posts_count} bài\n";
        }

        return "🌐 **Về TravelGuide:**\n\n"
             . "Chúng tôi có **{$totalPosts} bài viết** chia sẻ kinh nghiệm du lịch Việt Nam.\n\n"
             . "📂 **Danh mục:**\n{$catList}\n"
             . "🔍 Bạn có thể tìm kiếm bài viết theo:\n"
             . "• Địa điểm (Đà Nẵng, Hà Nội, Phú Quốc...)\n"
             . "• Danh mục (Ẩm thực, Điểm đến, Khách sạn...)\n"
             . "• Từ khóa bất kỳ\n\n"
             . "Bạn muốn tìm gì? 😊";
    }

    /**
     * TÌM KIẾM TỰ DO: Tách chuỗi tin nhắn thành cụm từ khóa để tìm kiếm full-text trong Database
     */
    private function searchPosts(string $message): ?array
    {
        // Tách chuỗi tin nhắn dựa vào khoảng trắng để lọc ra mảng các từ đơn lẻ
        $keywords = preg_split('/\s+/', trim($message));
        // Chỉ giữ lại những từ khóa có độ dài từ 3 ký tự trở lên để tránh các từ nối vô nghĩa (như: đi, ăn, ở...)
        $keywords = array_filter($keywords, function ($k) {
            return mb_strlen($k, 'UTF-8') >= 3;
        });

        if (empty($keywords)) return null;

        $query = Post::where('status', 'published');
        // Vòng lặp gộp điều kiện: Tất cả các từ khóa tách ra đều phải xuất hiện trong tiêu đề, tóm tắt hoặc vị trí
        foreach ($keywords as $kw) {
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('excerpt', 'like', "%{$kw}%")
                  ->orWhere('location', 'like', "%{$kw}%");
            });
        }

        // Thực hiện truy vấn lấy ra tối đa 3 kết quả phù hợp nhất theo lượt xem
        $posts = $query->orderBy('views_count', 'desc')->take(3)->get();

        if ($posts->isEmpty()) return null;

        $reply = "🔍 Tôi tìm thấy **{$posts->count()} bài viết** liên quan:\n\n";
        $postData = [];

        // Tạo chuỗi văn bản danh sách kết quả gửi lại cho khung chat
        foreach ($posts as $i => $post) {
            $reply .= ($i + 1) . ". **{$post->title}**";
            if ($post->location) $reply .= " 📍 {$post->location}";
            $reply .= "\n   👁️ " . number_format($post->views_count) . " lượt xem\n\n";

            // Gom dữ liệu nạp vào mảng để hiển thị kèm link/hình ảnh ở Client
            $postData[] = [
                'title'    => $post->title,
                'slug'     => $post->slug,
                'location' => $post->location,
                'views'    => number_format($post->views_count),
                'excerpt'  => '',
            ];
        }
        $reply .= "👉 Xem thêm tại trang **Bài viết**!";

        return [
            'text'  => $reply,
            'postData' => $postData
        ];
    }

    // Kịch bản phản hồi cứu cánh mặc định hiển thị danh sách câu hỏi gợi ý khi bot không nhận diện được từ khóa
    private function fallback(string $message): string
    {
        return "Xin lỗi, tôi chưa hiểu câu hỏi của bạn 😅\n\n"
             . "Bạn có thể hỏi tôi về:\n"
             . "• 🗺️ Địa điểm du lịch (Đà Nẵng, Hà Nội, Phú Quốc...)\n"
             . "• 🍜 Ẩm thực đặc sản từng vùng\n"
             . "• 🏨 Khách sạn, homestay\n"
             . "• 💰 Chi phí, ngân sách du lịch\n"
             . "• 📅 Thời điểm đi đẹp nhất\n"
             . "• 🚗 Phương tiện di chuyển\n\n"
             . "Ví dụ: *\"Du lịch Đà Nẵng cần bao nhiêu tiền?\"*";
    }

    // Hàm bổ trợ kiểm tra xem chuỗi văn bản người dùng nhập có chứa ít nhất 1 từ khóa thuộc danh sách mẫu hay không
    private function contains(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, $kw, 0, 'UTF-8') !== false) {
                return true; // Trả về đúng nếu phát hiện từ khóa xuất hiện trong văn bản
            }
        }
        return false;
    }

    // Route phụ dùng để test nhanh tình trạng sức khỏe (Health Check) của Chatbot trên môi trường Local Development
    public function test()
    {
        // Chặn bảo mật: Chỉ cho phép chạy link test này dưới môi trường chạy cục bộ ở máy cá nhân (Local)
        if (!app()->isLocal()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json([
            'status'       => 'OK - Chatbot tu dong san sang',
            'total_posts'  => Post::where('status', 'published')->count(),
            'total_cats'   => Category::count(),
            'mode'         => 'keyword-based (no AI API required)', // Chế độ định tuyến từ khóa cứng, không tốn tiền mua API Key AI
        ]);
    }
}