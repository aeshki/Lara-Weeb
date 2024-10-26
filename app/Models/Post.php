<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'message',
        'image',
        'visible',
        'user_id',
    ];

    protected $hidden = [
        'user_id',
        'updated_at'
    ];

    protected $appends = [
        'is_liked',
        'likes_count'
    ];

    protected function casts(): array
    {
        return [
            'visible' => 'boolean'
        ];
    }

    protected function getIsLikedAttribute()
    {
        return $this->likes()->where('user_id', auth()->id())->exists();
    }

    protected function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function scopePublic(Builder $query, User $currentUser): void
    {
        $query->where('visible', true)
              ->whereHas('author', function($authorQuery) use ($currentUser) {
                  $authorQuery->where('is_private', false)
                              ->orWhereHas('followers', function($followerQuery) use ($currentUser) {
                                  $followerQuery->where('follower_id', $currentUser->id);
                              });
              });
    }

    public function author()
    {   
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {   
        return $this->hasMany(Comment::class);
    }

    public function likes()
    {   
        return $this->hasMany(PostLike::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class)->withTimestamps();
    }

    public function latestComment()
    {   
        return $this->hasOne(Comment::class)->latestOfMany();
    }
}
