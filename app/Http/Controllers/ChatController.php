<?php

namespace App\Http\Controllers;

use App\Events\MeSawMessage;
use App\Events\MessageNotification;
use App\Events\NewChatMessage;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;

class ChatController extends Controller
{
    protected $chatGroupRepo, $userRepo, $chatMessageRepo;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo, UserEloquentRepo $userRepo, ChatMessageEloquentRepo $chatMessageRepo)
    {
        $this->chatGroupRepo = $chatGroupRepo;
        $this->userRepo = $userRepo;
        $this->chatMessageRepo = $chatMessageRepo;
    }

    public function getAllMessages($groupId)
    {
        $messages = $this->chatMessageRepo->getMany(['chat_group_id' => $groupId], ['user']);

        $seenStates = DB::table('group_participants')
            ->where('chat_group_id', $groupId)
            ->get();

        return ['messages' => $messages, 'seen_states' => $seenStates]; 
    }

    public function getMissingMessages($groupId, $latestMsg)
    {
        return $this->chatMessageRepo->getMissingMessages($groupId, $latestMsg);
    }

    public function store(Request $request, $groupId)
    {
        $sender = auth()->user();

        $message = $this->chatMessageRepo->create([
            'user_id' => $sender->id,
            'chat_group_id' => $groupId,
            'text' => $request->text,
        ]);

        DB::table('group_participants')
            ->where('user_id', $sender->id)
            ->where('chat_group_id', $groupId)
            ->update([
                'last_message_seen_id' => $message->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // update groups field 'updated_at' to NOW in order to be able to sort users groups by 'time of last message'
        $this->chatGroupRepo->update($this->chatGroupRepo->find($groupId), ['updated_at' => date('Y-m-d H:i:s')]);

        broadcast(new NewChatMessage($message))->toOthers();
        $this->newChatMessageNotification($groupId, $sender, $message);

        return $message;
    }

    public function newChatMessageNotification($groupId, $sender, $newMessage)
    {
        $group = $this->chatGroupRepo->find($groupId);
        $participants = $group->participants->where('id', '!=' , $sender->id);

        foreach ($participants as $participant){
            $messageNotification = [
                'groupId' => $groupId,
                'sender' => $sender,
                'created_at' => $newMessage->created_at,
                'forUserId' => $participant->id ,
            ];
            broadcast(new MessageNotification($messageNotification))->toOthers();
        }
        return $messageNotification;
    }

    public function messageIsSeen(Request $request)
    {
        $groupId = $request->groupId;
        $lastMessageId = $request->lastMessageId;
        $selfId = $request->selfId;

        $success = DB::table('group_participants')
            ->where('user_id', $selfId)
            ->where('chat_group_id', $groupId)
            ->update([
                'last_message_seen_id' => $lastMessageId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if($success){
            $seenData = [
                'groupId' => $groupId,
                'lastMessageId' => $lastMessageId,
                'selfId' => $selfId
            ];
            broadcast(new MeSawMessage($seenData))->toOthers();
        }
    }

    public function getAllUsersExceptSelf()
    {
        return User::all()->except(Auth::id());
    }

    public function getParticipantsWithoutSelf(ChatGroup $group, $selfUserId)
    {
        return $group->participants->where('id', '!=' , $selfUserId);
    }

}
