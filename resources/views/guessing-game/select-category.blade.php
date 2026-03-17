<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game | Select Category</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

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
            position: relative;
            overflow-y: auto;
            overflow-x: hidden;
            animation: rainbowShift 8s ease infinite;
        }

        @keyframes rainbowShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image:
                radial-gradient(circle at 15% 20%, rgba(100, 200, 255, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 85% 80%, rgba(0, 150, 255, 0.1) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(65, 180, 200, 0.08) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }

        body::after {
            content: '⚡ 🎮 ⚡';
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2.5em;
            opacity: 0.4;
            pointer-events: none;
            z-index: 2;
        }

        .container {
            background: linear-gradient(135deg, rgba(20, 35, 50, 0.95) 0%, rgba(15, 30, 55, 0.98) 100%);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px;
            max-width: 500px;
            width: 100%;
            animation: slideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 4px solid #00bfff;
            box-shadow:
                0 8px 32px rgba(0, 191, 255, 0.2),
                0 0 0 8px rgba(0, 150, 255, 0.15),
                inset 0 0 20px rgba(100, 200, 255, 0.08);
            position: relative;
            z-index: 3;
        }

        .container::before {
            content: '⚙️';
            position: absolute;
            top: -24px;
            right: 34px;
            font-size: 3em;
            animation: spinSlow 4s linear infinite;
        }

        @keyframes spinSlow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.8) rotateX(-10deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
            }
        }

        h1 {
            text-align: center;
            background: linear-gradient(135deg, #00bfff 0%, #00d4ff 50%, #00ffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            font-size: 3em;
            font-weight: 700;
            letter-spacing: 1px;
            font-family: 'Fredoka', sans-serif;
            animation: smile 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes smile {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.05); }
        }

        .subtitle {
            text-align: center;
            color: #c8e8ff;
            margin-bottom: 30px;
            font-size: 1.1em;
            font-weight: 500;
        }

        .categories {
            display: grid;
            grid-template-columns: 1fr;
            gap: 15px;
        }

        .category-btn {
            display: block;
            width: 100%;
            padding: 22px;
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.3) 0%, rgba(100, 200, 255, 0.3) 100%);
            color: #00d4ff;
            border: 3px solid #00bfff;
            border-radius: 20px;
            font-size: 1.2em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: capitalize;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2), inset 0 0 10px rgba(0, 191, 255, 0.1);
            font-family: 'Fredoka', sans-serif;
            position: relative;
            overflow: hidden;
        }

        .category-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: rgba(0, 191, 255, 0.15);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .category-btn:hover {
            transform: translateY(-6px) scale(1.05);
            box-shadow: 0 10px 30px rgba(0, 191, 255, 0.45), inset 0 0 15px rgba(0, 191, 255, 0.2);
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.5) 0%, rgba(100, 200, 255, 0.5) 100%);
        }

        .category-btn:hover::before {
            left: 0;
        }

        .category-btn:active {
            transform: translateY(-2px) scale(1.02);
        }

        .home-link {
            text-align: center;
            margin-top: 35px;
        }

        .home-link a {
            color: #00d4ff;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 1.05em;
            display: inline-block;
            font-family: 'Fredoka', sans-serif;
            padding: 8px 16px;
            border: 2px solid #00bfff;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.12) 0%, rgba(100, 200, 255, 0.12) 100%);
        }

        .home-link a:hover {
            color: #001b2a;
            background: linear-gradient(135deg, #00bfff 0%, #00d4ff 100%);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.35);
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2.2em;
            }

            .category-btn {
                font-size: 1.1em;
                padding: 18px;
            }
        }
    </style>
</head>
<body>
    <style>
        .user-nav{position:fixed;top:16px;right:20px;display:flex;align-items:center;gap:12px;z-index:999;background:rgba(0,0,0,0.4);border:1px solid rgba(100,200,255,0.25);border-radius:30px;padding:6px 18px;color:rgba(255,255,255,0.85);font-size:0.9rem;backdrop-filter:blur(8px);}
        .user-nav a{color:#64c8ff;text-decoration:none;font-weight:600;}
        .user-nav a:hover{text-decoration:underline;}
        .logout-btn{background:none;border:1px solid rgba(255,120,120,0.5);border-radius:20px;padding:3px 14px;color:#ff9090;font-family:'Fredoka',sans-serif;font-size:0.88rem;cursor:pointer;transition:all 0.2s;}
        .logout-btn:hover{background:rgba(255,100,100,0.15);}
    </style>
    <nav class="user-nav">
        @auth
            <span>👤 {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">@csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        @elseif(session('guest_mode'))
            <span>🎮 Guest Mode</span>
            <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">@csrf
                <button type="submit" class="logout-btn">Exit</button>
            </form>
        @else
            <a href="{{ route('auth.login') }}">Log In</a>
            <a href="{{ route('auth.register') }}">Register</a>
        @endauth
    </nav>
    <div class="container">
        <h1>Guess with Justin</h1>
        <p class="subtitle">Select a category to begin</p>

        <div class="categories">
            @foreach ($categories as $category)
                <form action="{{ route('guessing-game.start', $category) }}" method="POST" style="width: 100%;">
                    @csrf
                    <input type="hidden" name="difficulty" value="{{ $difficulty ?? 'easy' }}">
                    <button type="submit" class="category-btn">
                        @switch($category)
                            @case('animals')
                                🐾 {{ ucfirst(str_replace('_', ' ', $category)) }}
                            @break
                            @case('countries')
                                🌍 {{ ucfirst(str_replace('_', ' ', $category)) }}
                            @break
                            @case('programming_languages')
                                💻 {{ ucfirst(str_replace('_', ' ', $category)) }}
                            @break
                            @default
                                {{ ucfirst(str_replace('_', ' ', $category)) }}
                        @endswitch
                    </button>
                </form>
            @endforeach
        </div>

        <div class="home-link">
            <a href="{{ route('home') }}">← Back to Home</a>
        </div>
    </div>
</body>
</html>

