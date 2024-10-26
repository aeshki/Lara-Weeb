<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Http\Helpers\ImageManager;
use App\Http\Requests\StorePostRequest;
use App\Models\Post;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Notification;
use App\Models\Tag;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    use ImageManager;

    public function index(Request $request)
    {        
        $query = Post::public(Auth::user())->with('author');
        
        if ($request->has('tag')) {
            $tagName = $request->query('tag');
            
            $query->whereHas('tags', function ($query) use ($tagName) {
                $query->where('name', $tagName);
            });
        }
        
        $query->orderByDesc('created_at');
        
        $posts = $query->simplePaginate(25);
        
        return response()->json([
            'message' => 'Posts.',
            'posts' => $posts
        ]);
    }

    public function show(Post $post)
    {
        $this->authorize('view', $post);

        return response()->json([
            'message' => 'Post show.',
            'post' => $post->load('author')
        ]);
    }

    public function store(StorePostRequest $req)
    {
        DB::beginTransaction();

        // DEFAULT POST PROPS

        $post = Post::create([
            ...$req->validated(),
            'user_id' => Auth::id()
        ]);

        // TAGS

        preg_match_all('/#(\w+)/', $req->input('message'), $matches);

        $tags = $matches[1];

        foreach ($tags as $tagName) {
            $tag = Tag::firstOrCreate([
                'name' => $tagName
            ]);

            $post->tags()->attach($tag);
        }

        $imageFile = $req->file('image');

        if ($imageFile) {
            $post->image = $this->saveImage($imageFile, $post->id, 'posts', false);
            $post->save();
        }

        DB::commit();

        foreach (Auth::user()->followers()->get() as $follower) {
            $notification = Notification::create([
                'type' => 'posted',
                'author_id' => Auth::id(),
                'recipient_id' => $follower->id,
                'notifiable_id' => $post->id,
                'notifiable_type' => Post::class
            ]);

            broadcast(new NotificationCreated($notification))->toOthers();
        }

        return response()->json([
            'message' => 'Post created succesfully.',
            'post' => $post->load([ 'author' ])
        ]);
    }

    public function update(UpdatePostRequest $request, Post $post)
    {
        $this->authorize('update', $post);

        $post->update($request->validated());

        return response()->json([
            'message'=> 'Post updated.',
            'post' => $post
        ], 201);
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);

        if ($post->image) {
            $path = public_path('storage/posts/').$post->image;

            if (File::exists($path)) {
                File::delete($path);
            };
        };

        return response()->json([
            'message'=> 'Post deleted.',
            'post' => $post->delete()
        ]);
    }
}
