<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('user.{userId}.notifications', function (User $user) {
    return $user->id === Auth::id();
});