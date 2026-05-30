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
     * POST /chatbot — Xử lý tin nhắn từ Client
     */
    public function chat(Request $request)
    {
        // Kiểm tra dữ liệu đầu vào: message bắt buộc, là chuỗi, độ dài 1-500 ký tự
        $request->validate([
            'message' => 'required|string|min:1|max:500',
        ]);

        $message = trim($request->input('message')); // Loại bỏ khoảng trắng thừa

        // Cấu hình Rate Limiting để chống spam request trên môi trường Cloud
        $ip       = $request->ip(); // Lấy IP người dùng
        $cacheKey = 'chatbot_rate_' . md5($ip); // Tạo khóa cache riêng biệt cho từng IP

        try {
            $count = Cache::get($cacheKey, 0); // Đọc số lượng request hiện tại từ cache
            if ($count >= 30) { // Nếu vượt quá 30 request/phút
                return response()->json([
                    'success' => false,
                    'message' => 'Bạn gửi quá nhiều tin nhắn. Vui lòng thử lại sau 1 phút! ⏳',
                    'posts'   => [],
                ], 429); // Trả về lỗi 429 (Too Many Requests)
            }
            Cache::put($cacheKey, $count + 1, 60); // Lưu lại số lượt, hiệu lực 60 giây
        } catch (\Exception $e) {
            // Trường hợp cache lỗi (ví dụ file driver trên Render bị reset), log lỗi và vẫn tiếp tục xử lý
            Log::warning('Chatbot Cache Driver Warning: ' . $e->getMessage());
        }

        $result = $this->processMessage($message); // Chuyển sang hàm xử lý logic nội dung

        return response()->json([
            'success' => true,
            'message' => $result['text'], // Nội dung phản hồi
            'posts'   => $result['posts'], // Danh sách bài viết đính kèm
        ]);
    }

    /**
     * Điều hướng xử lý tin nhắn theo từ khóa
     */
    private function processMessage(string $message): array
    {
        $msg = mb_strtolower($message, 'UTF-8'); // Chuẩn hóa về chữ thường để so sánh

        // 1. Kiểm tra nhóm từ khóa chào hỏi
        if ($this->contains($msg, ['xin chào', 'chào', 'hello', 'hi', 'hey', 'alo'])) {
            return ['text' => $this->greet(), 'posts' => []];
        }
        // 2. Nhóm từ khóa cảm ơn
        if ($this->contains($msg, ['cảm ơn', 'cám ơn', 'thanks', 'thank you', 'tks'])) {
            return ['text' => 'Không có gì! Tôi luôn sẵn sàng hỗ trợ bạn. Bạn cần tư vấn thêm gì không? 😊', 'posts' => []];
        }
        // 3. Nhóm từ khóa tạm biệt
        if ($this->contains($msg, ['tạm biệt', 'bye', 'goodbye', 'hẹn gặp lại'])) {
            return ['text' => 'Tạm biệt! Chúc bạn có chuyến du lịch thật vui vẻ! ✈️🌏', 'posts' => []];
        }
        // 4. Xử lý tìm theo địa điểm (gọi hàm chuyên biệt)
        $loc = $this->searchByLocation($msg);
        if ($loc) return $loc;
        // 5. Xử lý tìm theo danh mục (gọi hàm chuyên biệt)
        $cat = $this->searchByCategory($msg);
        if ($cat) return $cat;
        // 6. Xử lý tư vấn chi phí/ngân sách
        if ($this->contains($msg, ['chi phí', 'bao nhiêu tiền', 'giá', 'ngân sách', 'tiết kiệm', 'rẻ', 'budget'])) {
            return $this->budgetAdvice($msg);
        }
        // 7. Xử lý tư vấn thời điểm/thời tiết
        if ($this->contains($msg, ['khi nào', 'tháng mấy', 'mùa nào', 'thời điểm', 'thời tiết', 'mùa'])) {
            return ['text' => $this->seasonAdvice($msg), 'posts' => []];
        }
        // 8. Xử lý tư vấn ẩm thực
        if ($this->contains($msg, ['ăn gì', 'món ăn', 'đặc sản', 'ẩm thực', 'quán ăn', 'nhà hàng', 'food'])) {
            return $this->foodAdvice($msg);
        }
        // 9. Xử lý tư vấn lưu trú (khách sạn)
        if ($this->contains($msg, ['khách sạn', 'homestay', 'resort', 'ở đâu', 'lưu trú', 'phòng', 'hotel'])) {
            return $this->hotelAdvice($msg);
        }
        // 10. Xử lý tư vấn phương tiện di chuyển
        if ($this->contains($msg, ['đi bằng gì', 'phương tiện', 'xe', 'máy bay', 'tàu', 'di chuyển', 'đường đi'])) {
            return ['text' => $this->transportAdvice($msg), 'posts' => []];
        }
        // 11. Xử lý thông tin về website
        if ($this->contains($msg, ['website', 'trang web', 'travelguide', 'bài viết', 'danh mục', 'tìm kiếm'])) {
            return ['text' => $this->websiteInfo(), 'posts' => []];
        }
        // 12. Tìm kiếm tự do theo từ khóa trong nội dung bài viết
        $search = $this->searchPosts($message);
        if ($search) return $search;

        // 13. Phản hồi mặc định khi không tìm thấy từ khóa phù hợp
        return ['text' => $this->fallback($message), 'posts' => []];
    }

    private function greet(): string
    {
        $totalPosts = Post::where('status', 'published')->count(); // Đếm tổng bài viết công khai
        $totalCats  = Category::count(); // Đếm tổng danh mục
        return "Xin chào! Tôi là **TravelBot** 🌏\n"
             . "Tôi có thể giúp bạn:\n"
             . "• 🗺️ Tư vấn địa điểm du lịch\n"
             . "• 🍜 Gợi ý ẩm thực đặc sản\n"
             . "• 🏨 Tìm khách sạn, homestay\n"
             . "• 💰 Lập kế hoạch chi phí\n"
             . "• 📖 Tìm bài viết (hiện có **{$totalPosts} bài** trong **{$totalCats} danh mục**)\n\n"
             . "Bạn muốn khám phá đâu hôm nay? ✈️";
    }

    private function searchByLocation(string $msg): ?array
    {
        // Định nghĩa các cặp từ khóa để tìm kiếm địa điểm
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
            // Kiểm tra tin nhắn có chứa từ khóa địa điểm không
            if (mb_strpos($msg, $keyword, 0, 'UTF-8') !== false) {
                // Truy vấn bài viết theo vị trí hoặc tiêu đề
                // mb_strpos(...): Đây là hàm của PHP dùng để "tìm vị trí" của một chuỗi nhỏ bên trong một chuỗi lớn.
                //mb_ (Multi-byte): Giúp máy tính hiểu và xử lý đúng các ký tự tiếng Việt (có dấu) như "Đà Nẵng", "Huế".
                //'UTF-8': Đảm bảo rằng việc đếm ký tự được thực hiện chính xác theo chuẩn tiếng Việt.
                //$msg: Là tin nhắn của khách hàng.
                //$keyword: Là từ khóa bạn đang kiểm tra trong vòng lặp (ví dụ: "đà nẵng").
                //!== false: Đây là cách nói với máy tính: "Nếu tìm thấy từ khóa này (tức là vị trí tìm thấy không phải là không tìm thấy), hãy làm tiếp các lệnh bên trong".
                $posts = Post::where('status', 'published')
                    ->where(function ($q) use ($searchTerms) {
                        foreach ($searchTerms as $term) {
                            $q->orWhere('location', 'like', "%{$term}%")
                              ->orWhere('title', 'like', "%{$term}%");
                        }
                    })
                    ->orderBy('views_count', 'desc') // Ưu tiên bài nhiều lượt xem
                    ->take(3) // Giới hạn lấy 3 bài
                    ->get();

                $locationName = ucwords($keyword);
                // ucwords là viết tắt của "Uppercase Words". Hàm này có công dụng tự động viết hoa chữ cái đầu tiên của mỗi từ trong một chuỗi.
                //Ví dụ: Nếu $keyword của bạn là 'đà nẵng', thì sau khi qua hàm này, nó sẽ biến thành 'Đà Nẵng'.
                if ($posts->isEmpty()) {
                    return [
                        'text'  => "Tôi chưa có bài viết về **{$locationName}** nhưng đây là điểm đến tuyệt vời! 🌟\nBạn có thể tìm kiếm thêm tại trang Bài viết nhé.",
                        'posts' => [],
                    ];
                }

                $text = "Tôi tìm thấy **{$posts->count()} bài viết** về **{$locationName}** 📍\n";
                $postData = [];
                foreach ($posts as $post) {
                    $excerpt = $post->excerpt ? mb_substr(strip_tags($post->excerpt), 0, 70, 'UTF-8') . '...' : '';
                    // strip_tags (Bộ lọc): Nó loại bỏ tất cả các thẻ xấu như <div>, <b>, <p>... để chỉ còn lại chữ thuần túy.
                    //mb_substr(..., 0, 70, 'UTF-8') (Lưỡi dao): Nó bắt đầu cắt từ chữ cái đầu tiên (vị trí 0) và dừng lại sau đúng 70 ký tự. Nó dùng mb_ để đảm bảo khi cắt vào chữ "Hà Nội" hay "đẹp", nó không cắt ngang giữa chừng gây lỗi font hay mất dấu.
                    //+ '...' (Dấu hiệu): Sau khi cắt xong, nó dán thêm 3 dấu chấm vào cuối để người xem biết: "À, nội dung vẫn còn dài lắm, muốn xem thêm thì bấm vào bài viết nhé".
                    //? : (Công tắc an toàn): Nó kiểm tra trước, nếu bài viết không có nội dung thì nó trả về kết quả rỗng thay vì làm lỗi chương trình.
                    $postData[] = [
                        'title'    => $post->title,
                        'slug'     => $post->slug,
                        'location' => $post->location,
                        'views'    => number_format($post->views_count),
                        'excerpt'  => $excerpt,
                    ];
                    // 'title' => $post->title: Bạn lấy giá trị tiêu đề từ Database và gán nó vào cái nhãn tên là 'title'.
                    //'slug' => $post->slug: Bạn gán đường dẫn thân thiện của bài viết vào nhãn 'slug'.
                    //'location' => $post->location: Bạn gán địa điểm vào nhãn 'location'.
                    //'views' => number_format($post->views_count): Ở đây bạn biến đổi dữ liệu. Bạn lấy con số thô (ví dụ: 1250) và dùng hàm number_format để biến nó thành chuỗi dễ đọc (ví dụ: "1,250").
                    //'excerpt' => $excerpt: Bạn lấy đoạn trích đã qua "gia công" (cắt gọt, làm sạch) ở bước trước đó và bỏ vào nhãn 'excerpt'
                }
                return ['text' => $text, 'posts' => $postData];
            }
        }
        return null;
    }

    private function searchByCategory(string $msg): ?array
    {
        // Danh mục tương ứng với các từ khóa tìm kiếm
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
                    $category = Category::where('name', 'like', "%{$catName}%")->first();
                    if (!$category) continue;

                    // Lấy bài viết theo ID danh mục
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

    private function budgetAdvice(string $msg): array
    {
        // Truy vấn bài viết chứa từ khóa về ngân sách/tiết kiệm
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

    private function seasonAdvice(string $msg): string
    {
        // Thông tin gợi ý thời điểm theo địa điểm
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

        // Thông tin chung nếu không tìm thấy địa điểm cụ thể
        return "🗓️ **Thời điểm du lịch tốt nhất theo vùng:**\n\n"
             . "• 🌞 **Miền Bắc** (Hà Nội, Sapa): Tháng 9-11 và 3-4\n"
             . "• ☀️ **Miền Trung** (Đà Nẵng, Huế): Tháng 3-8\n"
             . "• 🌴 **Miền Nam** (Phú Quốc, Cần Thơ): Tháng 11-4\n"
             . "• 🏔️ **Tây Nguyên** (Đà Lạt): Quanh năm đều đẹp\n\n"
             . "Bạn muốn biết thêm về địa điểm cụ thể nào không?";
    }

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

        // Lấy bài viết ẩm thực nổi bật nếu không có địa điểm
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

    private function websiteInfo(): string
    {
        $totalPosts = Post::where('status', 'published')->count();
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
     * SỬA LỖI TẠI ĐÂY: Chuyển đổi kiểu dữ liệu trả về từ ?string sang ?array
     */
    private function searchPosts(string $message): ?array
    {
        $keywords = preg_split('/\s+/', trim($message)); //Tách từ: preg_split cắt câu của người dùng thành từng từ đơn lẻ.
        $keywords = array_filter($keywords, function ($k) { // Lọc nhiễu: array_filter loại bỏ các từ quá ngắn (dưới 3 ký tự).
            return mb_strlen($k, 'UTF-8') >= 3; // Chỉ lấy từ có từ 3 ký tự trở lên
        });

        if (empty($keywords)) return null; //Chốt chặn: if (empty($keywords)) return null; nếu sau khi lọc mà không còn từ nào ý nghĩa, nó sẽ dừng ngay và báo không tìm thấy gì.

        $query = Post::where('status', 'published');
        foreach ($keywords as $kw) {
            // Tìm kiếm trong tiêu đề, mô tả hoặc vị trí
            $query->where(function ($q) use ($kw) {
                $q->where('title', 'like', "%{$kw}%")
                  ->orWhere('excerpt', 'like', "%{$kw}%")
                  ->orWhere('location', 'like', "%{$kw}%");
            });
        }

        $posts = $query->orderBy('views_count', 'desc')->take(3)->get();

        if ($posts->isEmpty()) return null;

        $reply = "🔍 Tôi tìm thấy **{$posts->count()} bài viết** liên quan:\n\n";
        $postData = [];

        foreach ($posts as $i => $post) {
            $reply .= ($i + 1) . ". **{$post->title}**";
            if ($post->location) $reply .= " 📍 {$post->location}";
            $reply .= "\n   👁️ " . number_format($post->views_count) . " lượt xem\n\n";

            $postData[] = [
                'title'    => $post->title,
                'slug'     => $post->slug,
                'location' => $post->location,
                'views'    => number_format($post->views_count),
                'excerpt'  => '',
            ];
        }
        $reply .= "👉 Xem thêm tại trang **Bài viết**!";
        //sử dụng kỹ thuật String Concatenation (nối chuỗi) thông qua toán tử .= có nghĩa là "lấy nội dung cũ đang có trên giấy, rồi viết thêm nội dung mới vào sau nó"
        return [
            'text'  => $reply,
            'posts' => $postData
        ];
    }

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

    private function contains(string $text, array $keywords): bool
    {
        foreach ($keywords as $kw) {
            if (mb_strpos($text, $kw, 0, 'UTF-8') !== false) {
                return true;
            }
        }
        return false;
    }

    public function test()
    {
        // Kiểm tra xem ứng dụng có đang chạy ở chế độ local không
        if (!app()->isLocal()) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json([
            'status'       => 'OK - Chatbot tu dong san sang',
            'total_posts'  => Post::where('status', 'published')->count(),
            'total_cats'   => Category::count(),
            'mode'         => 'keyword-based (no AI API required)',
        ]);
    }
}