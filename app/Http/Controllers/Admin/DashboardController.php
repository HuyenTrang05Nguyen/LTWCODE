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
        $stats = [
            'total_posts' => Post::count(),
            'published_posts' => Post::published()->count(),
            'total_users' => User::count(),
            'total_comments' => Comment::count(),
            'total_views' => Post::sum('views_count'),
            'pending_comments' => Comment::where('is_approved', false)->count(),
        ];

        // Posts per month for chart (last 6 months)
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

        // Views per category for chart
        $viewsPerCategory = Category::withSum('posts', 'views_count')
            ->get()
            ->map(fn($c) => ['name' => $c->name, 'views' => $c->posts_sum_views_count ?? 0]);

        // Recent posts
        $recentPosts = Post::with(['user', 'category'])->latest()->take(5)->get();

        // Recent comments
        $recentComments = Comment::with(['user', 'post'])->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'postsPerMonth', 'viewsPerCategory', 'recentPosts', 'recentComments'));
    }
}
