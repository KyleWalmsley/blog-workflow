<?php

namespace Database\Factories;

use App\Enums\BlogStatus;
use App\Models\Blog;
use App\Models\Job;
use Illuminate\Database\Eloquent\Factories\Factory;

class BlogFactory extends Factory
{
    protected $model = Blog::class;

    public function definition(): array
    {
        return [
            'job_id' => Job::factory(),
            'title' => fake()->sentence(6),
            'content' => '<h2>'.fake()->sentence(4).'</h2><p>'.fake()->paragraph(3).'</p><p>'.fake()->paragraph(2).'</p>',
            'meta_title' => fake()->sentence(6),
            'meta_description' => fake()->sentence(12),
            'focus_keyword' => fake()->words(2, true),
            'focus_location' => fake()->city(),
            'status' => BlogStatus::Pending,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => BlogStatus::Approved]);
    }

    public function declined(): static
    {
        return $this->state(fn () => [
            'status' => BlogStatus::Declined,
            'client_notes' => fake()->sentence(),
        ]);
    }
}
