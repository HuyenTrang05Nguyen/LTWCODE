<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        //Thu thập dữ liệu tổng quan cho hệ thống (Thống kê nhanh) --}}
        $stats = [
            'total_posts'      => Post::count(),
            'published_posts'  => Post::published()->count(),
            'total_users'      => User::count(),
            'total_comments'   => Comment::count(),
            'total_views'      => Post::sum('views_count'),
            'pending_comments' => Comment::where('is_approved', false)->count(),
        ];

        //{{-- 2. Truy vấn số liệu bài viết theo tháng (Dữ liệu cho Bar Chart) --}}
        //{{-- Lấy dữ liệu 6 tháng gần nhất, nhóm theo năm và tháng để đảm bảo chính xác --}}
        $postsPerMonth = Post::select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        //{{-- 3. Truy vấn tổng lượt xem theo từng Danh mục (Dữ liệu cho Doughnut Chart) --}}
        //{{-- Sử dụng withSum để tính tổng views của các bài viết thuộc danh mục đó --}}
        $viewsPerCategory = Category::withSum('posts', 'views_count')
            ->get()
            ->map(fn($c) => [
                'name'  => $c->name, 
                'views' => $c->posts_sum_views_count ?? 0
            ]);

        //{{-- 4. Lấy danh sách 5 bài viết mới nhất (Kèm theo thông tin Tác giả & Danh mục) --}}
        $recentPosts = Post::with(['user', 'category'])->latest()->take(5)->get();

        //{{-- 5. Lấy danh sách 5 bình luận mới nhất (Kèm theo thông tin Người bình luận & Bài viết tương ứng) --}}
        $recentComments = Comment::with(['user', 'post'])->latest()->take(5)->get();

        //{{-- 6. Trả về view admin.dashboard cùng với toàn bộ dữ liệu đã tổng hợp --}}
        return view('admin.dashboard', compact('stats', 'postsPerMonth', 'viewsPerCategory', 'recentPosts', 'recentComments'));
    }
}