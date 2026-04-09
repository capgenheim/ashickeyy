<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 | Page Not Found</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            text-align: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.02);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 4rem;
            max-width: 600px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: float 5s ease-in-out infinite;
        }
        h1 {
            font-size: 6rem;
            font-weight: 700;
            background: linear-gradient(to right, #fbbf24, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1rem;
            line-height: 1;
        }
        h2 {
            font-size: 2rem;
            font-weight: 500;
            margin-bottom: 1.5rem;
        }
        p {
            color: #94a3b8;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        .btn {
            display: inline-block;
            background: linear-gradient(to right, #eab308, #d97706);
            color: #fff;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(234, 179, 8, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(234, 179, 8, 0.4);
        }
        .glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(245,158,11,0.1) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            filter: blur(50px);
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <h1>404</h1>
        <h2>Lost in Space</h2>
        <p>The cosmic page you are attempting to locate has drifted out of bounds or no longer exists. Let's get you back on track.</p>
        <a href="/" class="btn">Return to Dashboard</a>
    </div>
</body>
</html>
