@extends('admin.layout')
@section('title', 'Categories')
@section('content')
<div class="page-header"><h1 class="page-title">Categories</h1></div>

<div class="card">
    <form method="POST" action="{{ route('admin.categories.store') }}" style="display:flex;gap:12px;margin-bottom:20px;">
        @csrf
        <input type="text" name="name" placeholder="Category name" class="form-input" required style="flex:1;">
        <input type="text" name="description" placeholder="Description (optional)" class="form-input" style="flex:2;">
        <button class="btn btn-primary">Add</button>
    </form>
    <table>
        <thead><tr><th>Name</th><th>Slug</th><th>Description</th><th>Actions</th></tr></thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td><strong>{{ $cat->name }}</strong></td>
                <td style="color:var(--text-muted)">{{ $cat->slug }}</td>
                <td style="color:var(--text-muted)">{{ $cat->description ?? '' }}</td>
                <td><form method="POST" action="{{ route('admin.categories.delete', $cat->_id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="btn btn-danger btn-sm">Delete</button></form></td>
            </tr>
            @empty
            <tr><td colspan="4" style="color:var(--text-muted)">No categories.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
