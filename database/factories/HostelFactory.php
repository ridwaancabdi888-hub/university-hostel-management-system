<?php

namespace Database\Factories;

use App\Models\Hostel;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hostel>
 */
class HostelFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->company().' Hostel';

        return [
            'name' => $name,
            'code' => strtoupper(fake()->unique()->lexify('???')),
            'address' => fake()->address(),
            'description' => fake()->sentence(),
        ];
    }
}
