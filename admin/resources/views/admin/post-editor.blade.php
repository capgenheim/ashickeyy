@extends('admin.layout')
@section('title', $post ? 'Edit Post' : 'New Post')
@section('content')
<div class="page-header">
    <h1 class="page-title">{{ $post ? 'Edit Post' : 'New Post' }}</h1>
    <a href="{{ route('admin.posts') }}" class="btn" style="background:var(--surface-hover)">← Back</a>
</div>

<div class="card">
    <form method="POST" action="{{ $post ? route('admin.posts.update', $post->_id) : route('admin.posts.store') }}">
        @csrf
        @if($post) @method('PUT') @endif

        <div class="form-group">
            <label class="form-label">Title</label>
            <input type="text" name="title" value="{{ old('title', $post?->title) }}" class="form-input" required maxlength="200">
        </div>

        <div class="form-group">
            <label class="form-label">Excerpt</label>
            <input type="text" name="excerpt" value="{{ old('excerpt', $post?->excerpt) }}" class="form-input" required maxlength="500">
        </div>

        <div class="form-group">
            <label class="form-label">Content (Markdown)</label>
            <textarea name="content" class="form-textarea" style="min-height:400px;font-family:monospace;" required>{{ old('content', $post?->content) }}</textarea>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">

            <div class="form-group">
                <label class="form-label">Status</label>
                <select name="status" class="form-select" required onchange="toggleSchedule(this.value)">
                    <option value="draft" {{ old('status', $post?->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="published" {{ old('status', $post?->status) === 'published' ? 'selected' : '' }}>Published</option>
                </select>
            </div>
            <div class="form-group" id="scheduleGroup" style="display: {{ old('status', $post?->status) === 'published' ? 'block' : 'none' }};">
                <label class="form-label">Publish Date (Leave empty for immediate)</label>
                <input type="datetime-local" name="publishedAt" value="{{ old('publishedAt', $post?->publishedAt ? \Carbon\Carbon::parse($post->publishedAt)->format('Y-m-d\TH:i') : '') }}" class="form-input">
                <span style="font-size: 11px; color: var(--text-muted);">Future dates will schedule the post to appear later.</span>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Tags (Type and press Enter, or enter an existing Tag ID)</label>
             <input type="text" name="tags" class="form-input" id="tags-input" value="{{ implode(',', \App\Models\Tag::whereIn('_id', old('tags', $post?->tags ?? []))->pluck('name')->toArray()) }}">
        </div>

        <div class="form-group">
            <label class="form-label">Cover Image URL (optional)</label>
            <input type="text" name="coverImage" value="{{ old('coverImage', $post?->coverImage) }}" class="form-input" placeholder="https://...">
        </div>

        <button type="submit" class="btn btn-primary">{{ $post ? 'Update Post' : 'Create Post' }}</button>
    </form>
</div>

<!-- EasyMDE Integration -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.css">
<script src="https://cdn.jsdelivr.net/npm/easymde/dist/easymde.min.js"></script>
    <!-- Tagify CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
    <style>
        .tagify {
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }
        .tagify__input {
            color: var(--text);
        }
        .tagify__tag > div::before {
            background: var(--surface-hover);
        }
        .tagify__tag-text {
            color: var(--text) !important;
        }
        .tagify__tag__removeBtn {
            color: var(--text);
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Tagify Initialization
        var input = document.querySelector('#tags-input');
        if (input) {
            new Tagify(input, {
                originalInputValueFormat: valuesArr => valuesArr.map(item => item.value).join(',')
            });
        }

        // EasyMDE Initialization
        new EasyMDE({ 
            element: document.querySelector('.form-textarea'),
            spellChecker: false,
            autosave: {
                enabled: true,
                uniqueId: "editor-{{ $post ? (string)$post->_id : 'new' }}",
                delay: 3000,
            },
            status: ["autosave", "lines", "words", "cursor"],
            uploadImage: true,
            imageUploadFunction: function(file, onSuccess, onError) {
                var formData = new FormData();
                formData.append('file', file);
                
                fetch("{{ route('admin.media.upload') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) throw new Error('Upload failed');
                    return response.json();
                })
                .then(data => {
                    if(data.url) onSuccess(data.url);
                    else onError('Invalid response format');
                })
                .catch(err => {
                    onError(err.message);
                });
            }
        });
    });

    function toggleSchedule(status) {
        document.getElementById('scheduleGroup').style.display = status === 'published' ? 'block' : 'none';
    }
</script>
@endsection
