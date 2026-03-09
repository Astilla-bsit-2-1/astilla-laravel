<?php

namespace App\Services;

class GameService
{
    /**
     * Game categories with items to guess
     */
    protected $categories = [
        'animals' => ['Lion', 'Elephant', 'Eagle', 'Dolphin'],
        'countries' => ['Japan', 'Brazil', 'Germany', 'Australia'],
        'programming_languages' => ['PHP', 'Python', 'JavaScript', 'Java']
    ];

    /**
     * Hints for each item in categories
     */
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

    /**
     * Get all available categories
     */
    public function getCategories()
    {
        return array_keys($this->categories);
    }

    /**
     * Validate if category exists
     */
    public function isValidCategory($category)
    {
        return isset($this->categories[$category]);
    }

    /**
     * Get a random item from a category
     */
    public function getRandomItem($category)
    {
        if (!$this->isValidCategory($category)) {
            return null;
        }

        $items = $this->categories[$category];
        return $items[array_rand($items)];
    }

    /**
     * Get hint for an answer in a category
     */
    public function getHint($category, $answer)
    {
        if (isset($this->hints[$category][$answer])) {
            return $this->hints[$category][$answer];
        }

        return 'No hint available';
    }

    /**
     * Build display answer based on guessed letters
     */
    public function buildDisplayAnswer($answer, $guessedLetters)
    {
        $answer = strtoupper($answer);
        $displayAnswer = '';

        foreach (str_split($answer) as $char) {
            if ($char === ' ') {
                $displayAnswer .= ' ';
            } else {
                $displayAnswer .= in_array($char, $guessedLetters) ? $char : '_';
            }
        }

        return $displayAnswer;
    }

    /**
     * Check if a guess is valid (single letter)
     */
    public function isValidGuess($guess)
    {
        return preg_match('/^[A-Z]$/', strtoupper($guess)) === 1;
    }

    /**
     * Check if letter was already guessed
     */
    public function isAlreadyGuessed($guess, $guessedLetters)
    {
        return in_array(strtoupper($guess), $guessedLetters);
    }

    /**
     * Check if guess is correct
     */
    public function isCorrectGuess($guess, $answer)
    {
        return in_array(strtoupper($guess), str_split(strtoupper($answer)));
    }

    /**
     * Check if game is won
     */
    public function isGameWon($displayAnswer, $answer)
    {
        return $displayAnswer === strtoupper($answer);
    }

    /**
     * Check if game is lost
     */
    public function isGameLost($attempts, $maxAttempts)
    {
        return $attempts >= $maxAttempts;
    }

    /**
     * Get game status information
     */
    public function getGameStatus($game)
    {
        $displayAnswer = $this->buildDisplayAnswer($game['answer'], $game['guesses']);
        $answer = strtoupper($game['answer']);

        return [
            'displayAnswer' => $displayAnswer,
            'guessedLetters' => implode(', ', $game['guesses']),
            'attempts' => $game['attempts'],
            'maxAttempts' => $game['max_attempts'],
            'isWon' => $this->isGameWon($displayAnswer, $answer),
            'isLost' => $this->isGameLost($game['attempts'], $game['max_attempts']),
            'lettersFound' => strlen($displayAnswer) - substr_count($displayAnswer, '_')
        ];
    }

    /**
     * Initialize a new game
     */
    public function initializeGame($category)
    {
        if (!$this->isValidCategory($category)) {
            return null;
        }

        $answer = $this->getRandomItem($category);

        return [
            'category' => strtolower($category),
            'answer' => $answer,
            'guesses' => [],
            'attempts' => 0,
            'max_attempts' => 6
        ];
    }

    /**
     * Process a guess and update game state
     */
    public function processGuess($game, $guess)
    {
        $guess = strtoupper($guess);

        // Validate guess
        if (!$this->isValidGuess($guess)) {
            return [
                'success' => false,
                'error' => 'Please enter a single letter',
                'status' => 'invalid'
            ];
        }

        // Check if already guessed
        if ($this->isAlreadyGuessed($guess, $game['guesses'])) {
            return [
                'success' => false,
                'error' => 'You already guessed that letter',
                'status' => 'already_guessed'
            ];
        }

        // Add guess to game
        $game['guesses'][] = $guess;

        // Check if correct and increment attempts if wrong
        if (!$this->isCorrectGuess($guess, $game['answer'])) {
            $game['attempts']++;
        }

        // Get updated game status
        $status = $this->getGameStatus($game);

        return array_merge([
            'success' => true,
            'answer' => strtoupper($game['answer'])
        ], $status);
    }

    /**
     * Count remaining attempts
     */
    public function getRemainingAttempts($game)
    {
        return $game['max_attempts'] - $game['attempts'];
    }

    /**
     * Get progress percentage
     */
    public function getProgressPercentage($game)
    {
        return (($game['max_attempts'] - $game['attempts']) / $game['max_attempts']) * 100;
    }

    /**
     * Get all hints for a category
     */
    public function getCategoryHints($category)
    {
        if (isset($this->hints[$category])) {
            return $this->hints[$category];
        }

        return [];
    }
}
