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
            ['name' => 'Single Premium', 'default_capacity' => 1, 'description' => 'Private single-occupancy room with premium amenities.'],
            ['name' => 'Double Shared', 'default_capacity' => 2, 'description' => 'Standard twin-sharing room.'],
            ['name' => 'Triple Shared', 'default_capacity' => 3, 'description' => 'Budget-friendly triple-sharing room.'],
            ['name' => 'Quad Suite', 'default_capacity' => 4, 'description' => 'Spacious suite shared by four students.'],
        ];

        foreach ($types as $type) {
            RoomType::firstOrCreate(['name' => $type['name']], $type);
        }
    }
}
