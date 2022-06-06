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
     * Creator is at 0-th index, therefore has highest level, 
     * while listener has index 3 and has lowest level in hierarchy.
     */
    const ROLES = [
        self::CREATOR, 
        // self::HIGH_MODERATOR, // example of adding new role, respecting hierarchy and not breaking code
        self::MODERATOR,
        self::PARTICIPANT,
        self::LISTENER,
    ];


    // --------------------------------------------------------------------------------------------------------------------
    //                                      How these nested constant arrays work                                        //
    // --------------------------------------------------------------------------------------------------------------------

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
     *    returns false if nested array doesn't contain value.
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

    const ROLE_CAN_PROMOTE_ROLE_IN_GROUP_TYPE_TO_ROLE = [
        self::CREATOR => [
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED => [
                    self::MODERATOR,
                ],
                ChatGroup::TYPE_PUBLIC_OPEN => [
                    self::MODERATOR,
                ],
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED => [
                    self::MODERATOR,
                ],
            ],
        ],

        self::MODERATOR => [
            self::PARTICIPANT => [
                ChatGroup::TYPE_PROTECTED => [
                    self::MODERATOR,
                ],
                ChatGroup::TYPE_PUBLIC_OPEN => [
                    self::MODERATOR,
                ],
            ],
            self::LISTENER => [
                ChatGroup::TYPE_PUBLIC_CLOSED => [
                    self::MODERATOR,
                ],
            ],
        ],
    ]; 

    const ROLE_CAN_DEMOTE_ROLE_TO_ROLE_IN = [
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

    public static function can($levels, $action = null)
    {
        if(!$action || !is_string($action) || !is_array($levels)) return false;

        switch(strtolower($action)){
            case 'add':
                return self::onLevel3($levels, self::ROLE_CAN_ADD_ROLE_IN);
                
            case 'remove':
                return self::onLevel3($levels, self::ROLE_CAN_REMOVE_ROLE_FROM);

            case 'promote':
                return self::onLevel4($levels, self::ROLE_CAN_PROMOTE_ROLE_IN_GROUP_TYPE_TO_ROLE);

            case 'demote':
                return self::onLevel4($levels, self::ROLE_CAN_DEMOTE_ROLE_TO_ROLE_IN);

            case 'send_message':
                return self::onLevel2($levels, self::ROLE_CAN_SEND_MESSAGE_IN);

            default:
                return false;
        }

        return false;

    }

    private static function onLevel2($levels, $rule)
    {
        if(!isset($rule[ $levels[0] ]))
            return false;

        $nested = $rule[ $levels[0] ];

        return in_array($levels[1], $nested);
    }

    private static function onLevel3($levels, $rule)
    {
        if(!isset($rule[$levels[0]] [$levels[1]]))
            return false;

        $nested = $rule[$levels[0]] [$levels[1]];

        return in_array($levels[2], $nested);
    }

    private static function onLevel4($levels, $rule)
    {
        if(!isset($rule[$levels[0]] [$levels[1]] [$levels[2]]))
            return false;

        $nested = $rule[$levels[0]] [$levels[1]] [$levels[2]];

        return in_array($levels[3], $nested);
    }

}
