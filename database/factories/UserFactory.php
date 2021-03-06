<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    const PASSWORD = '$2y$10$OzosT7AoTUDVzRfml.zozOlsxSljp4q3zVOhC5TuZvmrNl.MknU9G'; // qweqweqweQ1
    
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $pic = $this->getRandomPicRelPath();
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => self::PASSWORD,
            'remember_token' => NULL,
            'image' => $pic,
            'thumbnail' => $pic,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }

    private function getRandomPicRelPath()
    {
        $files = File::allFiles('public/basic-images');
        $randomFile = $files[rand(0, count($files) - 1)];

        return "/basic-images/" . $randomFile->getRelativePathName();
    }
}
