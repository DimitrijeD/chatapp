<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use App\Events\MeSawMessage;
use App\Events\MessageNotification;
use App\Events\NewChatMessage;

use Illuminate\Support\Facades\DB;

use App\ChatApp\Repos\User\UserEloquentRepo;
use App\ChatApp\Repos\ChatGroup\ChatGroupEloquentRepo;
use App\ChatApp\Repos\ChatMessage\ChatMessageEloquentRepo;

use App\Http\Requests\ChatMessage\StoreChatMessageRequest;
use App\Http\Requests\ChatMessage\SeenChatMessageRequest;

use Illuminate\Http\Request;

class MessageController extends Controller
{
    protected $chatGroupRepo, $userRepo, $chatMessageRepo;

    public function __construct(ChatGroupEloquentRepo $chatGroupRepo, UserEloquentRepo $userRepo, ChatMessageEloquentRepo $chatMessageRepo)
    {
        $this->chatGroupRepo = $chatGroupRepo;
        $this->userRepo = $userRepo;
        $this->chatMessageRepo = $chatMessageRepo;
    }

    public function getAllMessages(Request $request)
    {
        $messages = $this->chatMessageRepo->getMany(['group_id' => $request->group_id], ['user']);

        $seenStates = DB::table('group_participants')
            ->where('group_id', $request->group_id)
            ->get();

        return ['messages' => $messages, 'seen_states' => $seenStates]; 
    }

    public function getMissingMessages($group_id, $latestMsg)
    {
        return $this->chatMessageRepo->getMissingMessages($group_id, $latestMsg);
    }

    public function store(StoreChatMessageRequest $request)
    {
        $sender = auth()->user();

        $message = $this->chatMessageRepo->create([
            'user_id' => $sender->id,
            'group_id' => $request->group_id,
            'text' => $request->text,
        ]);

        DB::table('group_participants')
            ->where('user_id', $sender->id)
            ->where('group_id', $request->group_id)
            ->update([
                'last_message_seen_id' => $message->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // update groups field 'updated_at' to NOW in order to be able to sort users groups by 'time of last message'
        $this->chatGroupRepo->update($this->chatGroupRepo->find($request->group_id), ['updated_at' => date('Y-m-d H:i:s')]);

        broadcast(new NewChatMessage($message))->toOthers();
        $this->newChatMessageNotification($request->group_id, $sender, $message);

        return $message;
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

        $success = DB::table('group_participants')
            ->where('user_id', $user_id)
            ->where('group_id', $request->group_id)
            ->update([
                'last_message_seen_id' => $request->lastMessageId,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        if($success){
            $seenData = [
                'group_id' => $request->group_id,
                'lastMessageId' => $request->lastMessageId,
                'user_id' => $user_id
            ];
            broadcast(new MeSawMessage($seenData))->toOthers();
        }
    }

}
