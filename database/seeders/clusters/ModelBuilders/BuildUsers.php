<?php

namespace Database\Seeders\clusters\ModelBuilders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Database\Factories\UserFactory;

class BuildUsers
{
    public $participants, $numUsers, $users;

    public function __construct($participants = [], $numUsers = 2)
    {
        $this->participants = count($participants) > 1 ? $participants : $this->useDefault();
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
                    'first_name' => $participant['first_name'], 
                    'last_name' => $participant['last_name'], 
                    'email' => $participant['email'], 
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

    private function useDefault()
    {
        return [
            [
                'first_name' => 'Qwe',
                'last_name' => 'Qwe',
                'email' => 'qwe@qwe', 
            ],
            [
                'first_name' => 'Asd',
                'last_name' => 'Asd',
                'email' => 'asd@asd', 
            ],
        ];
    }

}