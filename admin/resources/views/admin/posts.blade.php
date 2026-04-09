@extends('admin.layout')
@section('title', 'Posts')
@section('content')
<div class="page-header">
    <h1 class="page-title">Posts</h1>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-primary">+ New Post</a>
</div>

<div class="card" style="padding:0;overflow:hidden;">
    <table>
        <thead><tr><th>Title</th><th>Status</th><th>Views</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($posts as $post)
            <tr>
                <td><strong>{{ $post->title }}</strong></td>
                <td><span class="badge badge-{{ $post->status }}">{{ $post->status }}</span></td>
                <td style="color:var(--text-muted)">{{ $post->views ?? 0 }}</td>
                <td style="color:var(--text-muted)">{{ $post->created_at?->format('M d, Y') }}</td>
                <td>
                    <div class="actions">
                        <a href="{{ route('admin.posts.edit', $post->_id) }}" class="btn btn-primary btn-sm">Edit</a>
                        <form method="POST" action="{{ route('admin.posts.delete', $post->_id) }}" onsubmit="return confirm('Delete this post?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" style="color:var(--text-muted)">No posts yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
