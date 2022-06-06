<?php

namespace Tests\Feature\Chat\Group;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\ChatGroup;
use Illuminate\Support\Str;

class CreateChatGroupTest extends TestCase
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $chatParticipant = User::factory()->create();

        Auth::login($this->user);

        $this->withHeaders([
            'Accept' => 'application/json',
        ]);

        $this->userFormData = [
            'name' => 'some',
            'model_type' => ChatGroup::TYPE_PUBLIC_OPEN,
            'users' => [$this->user, $chatParticipant],
        ];

        $this->storeGroupEndpoint = '/api/chat/group/store';
    }

    public function test_create_group_successfully()
    {
        $response = $this->post($this->storeGroupEndpoint, $this->userFormData);

        $response->assertStatus(201);
    }

    public function test_name_too_long()
    {
        $this->userFormData['name'] = Str::random(270);

        $response = $this->post($this->storeGroupEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "name" => [__("The name must not be greater than 255 characters.")]
            ],
        ]);
    }

    public function test_name_model_type_invalid()
    {
        $this->userFormData['model_type'] = Str::random(10);

        $response = $this->post($this->storeGroupEndpoint, $this->userFormData);

        $response->assertStatus(422)->assertJson([
            "errors" => [
                "model_type" => [__("Group type is not available.")]
            ],
        ]);
    }

}