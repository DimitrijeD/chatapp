<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
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

        $this->userFormData = [
            'email' => $this->email,
            'password' => $this->password,
        ];
        
        $this->loginEndpoint = '/api/login';
    }

    public function test_user_can_login()
    {
        $response = $this->post($this->loginEndpoint, $this->userFormData);

        $response
            ->assertStatus(200)
            ->assertJson([
                'email' => $this->email,
            ]);
        // dd($response->content());
    }

    public function test_incorrect_email()
    {
        $this->userFormData['email'] = $this->userFormData['email'] . 's';

        $response = $this->post($this->loginEndpoint, $this->userFormData);
        
        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => __("The provided credentials are incorrect."),
                "errors" => [
                    "email" => [__("The provided credentials are incorrect.")]
                ]
            ]); 
    }

    public function test_incorrect_password()
    {
        $this->userFormData['password'] = $this->userFormData['password'] . 's';

        $response = $this->post($this->loginEndpoint, $this->userFormData);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => __("The provided credentials are incorrect."),
                "errors" => [
                    "email" => [__("The provided credentials are incorrect.")]
                ]
            ]); 
    }

    public function test_password_case_missmatch()
    {
        $this->userFormData['password'] = ucfirst($this->userFormData['password']);

        $response = $this->post($this->loginEndpoint, $this->userFormData);

        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => __("The provided credentials are incorrect."),
                "errors" => [
                    "email" => [__("The provided credentials are incorrect.")]
                ]
            ]); 
    }


    public function test_loggedin_user_gets_redirected_to_home()
    {
        Auth::login($this->user);

        $response = $this->post($this->loginEndpoint, $this->userFormData);

        $response->assertStatus(302); 
    }
}
