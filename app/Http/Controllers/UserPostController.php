<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPostController extends Controller
{
    public function index(User $user)
    {
        // if ($user->is_private && $user->id != Auth::id() && !Auth::user()->is_super_admin) {
        //     abort(404, 'Not Found');
        // };

        $posts = $user->posts()
                      ->when(!Auth::user()->is_super_admin && $user->id != Auth::id(), function($query) {
                          $query->where('visible', true);
                      })
                      ->with('author')
                      ->orderByDesc('created_at')
                      ->simplePaginate(25);

        return response()->json([
            'message' => 'Success',
            'posts' => $posts
        ]);
    }
}
