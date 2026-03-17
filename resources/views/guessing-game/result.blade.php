<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game | Result</title>
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

        .container {
            position: relative;
            z-index: 2;
            background: linear-gradient(135deg, rgba(20, 35, 50, 0.95) 0%, rgba(15, 30, 55, 0.98) 100%);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 45px;
            max-width: 560px;
            width: 100%;
            text-align: center;
            border: 4px solid #00bfff;
            box-shadow:
                0 8px 32px rgba(0, 191, 255, 0.2),
                0 0 0 8px rgba(0, 150, 255, 0.15),
                inset 0 0 20px rgba(100, 200, 255, 0.08);
            animation: slideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(40px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .result-icon {
            font-size: 4.8em;
            margin-bottom: 16px;
            animation: bounce 1s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        h1 {
            background: linear-gradient(135deg, #00bfff 0%, #00d4ff 50%, #00ffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            font-size: 2.8em;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .message {
            color: #d8f4ff;
            font-size: 1.08em;
            margin: 16px 0 24px;
            line-height: 1.7;
            font-weight: 500;
        }

        .answer-reveal {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.2) 0%, rgba(0, 150, 200, 0.2) 100%);
            border: 3px solid #00bfff;
            border-radius: 20px;
            padding: 22px;
            margin: 24px 0;
            box-shadow: inset 0 0 10px rgba(0, 191, 255, 0.1), 0 4px 10px rgba(0, 191, 255, 0.15);
        }

        .answer-reveal p {
            color: #c7ecff;
            font-size: 1em;
            margin-bottom: 10px;
        }

        .answer-reveal strong {
            display: block;
            font-size: 2.1em;
            color: #00ffff;
            margin-top: 10px;
            font-family: 'Varela Round', sans-serif;
            letter-spacing: 2px;
            text-shadow: 2px 2px 0 rgba(0, 191, 255, 0.4);
            word-break: break-word;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 25px 0;
        }

        .guessed-summary {
            margin-top: 20px;
            text-align: left;
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.2) 0%, rgba(0, 150, 200, 0.2) 100%);
            border: 2px solid #00bfff;
            border-radius: 16px;
            padding: 18px;
        }

        .guessed-summary h3 {
            color: #9fe8ff;
            margin-bottom: 8px;
            font-size: 1.05em;
        }

        .guessed-summary p {
            color: #d8f4ff;
            line-height: 1.6;
            word-break: break-word;
        }

        .stat-box {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.25) 0%, rgba(0, 50, 100, 0.25) 100%);
            padding: 20px;
            border-radius: 14px;
            text-align: center;
            color: #00ffff;
            border: 2px solid #0096ff;
            box-shadow:
                inset 0 0 10px rgba(0, 191, 255, 0.15),
                0 0 15px rgba(0, 191, 255, 0.2);
        }

        .stat-box strong {
            display: block;
            font-size: 1.9em;
            margin-bottom: 8px;
            font-weight: 700;
            font-family: 'Varela Round', sans-serif;
        }

        .stat-box span {
            font-size: 0.95em;
            color: #b5eaff;
        }

        .buttons {
            display: grid;
            grid-template-columns: 1fr;
            gap: 12px;
            margin-top: 26px;
        }

        .btn {
            border-radius: 20px;
            padding: 14px;
            font-size: 1em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            border: 3px solid #00bfff;
            transition: all 0.25s ease;
            font-family: 'Fredoka', sans-serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.35) 0%, rgba(100, 200, 255, 0.35) 100%);
            color: #00e5ff;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2), inset 0 0 10px rgba(0, 191, 255, 0.1);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.35), inset 0 0 15px rgba(0, 191, 255, 0.15);
        }

        .btn-secondary {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.3) 0%, rgba(0, 150, 200, 0.3) 100%);
            color: #c7ecff;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.18), inset 0 0 10px rgba(0, 191, 255, 0.08);
        }

        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.3), inset 0 0 15px rgba(0, 191, 255, 0.12);
        }

        .celebration {
            animation: celebrate 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes celebrate {
            0% { transform: translateY(0) rotate(0deg) scale(0.8); opacity: 0; }
            50% { transform: translateY(-30px) rotate(8deg) scale(1.1); }
            100% { transform: translateY(0) rotate(0deg) scale(1); opacity: 1; }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
                border-radius: 24px;
            }

            .result-icon {
                font-size: 4em;
            }

            h1 {
                font-size: 2.2em;
            }

            .answer-reveal strong {
                font-size: 1.7em;
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
        @auth
        @php
            $diffColors = ['easy' => '#44dd88', 'medium' => '#ffcc00', 'hard' => '#ff6655'];
            $diffIcons  = ['easy' => '🟢', 'medium' => '🟡', 'hard' => '🔴'];
            $_diff = $difficulty ?? 'easy';
        @endphp
        <div style="text-align:center; margin-bottom: 18px;">
            <span style="display:inline-block; padding:5px 18px; border-radius:50px; border:2px solid {{ $diffColors[$_diff] ?? '#64c8ff' }}; color:{{ $diffColors[$_diff] ?? '#64c8ff' }}; font-size:0.88em; font-weight:700; background:rgba(0,0,0,0.35); letter-spacing:0.5px;">
                {{ $diffIcons[$_diff] ?? '🟢' }} {{ ucfirst($_diff) }} difficulty
            </span>
        </div>
        @endauth
        @if ($won)
            <div class="result-icon celebration">🎉</div>
            <h1>You Won!</h1>
            <p class="message">
                @if (!empty($milestone))
                    Congratulations! You completed 5 unique correct guesses. Game complete!
                @else
                    Excellent job! You successfully guessed the answer!
                @endif
            </p>

            <div class="answer-reveal">
                <p>The answer was:</p>
                <strong>{{ $answer }}</strong>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <strong>{{ $attempts }}</strong>
                    <span>Attempts Used</span>
                </div>
                <div class="stat-box">
                    <strong>{{ $maxAttempts - $attempts }}</strong>
                    <span>Remaining</span>
                </div>
                @if (!empty($milestone))
                    <div class="stat-box">
                        <strong>{{ $uniqueScore ?? 0 }}/5</strong>
                        <span>Unique Score</span>
                    </div>
                @endif
            </div>

            @if (!empty($milestone) && !empty($guessedWords))
                <div class="guessed-summary">
                    <h3>Guessed Words Summary</h3>
                    <p>{{ implode(', ', $guessedWords) }}</p>
                </div>
            @endif
        @else
            <div class="result-icon">@if(!empty($timerExpired ?? false))⏱@else😢@endif</div>
            <h1>@if(!empty($timerExpired ?? false))Time's Up!@else Game Over!@endif</h1>
            <p class="message">
                @if(!empty($timerExpired ?? false))
                    The timer ran out before you could guess the word. Keep practicing!
                @else
                    You've used all your attempts. Better luck next time!
                @endif
            </p>

            <div class="answer-reveal">
                <p>The answer was:</p>
                <strong>{{ $answer }}</strong>
            </div>

            <div class="stats">
                <div class="stat-box">
                    <strong>{{ $attempts }}/{{ $maxAttempts }}</strong>
                    <span>Attempts Used</span>
                </div>
                <div class="stat-box">
                    <strong>{{ max(0, $maxAttempts - $attempts) }}</strong>
                    <span>Remaining</span>
                </div>
            </div>
        @endif

        <div class="buttons">
            <a href="{{ route('guessing-game.select') }}" class="btn btn-primary">Play Again</a>
            <a href="{{ route('home') }}" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>
