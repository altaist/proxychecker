<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProxyCheckingTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_create(): void
    {
        $user = User::factory()->create();

        $hosts = ['85.214.56.195:51802', '27.147.28.73:8080'];

        $response = $this
            ->actingAs($user)
            ->postJson('/api/task', [ 'hosts' => $hosts]);
        $response->assertOk();

        
    }

}
