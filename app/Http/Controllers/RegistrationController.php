<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeToGuessingGameMail;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\Rules\Password;

class RegistrationController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration
    |--------------------------------------------------------------------------
    */

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);
        $mailFailed = false;

        try {
            event(new Registered($user));
        } catch (\Throwable $e) {
            $mailFailed = true;
            report($e);
        }

        try {
            Mail::to($user->email)->send(new WelcomeToGuessingGameMail($user->name));
        } catch (\Throwable $e) {
            $mailFailed = true;
            report($e);
        }

        $request->session()->forget('guest_mode');

        if ($mailFailed) {
            return redirect()->route('verification.notice')
                ->with('success', 'Welcome, ' . $user->name . '! Your account has been created.')
                ->with('error', 'We could not send email right now. Please use "Resend Verification Email" after mail settings are fixed.');
        }

        return redirect()->route('verification.notice')
            ->with('success', 'Welcome, ' . $user->name . '! Your account has been created. Please verify your email.');
    }

    /*
    Login
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    public function showChangePassword()
    {
        return view('auth.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($validated['password']),
        ])->save();

        return redirect()->route('auth.password.edit')
            ->with('success', 'Your password has been updated successfully.');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $throttleKey = strtolower((string) $request->input('username')) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            return back()
                ->withErrors([
                    'username' => 'Too many failed attempts. Please try again in ' . $seconds . ' seconds.',
                ])
                ->onlyInput('username');
        }

        $user = \App\Models\User::where('name', $request->input('username'))->first();

        if ($user && Auth::attempt(['email' => $user->email, 'password' => $request->input('password')], $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();
            $request->session()->forget('guest_mode');

            if (!Auth::user()->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('success', 'Please verify your email address to continue.');
            }

            return redirect()->intended(route('guessing-game.select'))
                ->with('success', 'Welcome back, ' . Auth::user()->name . '!');
        }

        RateLimiter::hit($throttleKey, 30);

        return back()
            ->withErrors(['username' => 'These credentials do not match our records.'])
            ->onlyInput('username');
    }

    /*
    Guest Mode
    */

    public function continueAsGuest(Request $request)
    {
        $request->session()->put('guest_mode', true);

        return redirect()->route('guessing-game.select');
    }

    /* Logout */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth.login')
            ->with('success', 'You have been logged out.');
    }
}

