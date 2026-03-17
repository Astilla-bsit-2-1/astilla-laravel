<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Guessing Game</title>
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
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(100, 200, 255, 0.2);
            border-radius: 20px;
            padding: 40px 36px;
            width: 100%;
            max-width: 460px;
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

        .form-group {
            margin-bottom: 16px;
        }

        label {
            display: block;
            color: rgba(255,255,255,0.75);
            font-size: 0.9rem;
            margin-bottom: 6px;
        }

        input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(100, 200, 255, 0.25);
            border-radius: 10px;
            color: #fff;
            font-family: 'Fredoka', sans-serif;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.2s;
        }

        input[type="password"]:focus {
            border-color: #64c8ff;
            box-shadow: 0 0 8px rgba(100, 200, 255, 0.3);
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

        .alert-success {
            background: rgba(0, 200, 100, 0.15);
            border: 1px solid rgba(0, 200, 100, 0.3);
            color: #4ecca3;
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 12px;
            font-size: 0.9rem;
        }

        .error {
            color: #ff8f8f;
            font-size: 0.85rem;
            margin-top: 5px;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 16px;
            color: #64c8ff;
            text-decoration: none;
            font-size: 0.92rem;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="card">
        <h1>Change Password</h1>
        <p class="subtitle">Update your account password securely.</p>

        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('auth.password.update') }}">
            @csrf

            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required autocomplete="current-password">
                @error('current_password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required autocomplete="new-password">
                @error('password') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm New Password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit" class="btn">Update Password</button>
        </form>

        <a href="{{ route('guessing-game.select') }}" class="back-link">Exit to Menu</a>
    </div>
</body>
</html>
