<?php

namespace Tests\Feature\Models;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskOverdue;
use App\Enum\TaskState;
use App\Models\Project;
use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $token;

    public function setUp(): void
    {
        parent::setUp();

        $userRole = Role::factory()->create([
            'name' => 'user',
        ]);

        $this->user = User::factory()->create([
            'role_id' => $userRole->id,
        ]);
        $this->token = $this->user->createToken('api-token')->plainTextToken;
    }

    /**
     * Tests retrieving a collection of tasks.
     */
    public function test_get_all_tasks(): void
    {
        Task::factory()->count(6)->create();

        $this->withToken($this->token)
            ->getJson('/api/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                'tasks' => [
                    'current_page',
                    'data' => [
                        [
                            'id',
                            'title',
                            'description',
                            'deadline',
                            'user_id',
                            'state',
                            'created_by',
                            'project_id',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    /**
     * Tests getting overdue tasks.
     */
    public function test_get_all_open_overdue_tasks(): void
    {

        Task::factory()->count(10)->create([
            'deadline' => now()->subDays(1),
            'state' => TaskState::TODO,
        ]);

        $this->withToken($this->token)
            ->getJson('/api/tasks/overdue')
            ->assertJsonPath('tasks.total', 10)
            ->assertJsonStructure([
                'tasks' => [
                    'current_page',
                    'data' => [
                        [
                            'id',
                            'title',
                            'description',
                            'deadline',
                            'user_id',
                            'state',
                            'created_by',
                            'project_id',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
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
                    'deadline' => $task->deadline,
                    'user_id' => $task->user_id,
                    'state' => TaskState::TODO->value,
                    'created_by' => $this->user->id,
                    'project_id' => $task->project_id,
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
                'description' => 'new description',
                'state' => TaskState::IN_PROGRESS,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'task' => [
                    'id' => $task->id,
                    'title' => 'New title',
                    'description' => 'new description',
                    'deadline' => $task->deadline,
                    'user_id' => $task->user_id,
                    'state' => TaskState::IN_PROGRESS->value,
                    'created_by' => $task->created_by,
                    'project_id' => $task->project_id,
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

    /**
     * Tests relationship between project and tasks
     */
    public function test_project_has_many_tasks(): void
    {
        $project = Project::factory()->create();

        $tasks = Task::factory()
            ->count(8)
            ->create([
                'project_id' => $project->id,
            ]);

        $this->assertEquals($project->tasks()->count(), 8);
        $this->assertTrue($project->tasks()->get()->contains($tasks->first()));
    }

        /**
     * Tests relationship between task and a project
     */
    public function test_task_belongs_to_a_project(): void
    {
        $project = Project::factory()->create();

        $task = Task::factory()->create([
            'project_id' => $project->id,
        ]);

        $this->assertEquals($task->project()->count(), 1);
        $this->assertInstanceOf(Project::class, $task->project);
    }

    /**
     * Tests relationship between user and tasks
     */
    public function test_user_has_many_tasks(): void
    {
        $user = User::factory()->create();

        $tasks = Task::factory()
            ->count(10)
            ->create([
                'user_id' => $user->id
            ]);

        $this->assertEquals($user->tasks()->count(), 10);
        $this->assertTrue($user->tasks()->get()->contains($tasks->first()));
    }

    /**
     * Tests relationship between a task and a user
     */
    public function test_task_belongs_to_a_user(): void
    {
        $user = User::factory()->create();

        $task = Task::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($task->user()->count(), 1);
        $this->assertInstanceOf(User::class, $task->user);
    }

    /**
     * Tests that a mail is sent to user when task is updated and deadline is overdue
     */
    public function test_overdue_notification_is_sent_when_task_deadline_passed(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'created_by' => $this->user->id,
            'deadline' => now()->yesterday(),
            'user_id' => $this->user->id,
        ]);

        $this->withToken($this->token)
            ->patchJson('/api/tasks/' . $task->id, [
                'state' => TaskState::IN_PROGRESS,
            ])
            ->assertStatus(200);

        Notification::assertSentTo(
            [$this->user], TaskOverdue::class
        );
    }

    /**
     * Tests that a mail is sent to user when task is updated and deadline is overdue
     */
    public function test_does_not_send_overdue_notification_if_deadline_passed_and_state_done(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'created_by' => $this->user->id,
            'deadline' => now()->tomorrow(),
            'user_id' => $this->user->id,
        ]);

        $this->withToken($this->token)
            ->patchJson('/api/tasks/' . $task->id, [
                'state' => TaskState::DONE,
            ])
            ->assertStatus(200);

        Notification::assertNothingSent();
    }

    /**
     * Tests that a mail is sent to user when task is updated and deadline is overdue
     */
    public function test_does_not_send_overdue_notification_if_deadline_not_exceeded(): void
    {
        Notification::fake();

        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'created_by' => $this->user->id,
            'deadline' => now()->tomorrow(),
            'user_id' => $this->user->id,
        ]);

        $this->withToken($this->token)
            ->patchJson('/api/tasks/' . $task->id, [
                'state' => TaskState::IN_PROGRESS,
            ])
            ->assertStatus(200);

        Notification::assertNothingSent();
    }

    /**
     * Tests getting a users tasks
     */
    public function test_get_users_tasks(): void
    {
        $user = User::factory()->create();

        $userToken = $user->createToken('api-token')->plainTextToken;

        $tasks = Task::factory(5)->create([
            'user_id' => $user->id,
        ]);

        $this->withToken($userToken)
            ->getJson('/api/users/' . $user->id . '/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                    'id',
                    'name',
                    'email',
                    'email_verified_at',
                    'role_id',
                    'created_at',
                    'updated_at'
                ],
                'tasks' => [
                    'current_page',
                    'data' => [
                        [
                            'id',
                            'title',
                            'description',
                            'deadline',
                            'user_id',
                            'state',
                            'created_by',
                            'project_id',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ])
            ->assertJsonCount(5, 'tasks.data');
    }

    /**
     * Tests getting a projects tasks
     */
    public function test_get_project_tasks(): void
    {
        $project = Project::factory()->create();

        $tasks = Task::factory(12)->create([
            'user_id' => $this->user->id,
            'project_id' => $project->id,
        ]);

        $this->withToken($this->token)
            ->getJson('/api/projects/' . $project->id . '/tasks')
            ->assertStatus(200)
            ->assertJsonStructure([
                'project' => [
                    'id',
                    'name',
                    'created_at',
                    'updated_at'
                ],
                'tasks' => [
                    'current_page',
                    'data' => [
                        [
                            'id',
                            'title',
                            'description',
                            'deadline',
                            'user_id',
                            'state',
                            'created_by',
                            'project_id',
                            'created_at',
                            'updated_at',
                        ]
                    ],
                    'first_page_url',
                    'from',
                    'last_page',
                    'last_page_url',
                    'links',
                    'next_page_url',
                    'path',
                    'per_page',
                    'prev_page_url',
                    'to',
                    'total',
                ],
            ]);
    }

    /**
     * Tests that admin can see all tasks
     */
    public function test_admin_can_update_foreign_tasks(): void
    {
        $adminRole = Role::factory()->create([
            'name' => 'admin',
        ]);

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $user = User::factory()->create();

        $adminToken = $admin->createToken('api-token')->plainTextToken;

        $task = Task::factory()->create([
            'state' => TaskState::TODO,
            'user_id' => $user->id,
        ]);

        $response = $this->withToken($adminToken)
            ->patchJson('/api/tasks/' . $task->id, [
                'state' => TaskState::IN_PROGRESS,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'task' => [],
                'message' => 'Task was successfully updated.',
            ])
            ->assertJsonFragment(['state' => TaskState::IN_PROGRESS]);
    }
}
