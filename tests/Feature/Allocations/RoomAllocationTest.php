<?php

namespace Tests\Feature\Allocations;

use App\Enums\AllocationStatus;
use App\Enums\Role;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomAllocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_allocating_a_student_increments_room_occupied_beds(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $student = StudentProfile::factory()->create();

        $this->actingAs($admin)->post('/allocations', [
            'student_profile_id' => $student->id,
            'room_id' => $room->id,
            'bed_number' => 1,
        ])->assertRedirect("/students/{$student->id}");

        $this->assertSame(1, $room->fresh()->occupied_beds);
    }

    public function test_a_bed_already_taken_cannot_be_allocated_again(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $existingStudent = StudentProfile::factory()->create();
        $newStudent = StudentProfile::factory()->create();

        RoomAllocation::factory()->create([
            'room_id' => $room->id,
            'student_profile_id' => $existingStudent->id,
            'bed_number' => 1,
        ]);

        $this->actingAs($admin)->post('/allocations', [
            'student_profile_id' => $newStudent->id,
            'room_id' => $room->id,
            'bed_number' => 1,
        ])->assertSessionHasErrors('bed_number');

        $this->assertSame(1, $room->fresh()->occupied_beds);
    }

    public function test_transferring_a_student_vacates_the_old_room_and_fills_the_new_one(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $oldRoom = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $newRoom = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $student = StudentProfile::factory()->create();

        $allocation = RoomAllocation::factory()->create([
            'room_id' => $oldRoom->id,
            'student_profile_id' => $student->id,
            'bed_number' => 1,
        ]);

        $this->actingAs($admin)->post("/allocations/{$allocation->id}/transfer", [
            'room_id' => $newRoom->id,
            'bed_number' => 1,
        ])->assertRedirect("/students/{$student->id}");

        $this->assertSame(0, $oldRoom->fresh()->occupied_beds);
        $this->assertSame(1, $newRoom->fresh()->occupied_beds);
        $this->assertSame(AllocationStatus::Transferred, $allocation->fresh()->status);
    }

    public function test_vacating_frees_the_bed(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $student = StudentProfile::factory()->create();

        $allocation = RoomAllocation::factory()->create([
            'room_id' => $room->id,
            'student_profile_id' => $student->id,
            'bed_number' => 1,
        ]);

        $this->assertSame(1, $room->fresh()->occupied_beds);

        $this->actingAs($admin)->post("/allocations/{$allocation->id}/vacate")
            ->assertRedirect("/students/{$student->id}");

        $this->assertSame(0, $room->fresh()->occupied_beds);
        $this->assertSame(AllocationStatus::Vacated, $allocation->fresh()->status);
    }
}
