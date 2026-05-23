<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    // Khai báo mảng các trường dữ liệu được phép gán (Mass Assignment) khi tạo hoặc cập nhật bài viết
    protected $fillable = [
        'user_id', 'category_id', 'title', 'slug', 'excerpt',
        'content', 'image', 'location', 'views_count', 'status'
    ];

    /**
     * ==========================================
     * ĐỊNH NGHĨA CÁC MỐI QUAN HỆ (RELATIONSHIPS)
     * ==========================================
     */

    // Nhiều bài viết thuộc về một Người dùng (Tác giả)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Nhiều bài viết thuộc về một Danh mục du lịch
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    // Một bài viết có thể nhận được Nhiều bình luận (Tất cả)
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    // Một bài viết có Nhiều bình luận nhưng chỉ lấy các bình luận ĐÃ ĐƯỢC DUYỆT (is_approved = true)
    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)->where('is_approved', true);
    }

    // Liên kết Một - Nhiều trực tiếp tới bảng trung gian lưu bài viết yêu thích
    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    // Liên kết Nhiều - Nhiều (Many-to-Many): Bài viết được yêu thích bởi Nhiều Người dùng
    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    // Một bài viết có thể nhận được Nhiều lượt chấm điểm/đánh giá sao (Ratings)
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * ==========================================
     * ĐỊNH NGHĨA CÁC THUỘC TÍNH ẢO (ACCESSORS)
     * ==========================================
     */

    // Accessor: Tính số sao trung bình của bài viết (Ví dụ: 4.5), làm tròn về 1 chữ số thập phân
    public function getAverageRatingAttribute(): float
    {
        return round($this->ratings()->avg('score') ?? 0, 1);
    }

    // Accessor: Đếm tổng số lượt người dùng đã đánh giá sao cho bài viết này
    public function getRatingCountAttribute(): int
    {
        return $this->ratings()->count();
    }

    // Accessor: Chuẩn hóa link ảnh đại diện bài viết (Tự xử lý link URL ngoài hoặc ảnh trong Storage, sinh ảnh giữ chỗ nếu rỗng)
    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            // Nếu đường dẫn ảnh bắt đầu bằng http:// hoặc https:// (dữ liệu mẫu/seeder), giữ nguyên link
            if (Str::startsWith($this->image, ['http://', 'https://'])) {
                return $this->image;
            }
            // Nếu là ảnh upload cục bộ, nối chuỗi sinh đường dẫn tuyệt đối từ thư mục storage
            return asset('storage/' . $this->image);
        }
        // Nếu không có ảnh, sinh link ảnh placeholder mặc định chứa text là 20 ký tự đầu của tiêu đề bài viết
        return 'https://placehold.co/800x400/0D8ABC/white?text=' . urlencode(Str::limit($this->title, 20));
    }

    /**
     * ==========================================
     * ĐỊNH NGHĨA CÁC BỘ LỌC TRUY VẤN (LOCAL SCOPES)
     * ==========================================
     */

    // Scope: Chỉ lọc ra những bài viết đang ở trạng thái đã xuất bản (published)
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    // Scope: Sắp xếp các bài viết theo mức độ phổ biến (lượt xem giảm dần)
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc');
    }

    /**
     * ==========================================
     * ĐỊNH NGHĨA CÁC MUTATORS (TIỀN XỬ LÝ DỮ LIỆU)
     * ==========================================
     */

    // Mutator: Tự động tạo mã slug không trùng lặp khi điền tiêu đề bài viết
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        // Chỉ tự sinh slug ngẫu nhiên khi bài viết mới tinh (chưa có slug), tránh làm đổi URL bài cũ khi cập nhật tiêu đề
        if (empty($this->attributes['slug'])) {
            $baseSlug = Str::slug($value);
            $slug = $baseSlug . '-' . Str::random(5); // Cộng thêm chuỗi 5 ký tự ngẫu nhiên tránh trùng lặp URL
            $this->attributes['slug'] = $slug;
        }
    }

    // Mutator: Làm sạch (Sanitize) nội dung HTML của bài viết trước khi lưu để phòng chống tấn công XSS (Cross-Site Scripting)
    public function setContentAttribute($value)
    {
        // Sử dụng Regex để tìm và loại bỏ hoàn toàn các thẻ <script> nguy hiểm
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);
        // Loại bỏ các thẻ nội dung nhúng <iframe> không rõ nguồn gốc
        $value = preg_replace('/<iframe\b[^>]*>(.*?)<\/iframe>/is', '', $value);
        // Loại bỏ các thuộc tính bắt sự kiện Javascript dạng on* (Ví dụ: onclick, onerror, onload) dính trong tag HTML
        $value = preg_replace('/\bon\w+\s*=\s*["\'][^"\']*["\']/i', '', $value);
        
        $this->attributes['content'] = $value;
    }

    /**
     * Kiểm tra nhanh xem một Người dùng cụ thể có lưu bài viết này vào danh sách yêu thích hay chưa
     */
    public function isFavoritedBy(?User $user): bool
    {
        if (!$user) return false;
        return $this->favorites()->where('user_id', $user->id)->exists();
    }
}