<?php

namespace Database\Seeders\clusters\Init;

use Database\Seeders\ChatGroupClusterSeeder;

class NumOfMessages 
{
    public function __construct($num = 0)
    {
        $this->num = $num;
    }

    public function get()
    {
        if($this->num) return $this->num;

        return $this->useDefault();
    }

    private function useDefault()
    {
        return rand(ChatGroupClusterSeeder::MIN_NUM_MESSAGES, ChatGroupClusterSeeder::MAX_NUM_MESSAGES);
    }

}