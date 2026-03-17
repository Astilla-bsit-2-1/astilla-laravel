<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Hint;
use App\Models\Word;
use App\Services\GameService;
use Database\Seeders\GuessingGameDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(GuessingGameDataSeeder::class);
    }

    public function test_categories_table_has_correct_count(): void
    {
        $this->assertDatabaseCount('categories', 3);
    }

    public function test_categories_table_has_expected_names(): void
    {
        foreach (['animals', 'countries', 'programming_languages'] as $name) {
            $this->assertDatabaseHas('categories', ['name' => $name]);
        }
    }

    public function test_words_table_has_correct_count(): void
    {
        // 5 words per category × 3 categories = 15
        $this->assertDatabaseCount('words', 15);
    }

    public function test_hints_table_has_correct_count(): void
    {
        // 1 hint per word × 15 words = 15
        $this->assertDatabaseCount('hints', 15);
    }

    public function test_every_word_has_a_hint(): void
    {
        $wordsWithoutHints = Word::doesntHave('hints')->count();
        $this->assertSame(0, $wordsWithoutHints, 'Every word should have at least one hint');
    }

    public function test_every_word_belongs_to_a_category(): void
    {
        $orphanWords = Word::whereDoesntHave('category')->count();
        $this->assertSame(0, $orphanWords, 'Every word should belong to a category');
    }

    public function test_category_has_words_relationship(): void
    {
        $animals = Category::where('name', 'animals')->first();
        $this->assertNotNull($animals);
        $this->assertCount(5, $animals->words);
    }

    public function test_word_has_hint_relationship(): void
    {
        $word = Word::with('hints')->first();
        $this->assertNotNull($word);
        $this->assertGreaterThan(0, $word->hints->count());
    }

    public function test_game_service_get_categories_returns_db_data(): void
    {
        $service = new GameService();
        $categories = $service->getCategories();

        $this->assertCount(3, $categories);
        $this->assertContains('animals', $categories);
        $this->assertContains('countries', $categories);
        $this->assertContains('programming_languages', $categories);
    }

    public function test_game_service_is_valid_category(): void
    {
        $service = new GameService();

        $this->assertTrue($service->isValidCategory('animals'));
        $this->assertTrue($service->isValidCategory('countries'));
        $this->assertFalse($service->isValidCategory('nonexistent'));
    }

    public function test_game_service_initialize_game_returns_valid_game(): void
    {
        $service = new GameService();
        $game = $service->initializeGame('animals');

        $this->assertNotNull($game);
        $this->assertSame('animals', $game['category']);
        $this->assertContains($game['answer'], ['Lion', 'Elephant', 'Eagle', 'Dolphin', 'Tiger']);
        $this->assertSame(6, $game['max_attempts']); // easy default
    }

    public function test_game_service_initialize_game_respects_difficulty(): void
    {
        $service = new GameService();

        $easy = $service->initializeGame('animals', 'easy');
        $medium = $service->initializeGame('animals', 'medium');
        $hard = $service->initializeGame('animals', 'hard');

        $this->assertSame(6, $easy['max_attempts']);
        $this->assertNull($easy['timer_seconds']);

        $this->assertSame(5, $medium['max_attempts']);
        $this->assertSame(60, $medium['timer_seconds']);

        $this->assertSame(3, $hard['max_attempts']);
        $this->assertSame(30, $hard['timer_seconds']);
        $this->assertTrue($hard['hint_reveal_disabled']);
    }

    public function test_game_service_hint_comes_from_database(): void
    {
        $service = new GameService();

        // Manually set up a fake active session so we can call getCurrentHint
        session(['game_sessions' => [
            'testplayer' => ['label' => 'Test', 'game' => null, 'guessed_words' => []],
        ], 'active_game_session' => 'testplayer']);

        $game = [
            'category' => 'animals',
            'answer'   => 'Lion',
            'guesses'  => [],
            'hint_index' => 0,
        ];

        $hint = $service->getCurrentHint($game);
        $this->assertSame('King of the jungle, known for its mane', $hint);
    }

    public function test_game_service_excludes_already_guessed_words(): void
    {
        $service = new GameService();
        $excluded = ['Lion', 'Elephant', 'Eagle', 'Dolphin'];

        // With 4 out of 5 excluded, only Tiger should come back
        $game = $service->initializeGameExcluding('animals', $excluded);
        $this->assertNotNull($game);
        $this->assertSame('Tiger', $game['answer']);
    }

    public function test_game_service_resets_when_all_words_excluded(): void
    {
        $service = new GameService();
        $allWords = ['Lion', 'Elephant', 'Eagle', 'Dolphin', 'Tiger'];

        // All words excluded → should still return a game (fresh cycle)
        $game = $service->initializeGameExcluding('animals', $allWords);
        $this->assertNotNull($game);
        $this->assertContains($game['answer'], $allWords);
    }
}
