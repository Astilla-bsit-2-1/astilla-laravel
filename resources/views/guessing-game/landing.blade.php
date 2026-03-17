<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Justin's Guessing Game</title>
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
            animation: bounce 2s ease-in-out infinite;
            z-index: 2;
        }

        @keyframes bounce {
            0%, 100% { transform: translateX(-50%) translateY(0); }
            50% { transform: translateX(-50%) translateY(-15px); }
        }

        .container {
            background: linear-gradient(135deg, rgba(20, 35, 50, 0.95) 0%, rgba(15, 30, 55, 0.98) 100%);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 46px;
            max-width: 1100px;
            width: 100%;
            border: 4px solid #00bfff;
            box-shadow:
                0 8px 32px rgba(0, 191, 255, 0.2),
                0 0 0 8px rgba(0, 150, 255, 0.15),
                inset 0 0 20px rgba(100, 200, 255, 0.08);
            position: relative;
            z-index: 3;
            animation: slideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .container::before {
            content: '⚙️';
            position: absolute;
            top: -25px;
            right: 40px;
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
                transform: translateY(40px) scale(0.86) rotateX(-8deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1) rotateX(0deg);
            }
        }

        .layout {
            display: grid;
            grid-template-columns: 1.25fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .hero-card,
        .panel-card {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.2) 0%, rgba(0, 150, 200, 0.2) 100%);
            border: 3px solid #00bfff;
            border-radius: 24px;
            box-shadow: inset 0 0 10px rgba(0, 191, 255, 0.12), 0 4px 15px rgba(0, 191, 255, 0.2);
        }

        .hero-card {
            padding: 30px;
        }

        .eyebrow {
            display: inline-block;
            margin-bottom: 14px;
            color: #9fe8ff;
            border: 2px solid #00bfff;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 0.8em;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            background: rgba(0, 191, 255, 0.13);
        }

        h1 {
            background: linear-gradient(135deg, #00bfff 0%, #00d4ff 50%, #00ffff 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-size: 3.4em;
            line-height: 1;
            font-weight: 700;
            letter-spacing: 1px;
            margin-bottom: 16px;
        }

        .lead {
            color: #d8f4ff;
            font-size: 1.12em;
            line-height: 1.7;
            margin-bottom: 18px;
        }

        .language-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            margin-bottom: 16px;
        }

        .lang-box {
            border: 2px solid #00bfff;
            border-radius: 16px;
            padding: 12px;
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.3) 0%, rgba(0, 50, 100, 0.3) 100%);
            box-shadow: inset 0 0 10px rgba(0, 191, 255, 0.1);
        }

        .lang-box h3 {
            color: #00ffff;
            font-size: 0.95em;
            margin-bottom: 6px;
        }

        .lang-box p {
            color: #c7ecff;
            font-size: 0.92em;
            line-height: 1.5;
        }

        .chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .chip {
            border: 2px solid #00bfff;
            border-radius: 999px;
            padding: 7px 12px;
            color: #9fe8ff;
            font-size: 0.82em;
            font-weight: 700;
            background: rgba(0, 191, 255, 0.12);
        }

        .panel-card {
            padding: 24px;
        }

        h2 {
            color: #00ffff;
            margin-bottom: 14px;
            font-size: 1.6em;
            letter-spacing: 0.5px;
        }

        .alert {
            border: 2px solid #00bfff;
            border-radius: 14px;
            padding: 10px 12px;
            margin-bottom: 10px;
            color: #d8f4ff;
            background: rgba(0, 191, 255, 0.12);
            font-weight: 600;
        }

        .alert.error {
            border-color: #ff7fa3;
            color: #ffd7e3;
            background: rgba(255, 96, 141, 0.16);
        }

        .session-form {
            display: grid;
            gap: 10px;
            margin-top: 6px;
            margin-bottom: 14px;
        }

        .field {
            width: 100%;
            border: 3px solid #00bfff;
            border-radius: 18px;
            background: rgba(0, 100, 150, 0.2);
            color: #e5f8ff;
            padding: 13px 14px;
            font-size: 1.05em;
            font-family: 'Fredoka', sans-serif;
            outline: none;
        }

        .field::placeholder {
            color: #9fdff7;
        }

        .field:focus {
            box-shadow: 0 0 0 3px rgba(0, 212, 255, 0.22);
        }

        details.sessions {
            border: 2px solid #00bfff;
            border-radius: 16px;
            background: rgba(0, 100, 150, 0.2);
            margin-bottom: 12px;
        }

        details.sessions summary {
            list-style: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #c7ecff;
            font-weight: 700;
            padding: 10px 12px;
        }

        details.sessions summary::-webkit-details-marker {
            display: none;
        }

        .badge {
            border-radius: 999px;
            border: 2px solid #00bfff;
            padding: 2px 10px;
            color: #00ffff;
            background: rgba(0, 191, 255, 0.12);
        }

        .session-list {
            border-top: 2px solid rgba(0, 191, 255, 0.35);
            padding: 10px;
            display: grid;
            gap: 8px;
        }

        .session-btn {
            width: 100%;
            text-align: left;
            border-radius: 14px;
            border: 2px solid #00bfff;
            background: rgba(0, 150, 255, 0.2);
            color: #d8f4ff;
            padding: 10px 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.25s ease;
        }

        .session-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 191, 255, 0.3);
        }

        .session-btn.active {
            border-color: #00ffff;
            background: rgba(0, 212, 255, 0.24);
            color: #00ffff;
        }

        .active-session {
            border: 2px solid #00bfff;
            border-radius: 14px;
            background: rgba(0, 150, 255, 0.16);
            color: #c7ecff;
            padding: 10px 12px;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .button-group {
            display: grid;
            gap: 10px;
        }

        .btn {
            width: 100%;
            border-radius: 20px;
            padding: 13px;
            font-size: 0.98em;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            text-decoration: none;
            text-align: center;
            border: 3px solid #00bfff;
            transition: all 0.25s ease;
            font-family: 'Fredoka', sans-serif;
            cursor: pointer;
            display: block;
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

        .btn-muted {
            background: linear-gradient(135deg, rgba(80, 110, 140, 0.25) 0%, rgba(60, 95, 130, 0.3) 100%);
            color: #dbefff;
            box-shadow: 0 4px 15px rgba(120, 170, 210, 0.15), inset 0 0 10px rgba(200, 230, 255, 0.07);
        }

        .btn-muted:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(120, 170, 210, 0.22), inset 0 0 15px rgba(200, 230, 255, 0.1);
        }

        @media (max-width: 980px) {
            .container {
                padding: 32px 24px;
                border-radius: 26px;
            }

            .layout {
                grid-template-columns: 1fr;
            }

            .language-grid {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2.5em;
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
        .difficulty-selector{margin-bottom:14px;}
        .diff-label{color:#9fe8ff;font-weight:700;font-size:0.82em;text-transform:uppercase;letter-spacing:1px;margin-bottom:8px;}
        .diff-btns{display:grid;grid-template-columns:repeat(3,1fr);gap:6px;}
        .diff-btn{background:rgba(0,100,150,0.2);border:2px solid rgba(100,200,255,0.3);border-radius:12px;padding:9px 5px;color:#9fe8ff;font-family:'Fredoka',sans-serif;font-size:0.82em;font-weight:700;cursor:pointer;transition:all 0.2s;display:flex;flex-direction:column;align-items:center;gap:2px;text-align:center;line-height:1.3;}
        .diff-btn small{display:block;font-weight:400;font-size:0.78em;color:rgba(200,232,255,0.5);margin-top:2px;}
        .diff-btn:hover{border-color:#64c8ff;background:rgba(0,150,255,0.2);}
        .diff-btn.active{border-color:#00bfff;background:rgba(0,150,255,0.25);color:#00ffff;box-shadow:0 0 10px rgba(0,191,255,0.25);}
        .button-group form{display:block;width:100%;}
    </style>
    <nav class="user-nav">
        @auth
            <span>👤 {{ Auth::user()->name }}</span>
            <a href="{{ route('auth.password.edit') }}">Change Password</a>
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
        <div class="layout">
            <section class="hero-card">
                <p class="eyebrow">Guessing Game With</p>
                <h1><strong>Justin Nabunturan</strong></h1>

                <p class="lead">
                    or you can call him "Lester Bohol" from Surigao Del Sur.
                </p>

                <div class="language-grid">
                    <article class="lang-box">
                        <h3>English</h3>
                        <p>Crack hidden words one letter at a time and choose your own pace.</p>
                    </article>
                    <article class="lang-box">
                        <h3>Filipino</h3>
                        <p>Alamin ang mga nakatagong salita at pumili ng kategorya ayon sa gusto mong diskarte.</p>
                    </article>
                    <article class="lang-box">
                        <h3>Bisaya</h3>
                        <p>Tukiba ang mga tinagong pulong ug pilia ang hagit nga mohaom sa imong estilo.</p>
                    </article>
                </div>

                <div class="chips">
                    <span class="chip">Letter-by-letter guessing</span>
                    <span class="chip">Category challenges</span>
                    <span class="chip">Hint system</span>
                </div>
            </section>

            <aside class="panel-card">
                <h2>Start Playing</h2>

                @if (session('error'))
                    <div class="alert error">{{ session('error') }}</div>
                @endif

                @if (session('info'))
                    <div class="alert">{{ session('info') }}</div>
                @endif

                @if(!session('guest_mode'))
                <div class="difficulty-selector">
                    <p class="diff-label">⚡ Difficulty</p>
                    <div class="diff-btns">
                        <button type="button" class="diff-btn active" data-diff="easy">🟢 Easy<small>6 attempts · No timer</small></button>
                        <button type="button" class="diff-btn" data-diff="medium">🟡 Medium<small>5 attempts · 60s</small></button>
                        <button type="button" class="diff-btn" data-diff="hard">🔴 Hard<small>3 attempts · 30s</small></button>
                    </div>
                </div>
                <form action="{{ route('guessing-game.session.create') }}" method="POST" class="session-form">
                    @csrf
                    <input type="hidden" name="difficulty" value="easy" class="diff-input">
                    <input
                        type="text"
                        name="session_name"
                        placeholder="Enter your game session name"
                        value="{{ old('session_name') }}"
                        required
                        class="field"
                    >
                    <button type="submit" class="btn btn-primary">
                        Create / Load Session
                    </button>
                </form>
                @else
                <div style="border:2px solid rgba(100,200,255,0.25);border-radius:18px;padding:14px 16px;color:#9fe8ff;background:rgba(0,100,150,0.15);margin-bottom:14px;font-size:0.95em;line-height:1.5;">
                    🎮 <strong>Guest Mode</strong> — You can only play random games.<br>
                    <a href="{{ route('auth.login') }}" style="color:#64c8ff;">Log in</a> or <a href="{{ route('auth.register') }}" style="color:#64c8ff;">Register</a> to unlock all features.
                </div>
                @endif

                @if (!empty($playerSessions))
                    <details class="sessions">
                        <summary>
                            <span>Saved Sessions</span>
                            <span class="badge">{{ count($playerSessions) }}</span>
                        </summary>

                        <div class="session-list">
                            @foreach ($playerSessions as $sessionId => $sessionData)
                                <form action="{{ route('guessing-game.session.create') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="session_name" value="{{ $sessionData['label'] ?? $sessionId }}">
                                    <button
                                        type="submit"
                                        @class([
                                            'session-btn',
                                            'active' => (($activeSessionId ?? null) === $sessionId),
                                        ])
                                    >
                                        {{ $sessionData['label'] ?? $sessionId }}
                                    </button>
                                </form>
                            @endforeach
                        </div>
                    </details>
                @endif

                @if (!empty($activeSessionLabel))
                    <p class="active-session">Active session: <strong>{{ $activeSessionLabel }}</strong></p>
                @endif

                <div class="button-group">
                    <form method="POST" action="{{ route('guessing-game.random') }}" style="display:block;">
                        @csrf
                        <input type="hidden" name="difficulty" value="easy" class="diff-input">
                        <button type="submit" class="btn btn-primary" style="width:100%;">🎲 Play Random Game</button>
                    </form>
                    @if(!session('guest_mode'))
                        <form method="POST" action="{{ route('guessing-game.select-category') }}" style="display:block;">
                            @csrf
                            <input type="hidden" name="difficulty" value="easy" class="diff-input">
                            <button type="submit" class="btn btn-secondary" style="width:100%;">📂 Choose Category</button>
                        </form>
                    @else
                        <div style="border:2px dashed rgba(100,200,255,0.3);border-radius:20px;padding:11px;text-align:center;color:rgba(200,232,255,0.45);font-size:0.9em;">🔒 Choose Category — Log in to unlock</div>
                    @endif
                    <a class="btn btn-muted" href="{{ route('home') }}">Back to Home</a>
                </div>
            </aside>
        </div>
    </div>
    <script>
        document.querySelectorAll('.diff-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                document.querySelectorAll('.diff-btn').forEach(function(b) { b.classList.remove('active'); });
                btn.classList.add('active');
                var diff = btn.dataset.diff;
                document.querySelectorAll('.diff-input').forEach(function(inp) { inp.value = diff; });
            });
        });
    </script>
</body>
</html>
