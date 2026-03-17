<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;

class EnsureAuthOrGuest
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            $user = $request->user();

            if ($user instanceof MustVerifyEmail && !$user->hasVerifiedEmail()) {
                return redirect()->route('verification.notice')
                    ->with('error', 'Please verify your email address before continuing.');
            }

            return $next($request);
        }

        if (session('guest_mode')) {
            return $next($request);
        }

        return redirect()->route('auth.login');
    }
}
