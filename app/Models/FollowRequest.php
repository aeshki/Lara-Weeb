<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FollowRequest extends Model
{
    protected $with = [
        'follower',
        'followed'
    ];

    protected $fillable = [
        'follower_id',
        'followed_id',
    ];

    public function follower()
    {
        return $this->belongsTo(User::class, 'follower_id');
    }

    public function followed()
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
