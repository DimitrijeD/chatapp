<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Execute all of them by:
     *      php artisan db:seed
     * 
     * @return void
     */
    public function run()
    {
        (new ChatGroupClusterSeeder)->run();
        (new UserPopulusSeeder)     ->run();
    }
}
