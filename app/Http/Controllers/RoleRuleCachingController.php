<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChatRole;
use App\Cache\ChatRoleRulesCache;
use App\Models\ChatGroup;

class RoleRuleCachingController extends Controller
{
    public function setAllRules(ChatRoleRulesCache $chatRoleRules)
    {
        $chatRoleRules->storeAll();
        
        return response()->json(['success' => __("Role rules successfully cached.")]);
    }   
    
    /**
     * If caching rules doesn't work, it will fallback to create rules from definition from \App\Models\ChatRole arrays
     */
    public function getAllRules(Request $request, ChatRoleRulesCache $chatRoleRules)
    {
        $roleTables = $chatRoleRules->getAll();

        return response()->json([
            'chat_rules' => !$roleTables 
                ? $chatRoleRules->createTableArrayOfRules() 
                : $roleTables, 
            'keys' => ChatRole::ACTION_KEYS,
            'roles' => ChatRole::ROLES,
            'groupTypes' => ChatGroup::TYPES
        ]);
    }
}
