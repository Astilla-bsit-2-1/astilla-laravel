<?php

use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});
Route::get('/', function () {
    $categories = [
        'animals' => ['Lion', 'Elephant', 'Eagle', 'Dolphin'],
        'countries' => ['Japan', 'Brazil', 'Germany', 'Australia'],
        'programming_languages' => ['PHP', 'Python', 'JavaScript', 'Java', 'C++']
    ];

    return view('welcome', ['categories' => $categories]); 
    
})->name('home');


Route::get('/greet', function () {
    echo ('Nabunturan, Ian Mark');
    echo ' <a href="' . route('home') . '">Go to Home</a>';
})->name('greet');

// Guessing Game Routes
Route::prefix('guessing-game')->group(function () {
    Route::get('/', [App\Http\Controllers\GuessingGameController::class, 'startRandomGame'])->name('guessing-game.select');
    Route::get('/select', [App\Http\Controllers\GuessingGameController::class, 'selectCategory'])->name('guessing-game.select-category');
    Route::post('/start/{category}', [App\Http\Controllers\GuessingGameController::class, 'startGame'])->name('guessing-game.start');
    Route::get('/guess', [App\Http\Controllers\GuessingGameController::class, 'guessView'])->name('guessing-game.guess');
    Route::post('/guess', [App\Http\Controllers\GuessingGameController::class, 'makeGuess'])->name('guessing-game.guess.post');
    Route::get('/hint/change', [App\Http\Controllers\GuessingGameController::class, 'changeHint'])->name('guessing-game.hint.change');
    Route::get('/result', [App\Http\Controllers\GuessingGameController::class, 'result'])->name('guessing-game.result');
    Route::get('/reset', [App\Http\Controllers\GuessingGameController::class, 'reset'])->name('guessing-game.reset');
});



