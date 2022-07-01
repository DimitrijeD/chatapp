<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\ChatMessage;

class NewChatMessage implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $chatMessage;

    public function __construct(ChatMessage $chatMessage)
    {
        $this->chatMessage = $chatMessage;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('group.' . $this->chatMessage->group_id);
    }

    public function broadcastAs() {
        return 'message.new';
    }
}
