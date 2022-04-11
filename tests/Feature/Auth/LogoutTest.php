<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->email = 'qwe@qwe';
        $this->password = 'qweqweqweQ1';

        $this->user = User::factory()->create([
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $this->withHeaders([ 'Accept' => 'application/json', ]);
        
        $this->logoutEndpoint = '/api/logout';
    }

    public function test_user_can_logout()
    {
        Auth::login($this->user);

        $response = $this->get($this->logoutEndpoint);

        $this->assertNull(auth()->user());
    }

    public function test_guest()
    {
        $response = $this->get($this->logoutEndpoint);

        $response->assertJson([
            "message" => "Unauthenticated."
        ]);
    }
}
