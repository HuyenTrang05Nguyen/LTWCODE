<?php

namespace App\Http\Controllers;
// Namespace controller

use App\Models\Post;
// Import model Post

use App\Models\Category;
// Import model Category

use Illuminate\Http\Request;
// Import Request

class HomeController extends Controller
// Controller xử lý trang chủ
{
    public function index()
    // Hàm xử lý trang Home
    {
        // ------------------------------------------------
        // FEATURED POSTS
        // ------------------------------------------------

        // Featured:
        // top 6 bài nổi bật nhất

        $featuredPosts = Post::published()

        // published()
        // Scope chỉ lấy bài có status = published

            ->with(['user', 'category'])

            // with()
            // Eager Loading relationship
            // load sẵn user + category
            // tránh N+1 query
            ->popular()
            // popular()
            // scope custom
            // thường sort theo views_count DESC
            ->take(6)
            // take(6)
            // chỉ lấy 6 bài
            ->get();
            // get()
            // thực thi query SQL
        // ------------------------------------------------
        // LATEST POSTS
        // ------------------------------------------------

        $latestPosts = Post::published()

            ->with(['user', 'category'])

            ->latest()

            // latest()
            // ORDER BY created_at DESC
            // bài mới nhất lên đầu

            ->take(6)

            ->get();

        // ------------------------------------------------
        // FOOD POSTS
        // ------------------------------------------------

        $foodPosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

            // whereHas()
            // lọc theo relationship

                $q->where(
                    'name',
                    'like',
                    '%Ẩm thực%'
                );

                // category name chứa:
                // "Ẩm thực"
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // DESTINATION POSTS
        // ------------------------------------------------

        $destinationPosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

                $q->where(
                    'name',
                    'like',
                    '%Điểm đến%'
                );
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // CHECKIN POSTS
        // ------------------------------------------------

        $checkinPosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

                $q->where(
                    'name',
                    'like',
                    '%Checkin%'
                );
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // EXPERIENCE POSTS
        // ------------------------------------------------

        $experiencePosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

                $q->where(
                    'name',
                    'like',
                    '%Kinh nghiệm%'
                );
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // HOTEL POSTS
        // ------------------------------------------------

        $hotelPosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

                $q->where(
                    'name',
                    'like',
                    '%Khách sạn%'
                );
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // GUIDE POSTS
        // ------------------------------------------------

        $guidePosts = Post::published()

            ->with(['user', 'category'])

            ->whereHas('category', function ($q) {

                $q->where(
                    'name',
                    'like',
                    '%Cẩm nang%'
                );
            })

            ->latest()

            ->take(3)

            ->get();

        // ------------------------------------------------
        // CATEGORY LIST
        // ------------------------------------------------

        $categories = Category::withCount([

            'posts' => function ($q) {

            // withCount()
            // đếm số lượng posts

                $q->where(
                    'status',
                    'published'
                );

                // chỉ đếm bài published
            }

        ])->get();

        // get()
        // lấy toàn bộ category

        // ------------------------------------------------
        // RETURN VIEW
        // ------------------------------------------------

        return view('home', compact(

            'featuredPosts',
            // biến top bài nổi bật

            'latestPosts',
            // bài mới nhất

            'foodPosts',
            // bài ẩm thực

            'destinationPosts',
            // bài điểm đến

            'checkinPosts',
            // bài checkin

            'experiencePosts',
            // bài kinh nghiệm

            'hotelPosts',
            // bài khách sạn

            'guidePosts',
            // bài cẩm nang

            'categories'
            // danh mục
        ));

        // compact()
        // tạo mảng biến tự động
        // truyền dữ liệu sang view
    }
}
