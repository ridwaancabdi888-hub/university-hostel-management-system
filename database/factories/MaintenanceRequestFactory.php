<?php

namespace Database\Factories;

use App\Enums\MaintenanceCategory;
use App\Enums\MaintenancePriority;
use App\Enums\MaintenanceStatus;
use App\Models\MaintenanceRequest;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MaintenanceRequest>
 */
class MaintenanceRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'student_profile_id' => StudentProfile::factory(),
            'room_id' => null,
            'assigned_to' => null,
            'category' => fake()->randomElement(MaintenanceCategory::cases()),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'priority' => fake()->randomElement(MaintenancePriority::cases()),
            'status' => MaintenanceStatus::Pending,
        ];
    }

    /**
     * Assigned to staff and being worked on.
     */
    public function inProgress(): static
    {
        return $this->state(fn () => ['status' => MaintenanceStatus::InProgress]);
    }

    /**
     * Repaired, awaiting the student's confirmation.
     */
    public function awaitingVerification(): static
    {
        return $this->state(fn () => ['status' => MaintenanceStatus::Verification]);
    }

    /**
     * Confirmed fixed and closed.
     */
    public function completed(): static
    {
        return $this->state(fn () => [
            'status' => MaintenanceStatus::Completed,
            'resolution_notes' => fake()->sentence(),
            'resolved_at' => now(),
        ]);
    }
}
