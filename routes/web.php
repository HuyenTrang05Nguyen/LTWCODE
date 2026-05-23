<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController as AdminPostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CommentController;

use App\Http\Controllers\ChatbotController;

/*
|--------------------------------------------------------------------------
| Public Routes (Nhóm các đường dẫn ai cũng xem được, không cần đăng nhập)
|--------------------------------------------------------------------------
*/

// Trang chủ của website
Route::get('/', [HomeController::class, 'index'])->name('home');

// Gửi câu hỏi và nhận câu trả lời từ Chatbot API
Route::post('/chatbot', [ChatbotController::class, 'chat'])->name('chatbot.chat');

// Danh sách toàn bộ bài viết và chi tiết một bài viết dựa theo đường dẫn định danh (Slug)
Route::get('/bai-viet', [PostController::class, 'index'])->name('posts.index');
Route::get('/bai-viet/{post:slug}', [PostController::class, 'show'])->name('posts.show');

// Nhóm các trang chỉ dành cho khách vãng lai (Ai đăng nhập rồi sẽ không được quay lại đây nữa)
Route::middleware('guest')->group(function () {
    // Xem form đăng nhập và xử lý gửi dữ liệu đăng nhập
    Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/dang-nhap', [AuthController::class, 'login']);
    
    // Xem form đăng ký và xử lý gửi dữ liệu đăng ký thành viên
    Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/dang-ky', [AuthController::class, 'register']);

    // Nhóm tính năng Quên mật khẩu và Đặt lại mật khẩu qua Token xác thực
    Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Đường dẫn Đăng xuất tài khoản (Bắt buộc phải là thành viên đã đăng nhập thành công)
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes (Nhóm tính năng yêu cầu phải ĐĂNG NHẬP THÀNH VIÊN)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Gửi bình luận dưới bài viết
    Route::post('/bai-viet/{post}/binh-luan', [PostController::class, 'comment'])->name('posts.comment');

    // Thêm/Xóa bài viết khỏi danh sách yêu thích và Xem danh sách bài viết đã lưu yêu thích
    Route::post('/bai-viet/{post}/yeu-thich', [PostController::class, 'toggleFavorite'])->name('posts.favorite');
    Route::get('/yeu-thich', [PostController::class, 'favorites'])->name('posts.favorites');

    // Đánh giá số sao (Rating) cho bài viết
    Route::post('/bai-viet/{post}/danh-gia', [PostController::class, 'rate'])->name('posts.rate');

    // Xem trang cá nhân (Hồ sơ) và cập nhật thay đổi thông tin cá nhân
    Route::get('/ho-so', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/ho-so', [ProfileController::class, 'update'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Routes (Nhóm tính năng quản trị tối cao, yêu cầu đăng nhập và có quyền Admin)
|--------------------------------------------------------------------------
*/

// prefix('admin'): Gom chung tiền tố URL dạng /admin/...
// name('admin.'): Tự động nối thêm tiền tố vào tên route dạng admin.dashboard, admin.posts...
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Trang tổng quan (Dashboard) hệ thống quản trị
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Quản lý bài viết (Thêm, sửa, xóa danh sách bài viết) ngoại trừ trang xem chi tiết
    Route::resource('posts', AdminPostController::class)->except(['show']);

    // Quản lý danh mục bài viết (Thêm, sửa, xóa thể loại) ngoại trừ trang xem chi tiết
    Route::resource('categories', CategoryController::class)->except(['show']);

    // Quản lý người dùng: Xem danh sách, thay đổi quyền (User <=> Admin), xóa tài khoản viên
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::patch('/users/{user}/toggle-role', [UserController::class, 'toggleRole'])->name('users.toggleRole');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');

    // Quản lý bình luận: Xem danh sách, Duyệt bình luận, Từ chối hiển thị và Xóa bỏ bình luận xấu
    Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
    Route::patch('/comments/{comment}/reject', [CommentController::class, 'reject'])->name('comments.reject');
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');
});