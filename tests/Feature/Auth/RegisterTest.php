<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('avatars');
        $image = UploadedFile::fake()->image('avatar.jpg');

        $this->userFormData = [
            'first_name' => 'Qwe',
            'last_name' => 'Qwe',
            'email' => 'qwe@qwe',
            'password' => 'qweqweqweQ1',
            'password_confirmation' => 'qweqweqweQ1',
            'profilePicture' => $image
        ];

        $this->withHeaders([ 'Accept' => 'application/json', ]);
        
        $this->registerEndpoint = '/api/register';
    }

    public function test_user_stored()
    {
        $this->post($this->registerEndpoint, $this->userFormData);

        $this->assertDatabaseHas('users', [
            'email' => $this->userFormData['email'],
        ]);
    }

    public function test_user_can_register()
    {
        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['success' => 'Your account has been created and verification email has been sent. Check your inbox.']);
    }

    public function test__required__first_name()
    {
        $this->userFormData['first_name'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson([ 'errors' => ['first_name' => [__('The first name field is required.')]]]);
    }

    public function test__required__last_name()
    {
        $this->userFormData['last_name'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson([ 'errors' => ['last_name' => [__('The last name field is required.')]]]);
    }

    public function test__required__email()
    {
        $this->userFormData['email'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson([ 'errors' => ['email' => [__('The email field is required.')]]]);
    }

    public function test__required__password()
    {
        $this->userFormData['password'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson([ 'errors' => ['password' => [__('The password field is required.')]]]);
    }

    public function test__required__profile_picture()
    {
        $this->userFormData['profilePicture'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson([ 'errors' => ['profilePicture' => [__('The profile picture field is required.')]]]);
    }

    public function test__required__password_confirmation()
    {
        $this->userFormData['password_confirmation'] = '';

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['password' => [__('The password confirmation does not match.')]]]);
    }

    public function test__first_name__max()
    {
        $this->userFormData['first_name'] = Str::random(256);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['first_name' => [__('The first name must not be greater than 255 characters.')]]]);
    }

    public function test__first_name__min()
    {
        $this->userFormData['first_name'] = Str::random(2);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['first_name' => [__('The first name must be at least 3 characters.')]]]);
    }

    public function test__last_name__max()
    {
        $this->userFormData['last_name'] = Str::random(256);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['last_name' => [__('The last name must not be greater than 255 characters.')]]]);
    }

    public function test__last_name__min()
    {
        $this->userFormData['last_name'] = Str::random(2);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['last_name' => [__('The last name must be at least 3 characters.')]]]);
    }

    public function test__email__max()
    {
        $this->userFormData['email'] = 'w@' . Str::random(254);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['email' => [__('The email must not be greater than 255 characters.')]]]);
    }

    public function test__email__min()
    {
        $this->userFormData['email'] = Str::random(2);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['email' => [__('The email must be at least 3 characters.')]]]);
    }

    public function test__password__max()
    {
        $this->userFormData['password'] = Str::random(256);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['password' => [__('The password must not be greater than 255 characters.')]]]);
    }

    public function test__password__min()
    {
        $this->userFormData['password'] = Str::random(5);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['password' => [__('The password must be at least 6 characters.')]]]);
    }

    public function test__user_with_this_email_already_exists()
    {
        User::factory()->create(['email' => $this->userFormData['email']]);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['email' => [__('The email has already been taken.')]]]);
    }

    public function test_image_can_be_jpg()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpg')->size(5120);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertStatus(201);
    }

    public function test_image_can_be_png()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.png')->size(5120);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertStatus(201);
    }

    public function test_image_can_be_jpeg()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpeg')->size(5120);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertStatus(201);
    }

    public function test_image_max_size()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpeg')->size(5121);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertJson(['errors' => ['profilePicture' => [__('The profile picture must not be greater than 5120 kilobytes.')]]]);
    }

    public function test_logged_in_user_cannot_register()
    {
        $user = User::factory()->create([
            'email' => $this->userFormData['email'],
            'password' => Hash::make($this->userFormData['password']),
        ]);
        Auth::login($user);

        $response = $this->post($this->registerEndpoint, $this->userFormData);

        $response->assertStatus(302);
    }
}
