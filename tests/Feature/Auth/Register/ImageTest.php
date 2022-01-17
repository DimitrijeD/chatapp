<?php

namespace Tests\Feature\Auth\Register;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('avatars');

        $this->userFormData = [
            'firstName' => 'some',
            'lastName'  => 'name',
            'email'     => 'test@test',
            'password'              => 'Passwordtest1',
            'password_confirmation' => 'Passwordtest1',
            'profilePicture' => null,
        ];

        $this->registerRoute = '/api/register';
    }

    public function test_image_can_be_jpg()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpg')->size(5120);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertOk();
    }

    public function test_image_can_be_png()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.png')->size(5120);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertOk();
    }

    public function test_image_can_be_jpeg()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpeg')->size(5120);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertOk();
    }

    public function test_image_max_size()
    {
        $this->userFormData['profilePicture'] = UploadedFile::fake()->image('avatar.jpeg')->size(5121);

        $response = $this->post($this->registerRoute, $this->userFormData);

        $response->assertSessionHasErrors(['profilePicture' => __('The profile picture must not be greater than 5120 kilobytes.')]);
    }
}
