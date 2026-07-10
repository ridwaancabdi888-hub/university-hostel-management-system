<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Floor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Floor>
 */
class FloorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $level = fake()->unique()->numberBetween(0, 500);

        return [
            'block_id' => Block::factory(),
            'name' => $level === 0 ? 'Ground Floor' : "Floor {$level}",
            'level' => $level,
        ];
    }
}
