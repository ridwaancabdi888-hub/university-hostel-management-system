<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\Hostel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Block>
 */
class BlockFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $prefix = fake()->randomElement(['Alpha', 'Beta', 'Gamma', 'Delta', 'Epsilon', 'Zeta']);
        $name = "{$prefix} Block ".fake()->unique()->numberBetween(1, 100000);

        return [
            'hostel_id' => Hostel::factory(),
            'name' => $name,
            'code' => strtoupper(substr($prefix, 0, 1)),
            'description' => fake()->sentence(),
        ];
    }
}
