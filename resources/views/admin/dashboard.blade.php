{{-- Kế thừa layout admin --}}
@extends('layouts.admin')

{{-- Tiêu đề trang --}}
@section('title', 'Dashboard')

{{-- Nội dung chính --}}
@section('content')
<div class="admin-header">
    <div>
        <h2><i class="fas fa-tachometer-alt me-2" style="color:var(--gold);font-size:1.3rem;"></i>Dashboard</h2>
        <p style="color:var(--slate);font-size:0.85rem;margin:0;">Tổng quan hệ thống</p>
    </div>
    {{-- Hiển thị ngày tháng hiện tại --}}
    <div style="background:#fff;border:1px solid var(--glass-border);border-radius:12px;padding:0.5rem 1.25rem;font-size:0.85rem;color:var(--slate);box-shadow:0 2px 8px rgba(15,23,42,0.06);">
        <i class="fas fa-calendar me-2" style="color:var(--gold);"></i>{{ now()->format('d/m/Y') }}
    </div>
</div>

<div class="row g-3 mb-4">
    {{-- Tổng số bài viết --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(212,163,115,0.15);color:var(--gold);"><i class="fas fa-newspaper"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_posts'] }}</div>
                    <div class="stat-label">Bài viết</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bài viết đã đăng --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(26,58,42,0.12);color:var(--forest);"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['published_posts'] }}</div>
                    <div class="stat-label">Đã đăng</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tổng số người dùng --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(139,92,246,0.12);color:#7c3aed;"><i class="fas fa-users"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_users'] }}</div>
                    <div class="stat-label">Người dùng</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tổng số bình luận --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(59,130,246,0.12);color:#2563eb;"><i class="fas fa-comments"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['total_comments'] }}</div>
                    <div class="stat-label">Bình luận</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Tổng lượt xem --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(212,163,115,0.12);color:var(--gold-dark);"><i class="fas fa-eye"></i></div>
                <div>
                    <div class="stat-value" style="font-size:1.4rem;">{{ number_format($stats['total_views']) }}</div>
                    <div class="stat-label">Lượt xem</div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Bình luận chờ duyệt --}}
    <div class="col-lg-2 col-md-4 col-6">
        <div class="stat-card">
            <div class="d-flex align-items-center gap-3">
                <div class="stat-icon" style="background:rgba(239,68,68,0.12);color:#dc2626;"><i class="fas fa-clock"></i></div>
                <div>
                    <div class="stat-value">{{ $stats['pending_comments'] }}</div>
                    <div class="stat-label">Chờ duyệt</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    {{-- Biểu đồ cột --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin-bottom:1.25rem;font-size:1rem;">
                <i class="fas fa-chart-bar me-2" style="color:var(--gold);"></i>Bài viết theo tháng
            </h5>
            <canvas id="postsChart" height="250"></canvas>
        </div>
    </div>
    
    {{-- Biểu đồ tròn --}}
    <div class="col-lg-6">
        <div class="card-glass p-4">
            <h5 style="font-family:'Playfair Display',serif;font-weight:700;color:var(--navy);margin-bottom:1.25rem;font-size:1rem;">
                <i class="fas fa-chart-pie me-2" style="color:var(--gold);"></i>Lượt xem theo danh mục
            </h5>
            <canvas id="viewsChart" height="250"></canvas>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Bài viết gần đây --}}
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
                    <small style="color:var(--slate);">{{ $post->user->name }} · {{ $post->created_at->diffForHumans() }}</small>
                </div>
                <span class="badge-status {{ $post->status === 'published' ? 'badge-published' : 'badge-draft' }}">
                    {{ $post->status === 'published' ? 'Đã đăng' : 'Nháp' }}
                </span>
            </div>
            @endforeach
        </div>
    </div>
    
    {{-- Bình luận gần đây --}}
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const months = {!! json_encode($postsPerMonth->pluck('month')) !!};
const counts = {!! json_encode($postsPerMonth->pluck('count')) !!};
const monthNames = ['','Th1','Th2','Th3','Th4','Th5','Th6','Th7','Th8','Th9','Th10','Th11','Th12'];

new Chart(document.getElementById('postsChart'), {
    type: 'bar',
    data: {
        labels: months.map(m => monthNames[m]),
        datasets: [{
            label: 'Bài viết',
            data: counts,
            backgroundColor: 'rgba(212,163,115,0.45)',
            borderColor: '#D4A373',
            borderWidth: 2,
            borderRadius: 8,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { color: '#94a3b8' }, grid: { color: 'rgba(212,163,115,0.1)' } },
            x: { ticks: { color: '#94a3b8' }, grid: { display: false } }
        }
    }
});

const catNames = {!! json_encode($viewsPerCategory->pluck('name')) !!}; 
const catViews = {!! json_encode($viewsPerCategory->pluck('views')) !!};
const colors = ['#D4A373','#1a3a2a','#0F172A','#b8864e','#334155','#E7D7C9'];

new Chart(document.getElementById('viewsChart'), {
    type: 'doughnut',
    data: {
        labels: catNames,
        datasets: [{
            data: catViews,
            backgroundColor: colors.slice(0, catNames.length),
            borderWidth: 3,
            borderColor: '#FAF7F2',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#334155', padding: 16, font: { family: 'Inter', size: 12 } }
            }
        }
    }
});
</script>
@endpush
@endsection