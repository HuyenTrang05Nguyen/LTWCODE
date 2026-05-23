<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Favorite extends Model
{
    // Khai báo các cột được phép gán dữ liệu hàng loạt (Mass Assignment) khi người dùng bấm lưu bài viết
    protected $fillable = ['user_id', 'post_id'];

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bản ghi yêu thích phải thuộc về một Người dùng (User) cụ thể
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mối quan hệ Nhiều - Một (Many-to-One): Một bản ghi yêu thích phải liên kết với một Bài viết (Post) cụ thể
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}