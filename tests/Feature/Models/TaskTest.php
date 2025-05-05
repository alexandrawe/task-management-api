<?php

namespace Tests\Feature\Models;

use App\Models\Task;
use App\Models\User;
use App\TaskState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    /**
     * Tests retrieving a collection of tasks.
     */
    public function test_get_all_tasks(): void
    {
        $tasks = Task::factory()->count(6)->create();

        $this->withToken($this->token)
            ->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                'tasks' => [],
            ]);
    }

    /**
     * Tests fetching a task.
     */
    public function test_get_single_task(): void
    {
        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'created_by' => $this->user->id,
        ]);

        $this->withToken($this->token)
            ->getJson('/api/tasks/' . $task->id)        
            ->assertStatus(200)
            ->assertJson([
                'task' => [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'state' => TaskState::TODO->value,
                    'created_by' => $this->user->id,
                    'created_at' => $task->created_at->toISOString(),
                    'updated_at' => $task->updated_at->toISOString(),
                ],
            ]);
    }

    /**
     * Tests that a task can be successfully created.
     */
    public function test_create_task(): void
    {
        $response = $this->withToken($this->token)
            ->postJson('/api/tasks', [
                'title' => 'My first task',
                'description' => 'Lorem ipsum dolor ...',
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'task' => [],
                'message' => 'Task was successfully created.',
            ])
            ->assertJsonPath('task.title', 'My first task')
            ->assertJsonPath('task.description', 'Lorem ipsum dolor ...');
    }

    /**
     * Tests that a task can be successfully updated.
     */
    public function test_update_task(): void
    {
        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'created_by' => $this->user->id,
        ]);

        $response = $this->withToken($this->token)
            ->patchJson('/api/tasks/' . $task->id, [
                'title' => 'New title',
                'state' => TaskState::IN_PROGRESS,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'task' => [
                    'id' => $task->id,
                    'title' => 'New title',
                    'description' => $task->description,
                    'state' => TaskState::IN_PROGRESS->value,
                    'created_by' => $task->created_by,
                ],
                'message' => 'Task was successfully updated.',
            ]);
    }

    /**
     * Tests that a task can be successfully deleted.
     */
    public function test_destroy_task(): void
    {
        $task = Task::factory()->create([
            'created_by' => $this->user->id,
        ]);

        $response = $this->withToken($this->token)
            ->deleteJson('/api/tasks/' . $task->id);

        $response->assertStatus(200)
            ->assertJson([
                'task_id' => $task->id,
                'message' => 'Task was successfully deleted.',
            ]);
    }
}
