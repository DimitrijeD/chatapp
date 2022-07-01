<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRoleInGroupChangedEvent implements ShouldBroadcastNow // 1. must implement ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    // 2. Must set data passed via Websocket as public
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('group.' . $this->data['group_id']);
    }

    public function broadcastAs()
    {
        return 'participant.role.change';
    }
}
