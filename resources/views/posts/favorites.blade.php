{{ -- Kế thừa cấu trúc giao diện chung (Master Layout) từ file resources/views/layouts/app.blade.php -- }}
@extends('layouts.app')

{{ -- Định nghĩa nội dung cho thẻ tiêu đề <title> của trang web -- }}
@section('title', 'Bài viết yêu thích')

{{ -- Bắt đầu đổ nội dung vào vùng hiển thị chính (@yield('content')) trên Layout -- }}
@section('content')

<style>
/* Khối background tiêu đề trang (Hero Section) sử dụng dải màu gradient từ xanh Navy sang xanh Forest */
.favorites-hero {
    background: linear-gradient(135deg, var(--navy) 0%, var(--forest) 100%);
    padding: 4rem 0 3rem;
    position: relative;
    overflow: hidden; /* Ẩn các phần tử con bị tràn ra ngoài khối hero */
}
/* Lớp giả tạo một lớp ảnh nền mờ (Beach image) nằm ẩn phía dưới tiêu đề để tăng tính thẩm mỹ */
.favorites-hero::before {
    content:'';
    position:absolute; inset:0;
    background: url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=1200&q=60') center/cover;
    opacity: 0.07; /* Hạ độ mờ xuống 7% để không làm chìm chữ trắng phía trên */
}
</style>

<div class="favorites-hero">
    <div class="container position-relative text-center">
        <span style="display:inline-block; background:rgba(212,163,115,0.25); border:1px solid rgba(212,163,115,0.5); color:var(--gold); padding:0.3rem 1rem; border-radius:9999px; font-size:0.78rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; margin-bottom:1rem;">
            <i class="fas fa-heart me-1"></i> Yêu thích
        </span>
        <h1 style="font-family:'Playfair Display',serif; font-size:clamp(1.8rem,4vw,2.8rem); font-weight:700; color:#fff; margin-bottom:0.75rem;">
            Bài viết <span style="color:var(--gold);">Yêu thích</span>
        </h1>
        <p style="color:rgba(255,255,255,0.7); font-size:1rem;">Những bài viết bạn đã lưu lại</p>
    </div>
</div>

<section class="py-5"> <div class="container">
        {{ -- @if($posts->count()): Kiểm tra nếu mảng hoặc bộ sưu tập bài viết có chứa dữ liệu (lớn hơn 0) -- }}
        @if($posts->count())
        <div class="row g-4">
            {{ -- @foreach: Vòng lặp duyệt qua từng bài viết yêu thích lấy ra từ cơ sở dữ liệu -- }}
            @foreach($posts as $post)
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
                <div class="card-glass h-100" style="border-radius:16px; overflow:hidden;">
                    
                    <div class="card-img-wrapper" style="overflow:hidden; position:relative;">
                        <img src="{{ $post->image_url }}" class="card-img-top" alt="{{ $post->title }}"
                             style="height:230px; object-fit:cover; width:100%; transition:transform 0.5s ease;">
                        <div class="card-img-overlay-gradient"></div>
                        
                        <div class="position-absolute top-0 start-0 p-3" style="z-index:3;">
                            <span class="badge-category" style="backdrop-filter:blur(8px); background:rgba(255,255,255,0.92); color:var(--gold-dark);">{{ $post->category->name }}</span>
                        </div>
                        
                        <div class="position-absolute top-0 end-0 p-3" style="z-index:3;">
                            <span style="display:inline-flex; align-items:center; gap:4px; background:rgba(239,68,68,0.85); color:#fff; border-radius:9999px; font-size:0.72rem; font-weight:600; padding:0.3rem 0.75rem; backdrop-filter:blur(6px);">
                                <i class="fas fa-heart"></i> Đã lưu
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body d-flex flex-column p-4">
                        <h5 class="card-title" style="font-family:'Playfair Display',serif; font-size:1.05rem; font-weight:700; line-height:1.4;">
                            <a href="{{ route('posts.show', $post->slug) }}" style="color:var(--navy); text-decoration:none; transition:color 0.3s ease;">{{ $post->title }}</a>
                        </h5>
                        
                        <p class="card-text flex-grow-1" style="color:var(--text-secondary); font-size:0.88rem; line-height:1.65;">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 100) }}</p>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3" style="border-top:1px solid rgba(212,163,115,0.15);">
                            <div class="meta-info" style="font-size:0.8rem;">
                                <span><i class="fas fa-user me-1"></i>{{ $post->user->name }}</span>
                            </div>
                            
                            <form method="POST" action="{{ route('posts.favorite', $post) }}">
                                @csrf {{ -- @csrf: Token bắt buộc của Laravel để bảo vệ hệ thống khỏi lỗ hổng giả mạo yêu cầu chéo trang (CSRF) -- }}
                                <button type="submit" class="btn btn-favorite active btn-sm" style="border-radius:var(--radius-full); font-size:0.8rem; padding:0.35rem 0.9rem;">
                                    <i class="fas fa-heart me-1"></i>Bỏ lưu
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
        
        <div class="d-flex justify-content-center mt-5">{{ $posts->links() }}</div>
        
        {{ -- @else: Nhánh xử lý giao diện trống khi người dùng chưa bấm lưu bất kỳ bài viết nào -- }}
        @else
        <div class="text-center py-5 my-4" data-aos="fade-up">
            <div style="width:90px; height:90px; background:rgba(212,163,115,0.1); border-radius:50%; display:inline-flex; align-items:center; justify-content:center; margin-bottom:1.5rem;">
                <i class="fas fa-heart fa-2x" style="color:var(--gold);"></i>
            </div>
            <h4 style="font-family:'Playfair Display',serif; color:var(--navy); margin-bottom:0.75rem;">Chưa có bài viết yêu thích</h4>
            <p style="color:var(--text-secondary); margin-bottom:1.5rem;">Hãy lưu những bài viết bạn thích để xem lại sau!</p>
            <a href="{{ route('posts.index') }}" class="btn btn-primary-custom px-4">
                <i class="fas fa-compass me-2"></i>Khám phá bài viết
            </a>
        </div>
        @endif
    </div>
</section>

@endsection