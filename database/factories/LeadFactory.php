<?php

namespace Database\Factories;

use App\Domains\Leads\Models\Lead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Lead>
 */
class LeadFactory extends Factory
{
    protected $model = Lead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->optional()->phoneNumber(),
            'company' => fake()->optional()->company(),
            'project_type' => fake()->optional()->randomElement(['web-app', 'saas', 'integration']),
            'message' => fake()->paragraphs(2, true),
            'status' => Lead::STATUS_NEW,
            'source' => Lead::SOURCE_WEBSITE,
        ];
    }

    public function status(string $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
