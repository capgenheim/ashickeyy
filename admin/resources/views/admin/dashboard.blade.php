@extends('admin.layout')
@section('title', 'Dashboard')
@section('content')
<div class="page-header">
    <h1 class="page-title">Dashboard</h1>
</div>

<div class="stats-grid">
    <div class="stat-card"><div class="stat-value">{{ $stats['posts'] }}</div><div class="stat-label">Total Posts</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['published'] }}</div><div class="stat-label">Published</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['drafts'] }}</div><div class="stat-label">Drafts</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['categories'] }}</div><div class="stat-label">Categories</div></div>
    <div class="stat-card"><div class="stat-value">{{ $stats['tags'] }}</div><div class="stat-label">Tags</div></div>
</div>

<div class="card" style="margin-top:24px; margin-bottom:24px;">
    <h2 style="font-size:16px;margin-bottom:16px;">Telemetry Overview</h2>
    <canvas id="telemetryChart" height="80"></canvas>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var ctx = document.getElementById('telemetryChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['App Opens', 'Just Views', 'Actual Readers'],
                datasets: [{
                    label: 'Visitor Actions',
                    data: [{{ $stats['app_opens'] }}, {{ $stats['views'] }}, {{ $stats['readers'] }}],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    });
</script>

<div class="card">
    <h2 style="font-size:16px;margin-bottom:16px;">Recent Posts</h2>
    <table>
        <thead><tr><th>Title</th><th>Status</th><th>Date</th></tr></thead>
        <tbody>
            @forelse($recentPosts as $post)
            <tr>
                <td>{{ $post->title }}</td>
                <td><span class="badge badge-{{ $post->status }}">{{ $post->status }}</span></td>
                <td style="color:var(--text-muted)">{{ $post->created_at?->format('M d, Y') }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="color:var(--text-muted)">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
