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

class ChatController extends Controller
{
    // returns all messages for specific room with users and their pivot 'state'
    public function getAllMessages(Request $request, $groupId)
    {
        $messages = ChatMessage::
            where('chat_group_id', $groupId)
            ->with('user')
            ->get();

        $seenStates = DB::table('group_participants')
            ->where('chat_group_id', $groupId)
            ->get();

        return ['messages' => $messages, 'seen_states' => $seenStates]; 
    }

    public function getMissingMessages($groupId, $latestMsg)
    {
        return ChatMessage::
              where('chat_group_id', $groupId)
            ->where('id', '>', $latestMsg)
            ->with('user')
            ->get();
    }

    public function saveNewMessage(Request $request, $groupId)
    {
        $sender = Auth::user();
        $newMessage = new ChatMessage;
        $newMessage->user_id = $sender->id;
        $newMessage->chat_group_id = $groupId;
        $newMessage->text = $request->text;
        $newMessage->save();

        DB::table('group_participants')
            ->where('user_id', $sender->id)
            ->where('chat_group_id', $groupId)
            ->update([
                'last_message_seen_id' => $newMessage->id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        // update groups field 'updated_at' to NOW in order to be able to sort users groups by 'time of last message'
        $group = ChatGroup::find($groupId);
        $group->updated_at = date('Y-m-d H:i:s');
        $group->save();

        broadcast(new NewChatMessage($newMessage))->toOthers();
        $this->newChatMessageNotification($groupId, $sender, $newMessage);

        return $newMessage;
    }

    public function newChatMessageNotification($groupId, $sender, $newMessage)
    {
        $group = ChatGroup::find($groupId);
        $participants = $this->getParticipantsWithoutSelf($group, $sender->id);

        // foreach participant send notification
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
