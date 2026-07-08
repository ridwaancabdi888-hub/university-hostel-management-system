<?php

namespace Database\Seeders;

use App\Enums\RoomStatus;
use App\Models\Hostel;
use App\Models\RoomType;
use Illuminate\Database\Seeder;

class RoomInventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hostel = Hostel::firstOrCreate(
            ['name' => 'Main Campus Hostel'],
            ['code' => 'MAIN', 'address' => '1 University Drive', 'description' => 'The primary student residence on campus.']
        );

        $roomTypes = RoomType::all()->keyBy('name');

        $blocks = [
            'Alpha Block' => [
                ['name' => 'Ground Floor', 'level' => 0],
                ['name' => 'First Floor', 'level' => 1],
            ],
            'Beta Annex' => [
                ['name' => '2nd Floor', 'level' => 2],
                ['name' => '3rd Floor', 'level' => 3],
            ],
            'Gamma Tower' => [
                ['name' => '4th Floor', 'level' => 4],
                ['name' => '5th Floor', 'level' => 5],
            ],
            'Delta Lodge' => [
                ['name' => 'Ground Floor', 'level' => 0],
                ['name' => '2nd Floor', 'level' => 2],
            ],
        ];

        // [room type name, occupied beds, status]
        $roomPlan = [
            ['Single Premium', 1, RoomStatus::Available],
            ['Double Shared', 1, RoomStatus::Available],
            ['Double Shared', 0, RoomStatus::Available],
            ['Quad Suite', 0, RoomStatus::Available],
            ['Triple Shared', 3, RoomStatus::Available],
            ['Single Premium', 0, RoomStatus::Maintenance],
        ];

        foreach ($blocks as $blockName => $floors) {
            $block = $hostel->blocks()->firstOrCreate(['name' => $blockName], [
                'code' => strtoupper(substr($blockName, 0, 1)),
            ]);

            foreach ($floors as $floorIndex => $floorData) {
                $floor = $block->floors()->firstOrCreate(
                    ['level' => $floorData['level']],
                    ['name' => $floorData['name']]
                );

                foreach ($roomPlan as $roomIndex => [$typeName, $occupiedBeds, $status]) {
                    $roomType = $roomTypes[$typeName];

                    $floor->rooms()->firstOrCreate(
                        ['room_number' => sprintf('%s-%d%02d', $block->code, $floorData['level'], $roomIndex + 1)],
                        [
                            'room_type_id' => $roomType->id,
                            'capacity' => $roomType->default_capacity,
                            'occupied_beds' => $occupiedBeds,
                            'status' => $status,
                        ]
                    );
                }
            }
        }
    }
}
