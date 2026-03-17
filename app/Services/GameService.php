<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Word;

class GameService
{
    protected const SESSIONS_KEY = 'game_sessions';
    protected const ACTIVE_SESSION_KEY = 'active_game_session';
    protected const DIFFICULTY_CONFIG = [
        'easy'   => ['max_attempts' => 6, 'max_give_ups' => 3, 'timer_seconds' => null, 'hint_reveal_disabled' => false],
        'medium' => ['max_attempts' => 5, 'max_give_ups' => 2, 'timer_seconds' => 60,   'hint_reveal_disabled' => false],
        'hard'   => ['max_attempts' => 3, 'max_give_ups' => 1, 'timer_seconds' => 30,   'hint_reveal_disabled' => true],
    ];

    public function getCategories(): array
    {
        return Category::pluck('name')->toArray();
    }

    public function getPlayerSessions(): array
    {
        return session(self::SESSIONS_KEY, []);
    }

    public function getActiveSessionId(): ?string
    {
        $activeId = session(self::ACTIVE_SESSION_KEY);
        if (!is_string($activeId) || $activeId === '') {
            return null;
        }

        $sessions = $this->getPlayerSessions();
        return isset($sessions[$activeId]) ? $activeId : null;
    }

    public function getActiveSessionLabel(): ?string
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return null;
        }

        $sessions = $this->getPlayerSessions();
        return (string) ($sessions[$activeId]['label'] ?? $activeId);
    }

    public function createOrActivatePlayerSession(string $name): array
    {
        $normalizedName = strtolower(trim($name));
        $label = trim($name);
        $sessions = $this->getPlayerSessions();

        $created = false;
        if (!isset($sessions[$normalizedName])) {
            $sessions[$normalizedName] = [
                'label' => $label,
                'game' => null,
                'guessed_words' => [],
                'updated_at' => now()->toDateTimeString(),
            ];
            $created = true;
        }

        session([
            self::SESSIONS_KEY => $sessions,
            self::ACTIVE_SESSION_KEY => $normalizedName,
        ]);

        return [
            'id' => $normalizedName,
            'label' => (string) ($sessions[$normalizedName]['label'] ?? $label),
            'created' => $created,
        ];
    }

    public function getCurrentGame(): ?array
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return null;
        }

        $sessions = $this->getPlayerSessions();
        $game = $sessions[$activeId]['game'] ?? null;

        return is_array($game) ? $game : null;
    }

    public function setCurrentGame(array $game): void
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return;
        }

        $sessions = $this->getPlayerSessions();
        if (!isset($sessions[$activeId])) {
            return;
        }

        $sessions[$activeId]['game'] = $game;
        $sessions[$activeId]['updated_at'] = now()->toDateTimeString();
        session([self::SESSIONS_KEY => $sessions]);
    }

    public function clearCurrentGame(): void
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return;
        }

        $sessions = $this->getPlayerSessions();
        if (!isset($sessions[$activeId])) {
            return;
        }

        $sessions[$activeId]['game'] = null;
        $sessions[$activeId]['updated_at'] = now()->toDateTimeString();
        session([self::SESSIONS_KEY => $sessions]);
    }

    public function getRandomCategory(): string
    {
        $categories = $this->getCategories();
        return $categories[array_rand($categories)];
    }

    public function isValidCategory(string $category): bool
    {
        return Category::where('name', strtolower($category))->exists();
    }

    public function initializeGame(string $category, string $difficulty = 'easy'): ?array
    {
        return $this->initializeGameExcluding($category, [], $difficulty);
    }

    public function initializeGameExcluding(string $category, array $excludedWords = [], string $difficulty = 'easy'): ?array
    {
        $category = strtolower($category);
        if (!$this->isValidCategory($category)) {
            return null;
        }

        $excludedSet = array_map(fn ($word) => strtoupper((string) $word), $excludedWords);
        $categoryModel = Category::where('name', $category)->with('words')->first();
        $allWords = $categoryModel ? $categoryModel->words->pluck('word')->toArray() : [];
        $availableItems = array_values(array_filter($allWords, function ($item) use ($excludedSet) {
            return !in_array(strtoupper((string) $item), $excludedSet, true);
        }));

        // If all items were already used, start a fresh cycle in the same category.
        if (empty($availableItems)) {
            $availableItems = $allWords;
        }

        $answer = $availableItems[array_rand($availableItems)];

        $difficulty = array_key_exists($difficulty, self::DIFFICULTY_CONFIG) ? $difficulty : 'easy';
        $config = self::DIFFICULTY_CONFIG[$difficulty];

        return [
            'category' => $category,
            'answer' => $answer,
            'guesses' => [],
            'attempts' => 0,
            'max_attempts' => $config['max_attempts'],
            'hint_index' => 0,
            'hint_reveal_used' => false,
            'hint_reveal_disabled' => $config['hint_reveal_disabled'],
            'give_up_uses' => 0,
            'max_give_ups' => $config['max_give_ups'],
            'difficulty' => $difficulty,
            'timer_seconds' => $config['timer_seconds'],
        ];
    }

    public function getGuessedWords(): array
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return [];
        }

        $sessions = $this->getPlayerSessions();
        $words = $sessions[$activeId]['guessed_words'] ?? [];

        return is_array($words) ? $words : [];
    }

    public function collectGuessedWord(string $word): void
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return;
        }

        $sessions = $this->getPlayerSessions();
        if (!isset($sessions[$activeId])) {
            return;
        }

        $history = $sessions[$activeId]['guessed_words'] ?? [];
        $normalizedWord = strtoupper($word);
        if (!in_array($normalizedWord, $history, true)) {
            $history[] = $normalizedWord;
        }

        $sessions[$activeId]['guessed_words'] = $history;
        $sessions[$activeId]['updated_at'] = now()->toDateTimeString();
        session([self::SESSIONS_KEY => $sessions]);
    }

    public function guessedWordsCount(): int
    {
        return count($this->getGuessedWords());
    }

    public function resetGuessedWords(): void
    {
        $activeId = $this->getActiveSessionId();
        if (!$activeId) {
            return;
        }

        $sessions = $this->getPlayerSessions();
        if (!isset($sessions[$activeId])) {
            return;
        }

        $sessions[$activeId]['guessed_words'] = [];
        $sessions[$activeId]['updated_at'] = now()->toDateTimeString();
        session([self::SESSIONS_KEY => $sessions]);
    }

    public function buildDisplayAnswer(string $answer, array $guessedLetters): string
    {
        $answer = strtoupper($answer);
        $displayAnswer = '';

        foreach (str_split($answer) as $char) {
            if ($char === ' ') {
                $displayAnswer .= ' ';
                continue;
            }

            $displayAnswer .= in_array($char, $guessedLetters, true) ? $char : '_';
        }

        return $displayAnswer;
    }

    public function isGameLost(array $game): bool
    {
        return $game['attempts'] >= $game['max_attempts']
            || (($game['give_up_uses'] ?? 0) >= ($game['max_give_ups'] ?? 3));
    }

    public function getCurrentHint(array $game): string
    {
        $hintOptions = $this->buildHintOptions($game);
        $hintIndex = (int) ($game['hint_index'] ?? 0);
        $hintIndex = max(0, min($hintIndex, count($hintOptions) - 1));

        return $hintOptions[$hintIndex];
    }

    public function processHintReveal(array $game): array
    {
        if (!empty($game['hint_reveal_disabled'])) {
            return [
                'success' => false,
                'error' => 'Hint reveal is not available on Hard difficulty.',
                'status' => 'hint_reveal_disabled',
            ];
        }

        if (!empty($game['hint_reveal_used'])) {
            return [
                'success' => false,
                'error' => 'Hint letter reveal can only be used once per game.',
                'status' => 'hint_reveal_limit_reached',
            ];
        }

        $revealedLetter = $this->revealOneLetter($game);
        $game['hint_reveal_used'] = true;

        return [
            'success' => true,
            'game' => $game,
            'info' => $revealedLetter
                ? ('Hint letter revealed: ' . $revealedLetter)
                : 'All hint letters are already revealed.',
        ];
    }

    public function processLetterGuess(array $game, string $guess): array
    {
        $guess = strtoupper($guess);

        if (!preg_match('/^[A-Z]$/', $guess)) {
            return [
                'success' => false,
                'error' => 'Please enter a single letter',
                'status' => 'invalid',
            ];
        }

        if (in_array($guess, $game['guesses'], true)) {
            return [
                'success' => false,
                'error' => 'You already guessed that letter',
                'status' => 'already_guessed',
            ];
        }

        $game['guesses'][] = $guess;
        if (!in_array($guess, str_split(strtoupper($game['answer'])), true)) {
            $game['attempts']++;
        }

        return [
            'success' => true,
            'game' => $game,
        ];
    }

    public function processGiveUp(array $game): array
    {
        $hintOptions = $this->buildHintOptions($game);
        $currentIndex = (int) ($game['hint_index'] ?? 0);

        $game['hint_index'] = ($currentIndex + 1) % count($hintOptions);
        $game['attempts']++;
        $game['give_up_uses'] = (int) ($game['give_up_uses'] ?? 0) + 1;

        $remainingGiveUps = max(0, ($game['max_give_ups'] ?? 3) - $game['give_up_uses']);

        return [
            'game' => $game,
            'remainingGiveUps' => $remainingGiveUps,
            'isLost' => $this->isGameLost($game),
        ];
    }

    public function buildGameViewData(array $game, array $guessedWords = []): array
    {
        $displayAnswer = $this->buildDisplayAnswer($game['answer'], $game['guesses']);
        $answer = strtoupper($game['answer']);

        return [
            'hint' => $this->getCurrentHint($game),
            'displayAnswer' => $displayAnswer,
            'guessedLetters' => implode(', ', $game['guesses']),
            'guessedWords' => $guessedWords,
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts'],
            'giveUpUses' => $game['give_up_uses'] ?? 0,
            'maxGiveUps' => $game['max_give_ups'] ?? 3,
            'isWon' => $displayAnswer === $answer,
            'isLost' => $this->isGameLost($game),
            'difficulty' => $game['difficulty'] ?? 'easy',
            'timerSeconds' => $game['timer_seconds'] ?? null,
            'hintRevealDisabled' => !empty($game['hint_reveal_disabled']),
        ];
    }

    public function buildAjaxPayload(array $game, array $guessedWords = []): array
    {
        $displayAnswer = $this->buildDisplayAnswer($game['answer'], $game['guesses']);
        $answer = strtoupper($game['answer']);

        return [
            'success' => true,
            'displayAnswer' => $displayAnswer,
            'guessedLetters' => implode(', ', $game['guesses']),
            'guessedWords' => $guessedWords,
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts'],
            'giveUpUses' => $game['give_up_uses'] ?? 0,
            'maxGiveUps' => $game['max_give_ups'] ?? 3,
            'isWon' => $displayAnswer === $answer,
            'isLost' => $this->isGameLost($game),
            'answer' => $answer,
            'lettersFound' => strlen($displayAnswer) - substr_count($displayAnswer, '_'),
            'hint' => $this->getCurrentHint($game),
            'difficulty' => $game['difficulty'] ?? 'easy',
            'timerSeconds' => $game['timer_seconds'] ?? null,
        ];
    }

    protected function buildHintOptions(array $game): array
    {
        $answer = strtoupper($game['answer']);
        $categoryHint = Word::where('word', $game['answer'])
            ->whereHas('category', fn ($q) => $q->where('name', $game['category']))
            ->with('hints')
            ->first()
            ?->hints
            ->first()
            ?->hint ?? 'No hint available';
        $cleanAnswer = str_replace(' ', '', $answer);
        $firstLetter = $cleanAnswer !== '' ? $cleanAnswer[0] : '';
        $lastLetter = $cleanAnswer !== '' ? $cleanAnswer[strlen($cleanAnswer) - 1] : '';

        $options = [
            $categoryHint,
            'It has ' . strlen($cleanAnswer) . ' letters.',
            'It starts with the letter "' . $firstLetter . '".',
            'It ends with the letter "' . $lastLetter . '".',
        ];

        return array_values(array_unique($options));
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
}
