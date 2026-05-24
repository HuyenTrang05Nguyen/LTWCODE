<?php
// Mở thẻ PHP để bắt đầu viết code PHP

namespace App\Http\Controllers\Admin;
// Namespace của controller này.
// File nằm trong thư mục:
// app/Http/Controllers/Admin

use App\Http\Controllers\Controller;
// Import Controller gốc của Laravel.
// CommentController sẽ kế thừa các chức năng cơ bản từ đây.

use App\Models\Comment;
// Import model Comment.
// Model này dùng để thao tác bảng comments trong database.

use Illuminate\Http\Request;
// Import class Request.
// Dùng để lấy dữ liệu request từ form, URL, query string,...

class CommentController extends Controller
// Tạo controller CommentController
{
    public function index(Request $request)
    // Hàm hiển thị danh sách comment
    {
        $query = Comment::with(['user', 'post']);
        // Tạo query lấy comment

        // with(['user', 'post'])
        // -> eager loading
        // -> load luôn quan hệ user và post

        // Mục đích:
        // tránh lỗi N+1 Query
        // tăng hiệu năng hệ thống

        // Quan hệ:
        // comment thuộc về user
        // comment thuộc về post

        if ($request->filled('status')) {
        // Kiểm tra request có truyền status không

        // Ví dụ URL:
        // ?status=pending

            if ($request->status === 'pending') {
            // Nếu status = pending

                $query->where('is_approved', false);
                // Lấy các comment chưa duyệt

                // is_approved = false
                // nghĩa là comment đang bị ẩn/chờ duyệt
            } elseif ($request->status === 'approved') {
            // Nếu status = approved

                $query->where('is_approved', true);
                // Lấy các comment đã duyệt
            }
        }

        $comments = $query->latest()->paginate(15)->withQueryString();
        // latest()
        // -> sắp xếp comment mới nhất lên đầu

        // paginate(15)
        // -> phân trang
        // -> mỗi trang 15 comment

        // withQueryString()
        // -> giữ lại query string khi chuyển trang

        // Ví dụ:
        // ?status=pending&page=2

        return view('admin.comments.index', compact('comments'));
        // Trả về view:
        // resources/views/admin/comments/index.blade.php

        // compact('comments')
        // -> truyền biến $comments sang view
    }

    public function approve(Comment $comment)
    // Hàm duyệt comment
    {
        $comment->update(['is_approved' => true]);
        // Update cột is_approved = true

        // true nghĩa là:
        // comment được hiển thị công khai

        return back()->with('success', 'Đã duyệt bình luận!');
        // Quay lại trang trước
        // và gửi flash message thành công
    }

    public function reject(Comment $comment)
    // Hàm ẩn/từ chối comment
    {
        $comment->update(['is_approved' => false]);
        // Update is_approved = false

        // false nghĩa là:
        // comment bị ẩn khỏi website

        return back()->with('success', 'Đã ẩn bình luận!');
        // Quay lại trang trước + thông báo
    }

    public function destroy(Comment $comment)
    // Hàm xóa comment
    {
        $comment->delete();
        // Xóa comment khỏi database

        return back()->with('success', 'Đã xóa bình luận!');
        // Quay lại trang trước + thông báo thành công
    }
}
