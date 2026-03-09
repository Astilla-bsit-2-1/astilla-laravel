<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Category - Guessing Game</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @import url('https://fonts.googleapis.com/css2?family=Fredoka:wght@400;600;700&family=Varela+Round&display=swap');

        body {
            font-family: 'Fredoka', sans-serif;
            background: linear-gradient(135deg, #FFE5B4 0%, #FFD1DC 25%, #B4E7FF 50%, #D4F1D4 75%, #F0D1FF 100%);
            background-size: 400% 400%;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            animation: rainbowShift 8s ease infinite;
        }

        @keyframes rainbowShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background: linear-gradient(135deg, rgba(255, 245, 230, 0.95) 0%, rgba(255, 240, 245, 0.98) 100%);
            backdrop-filter: blur(10px);
            border-radius: 40px;
            padding: 50px;
            max-width: 500px;
            width: 100%;
            animation: slideIn 0.7s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: 4px solid #FF69B4;
            box-shadow: 
                0 8px 32px rgba(255, 105, 180, 0.3),
                0 0 0 8px rgba(255, 200, 255, 0.2),
                inset 0 0 20px rgba(255, 200, 255, 0.1);
            position: relative;
        }

        .container::before {
            content: '🎨';
            position: absolute;
            top: -25px;
            right: 30px;
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
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 50%, #FFD700 100%);
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

        > p {
            text-align: center;
            color: #667eea;
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
            background: linear-gradient(135deg, rgba(255, 105, 180, 0.3) 0%, rgba(255, 182, 193, 0.3) 100%);
            color: #FF1493;
            border: 3px solid #FF69B4;
            border-radius: 20px;
            font-size: 1.2em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
            text-transform: capitalize;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.3), inset 0 0 10px rgba(255, 200, 200, 0.1);
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
            background: rgba(255, 200, 200, 0.2);
            transition: left 0.3s ease;
            z-index: -1;
        }

        .category-btn:hover {
            transform: translateY(-6px) scale(1.05);
            box-shadow: 0 10px 30px rgba(255, 105, 180, 0.5), inset 0 0 15px rgba(255, 200, 200, 0.2);
            background: linear-gradient(135deg, rgba(255, 105, 180, 0.5) 0%, rgba(255, 182, 193, 0.5) 100%);
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
            color: #FF1493;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
            font-size: 1.05em;
            display: inline-block;
            font-family: 'Fredoka', sans-serif;
            padding: 8px 16px;
            border: 2px solid #FF69B4;
            border-radius: 15px;
            background: linear-gradient(135deg, rgba(255, 105, 180, 0.1) 0%, rgba(255, 182, 193, 0.1) 100%);
        }

        .home-link a:hover {
            color: white;
            background: linear-gradient(135deg, #FF69B4 0%, #FF1493 100%);
            transform: scale(1.05);
            box-shadow: 0 4px 15px rgba(255, 105, 180, 0.4);
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
    <div class="container">
        <h1>🎮 Guessing Game</h1>
        <p style="text-align: center; color: #666; margin-bottom: 30px;">Select a category to begin</p>

        <div class="categories">
            @foreach ($categories as $category)
                <form action="{{ route('guessing-game.start', $category) }}" method="POST" style="width: 100%;">
                    @csrf
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
