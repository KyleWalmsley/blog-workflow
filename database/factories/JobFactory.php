<?php

namespace Database\Factories;

use App\Enums\JobStatus;
use App\Models\Client;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class JobFactory extends Factory
{
    protected $model = Job::class;

    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'title' => fake()->sentence(4),
            'status' => JobStatus::Draft,
            'revision_count' => 0,
        ];
    }

    public function inReview(): static
    {
        return $this->state(fn () => ['status' => JobStatus::InReview]);
    }

    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => JobStatus::Completed,
            'completed_at' => now(),
        ]);
    }
}
