@extends('layouts.app')

{{-- Gán tiêu đề trang động bằng chính tiêu đề bài viết hiện tại lấy từ biến $post --}}
@section('title', $post->title)

{{-- Khởi tạo vùng nội dung chính đổ vào cấu trúc Layout Master --}}
@section('content')

<style>
/* Khung bọc ảnh bìa lớn ở đầu trang */
.post-cover {
    height: 420px;
    position: relative;
    overflow: hidden;
}
.post-cover img {
    width: 100%; height: 100%;
    object-fit: cover; /* Đảm bảo ảnh lấp đầy khung mà không bị bóp méo tỉ lệ */
    display: block;
}
/* Lớp phủ chuyển màu gradient từ trong suốt sang Slate sẫm màu dưới đáy để tạo tương phản giúp tiêu đề chữ trắng rõ nét hơn */
.post-cover-overlay {
    position: absolute; inset: 0;
    background: linear-gradient(transparent 30%, rgba(15,23,42,0.85));
}
/* Khối định vị chữ tiêu đề nằm xếp sát đáy của phần ảnh bìa */
.post-cover-title {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    padding: 2.5rem 0;
}
/* Phần thân bài viết quy định kích thước bề ngang vừa mắt và khoảng cách dòng thoáng (line-height: 1.9) chuyên nghiệp cho blog */
.article-body {
    max-width: 800px;
    margin: 0 auto;
    font-family: 'Inter', sans-serif;
    font-size: 1.05rem;
    line-height: 1.9;
    color: var(--slate);
}
/* Thanh bên (Sidebar) tự động ghim cố định vị trí cách đỉnh 100px khi người dùng cuộn chuột đọc nội dung dài */
.sticky-sidebar {
    position: sticky;
    top: 100px;
}
/* Bo tròn ảnh đại diện của người dùng trong phân đoạn bình luận */
.comment-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(212,163,115,0.3);
    flex-shrink: 0; /* Ngăn không cho flexbox bóp méo kích thước tròn của avatar */
}
/* Thẻ hiển thị các bài viết liên quan dạng danh bạ thu nhỏ */
.related-card {
    display: flex;
    gap: 1rem;
    padding: 0.85rem 0;
    border-bottom: 1px solid rgba(212,163,115,0.15);
    text-decoration: none;
    transition: all 0.3s ease;
}
/* Tạo hiệu ứng nhích nhẹ sang phải 4px khi rê chuột vào bài viết liên quan */
.related-card:hover { padding-left: 4px; }
.related-card img {
    width: 80px; height: 64px;
    object-fit: cover;
    border-radius: 10px;
    flex-shrink: 0;
}
/* Tùy chỉnh nhãn ngôi sao đánh giá giúp người dùng dễ tương tác */
.rating-star-label {
    cursor: pointer;
    font-size: 1.6rem;
    transition: color 0.2s, transform 0.2s;
}
/* Phóng to nhẹ ngôi sao 15% khi rê chuột chuẩn bị click đánh giá */
.rating-star-label:hover { transform: scale(1.15); }
</style>

@if($post->image)
<div class="post-cover">
    <img src="{{ $post->image_url }}" alt="{{ $post->title }}">
    <div class="post-cover-overlay"></div>
    <div class="post-cover-title">
        <div class="container">
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}" class="badge-category text-decoration-none" style="background:rgba(212,163,115,0.25); color:var(--gold); border-color:rgba(212,163,115,0.4);">{{ $post->category->name }}</a>
                @if($post->location)
                <span class="badge-category" style="background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(255,255,255,0.3);">
                    <i class="fas fa-map-marker-alt me-1"></i>{{ $post->location }}
                </span>
                @endif
            </div>
            <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:700; color:#fff; line-height:1.2; max-width:800px;">{{ $post->title }}</h1>
        </div>
    </div>
</div>
@endif

<article class="py-5">
    <div class="container">
        <div class="row g-4">
            
            <div class="col-lg-8">
                
                <div class="d-flex align-items-center gap-4 flex-wrap mb-4 pb-4" style="border-bottom:1px solid rgba(212,163,115,0.2);">
                    <div class="d-flex align-items-center gap-3">
                        <img src="{{ $post->user->avatar_url }}" alt="" class="user-avatar" style="width:48px; height:48px;">
                        <div>
                            <div style="font-weight:700; font-size:0.95rem; color:var(--navy);">{{ $post->user->name }}</div>
                            <small style="color:var(--text-muted);">{{ $post->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                    </div>
                    <div class="meta-info ms-auto flex-wrap" style="gap:1rem;">
                        <span><i class="fas fa-eye me-1"></i>{{ number_format($post->views_count) }}</span>
                        <span><i class="fas fa-comment me-1"></i>{{ $post->approvedComments->count() }}</span>
                        <span class="stars">
                            {{-- Vòng lặp vẽ sao điểm trung bình, chèn thêm class 'empty' nếu điểm số thấp hơn vị trí sao --}}
                            @for($i=1; $i<=5; $i++)
                                <i class="fas fa-star{{ $i <= round($post->average_rating) ? '' : ' empty' }}" style="font-size:0.85rem;"></i>
                            @endfor
                            <small style="color:var(--text-muted);">({{ $post->rating_count }})</small>
                        </span>
                    </div>
                </div>

                @if(!$post->image)
                <div class="d-flex gap-2 mb-4 flex-wrap">
                    <a href="{{ route('posts.index', ['category' => $post->category->slug]) }}" class="badge-category text-decoration-none">{{ $post->category->name }}</a>
                    @if($post->location)
                    <span class="badge-category" style="background:rgba(212,163,115,0.1); color:var(--gold-dark);">
                        <i class="fas fa-map-marker-alt me-1"></i>{{ $post->location }}
                    </span>
                    @endif
                </div>
                <h1 style="font-family:'Playfair Display',serif; font-size:2rem; font-weight:700; color:var(--navy); line-height:1.25; margin-bottom:1.5rem;">{{ $post->title }}</h1>
                @endif

                <div class="article-body post-content">
                    {!! $post->content !!}
                </div>

                <div class="d-flex gap-3 mt-5 mt-5 pt-4 flex-wrap" style="border-top:1px solid rgba(212,163,115,0.2);">
                    @auth
                    <form method="POST" action="{{ route('posts.favorite', $post) }}">
                        @csrf
                        {{-- isFavoritedBy(): Hàm kiểm tra bài viết đã được user hiện tại bấm lưu hay chưa để đổi trạng thái màu sắc class active --}}
                        <button type="submit" class="btn btn-favorite {{ $post->isFavoritedBy(auth()->user()) ? 'active' : '' }}" style="border-radius:var(--radius-full); padding:0.55rem 1.5rem;">
                            <i class="fas fa-heart me-1"></i>{{ $post->isFavoritedBy(auth()->user()) ? 'Đã lưu' : 'Lưu yêu thích' }}
                        </button>
                    </form>
                    @endauth
                </div>

                @auth
                <div style="background:#fff; border-radius:16px; padding:1.5rem; margin-top:1.5rem; border:1px solid rgba(212,163,115,0.2); box-shadow:var(--shadow-sm);">
                    <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1rem;">
                        <i class="fas fa-star me-2" style="color:var(--gold);"></i>Đánh giá bài viết
                    </h5>
                    <form method="POST" action="{{ route('posts.rate', $post) }}" class="d-flex align-items-center gap-3 flex-wrap">
                        @csrf
                        <div class="rating-input d-flex gap-1 flex-row-reverse">
                            @for($i=5; $i>=1; $i--)
                            <input type="radio" name="score" value="{{ $i }}" id="star{{ $i }}" {{ $userRating && $userRating->score == $i ? 'checked' : '' }} style="display:none;">
                            <label for="star{{ $i }}" class="rating-star-label" style="color:{{ $userRating && $userRating->score >= $i ? '#D4A373' : '#d6d3d1' }};">
                                <i class="fas fa-star"></i>
                            </label>
                            @endfor
                        </div>
                        <button type="submit" class="btn btn-primary-custom btn-sm" style="border-radius:var(--radius-full); padding:0.45rem 1.5rem;">Gửi đánh giá</button>
                        @if($userRating)<span style="color:var(--text-muted); font-size:0.85rem;">Đã đánh giá {{ $userRating->score }}/5</span>@endif
                    </form>
                </div>
                @endauth

                <div style="background:#fff; border-radius:16px; padding:2rem; margin-top:1.5rem; border:1px solid rgba(212,163,115,0.2); box-shadow:var(--shadow-sm);">
                    <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1.5rem;">
                        <i class="fas fa-comments me-2" style="color:var(--gold);"></i>Bình luận ({{ $post->approvedComments->count() }})
                    </h5>
                    
                    @auth
                    <form method="POST" action="{{ route('posts.comment', $post) }}" class="mb-4">
                        @csrf
                        <div class="d-flex gap-3">
                            <img src="{{ auth()->user()->avatar_url }}" alt="" class="comment-avatar">
                            <div class="flex-grow-1">
                                <textarea name="content" class="form-control form-control-dark" rows="3" placeholder="Viết bình luận của bạn..." required style="border-radius:var(--radius-sm);">{{ old('content') }}</textarea>
                                @error('content')<small class="text-danger">{{ $message }}</small>@enderror
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <button type="submit" class="btn btn-primary-custom btn-sm" style="border-radius:var(--radius-full);">
                                        <i class="fas fa-paper-plane me-1"></i>Gửi bình luận
                                    </button>
                                    <small style="color:var(--text-muted);"><i class="fas fa-info-circle me-1"></i>Bình luận sẽ hiển thị sau khi được duyệt</small>
                                </div>
                            </div>
                        </div>
                    </form>
                    @else
                    <div class="p-3 mb-4 text-center" style="background:rgba(212,163,115,0.07); border-radius:var(--radius-sm); border:1px solid rgba(212,163,115,0.2);">
                        <a href="{{ route('login') }}" style="color:var(--gold-dark); font-weight:600;">Đăng nhập</a> để bình luận
                    </div>
                    @endauth
                    
                    @forelse($post->approvedComments as $comment)
                    <div class="d-flex gap-3 mb-4 pb-4" style="border-bottom:1px solid rgba(212,163,115,0.12);">
                        <img src="{{ $comment->user->avatar_url }}" alt="" class="comment-avatar">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span style="font-weight:700; font-size:0.9rem; color:var(--navy);">{{ $comment->user->name }}</span>
                                <small style="color:var(--text-muted);">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p style="color:var(--text-secondary); font-size:0.95rem; margin:0; line-height:1.65;">{{ $comment->content }}</p>
                        </div>
                    </div>
                    @empty
                    <p style="color:var(--text-muted); text-align:center; padding:1rem 0;">Chưa có bình luận nào. Hãy là người đầu tiên!</p>
                    @endforelse
                </div>
            </div>

            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    
                    @if($relatedPosts->count())
                    <div style="background:#fff; border-radius:16px; padding:1.5rem; margin-bottom:1.5rem; border:1px solid rgba(212,163,115,0.2); box-shadow:var(--shadow-sm);">
                        <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1rem; font-size:1rem;">
                            <i class="fas fa-link me-2" style="color:var(--gold);"></i>Bài viết liên quan
                        </h5>
                        @foreach($relatedPosts as $related)
                        <a href="{{ route('posts.show', $related->slug) }}" class="related-card">
                            <img src="{{ $related->image_url }}" alt="{{ $related->title }}">
                            <div>
                                <h6 style="font-family:'Playfair Display',serif; font-size:0.85rem; font-weight:600; color:var(--navy); line-height:1.4; margin-bottom:0.3rem;">{{ Str::limit($related->title, 50) }}</h6>
                                <div class="meta-info" style="font-size:0.75rem;">
                                    <span><i class="fas fa-eye me-1"></i>{{ number_format($related->views_count) }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endif

                    <div style="background:#fff; border-radius:16px; padding:1.5rem; border:1px solid rgba(212,163,115,0.2); box-shadow:var(--shadow-sm);">
                        <h5 style="font-family:'Playfair Display',serif; font-weight:700; color:var(--navy); margin-bottom:1rem; font-size:1rem;">
                            <i class="fas fa-folder me-2" style="color:var(--gold);"></i>Danh mục
                        </h5>
                        {{-- Viết mã PHP thuần trực tiếp để truy vấn nhanh danh mục kèm theo bộ đếm số lượng bài viết đã xuất bản (published) --}}
                        @php $allCats = \App\Models\Category::withCount(['posts' => fn($q) => $q->published()])->get(); @endphp
                        @foreach($allCats as $cat)
                        <a href="{{ route('posts.index', ['category' => $cat->slug]) }}" class="d-flex justify-content-between align-items-center py-2 text-decoration-none" style="border-bottom:1px solid rgba(212,163,115,0.12); color:var(--text-secondary); transition:color 0.2s;" onmouseover="this.style.color='var(--gold-dark)'" onmouseout="this.style.color='var(--text-secondary)'">
                            <span style="font-size:0.9rem;">{{ $cat->name }}</span>
                            <span class="badge-category" style="font-size:0.7rem;">{{ $cat->posts_count }}</span>
                        </a>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</article>

@push('scripts')
<script>
(function(){
    // Truy vấn lấy ra các thẻ nhãn đại diện ngôi sao và các ô input radio ẩn tương ứng
    const labels = document.querySelectorAll('.rating-input label');
    const inputs = document.querySelectorAll('.rating-input input');
    
    // Hàm phụ tìm kiếm xem người dùng đang check chọn bao nhiêu điểm sao
    function getCheckedVal(){
        let v = 0;
        inputs.forEach(i => { if(i.checked) v = parseInt(i.value); });
        return v;
    }
    
    // Hàm tô màu hoặc trả lại màu xám cho các ngôi sao dựa theo giá trị điểm số truyền vào
    function paintStars(val){
        labels.forEach((l, idx) => {
            // Do cấu trúc flex-row-reverse nên nhãn index 0 thực chất ứng với Ngôi sao thứ 5
            const starVal = 5 - idx;
            l.style.color = starVal <= val ? '#D4A373' : '#d6d3d1';
        });
    }
    
    // Gán các sự kiện lắng nghe hành vi chuột của người dùng lên từng ngôi sao
    labels.forEach((l, idx) => {
        const starVal = 5 - idx;
        // Rê chuột vào sao nào thì thắp sáng tạm thời từ sao đó đổ xuống
        l.addEventListener('mouseover', () => paintStars(starVal));
        // Rời chuột ra ngoài thì trả lại đúng số điểm sao thực tế mà người dùng đang lựa chọn ban đầu
        l.addEventListener('mouseout', () => paintStars(getCheckedVal()));
        // Khi bấm click chuột, thiết lập độ trễ ngắn 50ms để đợi thuộc tính radio checked kịp cập nhật rồi tô lại màu chuẩn xác
        l.addEventListener('click', () => setTimeout(() => paintStars(getCheckedVal()), 50));
    });
    
    // Khởi chạy hàm paintStars lần đầu tiên để nhận diện số điểm cũ nếu có sẵn từ database
    paintStars(getCheckedVal());
})();
</script>
@endpush
@endsection