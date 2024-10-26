<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Support\Facades\Auth;

class PostLikeController extends Controller
{
    public function like(Post $post)
    {   
        $this->authorize('update', Auth::user());

        $userId = Auth::id();

        if ($post->is_liked) {
            return response()->json([
                'message' => 'Post already liked.'
            ], 409);
        };
        
        PostLike::create([
            'post_id' => $post->id,
            'user_id' => $userId
        ]);

        $notificationExists = Notification::where([
            'type' => 'like',
            'author_id' => $userId,
            'recipient_id' => $post->user_id,
            'notifiable_id' => $post->id,
            'notifiable_type' => Post::class,
        ])->exists();

        if ($userId !== $post->user_id && !$notificationExists) {
            $notification = Notification::create([
                'type' => 'like',
                'author_id' => Auth::id(),
                'recipient_id' => $post->user_id,
                'notifiable_id' => $post->id,
                'notifiable_type' => Post::class
            ]);
    
            broadcast(new NotificationCreated($notification))->toOthers();
        }

        return response()->json([
            'message' => 'Post liked successfully.'
        ], 201);
    }

    public function unlike(Post $post)
    {
        $this->authorize('delete', Auth::user());

        $like = $post->likes()->where('user_id', Auth::id())->first();

        if (!$like) {
            return response()->json([
                'message' => 'Like not found'
            ], 404);
        }

        $like->delete();

        return response()->json([
            'message' => 'Like removed successfully.'
        ], 200);
    }
}
