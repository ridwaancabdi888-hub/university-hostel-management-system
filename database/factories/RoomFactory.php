<?php

namespace Database\Factories;

use App\Enums\RoomStatus;
use App\Models\Floor;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'floor_id' => Floor::factory(),
            'room_type_id' => RoomType::factory(),
            'room_number' => strtoupper(fake()->unique()->bothify('??-###')),
            'capacity' => fake()->numberBetween(1, 4),
            'occupied_beds' => 0,
            'status' => RoomStatus::Available,
        ];
    }

    /**
     * The room is under maintenance and cannot be allocated.
     */
    public function underMaintenance(): static
    {
        return $this->state(fn () => ['status' => RoomStatus::Maintenance]);
    }
}
