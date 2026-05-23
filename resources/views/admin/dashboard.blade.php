{{-- Kế thừa cấu trúc giao diện layout chung của trang quản trị (Admin) --}}
@extends('layouts.admin')

{{-- Khai báo tiêu đề cho trang tổng quan hệ thống --}}
@section('title', 'Dashboard')

{{-- Bắt đầu định nghĩa vùng nội dung chính --}}
@section('content')

<div class="admin-header">
    <div>
        <h2><i class="fas fa-tachometer-alt me-2" style="color:var(--gold);font-size:1.3rem;"></i>Dashboard</h2>
        <p style="color:var(--slate);font-size:0.85rem;margin:0;">Tổng quan hệ thống</p>
    </div>
    {{-- Hiển thị ngày tháng năm hiện tại ngoài màn hình nhờ hàm helper now() của Laravel --}}
    <div style="background:#fff;border:1px solid var(--glass-border);border-radius:12px;padding:0.5rem 1.25rem;font-size:0.85rem;color:var(--slate);box-shadow:0 2px 8px rgba(15,23,42,0.06);">
        <i class="fas fa-calendar me-2" style="color:var(--gold);"></i>{{ now()->format('d/m/Y') }}
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Thống kê Tổng số bài viết --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(212,163,115,0.15);color:var(--gold);">
                    <i class="fas fa-newspaper"></i>
                </div>
                <div>
                    {{-- In ra giá trị tương ứng từ mảng kết quả $stats được truyền từ DashboardController --}}
                    <div class="stat-value">{{ $stats['total_posts'] }}</div>
                    <div class="stat-label">Bài viết</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Thống kê số bài viết Đã xuất bản --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(26,58,42,0.12);color:var(--forest);">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['published_posts'] }}</div>
                    <div class="stat-label">Đã đăng</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Thống kê Tổng số tài khoản người dùng --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#7c3aed;">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Người dùng</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Thống kê Tổng số lượng bình luận --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#2563eb;">
                    <i class="fas fa-comments"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['total_comments'] }}</div>
                    <div class="stat-label">Bình luận</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Thống kê Tổng số lượt xem (Views) bài viết (Có định dạng ngăn cách hàng nghìn) --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(212,163,115,0.12);color:var(--gold-dark);">
                    <i class="fas fa-eye"></i>
                </div>
                <div>
                    <div class="stat-value" style="font-size:1.4rem;">{{ number_format($stats['total_views']) }}</div>
                    <div class="stat-label">Lượt xem</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Thống kê Số lượng bình luận chưa phê duyệt (Chờ duyệt) --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(239,68,68,0.12);color:#dc2626;">
                    <i class="fas fa-clock"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $stats['pending_comments'] }}</div>
                    <div class="stat-label">Chờ duyệt</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Đồ thị dạng Cột (Bar Chart): Thống kê tần suất bài viết theo từng tháng --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin-bottom:1.25rem;font-size:1rem;">
                <i class="fas fa-chart-bar me-2" style="color:var(--gold);"></i>Bài viết theo tháng
            </h5>
            {{-- Thẻ canvas làm khung nền để thư viện Chart.js vẽ đồ thị cột lên --}}
            <canvas id="postsChart" height="250"></canvas>
        </div>
    </div>
    
    {{-- Đồ thị dạng Bánh (Doughnut Chart): Biểu diễn tỉ lệ lượt xem phân bổ theo danh mục --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin-bottom:1.25rem;font-size:1rem;">
                <i class="fas fa-chart-pie me-2" style="color:var(--gold);"></i>Lượt xem theo danh mục
            </h5>
            {{-- Thẻ canvas làm khung nền để thư viện Chart.js vẽ đồ thị bánh lên --}}
            <canvas id="viewsChart" height="250"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Danh sách bài viết mới đăng ký gần đây --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin:0;font-size:1rem;">
                    <i class="fas fa-newspaper me-2" style="color:var(--gold);"></i>Bài viết gần đây
                </h5>
                <a href="{{ route('admin.posts.index') }}" class="btn btn-primary-custom btn-sm" style="font-size:0.78rem;padding:0.35rem 1rem;">Xem tất cả</a>
            </div>
            @foreach($recentPosts as $post)
            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid rgba(212,163,115,0.1);">
                <div>
                    <div style="font-weight:600;font-size:0.88rem;color:var(--navy);">{{ Str::limit($post->title, 40) }}</div>
                    {{-- diffForHumans(): Hàm hiển thị thời gian đăng bài dưới dạng khoảng cách tương đối tiếng Việt (Ví dụ: 5 phút trước, 2 ngày trước) --}}
                    <small style="color:var(--slate);">{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</small>
                </div>
                <span class="badge-status {{ $post->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                    {{ $post->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    
    {{-- Danh sách các bình luận mới gửi lên hệ thống --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin:0;font-size:1rem;">
                    <i class="fas fa-comments me-2" style="color:var(--gold);"></i>Bình luận gần đây
                </h5>
                <a href="{{ route('admin.comments.index') }}" class="btn btn-primary-custom btn-sm" style="font-size:0.78rem;padding:0.35rem 1rem;">Xem tất cả</a>
            </div>
            @foreach($recentComments as $comment)
            <div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid rgba(212,163,115,0.1);">
                <div>
                    <div style="font-size:0.88rem;color:var(--navy);">{{ Str::limit($comment->content, 50) }}</div>
                    {{-- Hiển thị tên người viết bình luận và tiêu đề bài viết (dùng toán tử loại trừ lỗi ?? nếu bài viết gốc bị xóa) --}}
                    <small style="color:var(--slate);">{{ $comment->user->name }} · {{ $comment->post->title ?? 'N/A' }}</small>
                </div>
                <span class="badge-status {{ $comment->is_approved ? 'badge-published' : 'badge-draft' }}">
                    {{ $comment->is_approved ? 'Đã duyệt' : 'Chờ duyệt' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Đẩy mã kịch bản Javascript vào vùng chứa @stack('scripts') được định nghĩa ở file layout gốc --}}
@push('scripts')
{{-- Nạp thư viện vẽ biểu đồ Chart.js thông qua đường dẫn CDN công khai --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// BIỂU ĐỒ BÀI VIẾT THEO THÁNG (BAR CHART):
// Chuyển mảng dữ liệu tháng và số lượng từ PHP Eloquent Collection thành mảng dữ liệu Javascript dạng JSON hợp lệ
const months = {!! json_encode($postsPerMonth->pluck('month')) !!};
const counts = {!! json_encode($postsPerMonth->pluck('count')) !!};
const monthNames = ['','Th1','Th2','Th3','Th4','Th5','Th6','Th7','Th8','Th9','Th10','Th11','Th12'];

new Chart(document.getElementById('postsChart'), {
    type: 'bar', // Xác định loại đồ thị là dạng Cột
    data: {
        labels: months.map(m => monthNames[m]), // Ánh xạ số tháng thành chuỗi hiển thị tương ứng (Ví dụ: 5 -> Th5)
        datasets: [{
            label: 'Bài viết',
            data: counts, // Đổ mảng số lượng bài viết làm dữ liệu trục Y
            backgroundColor: 'rgba(212,163,115,0.45)', // Màu nền cột
            borderColor: '#D4A373',                     // Màu viền cột
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true, // Tự động co giãn đồ thị vừa vặn với kích thước màn hình thiết bị
        plugins: { legend: { display: false } }, // Ẩn phần ghi chú nhãn phân loại phía trên đồ thị
        scales: {
            y: { beginAtZero: true, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(212,163,115,0.1)' } },
            x: { ticks: { color: '#94a3b8' }, grid: { display: false } }
        }
    }
});

// BIỂU ĐỒ LƯỢT XEM DANH MỤC (DOUGHNUT CHART):
const catNames = {!! json_encode($viewsPerCategory->pluck('name')) !!}; // Lấy danh sách tên danh mục du lịch
const catViews = {!! json_encode($viewsPerCategory->pluck('views')) !!}; // Lấy danh sách tổng số lượt xem tương ứng
const colors = ['#D4A373','#1a3a2a','#0F172A','#b8864e','#334155','#E7D7C9']; // Bảng màu cấu hình cho từng cung bánh

new Chart(document.getElementById('viewsChart'), {
    type: 'doughnut', // Xác định loại đồ thị là hình bánh Khuyên (Doughnut)
    data: {
        labels: catNames,
        datasets: [{
            data: catViews, // Đổ danh sách số lượt xem vào các cung biểu đồ
            backgroundColor: colors.slice(0, catNames.length), // Cắt mảng lấy số lượng màu bằng đúng số danh mục thực tế
            borderWidth: 3,
            borderColor: '#FAF7F2',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom', // Đặt danh sách ghi chú chú thích nằm ở phía dưới biểu đồ bánh
                labels: { color: '#334155', padding: 16, font: { family: 'Inter', size: 12 } }
            }
        }
    }
});
</script>
@endpush
@endsection {{-- Kết thúc định nghĩa nội dung chính --}}