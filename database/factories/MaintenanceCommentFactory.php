<?php

namespace Database\Factories;

use App\Models\MaintenanceComment;
use App\Models\MaintenanceRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceComment>
 */
class MaintenanceCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'maintenance_request_id' => MaintenanceRequest::factory(),
            'user_id' => User::factory(),
            'body' => fake()->sentence(10),
        ];
    }
}
