<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Post on ashickey{}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            background-color: #f6f8fa;
            color: #24292e;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 14px rgba(0,0,0,0.05);
            border: 1px solid #e1e4e8;
        }
        .header {
            background-color: #0f172a;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            color: #ffffff;
            margin: 0;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .content {
            padding: 40px 30px;
        }
        .cover-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 24px;
        }
        h2 {
            font-size: 22px;
            margin-top: 0;
            margin-bottom: 16px;
            color: #111827;
            font-weight: 600;
            line-height: 1.3;
        }
        p {
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
            margin-bottom: 30px;
        }
        .read-button {
            display: inline-block;
            background-color: #3b82f6;
            color: #ffffff;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 8px;
            text-align: center;
        }
        .read-button:hover {
            background-color: #2563eb;
        }
        .footer {
            background-color: #f9fafb;
            padding: 24px 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer p {
            font-size: 13px;
            color: #6b7280;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ashickey{}</h1>
        </div>
        
        <div class="content">
            @if(!empty($coverImage))
                <img src="{{ $coverImage }}" alt="Cover Image" class="cover-image">
            @endif

            <h2>{{ $title }}</h2>
            <p>{{ $excerpt }}</p>
            
            <a href="https://ashickey.space/post/{{ $slug }}" class="read-button">Start reading</a>
        </div>
        
        <div class="footer">
            <p>You received this email because you subscribed to updates on ashickey.space.</p>
            <p>© {{ date('Y') }} ashickey{}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
