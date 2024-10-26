<?php

namespace App\Models;

use App\Enums\UserBadges;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'avatar',
        'banner',
        'banner_color',
        'pseudo',
        'username',
        'bio',
        'favorite_anime',
        'favorite_manga',
        'favorite_webtoon',
        'email',
        'password',
        'is_private'
    ];

    protected $hidden = [
        'active',
        'email',
        'updated_at',
        'is_super_admin',
        'email_verified_at',
        'password',
        'remember_token',
        'logged_today'
    ];

    protected $appends = [
        'flags',
        'total_likes',
        'followers_count',
        'following_count'
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_private' => 'boolean'
        ];
    }

    public function resolveRouteBinding($value, $field = null)
    {
        if (is_numeric($value)) {
            return $this->where('id', $value)->firstOrFail();
        } else {
            return $value === '@me' ? Auth::user()->makeVisible([ 'email' ]) : $this->where('username', $value)->firstOrFail();
        }
    }

    protected function getTotalLikesAttribute()
    {
        return $this->posts()
            ->where('visible', true)
            ->withCount('likes')
            ->get()
            ->sum('likes_count');
    }

    protected function getFollowersCountAttribute()
    {
        return $this->followers()->count();

    }

    protected function getFollowingCountAttribute()
    {
        return $this->following()->count();

    }

    protected function getFlagsAttribute()
    {
        $flags = 0;

        if ($this->id == 1) {
            $flags |= UserBadges::DEVELOPER->value;
        }

        if ($this->is_super_admin) {
            $flags |= UserBadges::STAFF->value;
        }

        if ($this->created_at->greaterThan(Carbon::now()->subWeek())) {
            $flags |= UserBadges::NEW_MEMBER->value;
        }

        return $flags;
    }

    public function scopePublic(Builder $query): void
    {
        $query->where('is_private', false);
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class);
    }

    public function posts()
    {   
        return $this->hasMany(Post::class);
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class, 'recipient_id');
    }

    public function followerRequests()
    {
        return $this->hasMany(FollowRequest::class, 'followed_id');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'followers', 'followed_id', 'follower_id');
    }

    public function following()
    {
        return $this->belongsToMany(User::class, 'followers', 'follower_id', 'followed_id');
    }

    public function isFollowedBy($userId)
    {
        return $this->followers()->where('follower_id', $userId)->exists();
    }

    public function hasPendingRequestFrom($userId)
    {
        return $this->followerRequests()->where('follower_id', $userId)->exists();
    }
}
