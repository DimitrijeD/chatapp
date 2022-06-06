<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class GetUserVerifiedAndLoggedInTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeaders([ 'Accept' => 'application/json', ]);        
        $this->getAuthUserEndpoint = "/api/get-user";
    }

    /**
     * Requires user has email verified. 
     */
    public function test_gets_loggedin_verified_user()
    {
        $this->user = User::factory()->create( ['email_verified_at' => now()] );
        Auth::login($this->user);

        $response = $this->get($this->getAuthUserEndpoint);

        $response->assertJson([
            'user' => [
                'id' => $this->user->id,
                'first_name' => $this->user->first_name,
                'last_name'  => $this->user->last_name,
                'email_verified_at' => $this->user->email_verified_at->jsonSerialize(),
                'updated_at'        => $this->user->updated_at->jsonSerialize(),
                'created_at'        => $this->user->created_at->jsonSerialize(),
            ]
        ]);
    }

    /**
     * If user hasn't verified email, returns 401. 
     */
    public function test_if_not_verified_return_info()
    {
        $this->user = User::factory()->create( ['email_verified_at' => null] );
        Auth::login($this->user);

        $response = $this->get($this->getAuthUserEndpoint);

        $response->assertJson([
            "status" => "must_verify_email",
            "email" => $this->user->email,
        ]);
    }

    /**
     * If user hasn't verified email, returns 401. 
     */
    public function test_guest_401()
    {
        $response = $this->get($this->getAuthUserEndpoint);

        $response->assertStatus(401);
    }
}
