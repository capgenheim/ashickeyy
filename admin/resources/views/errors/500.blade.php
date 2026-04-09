<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 | Internal Server Error</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Outfit', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
            color: #f8fafc;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            text-align: center;
        }
        .container {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 24px;
            padding: 4rem;
            max-width: 600px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            animation: float 6s ease-in-out infinite;
        }
        h1 {
            font-size: 6rem;
            font-weight: 700;
            background: linear-gradient(to right, #38bdf8, #818cf8);
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
            background: linear-gradient(to right, #6366f1, #8b5cf6);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 9999px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3);
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(99, 102, 241, 0.4);
        }
        .glow {
            position: absolute;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, rgba(0,0,0,0) 70%);
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: -1;
            filter: blur(50px);
        }
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-15px); }
            100% { transform: translateY(0px); }
        }
    </style>
</head>
<body>
    <div class="glow"></div>
    <div class="container">
        <h1>500</h1>
        <h2>System Exception</h2>
        <p>We're experiencing an unexpected technical issue. Don't worry, our system has automatically logged the error internally, and our administrative team will address it.</p>
        <a href="/" class="btn">Return to Safety</a>
    </div>
</body>
</html>
