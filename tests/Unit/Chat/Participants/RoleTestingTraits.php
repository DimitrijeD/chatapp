<?php

namespace Tests\Unit\Chat\Participants;

use App\Models\ChatGroup;

trait RoleTestingTraits
{
    private function doTest($roleMakingRequest, $secondLevel)
    {
        if(!$this->assertLeavesInNodeExist($roleMakingRequest, $secondLevel)){
            $this->assertTrue(
                $this->noGroupsInLevel($secondLevel)
            );
        }
    }

    private function keyChecker($array, $key)
    {
        return isset($array[$key]) ? $array[$key] : [];
    }

    private function noGroupsInLevel($secondLevel)
    {
        if(!$secondLevel) return true;
        if(empty($secondLevel)) return true;

        foreach($secondLevel as $thirdLevel){
            if($this->ifArrayContainsAnyGroup($thirdLevel)) return false;

            foreach($thirdLevel as $fourthLevel){
                if($this->ifArrayContainsAnyGroup($fourthLevel)) return false;
            }
        }

        return true;
    }

    private function ifArrayContainsAnyGroup($array = [])
    {
        foreach($array as $key => $value){
            if(in_array($key, ChatGroup::TYPES) || in_array($value, ChatGroup::TYPES))
                return true;
        }

        return false;
    }


}