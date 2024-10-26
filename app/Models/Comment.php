<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $with = [ 'author' ];

    protected $fillable = [
        'message',
        'user_id',
        'post_id'
    ];

    protected $hidden = [
        'user_id',
        'post_id'
    ];

    public function author()
    {   
        return $this->belongsTo(User::class, 'user_id');
    }

    public function post()
    {
        return $this->belongsToMany(Post::class);
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
