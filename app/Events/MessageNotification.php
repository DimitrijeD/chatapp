<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $messageNotification;

    public function __construct($messageNotification)
    {
        $this->messageNotification = $messageNotification;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('App.Models.User.' . $this->messageNotification['forUserId']);
    }

    public function broadcastAs() {
        return 'message.notification';
    }
}
