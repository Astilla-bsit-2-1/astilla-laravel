<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Mark users that already existed before email verification rollout
     * as verified, so only newly registered users must verify.
     */
    public function up(): void
    {
        DB::table('users')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);
    }

    /**
     * Revert only rows updated by this migration.
     */
    public function down(): void
    {
        DB::table('users')
            ->whereColumn('email_verified_at', 'created_at')
            ->update(['email_verified_at' => null]);
    }
};
