<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - Guessing Game</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Varela+Round&display=swap');

        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #0a1428 0%, #1a2a4e 25%, #0f3460 50%, #16213e 75%, #0d1b2a 100%);
            background-size: 400% 400%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: bgShift 8s ease infinite;
        }

        @keyframes bgShift {
            0%   { background-position: 0% 50%; }
            50%  { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(100, 200, 255, 0.2);
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%;
            max-width: 520px;
            backdrop-filter: blur(12px);
            box-shadow: 0 0 40px rgba(0, 150, 255, 0.15);
        }

        h1 {
            color: #64c8ff;
            font-size: 1.9rem;
            text-align: center;
            margin-bottom: 8px;
            text-shadow: 0 0 12px rgba(100, 200, 255, 0.5);
        }

        .subtitle {
            color: rgba(255,255,255,0.62);
            text-align: center;
            font-size: 0.95rem;
            margin-bottom: 24px;
            line-height: 1.5;
        }

        .alert-success {
            background: rgba(0, 200, 100, 0.15);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #4ecca3;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .alert-error {
            background: rgba(255, 80, 80, 0.15);
            border: 1px solid rgba(255, 100, 100, 0.35);
            color: #ff8f8f;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #0f3460, #1a5276);
            border: 1px solid rgba(100, 200, 255, 0.4);
            border-radius: 10px;
            color: #64c8ff;
            font-family: 'Fredoka', sans-serif;
            font-size: 1.05rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1a5276, #0f3460);
            box-shadow: 0 0 16px rgba(100, 200, 255, 0.35);
        }

        .actions {
            display: grid;
            gap: 12px;
            margin-top: 14px;
        }

        .logout-form button {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.2);
            background: transparent;
            color: rgba(255,255,255,0.7);
            cursor: pointer;
            font-family: 'Fredoka', sans-serif;
            font-size: 0.95rem;
        }

        .logout-form button:hover {
            background: rgba(255,255,255,0.06);
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Verify Your Email</h1>
        <p class="subtitle">
            We sent a verification link to your email address. Click that link to activate your account.
            If you did not receive it, request another email below.
        </p>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert-error">{{ session('error') }}</div>
        @endif

        <div class="actions">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn">Resend Verification Email</button>
            </form>

            <a href="{{ route('auth.password.edit') }}" class="btn" style="text-decoration:none;display:block;text-align:center;">Change Password</a>

            <form method="POST" action="{{ route('auth.logout') }}" class="logout-form">
                @csrf
                <button type="submit">Log Out</button>
            </form>
        </div>
    </div>
</body>
</html>
