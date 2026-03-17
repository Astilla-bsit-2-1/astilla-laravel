<?php

use App\Http\Controllers\GuessingGameController;
use App\Http\Controllers\RegistrationController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/* Authentication Routes (guest only) */

Route::middleware('guest')
    ->controller(RegistrationController::class)
    ->group(function () {
        Route::get('/register',  'showRegister')->name('auth.register');
        Route::get('/login',     'showLogin')->name('auth.login');
        Route::post('/register', 'register');
        Route::post('/login',    'login');
        Route::post('/guest',    'continueAsGuest')->name('auth.guest');
    });

/* Authentication Routes (auth only) */

Route::middleware('auth')
    ->controller(RegistrationController::class)
    ->group(function () {
        Route::get('/password/change', 'showChangePassword')->name('auth.password.edit');
        Route::post('/password/change', 'updatePassword')->name('auth.password.update');
        Route::post('/logout', 'logout')->name('auth.logout');
    });

/* Email Verification Routes (auth only) */

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();

        return redirect()->route('guessing-game.select')
            ->with('success', 'Your email has been verified successfully.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('guessing-game.select');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('success', 'A new verification link has been sent to your email address.');
    })->middleware('throttle:6,1')->name('verification.send');
});

/* General Routes */

Route::get('/', function () {
    return view('welcome');
})->name('home');

/* Guessing Game Routes (auth required) */

Route::middleware('auth_or_guest')
    ->prefix('guessing-game')
    ->name('guessing-game.')
    ->controller(GuessingGameController::class)
    ->group(function () {
        Route::get('/',                  'landing')->name('select');
        Route::post('/session',          'createGameSession')->name('session.create');
        Route::match(['get', 'post'], '/random', 'startRandomGame')->name('random');
        Route::match(['get', 'post'], '/select', 'selectCategory')->name('select-category');
        Route::post('/start/{category}', 'startGame')->name('start');
        Route::get('/guess',             'guessView')->name('guess');
        Route::post('/guess',            'makeGuess')->name('guess.post');
        Route::get('/hint/change',       'changeHint')->name('hint.change');
        Route::get('/result',            'result')->name('result');
        Route::get('/reset',             'reset')->name('reset');
        Route::post('/time-expired',     'timerExpired')->name('time-expired');
    });

