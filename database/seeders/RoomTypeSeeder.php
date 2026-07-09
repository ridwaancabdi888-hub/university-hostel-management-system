<?php

namespace Database\Seeders;

use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Single Premium', 'default_capacity' => 1, 'monthly_rate' => 450.00, 'description' => 'Private single-occupancy room with premium amenities.'],
            ['name' => 'Double Shared', 'default_capacity' => 2, 'monthly_rate' => 300.00, 'description' => 'Standard twin-sharing room.'],
            ['name' => 'Triple Shared', 'default_capacity' => 3, 'monthly_rate' => 225.00, 'description' => 'Budget-friendly triple-sharing room.'],
            ['name' => 'Quad Suite', 'default_capacity' => 4, 'monthly_rate' => 180.00, 'description' => 'Spacious suite shared by four students.'],
        ];

        foreach ($types as $type) {
            RoomType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
