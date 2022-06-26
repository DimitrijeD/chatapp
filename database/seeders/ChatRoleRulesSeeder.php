<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Cache\ChatRoleRulesCache;

class ChatRoleRulesSeeder extends Seeder
{
    /**
     * Run this seeder every time you change values in \App\Models\ChatRole rule arrays.
     *
     * @return void
     */
    public function run()
    {
        (new ChatRoleRulesCache)->storeAll();
    }
}
