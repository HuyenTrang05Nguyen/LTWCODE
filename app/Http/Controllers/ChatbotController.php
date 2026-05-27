<?php

namespace App\Http\Controllers;
// Namespace controller

use Illuminate\Http\Request;
// Lấy dữ liệu từ request/form

use Illuminate\Support\Facades\Cache;
// Dùng lưu cache
// ở đây dùng để rate limit chatbot

use Illuminate\Support\Facades\Log;
// Dùng ghi log lỗi hệ thống

use App\Models\Post;
// Model bài viết

use App\Models\Category;
// Model danh mục

class ChatbotController extends Controller
// Controller chatbot
{
    /**
     * POST /chatbot
     * Hàm nhận tin nhắn từ frontend
     */

    public function chat(Request $request)
    {
        $request->validate([
        // Validate dữ liệu gửi lên

            'message' => 'required|string|min:1|max:500',
            // message:
            // - bắt buộc
            // - kiểu string
            // - tối thiểu 1 ký tự
            // - tối đa 500 ký tự
        ]);

        $message = trim($request->input('message'));
        // Lấy nội dung tin nhắn
        // trim() -> xóa khoảng trắng dư

        // ----------------------------
        // RATE LIMIT CHATBOT
        // ----------------------------

        $ip = $request->ip();
        // Lấy IP user

        $cacheKey = 'chatbot_rate_' . md5($ip);
        // Tạo key cache theo IP

        try {

            $count = Cache::get($cacheKey, 0);
            // Lấy số lần gửi tin nhắn
            // mặc định = 0

            if ($count >= 30) {
            // Nếu spam quá 30 tin/phút

                return response()->json([
                    'success' => false,

                    'message' => 'Bạn gửi quá nhiều tin nhắn.',

                    'posts' => [],
                ], 429);

                // 429 = Too Many Requests
            }

            Cache::put($cacheKey, $count + 1, 60);
            // Tăng số lần chat
            // lưu cache 60 giây

        } catch (\Exception $e) {

            Log::warning(
                'Chatbot Cache Driver Warning: '
                . $e->getMessage()
            );

            // Nếu cache lỗi
            // chatbot vẫn chạy bình thường
        }

        $result = $this->processMessage($message);
        // Gửi message qua hàm xử lý chatbot

        return response()->json([
        // Trả JSON về frontend

            'success' => true,

            'message' => $result['text'],
            // Nội dung chatbot trả lời

            'posts' => $result['posts'],
            // Danh sách bài viết gợi ý
        ]);
    }

    /**
     * Hàm xử lý nội dung tin nhắn
     */

    private function processMessage(string $message): array
    {
        $msg = mb_strtolower($message, 'UTF-8');
        // Chuyển message thành chữ thường
        // hỗ trợ tiếng Việt UTF-8

        // ----------------------------
        // 1. CHÀO HỎI
        // ----------------------------

        if ($this->contains($msg, [
            'xin chào',
            'chào',
            'hello',
            'hi'
        ])) {

            return [
                'text' => $this->greet(),
                'posts' => []
            ];

            // Gọi hàm greet()
        }

        // ----------------------------
        // 2. CẢM ƠN
        // ----------------------------

        if ($this->contains($msg, [
            'cảm ơn',
            'thanks'
        ])) {

            return [
                'text' => 'Không có gì 😊',
                'posts' => []
            ];
        }

        // ----------------------------
        // 3. TẠM BIỆT
        // ----------------------------

        if ($this->contains($msg, [
            'bye',
            'tạm biệt'
        ])) {

            return [
                'text' => 'Tạm biệt 👋',
                'posts' => []
            ];
        }

        // ----------------------------
        // 4. TÌM ĐỊA ĐIỂM
        // ----------------------------

        $loc = $this->searchByLocation($msg);

        if ($loc) return $loc;
        // Nếu tìm được địa điểm
        // trả kết quả luôn

        // ----------------------------
        // 5. TÌM DANH MỤC
        // ----------------------------

        $cat = $this->searchByCategory($msg);

        if ($cat) return $cat;

        // ----------------------------
        // 6. CHI PHÍ
        // ----------------------------

        if ($this->contains($msg, [
            'chi phí',
            'giá',
            'budget'
        ])) {

            return $this->budgetAdvice($msg);
        }

        // ----------------------------
        // 7. THỜI ĐIỂM DU LỊCH
        // ----------------------------

        if ($this->contains($msg, [
            'mùa',
            'thời tiết',
            'khi nào'
        ])) {

            return [
                'text' => $this->seasonAdvice($msg),
                'posts' => []
            ];
        }

        // ----------------------------
        // 8. ẨM THỰC
        // ----------------------------

        if ($this->contains($msg, [
            'ăn gì',
            'ẩm thực',
            'đặc sản'
        ])) {

            return $this->foodAdvice($msg);
        }

        // ----------------------------
        // 9. KHÁCH SẠN
        // ----------------------------

        if ($this->contains($msg, [
            'khách sạn',
            'homestay',
            'resort'
        ])) {

            return $this->hotelAdvice($msg);
        }

        // ----------------------------
        // 10. DI CHUYỂN
        // ----------------------------

        if ($this->contains($msg, [
            'máy bay',
            'xe',
            'di chuyển'
        ])) {

            return [
                'text' => $this->transportAdvice($msg),
                'posts' => []
            ];
        }

        // ----------------------------
        // 11. WEBSITE
        // ----------------------------

        if ($this->contains($msg, [
            'website',
            'travelguide'
        ])) {

            return [
                'text' => $this->websiteInfo(),
                'posts' => []
            ];
        }

        // ----------------------------
        // 12. SEARCH TỰ DO
        // ----------------------------

        $search = $this->searchPosts($message);

        if ($search) return $search;

        // ----------------------------
        // 13. FALLBACK
        // ----------------------------

        return [
            'text' => $this->fallback($message),
            'posts' => []
        ];

        // Nếu không hiểu câu hỏi
    }

    private function greet(): string
    {
        $totalPosts = Post::where(
            'status',
            'published'
        )->count();
        // Đếm bài viết published

        $totalCats = Category::count();
        // Đếm danh mục

        return "Xin chào! Tôi là TravelBot";
        // Nội dung chatbot chào user
    }

    private function searchByLocation(string $msg): ?array
    {
        // Hàm tìm bài viết theo địa điểm

        $locations = [
            'đà nẵng' => ['Da Nang', 'Đà Nẵng'],
            'hà nội' => ['Ha Noi', 'Hà Nội'],
        ];

        foreach ($locations as $keyword => $searchTerms) {

            if (
                mb_strpos($msg, $keyword, 0, 'UTF-8')
                !== false
            ) {

                // Nếu user nhập địa điểm

                $posts = Post::where(
                    'status',
                    'published'
                )

                ->where(function ($q) use ($searchTerms) {

                    foreach ($searchTerms as $term) {

                        $q->orWhere(
                            'location',
                            'like',
                            "%{$term}%"
                        )

                        ->orWhere(
                            'title',
                            'like',
                            "%{$term}%"
                        );

                        // Search location/title
                    }
                })

                ->orderBy('views_count', 'desc')
                // Sắp xếp view cao nhất

                ->take(3)
                // Lấy 3 bài

                ->get();

                if ($posts->isEmpty()) {
                // Nếu không có bài viết

                    return [
                        'text' => 'Chưa có bài viết',
                        'posts' => []
                    ];
                }

                $postData = [];

                foreach ($posts as $post) {

                    $postData[] = [
                        'title' => $post->title,
                        // tiêu đề

                        'slug' => $post->slug,
                        // slug URL

                        'location' => $post->location,

                        'views' => number_format(
                            $post->views_count
                        ),
                    ];
                }

                return [
                    'text' => 'Tìm thấy bài viết',
                    'posts' => $postData
                ];
            }
        }

        return null;
        // Không tìm thấy địa điểm
    }

    private function contains(
        string $text,
        array $keywords
    ): bool
    {
        // Hàm kiểm tra keyword

        foreach ($keywords as $kw) {

            if (
                mb_strpos($text, $kw, 0, 'UTF-8')
                !== false
            ) {

                return true;
                // Nếu tìm thấy keyword
            }
        }

        return false;
        // Không tìm thấy keyword
    }

    public function test()
    {
        // API test chatbot

        if (!app()->isLocal()) {
        // Nếu không phải local environment

            return response()->json([
                'error' => 'Forbidden'
            ], 403);

            // Chặn production
        }

        return response()->json([

            'status' => 'OK',

            'total_posts' => Post::where(
                'status',
                'published'
            )->count(),

            'total_cats' => Category::count(),

            'mode' => 'keyword-based',
        ]);

        // API test trạng thái chatbot
    }
}
