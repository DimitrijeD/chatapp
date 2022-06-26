<?php

namespace Tests\Feature\Chat\GroupRules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SetRoleRulesInCacheTest extends TestCase
{
    public function test_set_all_rules()
    {
        $response = $this->get('/api/chat/role-rules/set');
        
        $response->assertJson([
            'success' => __("Role rules successfully cached.")
        ]);
    }

    public function test_get_all_rules()
    {
        $response = $this->get('/api/chat/role-rules/get');

        $response->assertStatus(200);
    }
}
