<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login – Guessing Game</title>
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
            max-width: 420px;
            backdrop-filter: blur(12px);
            box-shadow: 0 0 40px rgba(0, 150, 255, 0.15);
        }

        h1 {
            color: #64c8ff;
            font-size: 2rem;
            text-align: center;
            margin-bottom: 6px;
            text-shadow: 0 0 12px rgba(100, 200, 255, 0.5);
        }

        .subtitle {
            color: rgba(255,255,255,0.5);
            text-align: center;
            font-size: 0.95rem;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px 16px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(100, 200, 255, 0.25);
            border-radius: 10px;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #64c8ff;
            box-shadow: 0 0 8px rgba(100, 200, 255, 0.3);
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            color: rgba(255,255,255,0.6);
            font-size: 0.9rem;
        }

        .remember-row input[type="checkbox"] {
            width: 16px;
            height: 16px;
            accent-color: #64c8ff;
            cursor: pointer;
        }

        .error {
            color: #ff6b6b;
            font-size: 0.82rem;
            margin-top: 4px;
        }

        .btn {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, #0f3460, #1a5276);
            border: 1px solid rgba(100, 200, 255, 0.4);
            border-radius: 10px;
            color: #64c8ff;
            font-family: 'Fredoka', sans-serif;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn:hover {
            background: linear-gradient(135deg, #1a5276, #0f3460);
            box-shadow: 0 0 16px rgba(100, 200, 255, 0.35);
        }

        .alt-link {
            text-align: center;
            margin-top: 20px;
            color: rgba(255,255,255,0.5);
            font-size: 0.92rem;
        }

        .alt-link a {
            color: #64c8ff;
            text-decoration: none;
        }

        .alt-link a:hover { text-decoration: underline; }

        .divider {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 20px 0;
            color: rgba(255,255,255,0.3);
            font-size: 0.85rem;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: rgba(255,255,255,0.15);
        }

        .btn-guest {
            width: 100%;
            padding: 13px;
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 10px;
            color: rgba(255,255,255,0.55);
            font-family: 'Fredoka', sans-serif;
            font-size: 1rem;
            font-weight: 400;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-guest:hover {
            border-color: rgba(255,255,255,0.4);
            color: rgba(255,255,255,0.8);
            background: rgba(255,255,255,0.05);
        }

        .alert-success {
            background: rgba(0, 200, 100, 0.15);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #4ecca3;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Welcome Back, Justin</h1>
        <p class="subtitle">Log in to play the Guessing with Justin</p>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.login') }}">
            @csrf

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" placeholder="Your username" required autofocus autocomplete="username">
                @error('username') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Your password" required>
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember" style="margin-bottom:0;">Remember me</label>
            </div>

            <button type="submit" class="btn">Log In</button>
        </form>

        <div class="alt-link">
            Don't have an account? <a href="{{ route('auth.register') }}">Register</a>
        </div>

        <div class="divider">or</div>

        <form method="POST" action="{{ route('auth.guest') }}">
            @csrf
            <button type="submit" class="btn-guest">Continue as Guest</button>
        </form>
    </div>
</body>
</html>
