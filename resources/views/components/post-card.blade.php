{{-- Khối bao ngoài của một thẻ bài viết (Card):
     - col-lg-4 col-md-6: Responsive chia lưới Bootstrap (Màn hình máy tính hiển thị 3 card trên 1 hàng, máy tính bảng hiển thị 2 card).
     - animate-fade-in-up: Kích hoạt hiệu ứng hoạt họa mờ và trượt nhẹ từ dưới lên khi tải trang. --}}
<div class="col-lg-4 col-md-6 animate-fade-in-up">
    {{-- h-100: Đảm bảo các card trên cùng một hàng luôn có chiều cao bằng nhau, không bị lệch do tiêu đề dài ngắn --}}
    <div class="card-glass h-100">
        
        {{-- PHẦN 1: KHỐI HÌNH ẢNH VÀ CÁC BADGE ĐÈ LÊN ẢNH --}}
        <div class="card-img-wrapper">
            <img src="{{ $post->image_url }}" class="card-img-top" alt="{{ $post->title }}">
            {{-- Lớp phủ gradient mờ giúp phần text hoặc badge đè lên ảnh dễ nhìn hơn --}}
            <div class="card-img-overlay-gradient"></div>
            
            {{-- Badge hiển thị tên Danh mục (Nằm góc trên bên trái - top-0 start-0) 
                 Sử dụng liên kết Eloquent Relationship từ bài viết sang bảng danh mục ($post->category->name) --}}
            <div class="position-absolute top-0 start-0 p-3 z-3">
                <span class="badge-category bg-white shadow-sm">{{ $post->category->name }}</span>
            </div>
            
            {{-- LÀM MỀM GIAO DIỆN (Logic hiển thị lượt xem):
                 Kiểm tra xem biến cấu hình $showViews có tồn tại và bằng true không, nếu thỏa mãn mới in ra số lượt xem 
                 number_format(): Hàm định dạng số của PHP (Ví dụ: biến 1500 thành 1,500) --}}
            @if(isset($showViews) && $showViews)
            <div class="position-absolute top-0 end-0 p-3 z-3">
                <span class="badge bg-warning text-dark fw-bold shadow-sm">
                    <i class="fas fa-eye me-1"></i>{{ number_format($post->views_count) }}
                </span>
            </div>
            @endif
        </div>
        
        {{-- PHẦN 2: NỘI DUNG CHÍNH CỦA THẺ BÀI VIẾT (CARD BODY) --}}
        {{-- d-flex flex-column: Biến body thành flexbox hướng dọc để đẩy phần footer (meta-info) luôn dính chặt dưới đáy card --}}
        <div class="card-body d-flex flex-column">
            
            {{-- Tiêu đề bài viết: Nhấn vào sẽ trỏ tới route chi tiết dựa theo đường dẫn thân thiện (slug) --}}
            <h5 class="card-title">
                <a href="{{ route('posts.show', $post->slug) }}">{{ $post->title }}</a>
            </h5>
            
            {{-- Đoạn trích dẫn mô tả ngắn (Excerpt):
                 - flex-grow-1: Tự động giãn cách độ cao để lấp đầy khoảng trống thừa.
                 - strip_tags(): Hàm xóa bỏ hoàn toàn các thẻ HTML (như <strong>, <p>, <img>) nếu người dùng viết bài bằng trình soạn thảo tin nhắn Rich Text, tránh làm vỡ giao diện.
                 - Str::limit(..., 100): Helper của Laravel giúp cắt ngắn chuỗi văn bản, chỉ lấy đúng 100 ký tự đầu và tự nối thêm dấu "..." ở cuối. --}}
            <p class="card-text flex-grow-1">{{ Str::limit($post->excerpt ?? strip_tags($post->content), 100) }}</p>
            
            {{-- PHẦN 3: THANH THÔNG TIN PHỤ VÀ ĐÁNH GIÁ (META INFO & RATING) --}}
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="meta-info">
                    {{-- Lấy tên tác giả viết bài thông qua mối quan hệ BelongsTo ($post->user->name) --}}
                    <span><i class="fas fa-user me-1"></i> {{ $post->user->name }}</span>
                    
                    {{-- Logic hiển thị ngày đăng bài: Nếu biến cấu hình $showDate cho phép thì mới render ra 
                         format('d/m/Y'): Định dạng thời gian theo chuẩn Việt Nam (Ngày/Tháng/Năm) --}}
                    @if(isset($showDate) && $showDate)
                    <span><i class="fas fa-calendar me-1"></i> {{ $post->created_at->format('d/m/Y') }}</span>
                    @endif
                </div>
                
                {{-- KHỐI ĐÁNH GIÁ SAO (Rating System):
                     Chạy vòng lặp từ 1 đến 5 đại diện cho 5 ngôi sao.
                     Toán tử điều kiện (Ternary Operator): Nếu biến chạy $i nhỏ hơn hoặc bằng điểm đánh giá trung bình 
                     ($post->average_rating), hệ thống sẽ in ra ngôi sao vàng rực, ngược lại sẽ in ngôi sao có class '.empty' (sao rỗng màu xám) --}}
                <div class="stars">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star{{ $i <= $post->average_rating ? '' : ' empty' }}" style="font-size: 0.8rem;"></i>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>