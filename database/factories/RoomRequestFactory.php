<?php

namespace Database\Factories;

use App\Enums\RoomRequestStatus;
use App\Models\Room;
use App\Models\RoomRequest;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomRequest>
 */
class RoomRequestFactory extends Factory
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
            'room_id' => Room::factory(),
            'reviewed_by' => null,
            'status' => RoomRequestStatus::Pending,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Approved by staff.
     */
    public function approved(): static
    {
        return $this->state(fn () => [
            'status' => RoomRequestStatus::Approved,
            'reviewed_at' => now(),
        ]);
    }

    /**
     * Rejected by staff.
     */
    public function rejected(): static
    {
        return $this->state(fn () => [
            'status' => RoomRequestStatus::Rejected,
            'reviewed_at' => now(),
            'rejection_reason' => fake()->sentence(),
        ]);
    }
}
