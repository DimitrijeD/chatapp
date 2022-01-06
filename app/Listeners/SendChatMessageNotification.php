<?php

namespace App\Listeners;

use App\Events\NewChatMessage;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\InteractsWithQueue;

class SendChatMessageNotification extends Notification implements ShouldBroadcastNow
{

    public function __construct()
    {
        //
    }

/*    public function handle(NewChatMessage $event)
    {
        //
    }*/
}
