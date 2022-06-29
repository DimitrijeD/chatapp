<?php

namespace App\Models;

class ChatRole
{
    const CREATOR = 'CREATOR';
    const MODERATOR = 'MODERATOR';
    const PARTICIPANT = 'PARTICIPANT';
    const LISTENER = 'LISTENER';

    /**
     * These roles are in hierarchy! 
     * 
     * Creator is at 0-th index, therefore has highest, 
     * while listener has index 3 
     * and has lowest value in hierarchy.
     */
    const ROLES = [
        self::CREATOR, 
        // self::HIGH_MODERATOR, // example of adding new role, respecting hierarchy
        self::MODERATOR,
        self::PARTICIPANT,
        self::LISTENER,
    ];

    const ACTION_KEY_ADD = 'add';
    const ACTION_KEY_REMOVE = 'remove';
    const ACTION_KEY_SEND_MESSAGE = 'send_message';
    const ACTION_KEY_CHANGE_ROLE = 'change_role';
    const ACTION_KEY_CHANGE_GROUP_NAME = 'change_group_name';

    const ACTION_KEYS = [
        self::ACTION_KEY_ADD,
        self::ACTION_KEY_REMOVE,
        self::ACTION_KEY_SEND_MESSAGE,
        self::ACTION_KEY_CHANGE_ROLE,
        self::ACTION_KEY_CHANGE_GROUP_NAME,
    ];


    // --------------------------------------------------------------------------------------------------------------------
    //                                      How these nested constant arrays work                                        //
    // --------------------------------------------------------------------------------------------------------------------

    // ----------------------After editing rules----------------------------// 
    // If you edit following const arrays ROLE_ ... run                     // 
    //                                                                      //
    // php artisan db:seed --class=ChatRoleRulesSeeder                      //
    //                                                                      //
    // in order to re-cached rules as complete table                        //
    // ---------------------------------------------------------------------//

    /**
     * First  level is role of user which is making request 
     * Second level is role of user/s which is targeted for action
     * Third  level is group type in which current action is being executed
     * 
     * **** Example:
     * User is adding another user to group. We have: 
     *      his role in group ('CREATOR'), 
     *      role he wished to add user with ('PARTICIPANT') 
     *      and group type ('TYPE_PUBLIC_OPEN').
     *  
     * If  ROLE_CAN_ADD_ROLE_IN['CREATOR']['PARTICIPANT']  contains  'TYPE_PUBLIC_OPEN' , 
     *    will return true else 
     *    returns false if nested array doesn't contain value 'TYPE_PUBLIC_OPEN'.
     * **** 
     */

    const ROLE_CAN_ADD_ROLE_IN = [
        self::CREATOR => [
            self::MODERATOR => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
        ],

        self::MODERATOR => [
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
        ],

        self::PARTICIPANT => [
            self::PARTICIPANT => [
                ChatGroup::TYPE_PUBLIC_OPEN,
                ChatGroup::TYPE_PROTECTED,
            ],
        ],

        self::LISTENER => [
            self::PARTICIPANT => [
            ],
        ],
    ]; 

    const ROLE_CAN_REMOVE_ROLE_FROM = [
        self::CREATOR => [
            self::MODERATOR => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
        ],

        self::MODERATOR => [
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED,
                ChatGroup::TYPE_PUBLIC_OPEN,
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED,
            ],
        ],

    ];

    const ROLE_CAN_SEND_MESSAGE_IN = [
        self::CREATOR => [
            ChatGroup::TYPE_PRIVATE,
            ChatGroup::TYPE_PROTECTED, 
            ChatGroup::TYPE_PUBLIC_OPEN,
            ChatGroup::TYPE_PUBLIC_CLOSED,
        ], 
        self::MODERATOR => [
            ChatGroup::TYPE_PRIVATE,
            ChatGroup::TYPE_PROTECTED, 
            ChatGroup::TYPE_PUBLIC_OPEN,
            ChatGroup::TYPE_PUBLIC_CLOSED,
        ],
        self::PARTICIPANT => [
            ChatGroup::TYPE_PRIVATE,
            ChatGroup::TYPE_PROTECTED, 
            ChatGroup::TYPE_PUBLIC_OPEN,
        ],
    ];

    const ROLE_CAN_CHANGE_ROLE_TO_ROLE_IN_GROUP_TYPE = [
        self::CREATOR => [
            self::MODERATOR => [
                self::PARTICIPANT => [
                    ChatGroup::TYPE_PROTECTED, 
                    ChatGroup::TYPE_PUBLIC_OPEN
                ],
                self::LISTENER => [
                    ChatGroup::TYPE_PUBLIC_CLOSED,
                ],
            ],
            self::PARTICIPANT => [
                self::MODERATOR => [
                    ChatGroup::TYPE_PROTECTED,
                ],
                self::MODERATOR => [
                    ChatGroup::TYPE_PUBLIC_OPEN,
                ],
            ],
            self::LISTENER => [
                self::MODERATOR => [
                    ChatGroup::TYPE_PUBLIC_CLOSED,
                ],
            ],
        ],

        self::MODERATOR => [
            self::PARTICIPANT => [
                self::MODERATOR => [
                    ChatGroup::TYPE_PROTECTED,
                ],
                self::MODERATOR => [
                    ChatGroup::TYPE_PUBLIC_OPEN,
                ],
            ],
            self::LISTENER => [
                self::MODERATOR => [
                    ChatGroup::TYPE_PUBLIC_CLOSED,
                ],
            ],
        ],
    ]; 

    const ROLE_CAN_CHANGE_GROUP_NAME = [
        self::CREATOR => [
            ChatGroup::TYPE_PUBLIC_CLOSED, 
            ChatGroup::TYPE_PUBLIC_OPEN, 
            ChatGroup::TYPE_PROTECTED,
            ChatGroup::TYPE_PRIVATE,
        ],
        self::MODERATOR => [
            ChatGroup::TYPE_PUBLIC_CLOSED, 
            ChatGroup::TYPE_PUBLIC_OPEN,
            ChatGroup::TYPE_PRIVATE,
        ],
        self::PARTICIPANT => [
            ChatGroup::TYPE_PRIVATE,
        ],
        self::LISTENER => []
    ];

    public static function can($levels, $action = null)
    {
        if(!$action || !is_string($action) || !is_array($levels)) return false;

        switch(strtolower($action)){
            case self::ACTION_KEY_ADD:
                return self::onLevel3($levels, self::ROLE_CAN_ADD_ROLE_IN);
                
            case self::ACTION_KEY_REMOVE:
                return self::onLevel3($levels, self::ROLE_CAN_REMOVE_ROLE_FROM);

            case self::ACTION_KEY_CHANGE_ROLE:
                return self::onLevel4($levels, self::ROLE_CAN_CHANGE_ROLE_TO_ROLE_IN_GROUP_TYPE);

            case self::ACTION_KEY_SEND_MESSAGE:
                return self::onLevel2($levels, self::ROLE_CAN_SEND_MESSAGE_IN);

            case self::ACTION_KEY_CHANGE_GROUP_NAME:
                return self::onLevel2($levels, self::ROLE_CAN_CHANGE_GROUP_NAME);

            default:
                return false;
        }

        return false;
    }

    /**
     * OnLevel2, OnLevel3 ... means how deep given rule array is. 
     * 
     * $levels = [
     *      'role making request', 
     *      'group type', 
     * ]
     */
    private static function onLevel2($levels, $rule)
    {
        if(!isset($rule[ $levels[0] ]))
            return false;

        $nested = $rule[ $levels[0] ];

        return in_array($levels[1], $nested);
    }

    /**
     * $levels = [
     *      'role making request', 
     *      'role of user on which action is done', 
     *      'group type', 
     * ]
     */
    private static function onLevel3($levels, $rule)
    {
        if(!isset($rule[$levels[0]] [$levels[1]]))
            return false;

        $nested = $rule[$levels[0]] [$levels[1]];

        return in_array($levels[2], $nested);
    }

    /**
     * $levels = [
     *      'role making request', 
     *      'role of user on which action is done', 
     *      'role to which promote/demote', 
     *      'group type', 
     * ]
     */
    private static function onLevel4($levels, $rule)
    {
        if(!isset($rule[$levels[0]] [$levels[1]] [$levels[2]]))
            return false;

        $nested = $rule[$levels[0]] [$levels[1]] [$levels[2]];
                
        return in_array($levels[3], $nested);
    }

}
