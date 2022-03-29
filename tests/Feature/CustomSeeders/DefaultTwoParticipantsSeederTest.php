<?php

namespace Tests\Feature\CustomSeeders;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Database\Seeders\ChatGroupClusterSeeder;
use App\Models\ChatGroup;

class DefaultTwoParticipantsSeederTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // php artisan db:seed --class=ChatGroupClusterSeeder
        $this->chatGroup = (resolve(ChatGroupClusterSeeder::class))->run();
    }

    public function test_example()
    {
        
    }
}
