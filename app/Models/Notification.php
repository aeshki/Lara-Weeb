<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'author_id',
        'recipient_id',
        'read',
        'notifiable_type',
        'notifiable_id'
    ];

    protected function casts(): array
    {
        return [
            'read' => 'boolean'
        ];
    }

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
