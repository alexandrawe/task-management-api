<?php

namespace Database\Factories;

use App\Models\User;
use App\Enum\TaskState;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(8),
            'description' => fake()->paragraph(),
            'deadline' => Carbon::parse(fake()->dateTimeBetween('-1 year', '+1 year'))->format('Y-m-d H:i:s'),
            'user_id' => User::first()->id,
            'state' => fake()->randomElement(TaskState::class),
            'created_by' => User::first()->id,
        ];
    }
}
