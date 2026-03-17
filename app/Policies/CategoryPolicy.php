<?php

namespace App\Policies;

use App\Models\Category;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CategoryPolicy
{
    /**
     * Pre-authorization hook — runs before any individual policy method.
     *
     * Returning null falls through to the specific method below.
     * Returning true/false short-circuits all further checks.
     *
     * Reserved for future role-based bypass, e.g.:
     *   if ($user->isAdmin()) return true;
     */
    public function before(User $user, string $ability): ?bool
    {
        return null;
    }

    /**
     * Authenticated users may browse the full category list and reach the
     * select-category screen.
     *
     * Non-nullable User → Laravel automatically denies guests before this runs.
     */
    public function viewAny(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Authenticated users may view / inspect a single category.
     *
     * Non-nullable User → guests are auto-denied.
     */
    public function view(User $user, Category $category): Response
    {
        return Response::allow();
    }

    /**
     * Authenticated users may start a targeted game for a specific category.
     *
     * This is a class-level ability check (called with Category::class),
     * so only the User argument is expected here.
     */
    public function start(User $user): Response
    {
        return Response::allow();
    }

    /**
     * Authenticated users may create or load a named game session.
     *
     * Non-nullable User → guests are auto-denied.
     */
    public function createSession(User $user): Response
    {
        return Response::allow();
    }
}

