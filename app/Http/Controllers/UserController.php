<?php

namespace App\Http\Controllers;

use App\Http\Helpers\ImageManager;
use App\Models\User;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use App\Http\Requests\UpdateUserRequest;

class UserController extends Controller
{
    use ImageManager;

    public function index(Request $request)
    {
        $perPage = 50;
    
        if ($request->has('username')) {
            $username = $request->query('username');

            $users = User::where('username', 'like', $username . '%')
                ->simplePaginate($perPage);
        } else {
            $users = User::simplePaginate($perPage);
        }
    
        return response()->json([
            'message' => 'Users index.',
            'users' => $users
        ]);
    }

    public function show(User $user)
    {
        $data = $user->is_private && $user->id !== Auth::user()->id
            ? $user->makeHidden([
                    'favorite_anime',
                    'favorite_manga',
                    'favorite_webtoon'
                ])
            :  $user;

        if ($user->id !== Auth::id()) {
            $currentUserId = Auth::id();

            $data['is_followed'] = $user->isFollowedBy($currentUserId);
            $data['pending'] = $user->hasPendingRequestFrom($currentUserId);
        }

        return response()->json([
            'message' => 'Success.',
            'user' => $data
        ]);
    }

    public function update(UpdateUserRequest $req, User $user)
    {
        $this->authorize('update', $user);

        $user->fill($req->safe()->except([ 'avatar', 'banner' ]));

        $avatarFile = $req->file('avatar');

        if ($avatarFile) {
            $user->avatar = $this->saveImage($avatarFile, $req->user()->id, 'avatars');
        } else if ($req->has('avatar') && !$avatarFile) {
            $this->removeImage("avatars/$user->avatar");
            $user->avatar = null;
        };

        $bannerFile = $req->file('banner');

        if ($bannerFile) {
            $user->banner = $this->saveImage($bannerFile, $req->user()->id, 'banners');
        } else if ($req->has('banner') && !$bannerFile) {
            $this->removeImage("banners/$user->banner");
            $user->banner = null;
        };

        $user->save();

        return response()->json([
            'message'=> 'User updated.',
            'user' => $user
        ], 201);
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        if ($user->avatar) {
            $this->removeImage("avatars/".$user->avatar);
        };

        if ($user->banner) {
            $this->removeImage("avatars/".$user->banner);
        };
        
        if ($user->banner) {
            $path = public_path('storage/banners/').$user->banner;

            if (File::exists($path)) {
                File::delete($path);
            };
        };

        return response()->json([
            'message'=> 'User deleted.',
            'user' => $user->delete()
        ]);
    }
}
