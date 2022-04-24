<?php

namespace Database\Seeders\clusters\Init;

class GroupCreatorId 
{
    public function __construct($users, $email)
    {
        $this->users = $users;
        $this->email = $email;
    }

    public function get()
    {
        if(!$this->users->count()) dd("FATAL - Namespace Database\Seeders\clusters\Init- No users were created. ");

        $owner = $this->users->where('email', $this->email)->first();

        if(!$owner) return $this->useDefault($this->users);

        return $owner->id;
    }

    private function useDefault($users)
    {
        return $users->first()->id;
    }

}