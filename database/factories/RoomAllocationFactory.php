<?php

namespace Database\Factories;

use App\Enums\AllocationStatus;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomAllocation>
 */
class RoomAllocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'room_id' => Room::factory(),
            'student_profile_id' => StudentProfile::factory(),
            'bed_number' => fake()->numberBetween(1, 4),
            'status' => AllocationStatus::Active,
            'allocated_at' => now()->subDays(fake()->numberBetween(1, 200)),
            'vacated_at' => null,
        ];
    }

    /**
     * The student moved to a different room; this allocation is history.
     */
    public function transferred(): static
    {
        return $this->state(fn () => [
            'status' => AllocationStatus::Transferred,
            'vacated_at' => now()->subDays(fake()->numberBetween(1, 100)),
        ]);
    }

    /**
     * The student moved out entirely; this allocation is history.
     */
    public function vacated(): static
    {
        return $this->state(fn () => [
            'status' => AllocationStatus::Vacated,
            'vacated_at' => now()->subDays(fake()->numberBetween(1, 100)),
        ]);
    }
}
