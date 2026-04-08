<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — ashickey admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Inter', sans-serif; background: #0f1117; color: #e4e4e7; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-card { background: #1a1d27; border: 1px solid #2a2d3a; border-radius: 16px; padding: 40px; width: 100%; max-width: 400px; }
        .login-logo { font-size: 28px; font-weight: 700; color: #007AFF; text-align: center; margin-bottom: 8px; }
        .login-subtitle { text-align: center; color: #9ca3af; font-size: 14px; margin-bottom: 32px; }
        .form-group { margin-bottom: 18px; }
        .form-label { display: block; font-size: 13px; font-weight: 600; color: #9ca3af; margin-bottom: 6px; }
        .form-input { width: 100%; padding: 12px 16px; background: #0f1117; border: 1px solid #2a2d3a; border-radius: 10px; color: #e4e4e7; font-size: 14px; font-family: inherit; }
        .form-input:focus { outline: none; border-color: #007AFF; }
        .btn { width: 100%; padding: 12px; background: #007AFF; color: #fff; border: none; border-radius: 10px; font-size: 15px; font-weight: 600; cursor: pointer; font-family: inherit; transition: background .15s; }
        .btn:hover { background: #005BBB; }
        .alert-error { background: rgba(239,68,68,.1); color: #ef4444; border: 1px solid rgba(239,68,68,.2); padding: 10px 14px; border-radius: 10px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-logo">✦ ashickey</div>
        <div class="login-subtitle">Admin Panel</div>

        @if($errors->any())
            <div class="alert-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('admin.login') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" class="form-input" required autofocus>
            </div>
            <div class="form-group">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-input" required>
            </div>
            <button type="submit" class="btn">Sign In</button>
        </form>
    </div>
</body>
</html>
