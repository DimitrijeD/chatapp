<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Events\MeSawMessage;
use App\Events\MessageNotification;
use App\Events\NewChatMessage;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;
use App\ChatApp\Repos\ParticipantPivot\ParticipantPivotEloquentRepo;

use App\Http\Requests\ChatMessage\StoreChatMessageRequest;
use App\Http\Requests\ChatMessage\SeenChatMessageRequest;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $chatGroupRepo, $userRepo, $chatMessageRepo, $participantPivot;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo, UserEloquentRepo $userRepo, ChatMessageEloquentRepo $chatMessageRepo, ParticipantPivotEloquentRepo $participantPivot)
    {
        $this->chatGroupRepo = $chatGroupRepo;
        $this->userRepo = $userRepo;
        $this->chatMessageRepo = $chatMessageRepo;
        $this->participantPivot = $participantPivot;
    }

    public function getMissingMessages($group_id, $latest_msg_id)
    {
        return $this->chatMessageRepo->getMissingMessages($group_id, $latest_msg_id);
    }

    public function store(StoreChatMessageRequest $request)
    {
        $sender = auth()->user();

        $message = $this->chatMessageRepo->create([
            'user_id' => $sender->id,
            'group_id' => $request->group_id,
            'text' => $request->text,
        ]);

        $pivot = $this->participantPivot->first([
            'user_id' => $sender->id,
            'group_id' => $request->group_id
        ]);

        $this->participantPivot->update($pivot, [
            'last_message_seen_id' => $message->id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        // update groups field 'updated_at' to NOW in order to be able to sort users groups by 'time of last message'
        $this->chatGroupRepo->update(
            $this->chatGroupRepo->find($request->group_id), 
            ['updated_at' => date('Y-m-d H:i:s'), 'last_msg_id' => $message->id]
        );

        $message->user;
        
        broadcast(new NewChatMessage($message))->toOthers();
        $this->newChatMessageNotification($request->group_id, $sender, $message);

        return response()->json($message, 201);
    }

    /**
     * THIS REQUIRES COMPLETE REFACTOR
     * -- when user unlocks chat, he should auto listen for new messages in all chats.
     */
    private function newChatMessageNotification($group_id, $sender, $newMessage)
    {
        $group = $this->chatGroupRepo->find($group_id);
        $participants = $group->participants->where('id', '!=' , $sender->id);

        foreach ($participants as $participant){
            $messageNotification = [
                'group_id' => $group_id,
                'sender' => $sender,
                'created_at' => $newMessage->created_at,
                'forUserId' => $participant->id ,
            ];
            broadcast(new MessageNotification($messageNotification))->toOthers();
        }
    }

    public function messageIsSeen(SeenChatMessageRequest $request)
    {
        $user_id = auth()->user()->id;

        $pivot = $this->participantPivot->first([
            'user_id' => $user_id,
            'group_id' => $request->group_id
        ]);

        $updatedPivot = $this->participantPivot->update($pivot, [
            'last_message_seen_id' => $request->lastMessageId,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        if($updatedPivot){
            broadcast(new MeSawMessage($updatedPivot))->toOthers();
            return response()->json('success', 200);
        }
        
        return response()->json('error', 400);
    }

    public function getLatestMessages(Request $request)
    {
        return $this->chatMessageRepo->getLatestMessages($request->group_id);
    }

    public function getBeforeMessage($group_id, $earliest_msg_id)
    {
        return $this->chatMessageRepo->getBeforeMessage($group_id, $earliest_msg_id);
    }
}
