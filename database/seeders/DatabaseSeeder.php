<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Role::insert([
            ['name' => 'admin'],
            ['name' => 'user']
        ]);

        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();

        $admin = User::factory()->create([
            'role_id' => $adminRole->id,
        ]);

        $users = User::factory(9)->create([
            'role_id' => $userRole->id,
        ]);

        $projects = Project::factory(3)->create();

        $tasks = Task::factory(100)
            ->state(new Sequence(
                fn (Sequence $sequence) => [
                    'created_by' => $users->random()->id,
                    'user_id' => $users->random()->id,
                    'project_id' => $projects->random()->id,
                ]
            ))
            ->create();
    }
}
