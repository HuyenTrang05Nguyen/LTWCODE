<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    // Khai báo các cột trong bảng 'categories' được phép chèn hoặc cập nhật dữ liệu hàng loạt (Mass Assignment)
    protected $fillable = ['name', 'slug', 'description', 'image'];

    /**
     * Định nghĩa mối quan hệ Một - Nhiều (One-to-Many)
     * Một danh mục (Category) sẽ có thể chứa nhiều bài viết (Posts)
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Mutator: Tự động xử lý dữ liệu trước khi lưu vào Database
     * Khi đặt hoặc cập nhật giá trị cho 'name', hàm này sẽ tự sinh ra 'slug' tương ứng dạng không dấu (Ví dụ: "Ẩm thực" -> "am-thuc")
     */
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = $value;
        $this->attributes['slug'] = Str::slug($value); // Sử dụng Helper Str::slug để chuyển đổi chuỗi thành dạng slug url
    }

    /**
     * Accessor: Tự động xử lý dữ liệu sau khi lấy từ Database ra ngoài hiển thị
     * Tạo ra một thuộc tính ảo mang tên 'image_url' (gọi ngoài view qua: $category->image_url)
     */
    public function getImageUrlAttribute(): string
    {
        // Nếu danh mục có file ảnh được lưu trong database thì trả về đường dẫn đầy đủ của ảnh đó
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        // Nếu danh mục không có ảnh, trả về một link ảnh giữ chỗ (placeholder) tự động chèn tên danh mục làm text đại diện
        return 'https://placehold.co/400x300/0D8ABC/white?text=' . urlencode($this->name);
    }
}