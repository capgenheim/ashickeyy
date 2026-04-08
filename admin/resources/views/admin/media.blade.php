@extends('admin.layout')
@section('title', 'Media')
@section('content')
<div class="page-header"><h1 class="page-title">Media</h1></div>

<div class="card">
    <form method="POST" action="{{ route('admin.media.upload') }}" enctype="multipart/form-data" style="display:flex;gap:12px;margin-bottom:20px;">
        @csrf
        <input type="file" name="file" accept="image/*" class="form-input" required style="flex:1;">
        <button class="btn btn-primary">Upload</button>
    </form>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:12px;">
        @forelse($media as $item)
        <div style="background:var(--bg);border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;">
            <img src="{{ $item->url }}" alt="{{ $item->originalName }}" style="width:100%;height:120px;object-fit:cover;">
            <div style="padding:8px;font-size:11px;color:var(--text-muted);overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $item->originalName }}</div>
        </div>
        @empty
        <p style="color:var(--text-muted);grid-column:1/-1;">No media uploaded yet.</p>
        @endforelse
    </div>
</div>
@endsection
