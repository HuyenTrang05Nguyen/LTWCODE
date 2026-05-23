<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::with(['user', 'category']);

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $posts = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }

    public function store(PostRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Tạo slug duy nhất, bypass mutator bằng cách dùng DB::insert hoặc setRawAttributes
        $slug = \Illuminate\Support\Str::slug($data['title']) . '-' . \Illuminate\Support\Str::random(5);

        $post = new Post();
        $post->setRawAttributes([
            'user_id'     => $data['user_id'],
            'category_id' => $data['category_id'],
            'title'       => $data['title'],
            'slug'        => $slug,
            'excerpt'     => $data['excerpt'] ?? null,
            'content'     => $data['content'],
            'image'       => $data['image'] ?? null,
            'location'    => $data['location'] ?? null,
            'status'      => $data['status'],
            'views_count' => 0,
        ]);
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Tạo bài viết thành công!');
    }

    public function edit(Post $post)
    {
        $categories = Category::all();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(PostRequest $request, Post $post)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($post->image && !\Illuminate\Support\Str::startsWith($post->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($post->image);
            }
            $data['image'] = $request->file('image')->store('posts', 'public');
        }

        // Giữ nguyên slug cũ, bypass mutator setTitleAttribute
        $attributes = [
            'category_id' => $data['category_id'],
            'title'       => $data['title'],
            'slug'        => $post->slug, // giữ nguyên slug
            'excerpt'     => $data['excerpt'] ?? null,
            'content'     => $data['content'],
            'location'    => $data['location'] ?? null,
            'status'      => $data['status'],
        ];
        if (isset($data['image'])) {
            $attributes['image'] = $data['image'];
        }

        $post->setRawAttributes(array_merge($post->getAttributes(), $attributes));
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Cập nhật bài viết thành công!');
    }

    public function destroy(Post $post)
    {
        if ($post->image) {
            Storage::disk('public')->delete($post->image);
        }
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Xóa bài viết thành công!');
    }
}
