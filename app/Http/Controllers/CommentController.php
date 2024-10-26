<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;

use App\Models\Comment;
use App\Models\Notification;
use App\Models\Post;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function index(User $user)
    {
        $this->authorize('viewAny', $user);

        return response()->json([
            'message' => 'Comments index.',
            'posts' => Comment::all()
        ]);
    }

    public function show(Comment $comment)
    {
        $this->authorize('view', $comment);

        return response()->json([
            'message' => 'Comment show.',
            'post' => $comment->load('author')
        ]);
    }

    public function store(StoreCommentRequest $req)
    {
        $post = Post::find($req->post_id);

        if (!$post) {
            return response()->json([
                'error' => 'Post not found.'
            ], 404);
        }

        $comment = Comment::create([
            ...$req->validated(),
            'user_id' => Auth::id(),
        ]);

        if (Auth::id() != $post->author->id) {
            $notification = Notification::create([
                'type' => 'replied',
                'author_id' => Auth::id(),
                'recipient_id' => $post->author->id,
                'notifiable_id' => $post->id,
                'notifiable_type' => Post::class
            ]);
    
            broadcast(new NotificationCreated($notification))->toOthers();    
        }

        return response()->json([
            'message' => 'Comment created succesfully.',
            'comment' => $comment->load('author')
        ]);
    }

    public function update(UpdateCommentRequest $req, Comment $comment)
    {
        $this->authorize('update', $comment);

        $comment->update($req->validated());

        return response()->json([
            'message'=> 'Comment updated.',
            'post' => $comment
        ], 201);
    }

    public function destroy(Comment $comment)
    {
        $this->authorize('delete', $comment);

        return response()->json([
            'message'=> 'Comment deleted.',
            'post' => $comment->delete()
        ]);
    }
}