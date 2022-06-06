<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Support\Str;

class GetUsersByStrTrimByIdsTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->otherUsers = User::factory(10)->create();

        $same_first_name = 'Qwer';

        $this->user_one = User::factory()->create([ 
            'first_name' => $same_first_name,
            'last_name'  => 'Asfd',
        ]);

        $this->user_two = User::factory()->create([
            'first_name' => $same_first_name,
            'last_name'  => 'Zxcvb'
        ]);

        $this->userStructure = ['id', 'first_name', 'last_name', 'email', 'thumbnail', 'image'];

        Auth::login($this->user);
        $this->withHeaders([ 'Accept' => 'application/json' ]);
        
        $this->getUsersEndpoint = "/api/users/search";
    }

    private function dataExpander($user)
    {
        return [
            'id' =>         $user->id,
            'first_name' => $user->first_name,
            'last_name' =>  $user->last_name,
            'email' =>      $user->email,
            'thumbnail' =>  $user->thumbnail,
            'image' =>      $user->image,
        ];
    }

    public function test_gets_user_one_by_full_name()
    {
        $userInput = [
            'i_have_ids' => [],
            'search_str' => $this->user_one->fullName()
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJson([ $this->dataExpander($this->user_one) ]);
    }

    public function test_gets__user_one_and_two__because_they_have_same_first_name()
    {
        $userInput = [
            'i_have_ids' => [],
            'search_str' => $this->user_one->first_name
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJson([ $this->dataExpander($this->user_one), $this->dataExpander($this->user_two) ]);
    }

    public function test_gets__user_one_and_two__because_they_have_same_last_name()
    {
        $same_last_name = 'Qwer';

        $user_one = User::factory()->create([
            'first_name' => 'Skdal',
            'last_name'  => $same_last_name,
        ]);

        $user_two = User::factory()->create([
            'first_name' => 'asjdbd',
            'last_name'  => $same_last_name
        ]);

        $userInput = [
            'i_have_ids' => [],
            'search_str' => $user_one->last_name
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        // at least 2 users
        $response->assertJsonStructure([$this->userStructure, $this->userStructure]);
    }

    public function test_doesnt_get__user_one_and_two__because_their_ids_are_in_input()
    {
        $userInput = [
            'i_have_ids' => [$this->user_one->id, $this->user_two->id],
            'search_str' => $this->user_one->first_name
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJsonMissing([ $this->dataExpander($this->user_one), $this->dataExpander($this->user_two) ]);
    }

    public function test_doesnt_get__user_because_his_id_is_in_input()
    {
        $userInput = [
            'i_have_ids' => [$this->user_one->id],
            'search_str' => $this->user_one->email
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJsonMissing([ $this->dataExpander($this->user_one) ]);
    }

    public function test_get__user_by_email()
    {
        $userInput = [
            'i_have_ids' => [],
            'search_str' => $this->user_one->email
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);
        
        $response->assertJson([ $this->dataExpander($this->user_one) ]);
    }

    public function test_will_not_return_user_which_hasnt_yet_verified_email()
    {
        $user_three = User::factory()->create([ 
            'email_verified_at' => null
        ]);

        $userInput = [
            'i_have_ids' => [],
            'search_str' => $user_three->email
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJsonMissing([ $this->dataExpander($user_three) ]);
    }

    public function test_get__nothing_on_bad_input()
    {
        $userInput = [
            'i_have_ids' => [],
            'search_str' => "jsnkdjsldfn ksjdnfksj nflsdnfl ksdlfk nsldkfnslkdnf "
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJson( [] );
    }

    // xd
    public function test_get__nothing_if_contains_all_users()
    {
        $userInput = [
            'i_have_ids' => User::all()->pluck('id'),
            'search_str' => "asasdas"
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJson( [] );
    }

    public function test_array_of_ids_must_contain_only_ints()
    {
        $userInput = [
            'i_have_ids' => [$notInt = 'STRING, FAIL VALIDATION'],
            'search_str' => 'whatever'
        ];

        $response = $this->post($this->getUsersEndpoint, $userInput);

        $response->assertJson([
            "message" => __("The given data was invalid."),
            "errors" => [ 
                "i_have_ids" => [__("User ID-s provided in array must be numeric, '{$notInt}' given.")] 
            ]
        ]);
    }

}
