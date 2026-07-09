<?php

namespace Database\Seeders;

use App\Enums\AllocationStatus;
use App\Enums\RoomStatus;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class AllocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $availableRooms = Room::where('status', RoomStatus::Available)->orderBy('room_number')->get();
        $students = StudentProfile::whereDoesntHave('activeAllocation')->inRandomOrder()->get();

        // Give the demo "Student User" account a stable, visible allocation.
        $demoStudent = StudentProfile::whereHas('user', fn ($q) => $q->where('email', 'student@hostel.test'))->first();

        if ($demoStudent && $availableRooms->isNotEmpty()) {
            RoomAllocation::create([
                'room_id' => $availableRooms->first()->id,
                'student_profile_id' => $demoStudent->id,
                'bed_number' => 1,
                'status' => AllocationStatus::Active,
                'allocated_at' => now()->subMonths(3),
            ]);

            $students = $students->reject(fn ($student) => $student->is($demoStudent));
        }

        // Fill each room's first bed, then randomly fill the rest, leaving
        // some rooms and beds vacant for manual allocation testing.
        foreach ($availableRooms as $room) {
            foreach ($room->availableBedNumbers() as $bedNumber) {
                if ($bedNumber > 1 && fake()->boolean(50)) {
                    continue;
                }

                $student = $students->shift();

                if (! $student) {
                    break 2;
                }

                RoomAllocation::create([
                    'room_id' => $room->id,
                    'student_profile_id' => $student->id,
                    'bed_number' => $bedNumber,
                    'status' => AllocationStatus::Active,
                    'allocated_at' => now()->subDays(fake()->numberBetween(1, 200)),
                ]);
            }
        }

        // Seed a vacated stay for history variety.
        if ($students->isNotEmpty() && $availableRooms->isNotEmpty()) {
            $room = $availableRooms->first();

            RoomAllocation::create([
                'room_id' => $room->id,
                'student_profile_id' => $students->shift()->id,
                'bed_number' => $room->fresh()->availableBedNumbers()[0] ?? 1,
                'status' => AllocationStatus::Vacated,
                'allocated_at' => now()->subMonths(6),
                'vacated_at' => now()->subMonths(4),
            ]);
        }
    }
}
