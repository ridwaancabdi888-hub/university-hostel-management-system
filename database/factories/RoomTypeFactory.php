<?php

namespace Database\Factories;

use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = [
            ['name' => 'Single Premium', 'default_capacity' => 1, 'monthly_rate' => 450.00],
            ['name' => 'Double Shared', 'default_capacity' => 2, 'monthly_rate' => 300.00],
            ['name' => 'Triple Shared', 'default_capacity' => 3, 'monthly_rate' => 225.00],
            ['name' => 'Quad Suite', 'default_capacity' => 4, 'monthly_rate' => 180.00],
        ];

        $type = fake()->randomElement($types);

        return [
            'name' => $type['name'].' '.fake()->unique()->numberBetween(1, 100000),
            'default_capacity' => $type['default_capacity'],
            'monthly_rate' => $type['monthly_rate'],
            'description' => fake()->sentence(),
        ];
    }
}
