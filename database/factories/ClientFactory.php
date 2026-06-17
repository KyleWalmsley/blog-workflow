<?php

namespace Database\Factories;

use App\Enums\ClientStatus;
use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'website' => fake()->url(),
            'business_description' => fake()->paragraph(),
            'primary_keywords' => implode(', ', fake()->words(5)),
            'secondary_keywords' => implode(', ', fake()->words(4)),
            'target_locations' => fake()->city().', '.fake()->state(),
            'target_audience' => fake()->sentence(),
            'tone_of_voice' => fake()->randomElement(['Professional', 'Friendly', 'Authoritative', 'Conversational']),
            'status' => ClientStatus::Active,
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
