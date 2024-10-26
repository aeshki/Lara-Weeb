<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class ResetLoginStreaks extends Command
{
    protected $signature = 'streak:reset';
    protected $description = 'Reset login streaks for users who haven\'t logged in today';

    public function handle()
    {
        $users = User::all();

        foreach ($users as $user) {
            if (!$user->logged_today) {
                if (($user->login_streak > $user->better_login_streak) && $user->login_streak > 1) {
                    $user->better_login_streak = $user->login_streak;
                }

                $user->login_streak = 0;
            } else {
                $user->logged_today = false;
            }

            $user->save();
        }

        $this->info('Login streaks reset for users who haven\'t logged in today.');
    }
}
