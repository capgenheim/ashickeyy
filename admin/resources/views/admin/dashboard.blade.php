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
    <div class="stat-card"><div class="stat-value">{{ number_format($stats['totalViews']) }}</div><div class="stat-label">Total Views</div></div>
</div>

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
