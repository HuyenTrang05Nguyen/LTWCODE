<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rating extends Model
{
    // Khai báo các trường dữ liệu được phép chèn hoặc cập nhật hàng loạt (Mass Assignment) khi user chấm điểm
    protected $fillable = ['user_id', 'post_id', 'score'];

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bản ghi đánh giá phải thuộc về một Người dùng (User) cụ thể
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bản ghi đánh giá phải nằm trong một Bài viết (Post) cụ thể
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}