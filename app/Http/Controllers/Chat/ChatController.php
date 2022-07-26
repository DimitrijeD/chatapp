<?php

namespace App\Http\Controllers\Chat;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ChatRole;
use App\Cache\ChatRoleRulesCache;
use App\Models\ChatGroup;
use App\Models\ChatMessage;

class ChatController extends Controller
{
    /**
     * Provides all initial data required for chat
     */
    public function init(Request $request, ChatRoleRulesCache $chatRoleRules)
    {
        $groups = (auth()->user()->groups()->with(['participants', 'lastMessage.user']))
            ->orderBy('updated_at', 'desc')
            ->get();

        $roleTables = $chatRoleRules->getAll();

        return response()->json([
            'chat_rules' => [
                'chat_rules' => !$roleTables 
                    ? $chatRoleRules->createTableArrayOfRules() 
                    : $roleTables,
                    
                'keys' => ChatRole::ACTION_KEYS,
                'roles' => ChatRole::ROLES,
                'groupTypes' => ChatGroup::TYPES,

                'init_num_messages' => ChatMessage::INIT_NUM_MESSAGES,
                'earliest_num_messages' => ChatMessage::EARLIEST_NUM_MESSAGES,
            ],
            
            'groups' => $groups,
        ]);
    }
}
