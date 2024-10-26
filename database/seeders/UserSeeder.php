<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        /**
         * Create a hard user in the database.
         */
        User::factory()
            ->is_super_admin()
            ->create([
                'username' => 'admin',
                'pseudo' => 'Super Admin',
                'email' => 'admin@laraweeb.com',
                'banner_color' => '#000000'
            ]);

        User::factory(20)->create();

        /**
         * Create users between MIN_NUMBER_FAKE_USERS and MAX_NUMBER_FAKE_USERS or NUMBER_FAKE_USERS
         * if MIN_NUMBER_FAKE_USERS and MAX_NUMBER_FAKE_USERS are undefined.
         */
        User::factory(
            env(
                'NUMBER_FAKE_USERS',
                rand(
                    env('MIN_NUMBER_FAKE_USERS') ?? 0,
                    env('MAX_NUMBER_FAKE_USERS') ?? 0
                )
            )
        )->create();

        /**
         * Create users with post between MIN_NUMBER_FAKE_POSTS and MAX_NUMBER_FAKE_POSTS or NUMBER_FAKE_POSTS
         * if MIN_NUMBER_FAKE_POSTS and MAX_NUMBER_FAKE_POSTS are undefined.
         */
        User::factory(
            env(
                'NUMBER_FAKE_POSTS',
                rand(
                    env('MIN_NUMBER_FAKE_POSTS') ?? 0,
                    env('MAX_NUMBER_FAKE_POSTS') ?? 0
                )
            )
        )->withPost()->create();
    }
}