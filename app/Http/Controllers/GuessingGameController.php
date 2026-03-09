<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GuessingGameController extends Controller
{
    protected $categories = [
        'animals' => ['Lion', 'Elephant', 'Eagle', 'Dolphin'],
        'countries' => ['Japan', 'Brazil', 'Germany', 'Australia'],
        'programming_languages' => ['PHP', 'Python', 'JavaScript', 'Java']
    ];

    protected $hints = [
        'animals' => [
            'Lion' => 'King of the jungle, known for its mane',
            'Elephant' => 'Largest land animal with a trunk',
            'Eagle' => 'Large flying bird with excellent vision',
            'Dolphin' => 'Intelligent marine mammal'
        ],
        'countries' => [
            'Japan' => 'Island nation in East Asia, known for technology',
            'Brazil' => 'Largest country in South America',
            'Germany' => 'European country known for engineering',
            'Australia' => 'Island continent in the Southern Hemisphere'
        ],
        'programming_languages' => [
            'PHP' => 'Server-side language, powers many websites',
            'Python' => 'General-purpose language, popular in data science',
            'JavaScript' => 'Language that runs in web browsers',
            'Java' => 'Object-oriented language, "write once, run anywhere"'
        ]
    ];

    protected function buildDisplayAnswer(string $answer, array $guessedLetters): string
    {
        $displayAnswer = '';
        foreach (str_split(strtoupper($answer)) as $char) {
            if ($char === ' ') {
                $displayAnswer .= ' ';
            } else {
                $displayAnswer .= in_array($char, $guessedLetters) ? $char : '_';
            }
        }

        return $displayAnswer;
    }

    protected function buildHintOptions(array $game): array
    {
        $answer = strtoupper($game['answer']);
        $categoryHint = $this->hints[$game['category']][$game['answer']] ?? 'No hint available';
        $cleanAnswer = str_replace(' ', '', $answer);
        $firstLetter = $cleanAnswer !== '' ? $cleanAnswer[0] : '';
        $lastLetter = $cleanAnswer !== '' ? $cleanAnswer[strlen($cleanAnswer) - 1] : '';

        $options = [
            $categoryHint,
            'It has ' . strlen($cleanAnswer) . ' letters.',
            'It starts with the letter "' . $firstLetter . '".',
            'It ends with the letter "' . $lastLetter . '".'
        ];

        return array_values(array_unique($options));
    }

    protected function getCurrentHint(array $game): string
    {
        $hintOptions = $this->buildHintOptions($game);
        $hintIndex = (int) ($game['hint_index'] ?? 0);
        $hintIndex = max(0, min($hintIndex, count($hintOptions) - 1));

        return $hintOptions[$hintIndex];
    }

    protected function revealOneLetter(array &$game): ?string
    {
        $answerLetters = array_unique(str_split(strtoupper(str_replace(' ', '', $game['answer']))));
        $unguessedLetters = array_values(array_diff($answerLetters, $game['guesses']));

        if (empty($unguessedLetters)) {
            return null;
        }

        $revealedLetter = $unguessedLetters[array_rand($unguessedLetters)];
        $game['guesses'][] = $revealedLetter;

        return $revealedLetter;
    }

    public function selectCategory()
    {
        return view('guessing-game.select-category', ['categories' => array_keys($this->categories)]);
    }

    public function startRandomGame()
    {
        // Pick a random category
        $categories = array_keys($this->categories);
        $randomCategory = $categories[array_rand($categories)];
        
        // Start game with random category
        return $this->startGame($randomCategory);
    }

    public function startGame($category)
    {
        // normalize so URL is case‑insensitive
        $category = strtolower($category);

        if (!isset($this->categories[$category])) {
            return redirect()->route('guessing-game.select')->with('error', 'Invalid category');
        }

        $items = $this->categories[$category];
        $answer = $items[array_rand($items)];

        session(['game' => [
            'category' => $category,
            'answer' => $answer,
            'guesses' => [],
            'attempts' => 0,
            'max_attempts' => 6,
            'hint_index' => 0,
            'hint_reveal_used' => false,
            'give_up_uses' => 0,
            'max_give_ups' => 3
        ]]);

        return redirect()->route('guessing-game.guess');
    }

    public function guessView()
    {
        $game = session('game');
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        $hint = $this->getCurrentHint($game);
        $guessedLetters = $game['guesses'];

        // Build display answer
        $answer = strtoupper($game['answer']);
        $displayAnswer = $this->buildDisplayAnswer($answer, $guessedLetters);

        $isWon = $displayAnswer === $answer;
        $isLost = $game['attempts'] >= $game['max_attempts']
            || (($game['give_up_uses'] ?? 0) >= ($game['max_give_ups'] ?? 3));

        return view('guessing-game.guess', [
            'hint' => $hint,
            'displayAnswer' => $displayAnswer,
            'guessedLetters' => implode(', ', $guessedLetters),
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts'],
            'giveUpUses' => $game['give_up_uses'] ?? 0,
            'maxGiveUps' => $game['max_give_ups'] ?? 3,
            'isWon' => $isWon,
            'isLost' => $isLost
        ]);
    }

    public function makeGuess(Request $request)
    {
        $guess = strtoupper((string) $request->input('guess', ''));
        $game = session('game');

        if (!$game) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Game session expired'], 400);
            }
            return redirect()->route('guessing-game.select');
        }

        // Clicking "Guess Letter" without a letter reveals one hidden letter.
        if ($guess === '') {
            if (!empty($game['hint_reveal_used'])) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => 'Hint letter reveal can only be used once per game.',
                        'status' => 'hint_reveal_limit_reached'
                    ], 400);
                }

                return redirect()->route('guessing-game.guess')
                    ->with('info', 'Hint letter reveal can only be used once per game.');
            }

            $revealedLetter = $this->revealOneLetter($game);
            $game['hint_reveal_used'] = true;
            session(['game' => $game]);

            $answer = strtoupper($game['answer']);
            $displayAnswer = $this->buildDisplayAnswer($answer, $game['guesses']);
            $isWon = $displayAnswer === $answer;
            $isLost = $game['attempts'] >= $game['max_attempts']
                || (($game['give_up_uses'] ?? 0) >= ($game['max_give_ups'] ?? 3));

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'displayAnswer' => $displayAnswer,
                    'guessedLetters' => implode(', ', $game['guesses']),
                    'attempts' => $game['attempts'],
                    'maxAttempts' => $game['max_attempts'],
                    'giveUpUses' => $game['give_up_uses'] ?? 0,
                    'maxGiveUps' => $game['max_give_ups'] ?? 3,
                    'isWon' => $isWon,
                    'isLost' => $isLost,
                    'answer' => $answer,
                    'lettersFound' => strlen($displayAnswer) - substr_count($displayAnswer, '_'),
                    'hint' => $this->getCurrentHint($game),
                    'info' => $revealedLetter ? ('Hint letter revealed: ' . $revealedLetter) : 'All hint letters are already revealed.'
                ]);
            }

            if ($isWon) {
                return redirect()->route('guessing-game.result')->with('won', true);
            }

            return redirect()->route('guessing-game.guess')
                ->with('info', $revealedLetter ? ('Hint letter revealed: ' . $revealedLetter) : 'All hint letters are already revealed.');
        }

        // Validate input
        if (!preg_match('/^[A-Z]$/', $guess)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Please enter a single letter'], 400);
            }
            return redirect()->route('guessing-game.guess')->with('error', 'Please enter a single letter');
        }

        // Check if already guessed
        if (in_array($guess, $game['guesses'])) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'You already guessed that letter', 'status' => 'already_guessed'], 400);
            }
            return redirect()->route('guessing-game.guess')->with('info', 'You already guessed that letter');
        }

        // Add guess
        $game['guesses'][] = $guess;

        // Check if correct
        $answer = strtoupper($game['answer']);
        if (!in_array($guess, str_split($answer))) {
            $game['attempts']++;
        }

        session(['game' => $game]);

        // Check if won or lost
        $displayAnswer = $this->buildDisplayAnswer($answer, $game['guesses']);

        $isWon = $displayAnswer === $answer;
        $isLost = $game['attempts'] >= $game['max_attempts']
            || (($game['give_up_uses'] ?? 0) >= ($game['max_give_ups'] ?? 3));

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'displayAnswer' => $displayAnswer,
                'guessedLetters' => implode(', ', $game['guesses']),
                'attempts' => $game['attempts'],
                'maxAttempts' => $game['max_attempts'],
                'giveUpUses' => $game['give_up_uses'] ?? 0,
                'maxGiveUps' => $game['max_give_ups'] ?? 3,
                'isWon' => $isWon,
                'isLost' => $isLost,
                'answer' => $answer,
                'lettersFound' => strlen($displayAnswer) - substr_count($displayAnswer, '_'),
                'hint' => $this->getCurrentHint($game)
            ]);
        }

        // Traditional redirect for non-AJAX
        if ($isWon) {
            return redirect()->route('guessing-game.result')->with('won', true);
        } elseif ($isLost) {
            return redirect()->route('guessing-game.result')->with('won', false);
        }

        return redirect()->route('guessing-game.guess');
    }

    public function changeHint()
    {
        $game = session('game');
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        $hintOptions = $this->buildHintOptions($game);
        $currentIndex = (int) ($game['hint_index'] ?? 0);
        $game['hint_index'] = ($currentIndex + 1) % count($hintOptions);
        $game['attempts']++;
        $game['give_up_uses'] = (int) ($game['give_up_uses'] ?? 0) + 1;

        session(['game' => $game]);

        if ($game['attempts'] >= $game['max_attempts']
            || $game['give_up_uses'] >= ($game['max_give_ups'] ?? 3)) {
            return redirect()->route('guessing-game.result')->with('won', false);
        }

        $remainingGiveUps = max(0, ($game['max_give_ups'] ?? 3) - $game['give_up_uses']);

        return redirect()->route('guessing-game.guess')
            ->with('info', 'Hint changed. 1 attempt consumed. Give Up chances left: ' . $remainingGiveUps);
    }

    public function result(Request $request)
    {
        $game = session('game');
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        // the redirect above flashes 'won' into session; retrieve it here
        $won = $request->session()->get('won', false) === true;
        // clear the flash so refresh doesn't repeat the message
        $request->session()->forget('won');
        
        $answer = $game['answer'];

        if ($won) {
            session()->forget('game');
        }

        return view('guessing-game.result', [
            'won' => $won,
            'answer' => $answer,
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts']
        ]);
    }

    public function reset()
    {
        session()->forget('game');
        return redirect()->route('guessing-game.select');
    }
}
