<?php

namespace App\Http\Controllers;

use App\Events\NotificationCreated;
use App\Models\Follower;
use App\Models\FollowRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class FollowerController extends Controller
{
    public function follow(User $user)
    {
        if ($user->followers()->where('follower_id', Auth::id())->exists()) {
            return response()->json([
                'message' => 'You are already following this user.'
            ], 409);
        }

        if ($user->id == Auth::id()) {
            return Response::json([
                'message' => 'Cannot follow self.'
            ], 403);
        }

       if ($user->is_private) {
            if ($user->followerRequests()->where('follower_id', Auth::id())->exists()) {
                return response()->json([
                    'message' => 'Follow request is already pending.'
                ], 400);
            }

            $req = FollowRequest::create([
                'follower_id' => Auth::id(),
                'followed_id' => $user->id,
            ]);

            $notification = Notification::create([
                'type' => 'pending',
                'author_id' => Auth::id(),
                'recipient_id' => $user->id,
                'notifiable_id' => $req->id,
                'notifiable_type' => FollowRequest::class
            ]);
    
            broadcast(new NotificationCreated($notification))->toOthers();

            return response()->json([
                'data' => $user->followerRequests()->get(),
                'message' => 'Request sent successfully.'
            ]);
        }

        Follower::create([
            'follower_id' => Auth::id(),
            'followed_id' => $user->id
        ]);

        $notification = Notification::create([
            'type' => 'followed',
            'author_id' => Auth::id(),
            'recipient_id' => $user->id,
            'notifiable_id' => Auth::id(),
            'notifiable_type' => User::class
        ]);

        broadcast(new NotificationCreated($notification))->toOthers();

        return Response::json([
            'message' => 'Followed successfully.'
        ]);
    }
    
    public function unfollow(User $user)
    {
        if (!$user->isFollowedBy(Auth::id())) {
            return response()->json([
                'message' => 'You are already unfollowing this user.'
            ], 409);
        }

        Auth::user()->following()->detach($user->id);

        return response()->json([
            'message' => 'Unfollowed successfully.'
        ]);
    }

    public function acceptPendingRequest(User $user, FollowRequest $request)
    {
        if ($user->followers()->where('follower_id', Auth::id())->exists()) {
            return response()->json([
                'message' => 'You are already following this user.'
            ], 409);
        }

        $user->followers()->attach($request->follower_id);

        $request->delete();

        return response()->json([
            'message' => 'Follow request accepted.'
        ]);
    }

    public function rejectPendingRequest(User $user, FollowRequest $request)
    {
        $request->delete();

        return response()->json([
            'message' => 'Follow request rejected.'
        ]);
    }
}
