<?php

namespace Database\Factories;

use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Task>
 */
class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Task>
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'status' => 'todo',
            'priority' => 'medium',
            'due_date' => fake()->dateTimeBetween('+1 days', '+30 days')->format('Y-m-d'),
            'user_id' => User::factory(),
            'created_by' => User::factory(),
        ];
    }

    /**
     * Mark the task as To Do.
     */
    public function todo(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'todo']);
    }

    /**
     * Mark the task as In Progress.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'in_progress']);
    }

    /**
     * Mark the task as Done.
     */
    public function done(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'done']);
    }

    /**
     * Set the task priority to High.
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => ['priority' => 'high']);
    }

    /**
     * Set the task priority to Low.
     */
    public function low(): static
    {
        return $this->state(fn (array $attributes) => ['priority' => 'low']);
    }

    /**
     * Give the task a deadline in the past.
     */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => fake()->dateTimeBetween('-14 days', '-1 day')->format('Y-m-d'),
        ]);
    }
}
