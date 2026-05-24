<?php
// Mở thẻ PHP

namespace App\Http\Controllers\Admin;
// Namespace của controller
// File nằm trong:
// app/Http/Controllers/Admin

use App\Http\Controllers\Controller;
// Import Controller gốc của Laravel

use App\Http\Requests\PostRequest;
// Import PostRequest
// Dùng để validate dữ liệu form bài viết

use App\Models\Post;
// Import model Post

use App\Models\Category;
// Import model Category

use Illuminate\Http\Request;
// Import Request để lấy dữ liệu request

use Illuminate\Support\Facades\Storage;
// Import Storage để xử lý upload/xóa file

class PostController extends Controller
// Tạo PostController
{
    public function index(Request $request)
    // Hàm hiển thị danh sách bài viết
    {
        $query = Post::with(['user', 'category']);
        // Tạo query lấy posts

        // with(['user', 'category'])
        // -> eager loading user và category
        // -> tránh N+1 Query

        if ($request->filled('search')) {
        // Kiểm tra có ô search không

            $query->where('title', 'like', "%{$request->search}%");
            // Search title bằng LIKE

            // SQL tương đương:
            // WHERE title LIKE '%keyword%'
        }

        if ($request->filled('status')) {
        // Kiểm tra có filter status không

            $query->where('status', $request->status);
            // Lọc theo status

            // Ví dụ:
            // published
            // draft
        }

        if ($request->filled('category_id')) {
        // Kiểm tra có lọc category không

            $query->where('category_id', $request->category_id);
            // Lọc bài viết theo category
        }

        $posts = $query->latest()->paginate(10)->withQueryString();
        // latest()
        // -> bài mới nhất lên đầu

        // paginate(10)
        // -> phân trang 10 bài/trang

        // withQueryString()
        // -> giữ query filter khi chuyển trang

        $categories = Category::all();
        // Lấy toàn bộ category
        // để hiển thị dropdown filter

        return view('admin.posts.index', compact('posts', 'categories'));
        // Trả về view:
        // resources/views/admin/posts/index.blade.php
    }

    public function create()
    // Hiển thị form tạo bài viết
    {
        $categories = Category::all();
        // Lấy category để đổ vào select option

        return view('admin.posts.create', compact('categories'));
        // Trả về form create
    }

    public function store(PostRequest $request)
    // Hàm lưu bài viết mới
    {
        $data = $request->validated();
        // Lấy dữ liệu đã validate

        $data['user_id'] = auth()->id();
        // Gắn user_id bằng user đang đăng nhập

        // auth()->id()
        // -> lấy id user hiện tại

        if ($request->hasFile('image')) {
        // Kiểm tra có upload ảnh không

            $data['image'] = $request->file('image')->store('posts', 'public');
            // Upload ảnh vào:
            // storage/app/public/posts

            // Laravel tự random tên file
        }

        // Tạo slug duy nhất, bypass mutator bằng cách dùng DB::insert hoặc setRawAttributes

        $slug = \Illuminate\Support\Str::slug($data['title'])
            . '-'
            . \Illuminate\Support\Str::random(5);

        // Str::slug()
        // -> convert title thành slug URL

        // Ví dụ:
        // "Hello World"
        // -> hello-world

        // Str::random(5)
        // -> random 5 ký tự tránh trùng slug

        // Ví dụ:
        // hello-world-x7a2p

        $post = new Post();
        // Tạo object Post mới

        $post->setRawAttributes([
        // setRawAttributes()
        // -> gán dữ liệu trực tiếp vào model

        // KHÔNG chạy:
        // mutator
        // casting
        // accessor

        // Đây là kỹ thuật senior-level khá advanced

            'user_id'     => $data['user_id'],
            // ID người viết

            'category_id' => $data['category_id'],
            // ID category

            'title'       => $data['title'],
            // Tiêu đề bài viết

            'slug'        => $slug,
            // Slug custom vừa tạo

            'excerpt'     => $data['excerpt'] ?? null,
            // Mô tả ngắn

            // ?? null
            // nếu không có thì gán null

            'content'     => $data['content'],
            // Nội dung bài viết

            'image'       => $data['image'] ?? null,
            // Ảnh bài viết

            'location'    => $data['location'] ?? null,
            // Địa điểm

            'status'      => $data['status'],
            // Trạng thái bài viết

            'views_count' => 0,
            // Mặc định lượt xem = 0
        ]);

        $post->save();
        // Lưu vào database

        return redirect()->route('admin.posts.index')
        // Redirect về danh sách bài viết

        ->with('success', 'Tạo bài viết thành công!');
        // Flash message thành công
    }

    public function edit(Post $post)
    // Hiển thị form edit
    {
        $categories = Category::all();
        // Lấy categories

        return view('admin.posts.edit', compact('post', 'categories'));
        // Trả về view edit
    }

    public function update(PostRequest $request, Post $post)
    // Hàm cập nhật bài viết
    {
        $data = $request->validated();
        // Lấy dữ liệu validate

        if ($request->hasFile('image')) {
        // Nếu upload ảnh mới

            if (
                $post->image
                &&
                !\Illuminate\Support\Str::startsWith(
                    $post->image,
                    ['http://', 'https://']
                )
            ) {

            // Kiểm tra:
            // có ảnh cũ
            // và ảnh không phải URL online

                Storage::disk('public')->delete($post->image);
                // Xóa ảnh cũ khỏi storage
            }

            $data['image'] = $request->file('image')->store('posts', 'public');
            // Upload ảnh mới
        }

        // Giữ nguyên slug cũ, bypass mutator setTitleAttribute

        $attributes = [
        // Tạo mảng dữ liệu update

            'category_id' => $data['category_id'],
            // category mới

            'title'       => $data['title'],
            // title mới

            'slug'        => $post->slug,
            // GIỮ NGUYÊN slug cũ

            // Vì:
            // đổi slug sẽ làm hỏng URL SEO cũ

            'excerpt'     => $data['excerpt'] ?? null,
            // mô tả ngắn

            'content'     => $data['content'],
            // nội dung

            'location'    => $data['location'] ?? null,
            // địa điểm

            'status'      => $data['status'],
            // trạng thái
        ];

        if (isset($data['image'])) {
        // Nếu có ảnh mới

            $attributes['image'] = $data['image'];
            // thêm image vào attributes
        }

        $post->setRawAttributes(
            array_merge(
                $post->getAttributes(),
                $attributes
            )
        );

        // getAttributes()
        // -> lấy toàn bộ dữ liệu cũ

        // array_merge()
        // -> merge dữ liệu cũ + mới

        // setRawAttributes()
        // -> update trực tiếp không chạy mutator

        $post->save();
        // Lưu update vào database

        return redirect()->route('admin.posts.index')
            ->with('success', 'Cập nhật bài viết thành công!');
        // Redirect + flash message
    }

    public function destroy(Post $post)
    // Hàm xóa bài viết
    {
        if ($post->image) {
        // Kiểm tra có ảnh không

            Storage::disk('public')->delete($post->image);
            // Xóa file ảnh
        }

        $post->delete();
        // Xóa bài viết khỏi database

        return redirect()->route('admin.posts.index')
            ->with('success', 'Xóa bài viết thành công!');
        // Redirect + thông báo
    }
}
