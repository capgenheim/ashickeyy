<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — ashickey</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --blue: #007AFF; --blue-dark: #005BBB; --bg: #0f1117; --surface: #1a1d27;
            --surface-hover: #252833; --border: #2a2d3a; --text: #e4e4e7; --text-muted: #9ca3af;
            --danger: #ef4444; --success: #22c55e; --warning: #f59e0b; --radius: 10px;
        }
        body { font-family: 'Inter', sans-serif; background: var(--bg); color: var(--text); min-height: 100vh; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: var(--surface); border-right: 1px solid var(--border); padding: 24px 16px; position: fixed; top: 0; left: 0; bottom: 0; display: flex; flex-direction: column; }
        .sidebar-logo { font-size: 20px; font-weight: 700; color: var(--blue); margin-bottom: 32px; text-decoration: none; display: block; }
        .sidebar-nav { flex: 1; display: flex; flex-direction: column; gap: 4px; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: var(--radius); color: var(--text-muted); text-decoration: none; font-size: 14px; font-weight: 500; transition: all .15s; }
        .sidebar-link:hover, .sidebar-link.active { background: var(--surface-hover); color: var(--text); }
        .sidebar-link.active { color: var(--blue); }
        .main { margin-left: 240px; flex: 1; padding: 32px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
        .page-title { font-size: 24px; font-weight: 700; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 10px 20px; border-radius: var(--radius); font-size: 14px; font-weight: 600; border: none; cursor: pointer; transition: all .15s; text-decoration: none; }
        .btn-primary { background: var(--blue); color: #fff; }
        .btn-primary:hover { background: var(--blue-dark); }
        .btn-danger { background: var(--danger); color: #fff; }
        .btn-danger:hover { opacity: .85; }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 24px; margin-bottom: 16px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px,1fr)); gap: 16px; margin-bottom: 32px; }
        .stat-card { background: var(--surface); border: 1px solid var(--border); border-radius: var(--radius); padding: 20px; }
        .stat-value { font-size: 28px; font-weight: 700; color: var(--blue); }
        .stat-label { font-size: 13px; color: var(--text-muted); margin-top: 4px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 16px; border-bottom: 1px solid var(--border); font-size: 14px; }
        th { color: var(--text-muted); font-weight: 600; font-size: 12px; text-transform: uppercase; letter-spacing: .5px; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
        .badge-published { background: rgba(34,197,94,.15); color: var(--success); }
        .badge-draft { background: rgba(245,158,11,.15); color: var(--warning); }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: var(--text-muted); margin-bottom: 6px; }
        .form-input, .form-textarea, .form-select { width: 100%; padding: 10px 14px; background: var(--bg); border: 1px solid var(--border); border-radius: var(--radius); color: var(--text); font-size: 14px; font-family: inherit; }
        .form-input:focus, .form-textarea:focus, .form-select:focus { outline: none; border-color: var(--blue); }
        .form-textarea { min-height: 200px; resize: vertical; }
        .alert { padding: 12px 16px; border-radius: var(--radius); margin-bottom: 16px; font-size: 14px; }
        .alert-success { background: rgba(34,197,94,.1); color: var(--success); border: 1px solid rgba(34,197,94,.2); }
        .alert-error { background: rgba(239,68,68,.1); color: var(--danger); border: 1px solid rgba(239,68,68,.2); }
        .actions { display: flex; gap: 8px; }
        .logout-form { margin-top: auto; }
    </style>
</head>
<body>
    <div class="layout">
        <aside class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">✦ ashickey</a>
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">📊 Dashboard</a>
                <a href="{{ route('admin.posts') }}" class="sidebar-link {{ request()->routeIs('admin.posts*') ? 'active' : '' }}">📝 Posts</a>

                <a href="{{ route('admin.tags') }}" class="sidebar-link {{ request()->routeIs('admin.tags*') ? 'active' : '' }}">🏷️ Tags</a>
                <a href="{{ route('admin.media') }}" class="sidebar-link {{ request()->routeIs('admin.media*') ? 'active' : '' }}">🖼️ Media</a>
            </nav>
            <form method="POST" action="{{ route('admin.logout') }}" class="logout-form">
                @csrf
                <button type="submit" class="sidebar-link" style="width:100%;border:none;background:none;cursor:pointer;text-align:left;">🚪 Logout</button>
            </form>
        </aside>
        <main class="main">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            @yield('content')
        </main>
    </div>
</body>
</html>
