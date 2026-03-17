<?php

namespace App\Providers;

use App\Models\Category;
use App\Models\User;
use App\Policies\CategoryPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        Gate::policy(Category::class, CategoryPolicy::class);

        Gate::define('play-game', function (?User $user): Response {
            if ($user !== null || (bool) session('guest_mode')) {
                return Response::allow();
            }

            return Response::deny('You must be logged in or continue as a guest to play.');
        });
    }
}

