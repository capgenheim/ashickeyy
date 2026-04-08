@extends('admin.layout')
@section('title', 'Tags')
@section('content')
<div class="page-header"><h1 class="page-title">Tags</h1></div>

<div class="card">
    <form method="POST" action="{{ route('admin.tags.store') }}" style="display:flex;gap:12px;margin-bottom:20px;">
        @csrf
        <input type="text" name="name" placeholder="Tag name" class="form-input" required style="flex:1;">
        <button class="btn btn-primary">Add</button>
    </form>
    <table>
        <thead><tr><th>Name</th><th>Slug</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($tags as $tag)
            <tr>
                <td><strong>{{ $tag->name }}</strong></td>
                <td style="color:var(--text-muted)">{{ $tag->slug }}</td>
                <td><form method="POST" action="{{ route('admin.tags.delete', $tag->_id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form></td>
            </tr>
            @empty
            <tr><td colspan="3" style="color:var(--text-muted)">No tags.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
