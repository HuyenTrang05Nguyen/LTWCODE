<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    // Xử lý logic hiển thị dữ liệu cho Trang chủ
    public function index()
    {
        // Lấy 6 bài viết nổi bật, được xem nhiều nhất (popular) kèm thông tin người viết và danh mục
        $featuredPosts = Post::published()
            ->with(['user', 'category']) // Eager Loading: Nạp trước dữ liệu liên kết để tránh lỗi truy vấn N+1
            ->popular()                  // Gọi Scope sắp xếp theo lượt xem nhiều nhất
            ->take(6)                    // Giới hạn lấy đúng 6 bài viết
            ->get();

        // Lấy 6 bài viết mới xuất bản gần đây nhất (latest)
        $latestPosts = Post::published()
            ->with(['user', 'category'])
            ->latest()                   // Sắp xếp theo ngày tạo mới nhất giảm dần
            ->take(6)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Ẩm thực"
        $foodPosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) { // Kiểm tra điều kiện ở bảng liên kết (categories)
                $q->where('name', 'like', '%Ẩm thực%'); // Lọc danh mục có tên chứa chữ 'Ẩm thực'
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Điểm đến"
        $destinationPosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%Điểm đến%');
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Checkin"
        $checkinPosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%Checkin%');
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Kinh nghiệm"
        $experiencePosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%Kinh nghiệm%');
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Khách sạn"
        $hotelPosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%Khách sạn%');
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy 3 bài viết mới nhất thuộc danh mục "Cẩm nang"
        $guidePosts = Post::published()
            ->with(['user', 'category'])
            ->whereHas('category', function ($q) {
                $q->where('name', 'like', '%Cẩm nang%');
            })
            ->latest()
            ->take(3)
            ->get();

        // Lấy danh sách toàn bộ danh mục, đồng thời đếm số lượng bài viết đã xuất bản trong từng danh mục đó
        $categories = Category::withCount(['posts' => function ($q) {
            $q->where('status', 'published'); // Chỉ đếm những bài viết ở trạng thái công khai
        }])->get();

        // Trả dữ liệu về giao diện trang chủ (home.blade.php) thông qua hàm compact
        return view('home', compact(
            'featuredPosts',
            'latestPosts',
            'foodPosts',
            'destinationPosts',
            'checkinPosts',
            'experiencePosts',
            'hotelPosts',
            'guidePosts',
            'categories'
        ));
    }
}