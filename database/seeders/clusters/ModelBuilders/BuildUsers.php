<?php

namespace Database\Seeders\clusters\ModelBuilders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BuildUsers
{
    public $participants, $numUsers, $users;

    public function __construct($participants, $numUsers)
    {
        $this->participants = $participants;
        $this->numUsers = $numUsers; 
        $this->users = collect([]);
    }

    public function resolve()
    {
        if( !($this->numUsers - count($this->participants) <= 0 ) ){
            // there is extra space for random users
            $this->users = $this->users->merge( $this->makeRemainingRandomUsers( $this->numUsers - count($this->participants) ));
        } 

        return $this;
    }

    public function build()
    {
        foreach($this->participants as $participant){
            // check if user with that email 
            if( $user = User::where('email', $participant['email'])->first() ) {
                $this->users->push( $user );
            } else {
                $this->users->push( User::factory()->create([
                    'firstName' => $participant['firstName'], 
                    'lastName' => $participant['lastName'], 
                    'email' => $participant['email'], 
                    'password' => Hash::make($participant['password']),
                ]));
            }
        }

        return $this->users;
    }

    private function makeRemainingRandomUsers($num)
    {
        if($num){
            return User::factory($num)->create();
        }
        return [];
    }

}