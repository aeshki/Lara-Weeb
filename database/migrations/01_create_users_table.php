<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();

            $table->string('avatar')->nullable();
            $table->string('banner')->nullable();

            $table->string('banner_color')->default('#FFF');

            $table->string('pseudo')->nullable();

            $table->string('username')->unique();

            $table->string('bio')->nullable();

            $table->string('favorite_anime')->nullable();
            $table->string('favorite_manga')->nullable();
            $table->string('favorite_webtoon')->nullable();

            $table->boolean('logged_today')->default(false);
            $table->integer('login_streak')->default(0);
            $table->integer('better_login_streak')->default(0);

            $table->string('email')->unique();

            $table->string('password');

            $table->timestamp('email_verified_at')
                ->nullable()
                ->default(now());

            $table->boolean('active')->default(true);

            $table->boolean('is_super_admin')->default(false);
            $table->boolean('is_private')->default(false);

            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();

            $table->string('token');

            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();

            $table->foreignId('user_id')->nullable()->index();

            $table->string('ip_address', 45)->nullable();

            $table->text('user_agent')->nullable();

            $table->longText('payload');

            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
