<?php

namespace Database\Factories;

use App\Enums\VisitorStatus;
use App\Models\StudentProfile;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Visitor>
 */
class VisitorFactory extends Factory
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
            'approved_by' => null,
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'relationship' => fake()->randomElement(['Mother', 'Father', 'Sibling', 'Friend', 'Guardian', 'Cousin']),
            'purpose' => fake()->sentence(),
            'expected_at' => now()->addDays(fake()->numberBetween(1, 14)),
            'status' => VisitorStatus::Pending,
        ];
    }

    /**
     * Approved by staff.
     */
    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => VisitorStatus::Approved,
            'approved_at' => now(),
        ]);
    }

    /**
     * Rejected by staff.
     */
    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => VisitorStatus::Rejected,
            'approved_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
