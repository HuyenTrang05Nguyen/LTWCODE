<?php
// Mở thẻ PHP

namespace App\Http\Controllers\Admin;
// Namespace của controller này.
// File nằm trong:
// app/Http/Controllers/Admin

use App\Http\Controllers\Controller;
// Import Controller gốc của Laravel

use App\Models\Post;
// Import model Post
// Dùng thao tác bảng posts

use App\Models\User;
// Import model User
// Dùng thao tác bảng users

use App\Models\Comment;
// Import model Comment
// Dùng thao tác bảng comments

use App\Models\Category;
// Import model Category
// Dùng thao tác bảng categories

use Illuminate\Http\Request;
// Import Request để xử lý dữ liệu request

use Illuminate\Support\Facades\DB;
// Import DB facade
// Dùng để viết raw query SQL trong Laravel

class DashboardController extends Controller
// Tạo DashboardController
{
    public function index()
    // Hàm hiển thị dashboard admin
    {
        $stats = [
        // Tạo mảng thống kê tổng quan dashboard

            'total_posts' => Post::count(),
            // Đếm tổng số bài viết

            // SQL tương đương:
            // SELECT COUNT(*) FROM posts

            'published_posts' => Post::published()->count(),
            // Đếm bài viết đã publish

            // published()
            // là local scope trong model Post

            // Ví dụ:
            // public function scopePublished($query)
            // {
            //     return $query->where('status', 'published');
            // }

            'total_users' => User::count(),
            // Đếm tổng user

            'total_comments' => Comment::count(),
            // Đếm tổng comment

            'total_views' => Post::sum('views_count'),
            // Tính tổng lượt xem tất cả bài viết

            // SQL:
            // SELECT SUM(views_count) FROM posts

            'pending_comments' => Comment::where('is_approved', false)->count(),
            // Đếm comment chưa duyệt

            // is_approved = false
            // nghĩa là comment đang pending
        ];

        // Posts per month for chart (last 6 months)
        // Thống kê số bài viết theo tháng
        // dùng cho biểu đồ dashboard

        $postsPerMonth = Post::select(
        // Query select dữ liệu

            DB::raw('MONTH(created_at) as month'),
            // Lấy tháng từ created_at

            // Ví dụ:
            // 2026-05-20
            // -> month = 5

            DB::raw('YEAR(created_at) as year'),
            // Lấy năm từ created_at

            DB::raw('COUNT(*) as count')
            // Đếm số bài viết mỗi tháng
        )
            ->where('created_at', '>=', now()->subMonths(6))
            // Chỉ lấy dữ liệu 6 tháng gần nhất

            // now()
            // -> thời gian hiện tại

            // subMonths(6)
            // -> trừ 6 tháng

            ->groupBy('year', 'month')
            // Gom nhóm theo năm + tháng

            ->orderBy('year')
            // Sắp xếp theo năm tăng dần

            ->orderBy('month')
            // Sắp xếp theo tháng tăng dần

            ->get();
            // Lấy dữ liệu

        // Kết quả dạng:
        /*
        [
            {
                month: 1,
                year: 2026,
                count: 15
            }
        ]
        */

        // Views per category for chart
        // Thống kê lượt xem theo category

        $viewsPerCategory = Category::withSum('posts', 'views_count')
        // withSum()
        // -> tính tổng views_count của posts thuộc category

        // Quan hệ:
        // category hasMany posts

            ->get()
            // Lấy toàn bộ category

            ->map(fn($c) => [
            // map()
            // -> biến đổi dữ liệu collection

                'name' => $c->name,
                // Tên category

                'views' => $c->posts_sum_views_count ?? 0
                // Tổng views của category

                // posts_sum_views_count
                // là field Laravel tự sinh ra từ withSum()

                // ?? 0
                // nếu null thì trả về 0
            ]);

        // Recent posts
        // Lấy bài viết mới nhất

        $recentPosts = Post::with(['user', 'category'])
        // eager loading user + category

            ->latest()
            // bài mới nhất

            ->take(5)
            // lấy 5 bài

            ->get();
            // execute query

        // Recent comments
        // Lấy comment mới nhất

        $recentComments = Comment::with(['user', 'post'])
        // load user + post

            ->latest()
            // comment mới nhất

            ->take(5)
            // lấy 5 comment

            ->get();

        return view('admin.dashboard',
        // Trả về view dashboard

            compact(
                'stats',
                'postsPerMonth',
                'viewsPerCategory',
                'recentPosts',
                'recentComments'
            )
        );

        // compact()
        // -> truyền dữ liệu sang blade view
    }
}
