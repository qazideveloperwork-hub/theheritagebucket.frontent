<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // The `users` table was never actually created: this app's original
        // 0001_01_01_000000_create_users_table migration shares its filename
        // with the admin app's migration (same shared `migrations` tracking
        // table, same shared database), so it was marked as run without ever
        // executing here. This recreates just the missing `users` table —
        // `password_reset_tokens` and `sessions` already exist.
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->rememberToken();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
