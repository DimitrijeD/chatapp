<?php

namespace Tests\Feature\Auth\Register;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ValidInputTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('avatars');
        $image = UploadedFile::fake()->image('avatar.jpg');

        $this->userFormData = [
            'first_name' => 'some',
            'last_name'  => 'name',
            'email'     => 'test@test',
            'password'              => 'Passwordtest1',
            'password_confirmation' => 'Passwordtest1',
            'profilePicture' => $image,
        ];

        $this->registerRoute = '/api/register';

    }

    /**
     * Can new user be registered after providing valid data.
     */
    public function test_user_can_be_registered()
    {
        $this->post($this->registerRoute, $this->userFormData);

        $this->assertDatabaseHas('users', [
            'email' => $this->userFormData['email'],
        ]);
    }
}
