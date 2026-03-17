<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guessing Game | Play</title>
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
            padding: 50px;
            max-width: 1100px;
            width: 100%;
            animation: slideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 4px solid #00bfff;
            box-shadow: 
                0 8px 32px rgba(0, 191, 255, 0.2),
                0 0 0 8px rgba(0, 150, 255, 0.15),
                inset 0 0 20px rgba(100, 200, 255, 0.08);
            position: relative;
        }

        .game-layout {
            display: grid;
            grid-template-columns: 1.2fr 1fr;
            gap: 24px;
            align-items: start;
        }

        .game-left-column,
        .game-right-column {
            display: flex;
            flex-direction: column;
        }

        .game-right-column .button-group {
            margin-top: 6px;
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
            margin-bottom: 30px;
            font-size: 3em;
            font-weight: 700;
            letter-spacing: 2px;
            font-family: 'Fredoka', sans-serif;
            text-shadow: none;
            animation: smile 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        @keyframes smile {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.05); }
        }

        .hint-box {
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.15) 0%, rgba(100, 200, 255, 0.15) 100%);
            border-left: 4px solid #00bfff;
            border: 3px solid #00bfff;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.15), inset 0 0 10px rgba(0, 191, 255, 0.08);
            position: relative;
        }

        .hint-box::before {
            content: '💡';
            position: absolute;
            top: -15px;
            right: 20px;
            font-size: 2em;
            animation: pulse 1.5s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .hint-box:hover {
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.3), inset 0 0 15px rgba(255, 215, 0, 0.15);
            transform: translateY(-3px);
            background: linear-gradient(135deg, rgba(255, 220, 140, 0.3) 0%, rgba(255, 220, 200, 0.3) 100%);
        }

        .hint-box strong {
            color: #00d4ff;
            font-weight: 700;
            text-shadow: none;
        }

        .hint-box p {
            color: #e0e0e0;
            margin-top: 8px;
            font-size: 1.05em;
            line-height: 1.6;
            font-family: 'Fredoka', sans-serif;
        }

        .game-display {
            text-align: center;
            margin-bottom: 35px;
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.2) 0%, rgba(0, 150, 200, 0.2) 100%);
            padding: 25px;
            border: 3px solid #00bfff;
            border-radius: 20px;
            position: relative;
        }

        .game-display::before {
            content: '⚡ ⚡';
            position: absolute;
            top: -15px;
            left: 20px;
            color: #00ffff;
            font-size: 1.5em;
            animation: sparkle 1s ease-in-out infinite;
        }

        @keyframes sparkle {
            0%, 100% { opacity: 0.7; }
            50% { opacity: 1; }
        }

        .answer-display {
            font-size: 3.5em;
            letter-spacing: 12px;
            font-family: 'Varela Round', sans-serif;
            font-weight: 700;
            color: #00ffff;
            margin-bottom: 20px;
            word-break: break-all;
            animation: bounce-text 1s ease-in-out infinite;
            text-shadow: 2px 2px 0 rgba(0, 191, 255, 0.5);
        }

        @keyframes bounce-text {
            0%, 100% { transform: translateY(0); opacity: 1; }
            50% { transform: translateY(-5px); opacity: 0.95; }
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-box {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.25) 0%, rgba(0, 50, 100, 0.25) 100%);
            padding: 20px;
            border-radius: 0;
            text-align: center;
            color: #00ffff;
            border: 2px solid #0096ff;
            box-shadow: 
                inset 0 0 10px rgba(0, 191, 255, 0.15),
                0 0 15px rgba(0, 191, 255, 0.2);
            transition: all 0.3s ease;
            position: relative;
        }

        .stat-box::before {
            content: '⚡';
            position: absolute;
            top: -12px;
            right: -12px;
            font-size: 1.5em;
            color: #00d4ff;
            background: linear-gradient(135deg, rgba(15, 20, 35, 0.95) 0%, rgba(10, 20, 40, 0.98) 100%);
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #0096ff;
        }

        .stat-box:hover {
            transform: translateY(-3px) rotate(-1deg);
            box-shadow: 
                inset 0 0 15px rgba(0, 191, 255, 0.25),
                0 0 25px rgba(0, 191, 255, 0.35);
            background: linear-gradient(135deg, rgba(0, 140, 200, 0.35) 0%, rgba(0, 80, 150, 0.35) 100%);
        }

        .stat-box strong {
            display: block;
            font-size: 2em;
            margin-bottom: 8px;
            font-weight: 800;
            font-family: 'Courier Prime', monospace;
            text-shadow: 2px 2px 0 rgba(0, 0, 0, 0.5);
        }

        .stat-box span {
            font-size: 0.95em;
            opacity: 0.9;
            font-weight: 500;
            text-shadow: 1px 1px 0 rgba(0, 0, 0, 0.5);
        }

        .guesses-box {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.15) 0%, rgba(0, 150, 200, 0.15) 100%);
            padding: 20px;
            border-radius: 20px;
            margin-bottom: 30px;
            border: 3px solid #00bfff;
            box-shadow: inset 0 0 10px rgba(0, 191, 255, 0.1), 0 4px 10px rgba(0, 191, 255, 0.15);
            position: relative;
        }

        .guesses-box::before {
            content: '⚡';
            position: absolute;
            top: -18px;
            left: 15px;
            font-size: 1.8em;
            animation: swing 1s ease-in-out infinite;
        }

        @keyframes swing {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-5deg); }
            75% { transform: rotate(5deg); }
        }

        .guesses-box p {
            color: #00e5ff;
            font-weight: 600;
            font-size: 1.05em;
            font-family: 'Fredoka', sans-serif;
        }

        .form-group {
            margin-bottom: 25px;
        }

        label {
            display: block;
            color: #00d4ff;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1.1em;
            font-family: 'Fredoka', sans-serif;
            letter-spacing: 0.5px;
        }

        input[type="text"] {
            width: 100%;
            padding: 15px;
            border: 3px solid #00bfff;
            border-radius: 15px;
            font-size: 1.2em;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            transition: all 0.3s ease;
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.15) 0%, rgba(0, 150, 200, 0.15) 100%);
            color: #00d4ff;
            font-family: 'Fredoka', sans-serif;
            box-shadow: inset 0 0 8px rgba(0, 191, 255, 0.15);
        }

        input[type="text"]::placeholder {
            color: rgba(0, 191, 255, 0.3);
        }

        input[type="text"]:focus {
            outline: none;
            border-color: #00d4ff;
            box-shadow: 
                inset 0 0 12px rgba(0, 191, 255, 0.2),
                0 0 25px rgba(0, 191, 255, 0.3),
                0 0 0 4px rgba(0, 150, 255, 0.15);
            background: linear-gradient(135deg, rgba(0, 140, 200, 0.25) 0%, rgba(0, 180, 255, 0.25) 100%);
            transform: scale(1.02);
        }

        .button-group {
            display: flex;
            gap: 12px;
        }


        button {
            flex: 1;
            padding: 15px;
            border: 3px solid #00bfff;
            border-radius: 20px;
            font-size: 1.05em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-family: 'Fredoka', sans-serif;
            position: relative;
            overflow: hidden;
        }

        button::before {
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

        button:hover::before {
            left: 0;
        }

        .btn-guess {
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.3) 0%, rgba(100, 200, 255, 0.3) 100%);
            color: #00d4ff;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2), inset 0 0 10px rgba(0, 191, 255, 0.1);
        }

        .btn-guess:hover {
            transform: translateY(-4px) scale(1.05);
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.35), inset 0 0 15px rgba(0, 191, 255, 0.15);
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.5) 0%, rgba(100, 200, 255, 0.5) 100%);
        }

        .btn-guess:active {
            transform: translateY(-1px) scale(1.02);
        }

        .btn-reset {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.3) 0%, rgba(0, 150, 200, 0.3) 100%);
            color: #00d4ff;
            border: 3px solid #00bfff;
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.2), inset 0 0 10px rgba(0, 191, 255, 0.1);
        }

        .btn-reset:hover {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.5) 0%, rgba(0, 150, 200, 0.5) 100%);
            transform: translateY(-4px) scale(1.05) rotate(-2deg);
            box-shadow: 0 8px 25px rgba(0, 191, 255, 0.35), inset 0 0 15px rgba(0, 191, 255, 0.15);
        }

        .btn-exit {
            background: linear-gradient(135deg, rgba(20, 40, 80, 0.5) 0%, rgba(10, 25, 60, 0.5) 100%);
            color: #8ab4d4;
            border: 3px solid rgba(100, 180, 255, 0.35);
            font-weight: 700;
            box-shadow: 0 4px 15px rgba(50, 120, 200, 0.15), inset 0 0 10px rgba(50, 120, 200, 0.08);
        }

        .btn-exit:hover {
            background: linear-gradient(135deg, rgba(20, 60, 120, 0.5) 0%, rgba(10, 40, 90, 0.5) 100%);
            transform: translateY(-4px) scale(1.05);
            color: #c0d8f0;
            box-shadow: 0 8px 25px rgba(50, 150, 255, 0.25), inset 0 0 15px rgba(50, 150, 255, 0.1);
        }

        .alert {
            padding: 18px;
            margin-bottom: 25px;
            border-radius: 20px;
            font-weight: 600;
            animation: popIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 3px solid;
            background: linear-gradient(135deg, rgba(20, 35, 50, 0.9) 0%, rgba(15, 30, 55, 0.9) 100%);
            box-shadow: 0 4px 15px rgba(0, 191, 255, 0.15), inset 0 0 10px rgba(0, 191, 255, 0.08);
            font-family: 'Fredoka', sans-serif;
        }

        @keyframes popIn {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.8);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .alert-danger {
            background: linear-gradient(135deg, rgba(200, 50, 50, 0.25) 0%, rgba(150, 30, 30, 0.25) 100%);
            color: #ff6666;
            border-color: #FF4444;
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(50, 150, 100, 0.25) 0%, rgba(30, 120, 80, 0.25) 100%);
            color: #66ff66;
            border-color: #33dd33;
        }

        .alert-info {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.25) 0%, rgba(0, 150, 200, 0.25) 100%);
            color: #00e5ff;
            border-color: #00bfff;
        }

        .reset-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 20px;
            padding: 10px 14px;
            border-radius: 999px;
            border: 2px solid #ffa726;
            background: linear-gradient(135deg, rgba(255, 167, 38, 0.2) 0%, rgba(255, 193, 7, 0.2) 100%);
            color: #ffd166;
            font-weight: 700;
            font-size: 0.95em;
            letter-spacing: 0.3px;
            box-shadow: 0 4px 12px rgba(255, 167, 38, 0.2), inset 0 0 8px rgba(255, 193, 7, 0.1);
            animation: popIn 0.4s ease;
        }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: linear-gradient(135deg, #0a2a4e 0%, #0f3460 100%);
            border: 3px solid #00bfff;
            border-radius: 15px;
            margin-top: 15px;
            overflow: hidden;
            box-shadow: inset 0 0 8px rgba(0, 191, 255, 0.2), 0 4px 10px rgba(0, 191, 255, 0.15);
            position: relative;
        }

        .progress-bar::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: none;
            pointer-events: none;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #00ffff 0%, #00d4ff 100%);
            width: 0%;
            transition: width 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            box-shadow: 
                0 0 15px rgba(0, 255, 255, 0.6),
                inset 0 0 8px rgba(255, 255, 255, 0.2);
            animation: shimmer 2s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { box-shadow: 0 0 15px rgba(0, 255, 255, 0.6), inset 0 0 8px rgba(255, 255, 255, 0.2); }
            50% { box-shadow: 0 0 20px rgba(0, 212, 255, 0.8), inset 0 0 10px rgba(255, 255, 255, 0.4); }
        }

        @media (max-width: 600px) {
            .container {
                padding: 30px 20px;
            }

            h1 {
                font-size: 2.2em;
            }

            .answer-display {
                font-size: 2.5em;
                letter-spacing: 8px;
            }

            .stat-box strong {
                font-size: 1.6em;
            }

            .keyboard-grid {
                gap: 6px;
            }

            .keyboard-row {
                gap: 6px;
            }

            .keyboard-key {
                padding: 8px;
                font-size: 0.85em;
                min-height: 38px;
            }
        }

        @media (max-width: 980px) {
            .game-layout {
                grid-template-columns: 1fr;
            }
        }

        .keyboard-section {
            margin: 30px 0;
            padding: 20px;
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.15) 0%, rgba(0, 100, 200, 0.15) 100%);
            border: 3px solid #00bfff;
            border-radius: 20px;
            animation: wiggle 3s ease-in-out infinite;
            box-shadow: inset 0 0 15px rgba(0, 191, 255, 0.1), 0 4px 15px rgba(0, 191, 255, 0.15);
            position: relative;
        }

        .keyboard-section::before {
            content: '🎮';
            position: absolute;
            top: -20px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 2em;
            animation: spinFast 2s linear infinite;
        }

        @keyframes spinFast {
            from { transform: translateX(-50%) rotate(0deg); }
            to { transform: translateX(-50%) rotate(360deg); }
        }

        @keyframes wiggle {
            0%, 100% { transform: skewY(0deg); }
            25% { transform: skewY(0.5deg); }
            50% { transform: skewY(-0.5deg); }
            75% { transform: skewY(0.5deg); }
        }

        .keyboard-label {
            color: #00d4ff;
            font-weight: 700;
            margin-bottom: 15px;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 1.1em;
            font-family: 'Fredoka', sans-serif;
        }

        .keyboard-grid {
            display: flex;
            flex-direction: column;
            gap: 10px;
            max-width: 100%;
        }

        .keyboard-row {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .keyboard-key {
            background: linear-gradient(135deg, rgba(0, 100, 150, 0.4) 0%, rgba(0, 150, 200, 0.4) 100%);
            color: #00d4ff;
            border: 3px solid #00bfff;
            border-radius: 15px;
            padding: 10px;
            font-size: 0.95em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
            font-family: 'Fredoka', sans-serif;
            text-transform: uppercase;
            box-shadow: inset 0 0 5px rgba(0, 191, 255, 0.15), 0 3px 8px rgba(0, 191, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 45px;
            position: relative;
            overflow: hidden;
        }

        .keyboard-key::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: none;
            pointer-events: none;
        }

        .keyboard-key:hover:not(.guessed):not(:disabled) {
            transform: scale(1.15) rotate(-3deg) translateY(-2px);
            box-shadow: 
                inset 0 0 10px rgba(0, 191, 255, 0.25),
                0 8px 20px rgba(0, 191, 255, 0.4);
            background: linear-gradient(135deg, rgba(0, 150, 255, 0.6) 0%, rgba(100, 200, 255, 0.6) 100%);
        }

        .keyboard-key:active:not(.guessed):not(:disabled) {
            transform: scale(1.08) rotate(0deg) translateY(0);
        }

        .keyboard-key.guessed {
            background: linear-gradient(135deg, rgba(100, 100, 100, 0.3) 0%, rgba(80, 80, 80, 0.3) 100%);
            color: #888;
            border-color: #666;
            cursor: not-allowed;
            box-shadow: inset 0 0 8px rgba(100, 100, 100, 0.3);
            opacity: 0.5;
        }

        .keyboard-key:disabled {
            opacity: 0.5;
            cursor: not-allowed;
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
        .exit-nav-btn{background:none;border:1px solid rgba(100,180,255,0.4);border-radius:20px;padding:3px 14px;color:#8ab4d4;font-family:'Fredoka',sans-serif;font-size:0.88rem;cursor:pointer;transition:all 0.2s;text-decoration:none;}
        .exit-nav-btn:hover{background:rgba(50,120,200,0.15);color:#c0d8f0;}
    </style>
    <nav class="user-nav">
        @auth
            <span>👤 {{ Auth::user()->name }}</span>
            <form method="POST" action="{{ route('auth.logout') }}" style="display:inline;">@csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
            <a href="{{ route('guessing-game.select') }}" class="exit-nav-btn">🏠 Exit to Menu</a>
        @elseif(session('guest_mode'))
            <span>🎮 Guest Mode</span>
            <a href="{{ route('guessing-game.select') }}" class="exit-nav-btn">🏠 Menu</a>
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
        <form id="timer-expired-form" method="POST" action="{{ route('guessing-game.time-expired') }}" style="display:none;">@csrf</form>
        @endauth
        <h1>🎮 Guessing with Justin</h1>
        @auth
        @php
            $diffColors = ['easy' => '#44dd88', 'medium' => '#ffcc00', 'hard' => '#ff6655'];
            $diffIcons  = ['easy' => '🟢', 'medium' => '🟡', 'hard' => '🔴'];
            $_diff = $difficulty ?? 'easy';
        @endphp
        <div style="text-align:center; margin: -20px 0 18px;">
            <span style="display:inline-block; padding:4px 16px; border-radius:50px; border:2px solid {{ $diffColors[$_diff] ?? '#64c8ff' }}; color:{{ $diffColors[$_diff] ?? '#64c8ff' }}; font-size:0.88em; font-weight:700; background:rgba(0,0,0,0.35);">
                {{ $diffIcons[$_diff] ?? '🟢' }} {{ ucfirst($_diff) }} mode
                @if($timerSeconds ?? null) &nbsp;&bull;&nbsp; ⏱ {{ $timerSeconds }}s @endif
            </span>
        </div>
        @endauth
        @if ($errors->has('error'))
            <div class="alert alert-danger">{{ $errors->first('error') }}</div>
        @endif

        @if (session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
        @endif

        @if (session('guessed_words_reset'))
            <div class="reset-badge">⟲ {{ session('guessed_words_reset') }}</div>
        @endif

        <div class="game-layout">
            <div class="game-left-column">
                <div class="hint-box">
                    <strong>Hint:</strong>
                    <p id="current-hint">{{ $hint }}</p>
                </div>

                <div class="game-display">
                    <div class="answer-display">{{ $displayAnswer }}</div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ (($maxAttempts - $attempts) / $maxAttempts) * 100 }}%;"></div>
                    </div>
                </div>

                <div class="stats">
                    <div class="stat-box">
                        <strong>{{ $maxAttempts - $attempts }}/{{ $maxAttempts }}</strong>
                        <span>Attempts Left</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ strlen($displayAnswer) - substr_count($displayAnswer, '_') }}/{{ strlen(str_replace(' ', '', strtoupper(substr($displayAnswer, 0, strlen($displayAnswer))))) }}</strong>
                        <span>Letters Found</span>
                    </div>
                    <div class="stat-box">
                        <strong>{{ $maxGiveUps - $giveUpUses }}/{{ $maxGiveUps }}</strong>
                        <span>Give Up Chances Left</span>
                    </div>
                    @auth
                    @if(($timerSeconds ?? null) && !$isWon && !$isLost)
                    <div class="stat-box" id="timer-stat" style="border-color:#ff9900;">
                        <strong id="timer-display" style="color:#ff9900;">{{ $timerSeconds }}s</strong>
                        <span style="color:#ffcc88;">⏱ Time Left</span>
                    </div>
                    @endif
                    @endauth
                </div>
            </div>

            <div class="game-right-column">
                @if ($guessedLetters)
                    <div class="guesses-box">
                        <p><strong>Guessed Letters:</strong> {{ $guessedLetters }}</p>
                    </div>
                @endif

                @if (!empty($guessedWords))
                    <div class="guesses-box">
                        <p><strong>Guessed Words:</strong> {{ implode(', ', $guessedWords) }}</p>
                    </div>
                @endif

                @if ($isWon || $isLost)
                    <div class="alert @if($isWon) alert-success @else alert-danger @endif" style="background: @if($isWon) linear-gradient(135deg, rgba(50, 150, 100, 0.3) 0%, rgba(30, 120, 80, 0.3) 100%) @else linear-gradient(135deg, rgba(200, 50, 50, 0.3) 0%, rgba(150, 30, 30, 0.3) 100%) @endif; color: @if($isWon) #66ff66 @else #ff6666 @endif; border-left: 4px solid @if($isWon) #33dd33 @else #FF4444 @endif;">
                        @if ($isWon)
                            🎉 <strong>Congratulations!</strong> You guessed correctly!
                        @else
                            😢 <strong>Game Over!</strong> You've run out of attempts.
                        @endif
                    </div>

                    <div class="button-group">
                        <a href="{{ route('guessing-game.reset') }}" style="text-decoration: none; flex: 1;">
                            <button class="btn-reset" style="width: 100%;">← Select Category</button>
                        </a>
                    </div>
                @else
                    <form action="{{ route('guessing-game.guess.post') }}" method="POST">
                        @csrf

                        <div class="button-group">
                            <button type="submit" class="btn-guess"
                            @if(!empty($hintRevealDisabled)) disabled title="Not available on Hard difficulty" style="opacity:0.4;cursor:not-allowed;" @endif>
                            @if(!empty($hintRevealDisabled)) 🔒 Hint Reveal @else Guess One Letter @endif
                        </button>
                            <a href="{{ route('guessing-game.hint.change') }}" style="text-decoration: none; flex: 1;">
                                <button type="button" class="btn-reset" style="width: 100%;">Give Up</button>
                            </a>
                        </div>
                    </form>

                    <div class="keyboard-section">
                        <div class="keyboard-label">⌨️ Click a Letter</div>
                        <div class="keyboard-grid" id="keyboard">
                            @php
                                $guessedArray = array_map('trim', array_filter(explode(',', str_replace(', ', ',', $guessedLetters))));
                                $keyboardRows = ['QWERTYUIOP', 'ASDFGHJKL', 'ZXCVBNM'];
                            @endphp
                            @foreach ($keyboardRows as $row)
                                <div class="keyboard-row">
                                    @foreach (str_split($row) as $letter)
                                        <button 
                                            type="button" 
                                            class="keyboard-key @if(in_array($letter, $guessedArray)) guessed @endif" 
                                            data-letter="{{ strtoupper($letter) }}"
                                            @if(in_array($letter, $guessedArray)) disabled @endif
                                            onclick="guessKeyboardLetter('{{ strtoupper($letter) }}', event)">
                                            {{ $letter }}
                                        </button>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <script>
        let isSubmitting = false;

        // Audio context for sound effects
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();

        function playKeySound() {
            try {
                const now = audioContext.currentTime;
                const osc = audioContext.createOscillator();
                const gain = audioContext.createGain();

                osc.connect(gain);
                gain.connect(audioContext.destination);

                osc.frequency.setValueAtTime(800, now);
                osc.frequency.exponentialRampToValueAtTime(200, now + 0.1);
                
                gain.gain.setValueAtTime(0.3, now);
                gain.gain.exponentialRampToValueAtTime(0.01, now + 0.1);

                osc.start(now);
                osc.stop(now + 0.1);
            } catch (e) {
                console.log('Audio playback not available');
            }
        }

        function guessKeyboardLetter(letter, event) {
            if (isSubmitting) return;
            event.preventDefault();
            playKeySound();
            setTimeout(() => submitGuessAsync(letter), 100);
        }

        async function submitGuessAsync(letter) {
            if (!letter || isSubmitting) return;
            
            letter = letter.toUpperCase();
            isSubmitting = true;

            try {
                const formData = new FormData();
                formData.append('guess', letter);
                formData.append('_token', document.querySelector('input[name="_token"]').value);

                const response = await fetch('{{ route("guessing-game.guess.post") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    updateGameDisplay(data);
                } else {
                    const error = await response.json();
                    if (error.status !== 'already_guessed') {
                        alert(error.error || 'Error submitting guess');
                    }
                    isSubmitting = false;
                }
            } catch (error) {
                console.error('Error:', error);
                isSubmitting = false;
            }
        }

        function updateGameDisplay(data) {
            if (data.streakCompleted) {
                if (window.gameTimerInterval) clearInterval(window.gameTimerInterval);
                window.location.href = data.redirectUrl || '{{ route('guessing-game.result') }}';
                return;
            }

            if (data.roundAdvanced) {
                if (window.gameTimerInterval) clearInterval(window.gameTimerInterval);
                window.location.href = data.redirectUrl || '{{ route('guessing-game.guess') }}';
                return;
            }

            // Update answer display
            const answerDisplay = document.querySelector('.answer-display');
            if (answerDisplay) answerDisplay.textContent = data.displayAnswer;

            // Update hint text when provided by server.
            const hintElement = document.getElementById('current-hint');
            if (hintElement && data.hint) {
                hintElement.textContent = data.hint;
            }

            // Update guessed letters
            const guessesBox = document.querySelector('.guesses-box p');
            if (guessesBox) {
                guessesBox.innerHTML = '<strong>Guessed Letters:</strong> ' + data.guessedLetters;
            }

            // Update keyboard buttons
            const keyboardButtons = document.querySelectorAll('.keyboard-key');
            keyboardButtons.forEach(btn => {
                const letter = btn.getAttribute('data-letter');
                if (data.guessedLetters.includes(letter)) {
                    btn.classList.add('guessed');
                    btn.disabled = true;
                }
            });

            // Update attempts display
            const attemptStats = document.querySelectorAll('.stat-box');
            if (attemptStats[0]) {
                attemptStats[0].querySelector('strong').textContent = data.attempts + '/' + data.maxAttempts;
            }

            // Update letters found
            if (attemptStats[1]) {
                const totalLetters = data.answer.replace(/\s/g, '').length;
                attemptStats[1].querySelector('strong').textContent = data.lettersFound + '/' + totalLetters;
            }

            // Update give up chances in stats and action indicator.
            if (attemptStats[2]) {
                const remainingHints = Math.max(0, data.maxGiveUps - data.giveUpUses);
                attemptStats[2].querySelector('strong').textContent = remainingHints + '/' + data.maxGiveUps;
            }

            // Update progress bar
            const progressFill = document.querySelector('.progress-fill');
            if (progressFill) {
                const percentage = ((data.maxAttempts - data.attempts) / data.maxAttempts) * 100;
                progressFill.style.width = percentage + '%';
            }

            // Check for win/loss
            if (data.isWon || data.isLost) {
                if (window.gameTimerInterval) clearInterval(window.gameTimerInterval);
                setTimeout(() => {
                    showGameResult(data);
                }, 500);
            } else {
                isSubmitting = false;
            }
        }

        function showGameResult(data) {
            const container = document.querySelector('.container');
            const gameForm = document.querySelector('form');
            const keyboardSection = document.querySelector('.keyboard-section');
            
            if (gameForm) gameForm.style.display = 'none';
            if (keyboardSection) keyboardSection.style.display = 'none';

            const guessedWordsHtml = Array.isArray(data.guessedWords) && data.guessedWords.length
                ? `<div style="text-align: center; margin: 10px 0 20px; color: #d9e4ff; font-weight: 600;">Guessed Words: ${data.guessedWords.join(', ')}</div>`
                : '';

            const resultHtml = `
                <div class="alert ${data.isWon ? 'alert-success' : 'alert-danger'}" style="
                    background: ${data.isWon ? '#d4edda' : '#f8d7da'};
                    color: ${data.isWon ? '#155724' : '#721c24'};
                    border-left: 4px solid ${data.isWon ? '#c3e6cb' : '#f5c6cb'};
                    margin: 20px 0;
                    padding: 15px;
                    border-radius: 8px;
                ">
                    ${data.isWon ? '🎉 <strong>Congratulations!</strong> You guessed correctly!' : '😢 <strong>Game Over!</strong> You\'ve run out of attempts.'}
                </div>
                <div style="text-align: center; margin: 20px 0; padding: 20px; background: rgba(102, 126, 234, 0.1); border-radius: 8px; border: 2px dashed #667eea;">
                    <p style="color: #666; margin-bottom: 10px; font-weight: 600;">The answer was:</p>
                    <strong style="font-size: 2em; color: #667eea; display: block; font-family: 'Courier New', monospace;">${data.answer}</strong>
                </div>
                ${guessedWordsHtml}
                <div style="display: grid; grid-template-columns: 1fr; gap: 12px; margin-top: 20px;">
                    <a href="{{ route('guessing-game.select') }}" style="
                        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                        color: white;
                        padding: 12px;
                        border: none;
                        border-radius: 8px;
                        text-align: center;
                        text-decoration: none;
                        font-weight: 600;
                        cursor: pointer;
                        transition: all 0.3s ease;
                    ">Play Again</a>
                    <a href="{{ route('home') }}" style="
                        background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
                        color: #333;
                        padding: 12px;
                        border: 2px solid #667eea;
                        border-radius: 8px;
                        text-align: center;
                        text-decoration: none;
                        font-weight: 600;
                        cursor: pointer;
                    ">Back to Home</a>
                </div>
            `;

            container.insertAdjacentHTML('beforeend', resultHtml);
            isSubmitting = false;
        }

        // Countdown timer for timed difficulty modes
        @auth
        @if(isset($timerSeconds) && $timerSeconds && !$isWon && !$isLost)
        (function() {
            var secs = {{ (int)$timerSeconds }};
            var display = document.getElementById('timer-display');
            var timerBox = document.getElementById('timer-stat');
            var expiredForm = document.getElementById('timer-expired-form');
            window.gameTimerInterval = setInterval(function() {
                secs--;
                if (display) display.textContent = secs + 's';
                if (secs <= 10) {
                    if (display) display.style.color = '#ff4444';
                    if (timerBox) timerBox.style.borderColor = '#ff4444';
                }
                if (secs <= 0) {
                    clearInterval(window.gameTimerInterval);
                    if (!isSubmitting && expiredForm) expiredForm.submit();
                }
            }, 1000);
        })();
        @endif
        @endauth

        // Keyboard support - press any letter key to guess
        document.addEventListener('keydown', (e) => {
            const letter = e.key.toUpperCase();
            if (/^[A-Z]$/.test(letter) && !isSubmitting) {
                playKeySound();
                setTimeout(() => submitGuessAsync(letter), 100);
            }
        });
    </script>
</body>
</html>
