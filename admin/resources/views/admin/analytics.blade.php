@extends('admin.layout')

@section('title', 'Analytics Dashboard')

@section('content')
<div class="page-header">
    <h1 class="page-title">Traffic & Analytics</h1>
</div>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value">{{ number_format($stats['total_views']) }}</div>
        <div class="stat-label">Total Page Views</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value" style="color: var(--success);">{{ number_format($stats['unique_visitors']) }}</div>
        <div class="stat-label">Unique Visitors</div>
    </div>
    
    <div class="stat-card">
        <div class="stat-value" style="color: var(--warning);">{{ number_format($stats['mobile_users']) }}</div>
        <div class="stat-label">Mobile Traffic</div>
    </div>

    <div class="stat-card">
        <div class="stat-value" style="color: var(--blue);">{{ number_format($stats['desktop_users']) }}</div>
        <div class="stat-label">Desktop Traffic</div>
    </div>
</div>

<div class="card">
    <h2 style="margin-bottom: 20px; font-size: 18px;">Recent Traffic Logs (With GeoIP)</h2>
    <div style="overflow-x: auto;">
        <table>
            <thead>
                <tr>
                    <th>Time</th>
                    <th>Method & URL</th>
                    <th>Location</th>
                    <th>Device & Browser</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recent as $log)
                <tr>
                    <td>{{ $log->created_at->diffForHumans() }}</td>
                    <td title="{{ $log->url }}">
                        <span class="badge" style="background:var(--surface-hover);">{{ $log->method }}</span>
                        {{ \Illuminate\Support\Str::limit($log->url, 40) }}
                    </td>
                    <td>
                        @if($log->city && $log->country)
                            {{ $log->city }}, {{ $log->country }}
                        @else
                            <span style="color: var(--text-muted);">Local or Unknown</span>
                        @endif
                    </td>
                    <td>{{ $log->platform }} / {{ $log->browser }}</td>
                    <td>
                        <span class="badge {{ $log->response_status >= 400 ? 'badge-draft' : 'badge-published' }}">
                            {{ $log->response_status }} ({{ $log->response_time_ms }}ms)
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
