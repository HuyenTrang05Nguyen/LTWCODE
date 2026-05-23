<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable; // Sử dụng trait tạo dữ liệu mẫu (HasFactory) và gửi thông báo/email (Notifiable)

    /**
     * Khai báo các cột trong bảng 'users' được phép chèn hoặc cập nhật dữ liệu hàng loạt (Mass Assignment)
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',   // Phân quyền tài khoản (ví dụ: 'admin', 'user')
        'avatar', // Đường dẫn file ảnh đại diện
    ];

    /**
     * Khai báo các trường dữ liệu cần ẩn đi khi Laravel biến đổi dữ liệu thành dạng JSON (Serialization)
     * Giúp bảo mật thông tin, không cho hiện mật khẩu và token ra API hoặc log
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu (Data Casting) tự động khi đọc hoặc ghi vào cơ sở dữ liệu
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime', // Ép kiểu ngày xác thực email về định dạng Datetime của PHP
            'password' => 'hashed',            // Tự động mã hóa mật khẩu khi lưu (bằng thuật toán bcrypt)
        ];
    }

    /**
     * Hàm kiểm tra xem người dùng hiện tại có phải là Quản trị viên (Admin) hay không
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * ==========================================
     * ĐỊNH NGHĨA CÁC MỐI QUAN HỆ (RELATIONSHIPS)
     * ==========================================
     */

    // Mối quan hệ Một - Nhiều (One-to-Many): Một người dùng có thể viết Nhiều bài viết (Post)
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    // Mối quan hệ Một - Nhiều (One-to-Many): Một người dùng có thể gửi Nhiều bình luận (Comment)
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Kết nối Một - Nhiều trực tiếp tới bảng trung gian lưu danh sách yêu thích
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    // Mối quan hệ Nhiều - Nhiều (Many-to-Many): Một người dùng có thể lưu Nhiều bài viết yêu thích khác nhau
    // Đi qua bảng trung gian tên là 'favorites', đồng thời lấy thêm 2 cột mốc thời gian (created_at, updated_at) của bảng này
    public function favoritePosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'favorites')->withTimestamps();
    }

    // Mối quan hệ Một - Nhiều (One-to-Many): Một người dùng có thể thực hiện Nhiều lượt đánh giá sao (Rating)
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Accessor: Tự động xử lý đường dẫn ảnh đại diện khi lấy dữ liệu ra ngoài View
     * Tạo ra một thuộc tính ảo mang tên 'avatar_url' (gọi ngoài view qua: $user->avatar_url)
     */
    public function getAvatarUrlAttribute(): string
    {
        // Nếu người dùng đã tải lên ảnh đại diện cá nhân, trả về link tuyệt đối dẫn đến file ảnh đó trong Storage
        if ($this->avatar) {
            return asset('storage/' . $this->avatar);
        }
        // Nếu chưa có ảnh, trả về link ảnh avatar mặc định theo tên người dùng được sinh tự động từ dịch vụ ui-avatars.com
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0D8ABC&color=fff';
    }
}