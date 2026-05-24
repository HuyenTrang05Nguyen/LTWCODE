<?php
// Mở thẻ PHP để bắt đầu viết code PHP

namespace App\Http\Controllers\Admin;
// Khai báo namespace.
// Giúp tổ chức code theo thư mục logic.
// File này thuộc thư mục: app/Http/Controllers/Admin

use App\Http\Controllers\Controller;
// Import class Controller gốc của Laravel.
// CategoryController sẽ kế thừa các chức năng cơ bản từ Controller này.

use App\Http\Requests\CategoryRequest;
// Import class CategoryRequest.
// Đây là nơi chứa validate dữ liệu nhập vào từ form.

use App\Models\Category;
// Import Model Category.
// Dùng để thao tác với bảng categories trong database.

use Illuminate\Support\Facades\Storage;
// Import Storage facade.
// Dùng để thao tác file: lưu ảnh, xóa ảnh,...

class CategoryController extends Controller
// Tạo class CategoryController kế thừa từ Controller
{
    public function index()
    // Hàm hiển thị danh sách category
    {
        $categories = Category::withCount('posts')->latest()->paginate(10);
        // Category::withCount('posts')
        // -> đếm số lượng bài viết thuộc mỗi category
        // -> tạo thêm cột posts_count

        // latest()
        // -> sắp xếp dữ liệu mới nhất lên đầu
        // -> mặc định theo created_at DESC

        // paginate(10)
        // -> phân trang
        // -> mỗi trang hiển thị 10 category

        return view('admin.categories.index', compact('categories'));
        // Trả về view:
        // resources/views/admin/categories/index.blade.php

        // compact('categories')
        // -> truyền biến $categories sang view
    }

    public function create()
    // Hàm hiển thị form tạo category mới
    {
        return view('admin.categories.create');
        // Trả về giao diện create.blade.php
    }

    public function store(CategoryRequest $request)
    // Hàm xử lý lưu category mới vào database
    {
        $data = $request->validated();
        // validated()
        // -> lấy dữ liệu đã validate thành công
        // -> đảm bảo dữ liệu sạch và đúng format

        if ($request->hasFile('image')) {
        // Kiểm tra user có upload ảnh không

            $data['image'] = $request->file('image')->store('categories', 'public');
            // Lấy file image upload lên

            // store('categories', 'public')
            // -> lưu file vào:
            // storage/app/public/categories

            // Laravel sẽ tự random tên file để tránh trùng.

            // Giá trị trả về ví dụ:
            // categories/abc123.jpg

            // Sau đó lưu đường dẫn này vào database.
        }

        Category::create($data);
        // Tạo category mới trong database bằng dữ liệu $data

        return redirect()->route('admin.categories.index')
        // Chuyển hướng về route danh sách category

        ->with('success', 'Tạo danh mục thành công!');
        // Flash session thông báo thành công
    }

    public function edit(Category $category)
    // Hàm hiển thị form sửa category
    {
        return view('admin.categories.edit', compact('category'));
        // Truyền category hiện tại sang form edit
    }

    public function update(CategoryRequest $request, Category $category)
    // Hàm cập nhật category
    {
        $data = $request->validated();
        // Lấy dữ liệu đã validate

        if ($request->hasFile('image')) {
        // Kiểm tra có upload ảnh mới không

            if ($category->image) {
            // Kiểm tra category cũ có ảnh không

                Storage::disk('public')->delete($category->image);
                // Xóa ảnh cũ trong storage

                // disk('public')
                // -> dùng ổ public

                // delete(...)
                // -> xóa file cũ khỏi server
            }

            $data['image'] = $request->file('image')->store('categories', 'public');
            // Upload ảnh mới và lưu đường dẫn mới
        }

        $category->update($data);
        // Update dữ liệu category trong database

        return redirect()->route('admin.categories.index')
        // Quay về trang danh sách

        ->with('success', 'Cập nhật danh mục thành công!');
        // Thông báo cập nhật thành công
    }

    public function destroy(Category $category)
    // Hàm xóa category
    {
        if ($category->posts()->count() > 0) {
        // Kiểm tra category còn bài viết không

            return back()->with('error', 'Không thể xóa danh mục đang có bài viết!');
            // Nếu còn bài viết thì không cho xóa
            // return back()
            // -> quay lại trang trước
        }

        if ($category->image) {
        // Kiểm tra có ảnh không

            Storage::disk('public')->delete($category->image);
            // Xóa file ảnh khỏi storage
        }

        $category->delete();
        // Xóa category khỏi database

        return redirect()->route('admin.categories.index')
        // Quay về danh sách category

        ->with('success', 'Xóa danh mục thành công!');
        // Thông báo xóa thành công
    }
}
