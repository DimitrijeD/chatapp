<?php

namespace App\Cache;

use Illuminate\Support\Facades\Redis;
use App\Models\ChatRole;
use App\Models\ChatGroup;

class ChatRoleRulesCache 
{
    const ALL_RULES_TABLE_KEY = 'all_role_rules';

    public function __construct()
    {
        $this->chatRole = new ChatRole;
    }

    public function storeAll()
    {
        Redis::set(self::ALL_RULES_TABLE_KEY, $this->createTableArrayOfRules());
    }

    public function getAll()
    {
        return json_decode(Redis::get(self::ALL_RULES_TABLE_KEY));
    }

    public function createTableArrayOfRules()
    {
        return json_encode([
            ChatRole::ACTION_KEY_CHANGE_GROUP_NAME => $this->depth2($this->chatRole::ACTION_KEY_CHANGE_GROUP_NAME),
            ChatRole::ACTION_KEY_SEND_MESSAGE      => $this->depth2($this->chatRole::ACTION_KEY_SEND_MESSAGE),
            ChatRole::ACTION_KEY_ADD               => $this->depth3($this->chatRole::ACTION_KEY_ADD),
            ChatRole::ACTION_KEY_REMOVE            => $this->depth3($this->chatRole::ACTION_KEY_REMOVE),
            ChatRole::ACTION_KEY_CHANGE_ROLE       => $this->depth4($this->chatRole::ACTION_KEY_CHANGE_ROLE),
        ]);
    }

    private function depth2($actionKey)
    {
        $table = [];

        foreach($this->chatRole::ROLES as $initiatorRole){
            foreach(ChatGroup::TYPES as $group){

                $table[$initiatorRole][$group] = 
                    $this->chatRole::can([$initiatorRole, $group], $actionKey);
                
            }
        }

        return $table;
    }

    private function depth3($actionKey)
    {
        $table = [];

        foreach($this->chatRole::ROLES as $initiatorRole){
            foreach($this->chatRole::ROLES as $targetRole){
                foreach(ChatGroup::TYPES as $group){

                    $table[$initiatorRole][$targetRole][$group] = 
                        $this->chatRole::can([$initiatorRole, $targetRole, $group], $actionKey);
                    
                }
            }    
        }

        return $table;
    }

    private function depth4($actionKey)
    {
        $table = [];

        foreach($this->chatRole::ROLES as $initiatorRole){
            foreach($this->chatRole::ROLES as $targetRole){
                foreach(ChatGroup::TYPES as $group){
                    foreach($this->chatRole::ROLES as $newRole){

                        $table[$initiatorRole][$targetRole][$newRole][$group] = 
                            $this->chatRole::can([$initiatorRole, $targetRole, $newRole, $group], $actionKey);
                        
                    }
                }
            }    
        }

        return $table;
    }

}