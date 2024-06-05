<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProxyCheckingTest extends TestCase
{
    use RefreshDatabase;

    public function test_task_create(): void
    {

        $hosts = ['85.214.56.195:51802', '27.147.28.73:8080'];

        $response = $this
            ->postJson('/api/task', ['hosts' => $hosts]);
        $response->assertStatus(201);

        $task = Task::first();
        $this->assertNotNull($task);
        $this->assertNotNull($task->hosts);
        $this->assertEquals(count($task->hosts), 2);

        $response = $this
            ->getJson('/api/task');
        $response->assertOk();

        $response = $this
            ->getJson('/api/task/1/hosts/completed');
        $response->assertOk();

    }
}
