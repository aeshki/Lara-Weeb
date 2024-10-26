<?php

namespace App\Events;

use App\Models\Notification;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Notification $notification,
    ) {}

    public function broadcastAs(): string
    {
        return 'notification.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->notification->id,
            'read' =>  $this->notification->read,
            'type' =>  $this->notification->type,
            'created_at' =>  $this->notification->created_at,
            'author' =>  $this->notification->author,
            'notifiable' => $this->notification->notifiable
        ];
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user.'.$this->notification->recipient_id.'.notifications'),
        ];
    }
}
