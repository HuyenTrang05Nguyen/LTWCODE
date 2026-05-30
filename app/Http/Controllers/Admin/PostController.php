<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Http\Requests\PostRequest;
use App\Http\Requests\CommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Hiển thị danh sách bài viết kèm bộ lọc tìm kiếm, phân loại và sắp xếp
     */
    public function index(Request $request)
    {
        // Khởi tạo truy vấn các bài viết đã xuất bản và nạp sẵn (Eager Loading) thông tin user, category
        $query = Post::published()->with(['user', 'category']);

        // Bộ lọc 1: Lọc bài viết theo danh mục (Category Slug) nếu có yêu cầu gửi lên
        if ($request->filled('category')) { //Kiểm tra xem người dùng có thực sự gửi tham số category lên không và nó không bị trống.
            $query->whereHas('category', function ($q) use ($request) { 
                // kiểm tra mối quan hệ giữa Post và Category. Nó nói rằng: "Hãy tìm cho tôi những bài viết mà có tồn tại một danh mục thỏa mãn điều kiện bên trong...
                // function ($q) use ($request): Đây là cách truyền dữ liệu từ bên ngoài vào trong hàm lọc. $q là một truy vấn con (sub-query) dùng để đào sâu vào bảng categories.
                $q->where('slug', $request->category); // Điều kiện lọc: danh mục đó phải có slug (đường dẫn thân thiện) trùng khớp với cái tên người dùng gửi lên
            });
        }

        // Bộ lọc 2: Tìm kiếm từ khóa tự do (Search) theo tiêu đề, nội dung, địa điểm hoặc mô tả ngắn
        if ($request->filled('search')) { 
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%") // function: Khai báo tạo ra một người trợ lý mới.
                                                        // ($q): Cái tên bạn đặt cho người trợ lý đó (để bạn ra lệnh cho nó bằng cách dùng $q->...).
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        // Bộ lọc 3: Lọc chính xác hoặc gần đúng theo địa điểm (Location)
        if ($request->filled('location')) {
            $query->where('location', 'like', "%{$request->location}%");
        }

        // Xử lý tiêu chí sắp xếp (Mặc định là 'latest' - mới nhất)
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'popular': // Xem nhiều nhất
                $query->orderBy('views_count', 'desc');
                break;
            case 'oldest':  // Cũ nhất
                $query->oldest();
                break;
            default:        // Mới nhất
                $query->latest();
        }

        // Thực hiện phân trang (9 bài viết trên một trang) và giữ lại các tham số trên URL khi bấm chuyển trang
        $posts = $query->paginate(9)->withQueryString();
        
        // Lấy danh sách tất cả danh mục và đếm số bài viết công khai thuộc mỗi danh mục
        $categories = Category::withCount(['posts' => fn($q) => $q->published()])->get();

        return view('posts.index', compact('posts', 'categories'));
    }

    /**
     * Hiển thị chi tiết một bài viết cụ thể
     */
    public function show(Post $post)
    {
        // Kiểm tra quyền truy cập: Nếu bài viết chưa công khai thì chỉ có chính tác giả mới được xem, ngược lại trả về lỗi 404
        if ($post->status !== 'published' && (!auth()->check() || auth()->id() !== $post->user_id)) {
            abort(404);
        }

        // Tự động tăng số lượt xem (views_count) của bài viết lên 1 đơn vị
        $post->increment('views_count');
        
        // Nạp các mối quan hệ liên quan bao gồm cả những bình luận đã được admin phê duyệt
        $post->load(['user', 'category', 'approvedComments.user', 'ratings']);

        // Gợi ý bài viết liên quan: Lấy tối đa 3 bài viết cùng danh mục và loại trừ bài viết hiện tại
        $relatedPosts = Post::published()
            ->where('category_id', $post->category_id)
            ->where('id', '!=', $post->id)
            ->take(3)
            ->get();

        // Lấy thông tin số sao đánh giá của người dùng hiện tại đối với bài viết này (nếu đã đăng nhập)
        $userRating = null;
        if (auth()->check()) {
            $userRating = $post->ratings()->where('user_id', auth()->id())->first();
        }

        return view('posts.show', compact('post', 'relatedPosts', 'userRating'));
    }

    /**
     * Xử lý gửi bình luận mới cho bài viết
     */
    public function comment(CommentRequest $request, Post $post)
    {
        // Tạo bản ghi bình luận mới với trạng thái mặc định là chưa duyệt (is_approved = false)
        $post->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
            'is_approved' => false, // Chờ quản trị viên duyệt mới hiển thị công khai
        ]);

        return back()->with('success', 'Bình luận của bạn đã được gửi và đang chờ duyệt!');
    }

    /**
     * Tính năng lưu/bỏ lưu bài viết yêu thích (Bật/Tắt trạng thái)
     */
    public function toggleFavorite(Post $post)
    {
        $user = auth()->user();
        // Kiểm tra xem người dùng hiện tại đã từng lưu bài viết này chưa
        $favorite = $post->favorites()->where('user_id', $user->id)->first();

        // Nếu đã lưu rồi thì thực hiện xóa (Bỏ lưu)
        if ($favorite) {
            $favorite->delete();
            return back()->with('success', 'Đã bỏ lưu bài viết.');
        }

        // Nếu chưa lưu thì tiến hành tạo bản ghi mới (Lưu yêu thích)
        $post->favorites()->create(['user_id' => $user->id]);
        return back()->with('success', 'Đã lưu bài viết yêu thích!');
    }

    /**
     * Đánh giá số sao (Rating) từ 1 đến 5 cho bài viết
     */
    public function rate(Request $request, Post $post)
    {
        // Xác thực số sao gửi lên phải là số nguyên nằm trong khoảng từ 1 đến 5
        $request->validate([
            'score' => 'required|integer|min:1|max:5',
        ]);

        // Sử dụng updateOrCreate: Nếu đã đánh giá rồi thì cập nhật số sao mới, nếu chưa thì tạo bản ghi mới
        $post->ratings()->updateOrCreate(
            ['user_id' => auth()->id()], // Điều kiện tìm kiếm bản ghi cũ
            ['score' => $request->score] // Dữ liệu cần cập nhật hoặc thêm mới
        );

        return back()->with('success', 'Cảm ơn bạn đã đánh giá!');
    }

    /**
     * Hiển thị danh sách các bài viết mà người dùng hiện tại đã nhấn lưu yêu thích
     */
    public function favorites()
    {
        // Lấy danh sách bài viết yêu thích của user, sắp xếp theo thời gian lưu gần nhất và phân trang
        $posts = auth()->user()->favoritePosts()
            ->published()
            ->with(['user', 'category'])
            ->latest('favorites.created_at') // Sắp xếp theo ngày nhấn nút lưu yêu thích
            ->paginate(9);

        return view('posts.favorites', compact('posts'));
    }
}