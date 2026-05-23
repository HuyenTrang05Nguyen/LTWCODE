<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    use HasFactory;

    // Khai báo các thuộc tính được phép gán dữ liệu hàng loạt (Mass Assignment) khi tạo hoặc cập nhật bình luận
    protected $fillable = ['user_id', 'post_id', 'content', 'is_approved'];

    /**
     * Thuộc tính Casts: Ép kiểu dữ liệu tự động khi tương tác với Database
     */
    protected function casts(): array
    {
        return [
            // Ép kiểu cột 'is_approved' về dạng Boolean (true/false) thay vì nhận giá trị số 0/1 thuần túy từ MySQL
            'is_approved' => 'boolean',
        ];
    }

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bình luận thuộc về một Người dùng (User) cụ thể
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bình luận thuộc về một Bài viết (Post) cụ thể
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Local Scope: Khai báo một bộ lọc truy vấn tái sử dụng được nhiều nơi
     * Giúp viết ngắn gọn câu lệnh khi chỉ muốn lấy các bình luận đã được Quản trị viên phê duyệt công khai
     */
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }
}