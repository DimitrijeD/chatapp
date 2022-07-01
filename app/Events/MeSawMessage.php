<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MeSawMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $seenData;

    public function __construct($seenData)
    {
        $this->seenData = $seenData;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('group.' . $this->seenData['group_id']);
    }

    public function broadcastAs()
    {
        return 'message.seen';
    }
}
