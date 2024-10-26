<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostCommentController;
use App\Http\Controllers\PostLikeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function() {
    Route::apiResources([
        'users' => UserController::class,
        'posts' => PostController::class,
        'comments' => CommentController::class,
    ]);

    Route::post('users/{user}/follow', [FollowerController::class, 'follow']);
    Route::delete('users/{user}/unfollow', [FollowerController::class, 'unfollow']);

    Route::post('users/{user}/follow-request/{request}', [FollowerController::class, 'acceptPendingRequest']);
    Route::delete('users/{user}/follow-request/{request}', [FollowerController::class, 'rejectPendingRequest']);

    Route::resource('users.posts', UserPostController::class);
    Route::resource('users.notifications', NotificationController::class);
    Route::post('users/{user}/notifications/mark-as-read', [NotificationController::class, 'markAsRead']);
    
    Route::resource('posts.comments', PostCommentController::class);
    
    Route::post('posts/{post}/like', [PostLikeController::class, 'like']);
    Route::delete('posts/{post}/like', [PostLikeController::class, 'unlike']);
});