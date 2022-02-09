<?php

namespace Tests\Feature\Auth\Register;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class InvalidInputTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('avatars');
        $image = UploadedFile::fake()->image('avatar.jpg');

        $this->userFormData = [
            'firstName' => 'some',
            'lastName'  => 'name',
            'email'     => 'test@test',
            'password'              => 'Passwordtest1',
            'password_confirmation' => 'Passwordtest1',
            'profilePicture' => $image,
        ];

        $this->registerRoute = '/api/register';
    }


    /**
     *  ******************************** rule: required ********************************
     */

    public function test__required__first_name()
    {
        $this->userFormData['firstName'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['firstName' => __('The first name field is required.')]);
    }

    public function test__required__last_name()
    {
        $this->userFormData['lastName'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['lastName' => __('The last name field is required.')]);
    }

    public function test__required__email()
    {
        $this->userFormData['email'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['email' => __('The email field is required.')]);
    }

    public function test__required__password()
    {
        $this->userFormData['password'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['password' => __('The password field is required.')]);
    }

    public function test__required__profile_picture()
    {
        $this->userFormData['profilePicture'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['profilePicture' => __('The profile picture field is required.')]);
    }

    public function test__required__password_confirmation()
    {
        $this->userFormData['password_confirmation'] = '';

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['password' => __('The password confirmation does not match.')]);
    }
    /**
     *  **********************************************************************************
     */

    /**
     * ******************************** rule: length ********************************
     */
    public function test__first_name__max()
    {
        $this->userFormData['firstName'] = Str::random(256);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['firstName' => __('The first name must not be greater than 255 characters.')]);
    }

    public function test__first_name__min()
    {
        $this->userFormData['firstName'] = Str::random(2);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['firstName' => __('The first name must be at least 3 characters.')]);
    }

    public function test__last_name__max()
    {
        $this->userFormData['lastName'] = Str::random(256);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['lastName' => __('The last name must not be greater than 255 characters.')]);
    }

    public function test__last_name__min()
    {
        $this->userFormData['lastName'] = Str::random(2);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['lastName' => __('The last name must be at least 3 characters.')]);
    }

    public function test__email__max()
    {
        $this->userFormData['email'] = 'w@' . Str::random(254);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['email' => __('The email must not be greater than 255 characters.')]);
    }

    public function test__email__min()
    {
        $this->userFormData['email'] = Str::random(2);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['email' => __('The email must be at least 3 characters.')]);
    }

    public function test__password__max()
    {
        $this->userFormData['password'] = Str::random(256);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['password' => __('The password must not be greater than 255 characters.')]);
    }

    public function test__password__min()
    {
        $this->userFormData['password'] = Str::random(5);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['password' => __('The password must be at least 6 characters.')]);
    }

    /**
     *  **********************************************************************************
     */

    /**
     * User with that 'email' already exists.
     */
    public function test__user_with_this_email_already_exists()
    {
        // create user
        $response = $this->post($this->registerRoute, $this->userFormData);

        // Try to create user with same email
        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['email' => __('The email has already been taken.')]);
    }
}
