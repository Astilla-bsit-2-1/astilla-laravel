<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateGameSessionRequest;
use App\Http\Requests\MakeGuessRequest;
use App\Http\Requests\StartGameRequest;
use App\Models\Category;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class GuessingGameController extends Controller
{
    public function __construct(protected GameService $gameService)
    {
    }

    public function landing()
    {
        if (session('guest_mode')) {
            return $this->startRandomGame();
        }

        return view('guessing-game.landing', [
            'playerSessions' => $this->gameService->getPlayerSessions(),
            'activeSessionId' => $this->gameService->getActiveSessionId(),
            'activeSessionLabel' => $this->gameService->getActiveSessionLabel(),
        ]);
    }

    public function createGameSession(CreateGameSessionRequest $request)
    {
        $data = $request->validated();
        session(['game_difficulty' => $data['difficulty'] ?? 'easy']);
        $session = $this->gameService->createOrActivatePlayerSession((string) $data['session_name']);
        $game = $this->gameService->getCurrentGame();

        if ($game) {
            return redirect()->route('guessing-game.guess')
                ->with('info', 'Session "' . $session['label'] . '" loaded. Progress restored.');
        }

        return redirect()->route('guessing-game.select-category')
            ->with('info', 'Session "' . $session['label'] . '" is ready. Start a game!');
    }

    public function selectCategory()
    {
        if (Gate::denies('viewAny', Category::class)) {
            return redirect()->route('guessing-game.select')
                ->with('error', 'Guest mode is restricted to random games only. Please log in or register to choose a category.');
        }

        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $difficulty = request()->post('difficulty', session('game_difficulty', 'easy'));
        if (in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            session(['game_difficulty' => $difficulty]);
        }

        return view('guessing-game.select-category', [
            'categories' => $this->gameService->getCategories(),
            'difficulty' => session('game_difficulty', 'easy'),
        ]);
    }

    public function startRandomGame()
    {
        if (!session('guest_mode')) {
            $difficulty = request()->post('difficulty', 'easy');
            if (in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
                session(['game_difficulty' => $difficulty]);
            }
        }
        return $this->beginGame($this->gameService->getRandomCategory());
    }

    public function startGame(StartGameRequest $request, string $category)
    {
        // Authorization is handled by StartGameRequest::authorize() via the
        // CategoryPolicy::start() policy method — no manual Gate check needed here.
        $difficulty = $request->post('difficulty', session('game_difficulty', 'easy'));
        if (in_array($difficulty, ['easy', 'medium', 'hard'], true)) {
            session(['game_difficulty' => $difficulty]);
        }

        return $this->beginGame($category);
    }

    protected function beginGame(string $category)
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $difficulty = session('guest_mode') ? 'easy' : session('game_difficulty', 'easy');
        $game = $this->gameService->initializeGame($category, $difficulty);
        if (!$game) {
            return redirect()->route('guessing-game.select')->with('error', 'Invalid category');
        }
        $this->gameService->resetGuessedWords();

        $this->gameService->setCurrentGame($game);

        return redirect()->route('guessing-game.guess');
    }

    public function guessView()
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $game = $this->gameService->getCurrentGame();
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        return view('guessing-game.guess', $this->gameService->buildGameViewData(
            $game,
            $this->gameService->getGuessedWords()
        ));
    }

    public function makeGuess(MakeGuessRequest $request)
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $rawGuess = (string) $request->input('guess', '');
        $guess = strtoupper($rawGuess);
        $game = $this->gameService->getCurrentGame();

        if (!$game) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Game session expired'], 400);
            }
            return redirect()->route('guessing-game.select');
        }

        // Clicking "Guess Letter" without a letter reveals one hidden letter.
        if ($guess === '') {
            $result = $this->gameService->processHintReveal($game);
            if (!$result['success']) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'error' => $result['error'],
                        'status' => $result['status']
                    ], 400);
                }

                return redirect()->route('guessing-game.guess')
                    ->with('info', $result['error']);
            }

            $game = $result['game'];
            $this->gameService->setCurrentGame($game);

            $state = $this->finalizeGuessState($request, $game);
            if ($state['response']) {
                return $state['response'];
            }
            $payload = $state['payload'];

            if ($request->expectsJson()) {
                $payload['info'] = $result['info'];
                return response()->json($payload);
            }

            if ($payload['isWon']) {
                return redirect()->route('guessing-game.result')->with('won', true);
            }

            return redirect()->route('guessing-game.guess')
                ->with('info', $result['info']);
        }

        $result = $this->gameService->processLetterGuess($game, $guess);
        if (!$result['success']) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => $result['error'],
                    'status' => $result['status']
                ], 400);
            }

            if (($result['status'] ?? '') === 'already_guessed') {
                return redirect()->route('guessing-game.guess')->with('info', $result['error']);
            }

            return redirect()->route('guessing-game.guess')->with('error', $result['error']);
        }

        $game = $result['game'];
        $this->gameService->setCurrentGame($game);

        $state = $this->finalizeGuessState($request, $game);
        if ($state['response']) {
            return $state['response'];
        }
        $payload = $state['payload'];

        // Return JSON for AJAX requests
        if ($request->expectsJson()) {
            return response()->json($payload);
        }

        // Traditional redirect for non-AJAX
        if ($payload['isWon']) {
            return redirect()->route('guessing-game.result')->with('won', true);
        } elseif ($payload['isLost']) {
            return redirect()->route('guessing-game.result')->with('won', false);
        }

        return redirect()->route('guessing-game.guess');
    }

    protected function handleWinningRound(Request $request, array $game): array
    {
        $this->gameService->collectGuessedWord($game['answer']);

        if ($this->gameService->guessedWordsCount() >= 5) {
            if ($request->expectsJson()) {
                $request->session()->flash('won', true);
                $request->session()->flash('milestone', true);

                return [
                    'response' => response()->json([
                        'success' => true,
                        'streakCompleted' => true,
                        'redirectUrl' => route('guessing-game.result'),
                        'info' => 'Congratulations! You completed 5 unique guesses.'
                    ]),
                    'payload' => null,
                ];
            }

            return [
                'response' => redirect()->route('guessing-game.result')
                    ->with('won', true)
                    ->with('milestone', true),
                'payload' => null,
            ];
        }

        if ((int) ($game['give_up_uses'] ?? 0) === 0) {
            $nextGame = $this->gameService->initializeGameExcluding(
                (string) $game['category'],
                $this->gameService->getGuessedWords(),
                $game['difficulty'] ?? 'easy'
            );

            if ($nextGame) {
                $this->gameService->setCurrentGame($nextGame);

                if ($request->expectsJson()) {
                    $nextPayload = $this->gameService->buildAjaxPayload($nextGame, $this->gameService->getGuessedWords());
                    $nextPayload['roundAdvanced'] = true;
                    $nextPayload['redirectUrl'] = route('guessing-game.guess');
                    $nextPayload['info'] = 'Great job! Next word loaded.';

                    return [
                        'response' => response()->json($nextPayload),
                        'payload' => null,
                    ];
                }

                return [
                    'response' => redirect()->route('guessing-game.guess')->with('info', 'Great job! Next word loaded.'),
                    'payload' => null,
                ];
            }
        }

        return [
            'response' => null,
            'payload' => $this->gameService->buildAjaxPayload($game, $this->gameService->getGuessedWords()),
        ];
    }

    protected function finalizeGuessState(Request $request, array $game): array
    {
        $payload = $this->gameService->buildAjaxPayload($game, $this->gameService->getGuessedWords());

        if ($payload['isWon']) {
            $winFlow = $this->handleWinningRound($request, $game);
            if ($winFlow['response']) {
                return $winFlow;
            }

            $payload = $winFlow['payload'];
        }

        if ($payload['isLost']) {
            $this->gameService->resetGuessedWords();
            $payload = $this->gameService->buildAjaxPayload($game, $this->gameService->getGuessedWords());
        }

        return [
            'response' => null,
            'payload' => $payload,
        ];
    }

    public function changeHint()
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $game = $this->gameService->getCurrentGame();
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        // Choosing Give Up clears the collected guessed-word history.
        $this->gameService->resetGuessedWords();

        $result = $this->gameService->processGiveUp($game);
        $game = $result['game'];

        $this->gameService->setCurrentGame($game);

        if ($result['isLost']) {
            return redirect()->route('guessing-game.result')->with('won', false);
        }

        return redirect()->route('guessing-game.guess')
            ->with('info', 'Hint changed. 1 attempt consumed. Give Up chances left: ' . $result['remainingGiveUps'])
            ->with('guessed_words_reset', 'Guessed words reset');
    }

    public function result(Request $request)
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $game = $this->gameService->getCurrentGame();
        if (!$game) {
            return redirect()->route('guessing-game.select');
        }

        // the redirect above flashes 'won' into session; retrieve it here
        $won = $request->session()->get('won', false) === true;
        $milestone = $request->session()->get('milestone', false) === true;
        $guessedWords = $this->gameService->getGuessedWords();
        $uniqueScore = count($guessedWords);
        // clear the flash so refresh doesn't repeat the message
        $request->session()->forget('won');
        $request->session()->forget('milestone');
        
        $answer = $game['answer'];

        if ($won) {
            $this->gameService->clearCurrentGame();
            $this->gameService->resetGuessedWords();
        } else {
            // Any game-over/loss clears the guessed-word collection.
            $this->gameService->resetGuessedWords();
        }

        return view('guessing-game.result', [
            'won' => $won,
            'milestone' => $milestone,
            'guessedWords' => $guessedWords,
            'uniqueScore' => $uniqueScore,
            'answer' => $answer,
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts'],
            'difficulty' => $game['difficulty'] ?? 'easy',
            'timerExpired' => $request->session()->pull('time_expired', false) === true,
        ]);
    }

    public function timerExpired(Request $request)
    {
        if ($redirect = $this->ensureActiveSession()) {
            return $redirect;
        }

        $request->session()->flash('won', false);
        $request->session()->flash('time_expired', true);
        return redirect()->route('guessing-game.result');
    }

    public function reset()
    {
        $this->gameService->clearCurrentGame();
        return redirect()->route('guessing-game.select');
    }

    protected function ensureActiveSession()
    {
        if (session('guest_mode')) {
            $this->gameService->createOrActivatePlayerSession('__guest__');
            return null;
        }

        if ($this->gameService->getActiveSessionId()) {
            return null;
        }

        // Authenticated users get a session auto-created from their account name.
        if (auth()->check()) {
            $this->gameService->createOrActivatePlayerSession(auth()->user()->name);
            return null;
        }

        return redirect()->route('guessing-game.select')
            ->with('error', 'Create or load a game session first.');
    }
}
