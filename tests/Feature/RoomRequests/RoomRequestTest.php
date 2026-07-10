<?php

namespace Tests\Feature\RoomRequests;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Enums\RoomRequestStatus;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\RoomAllocation;
use App\Models\RoomRequest;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoomRequestTest extends TestCase
{
    use RefreshDatabase;

    private function studentWithProfile(): User
    {
        $user = User::factory()->create(['role' => Role::Student]);
        StudentProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_an_unallocated_student_can_request_an_available_room(): void
    {
        $student = $this->studentWithProfile();
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);

        $this->actingAs($student)->post('/room-requests', [
            'room_id' => $room->id,
        ])->assertRedirect('/room-requests');

        $this->assertDatabaseHas('room_requests', [
            'student_profile_id' => $student->studentProfile->id,
            'room_id' => $room->id,
            'status' => RoomRequestStatus::Pending->value,
        ]);
    }

    public function test_a_student_with_an_active_allocation_cannot_submit_a_room_request(): void
    {
        $student = $this->studentWithProfile();
        $currentRoom = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 1]);
        RoomAllocation::factory()->create([
            'room_id' => $currentRoom->id,
            'student_profile_id' => $student->studentProfile->id,
            'bed_number' => 1,
        ]);

        $newRoom = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);

        $this->actingAs($student)->post('/room-requests', [
            'room_id' => $newRoom->id,
        ]);

        $this->assertDatabaseMissing('room_requests', ['room_id' => $newRoom->id]);
    }

    public function test_a_student_with_a_pending_request_cannot_submit_another(): void
    {
        $student = $this->studentWithProfile();
        RoomRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);

        $newRoom = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);

        $this->actingAs($student)->post('/room-requests', [
            'room_id' => $newRoom->id,
        ]);

        $this->assertDatabaseMissing('room_requests', ['room_id' => $newRoom->id]);
    }

    public function test_staff_approving_a_request_creates_a_real_allocation_and_fills_a_bed(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $roomRequest = RoomRequest::factory()->create(['room_id' => $room->id]);

        $this->actingAs($warden)->post("/room-requests/{$roomRequest->id}/approve")
            ->assertRedirect('/room-requests');

        $this->assertSame(RoomRequestStatus::Approved, $roomRequest->fresh()->status);
        $this->assertSame(1, $room->fresh()->occupied_beds);
        $this->assertDatabaseHas('room_allocations', [
            'room_id' => $room->id,
            'student_profile_id' => $roomRequest->student_profile_id,
            'bed_number' => 1,
        ]);
    }

    public function test_approving_a_request_generates_the_first_months_invoice(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $roomRequest = RoomRequest::factory()->create(['room_id' => $room->id]);

        $this->actingAs($warden)->post("/room-requests/{$roomRequest->id}/approve");

        $invoice = Invoice::where('student_profile_id', $roomRequest->student_profile_id)->first();

        $this->assertNotNull($invoice);
        $this->assertSame(InvoiceStatus::Unpaid, $invoice->status);
        $this->assertSame((float) $room->roomType->monthly_rate, (float) $invoice->rent_amount);
        $this->assertNotNull($invoice->room_allocation_id);
    }

    public function test_approving_a_request_skips_billing_if_already_billed_this_month(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $room = Room::factory()->create(['capacity' => 2, 'occupied_beds' => 0]);
        $roomRequest = RoomRequest::factory()->create(['room_id' => $room->id]);

        Invoice::factory()->create(['student_profile_id' => $roomRequest->student_profile_id]);

        $this->actingAs($warden)->post("/room-requests/{$roomRequest->id}/approve");

        $this->assertSame(1, Invoice::where('student_profile_id', $roomRequest->student_profile_id)->count());
    }

    public function test_approving_a_request_for_a_room_with_no_beds_left_fails_cleanly(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $room = Room::factory()->create(['capacity' => 1, 'occupied_beds' => 1]);
        $existingStudent = StudentProfile::factory()->create();
        RoomAllocation::factory()->create([
            'room_id' => $room->id,
            'student_profile_id' => $existingStudent->id,
            'bed_number' => 1,
        ]);

        $roomRequest = RoomRequest::factory()->create(['room_id' => $room->id]);

        $this->actingAs($warden)->post("/room-requests/{$roomRequest->id}/approve")
            ->assertSessionHasErrors('room_id');

        $this->assertSame(RoomRequestStatus::Pending, $roomRequest->fresh()->status);
    }

    public function test_staff_can_reject_a_pending_request(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $roomRequest = RoomRequest::factory()->create();

        $this->actingAs($warden)->post("/room-requests/{$roomRequest->id}/reject", [
            'rejection_reason' => 'Room reserved for another program.',
        ])->assertRedirect('/room-requests');

        $this->assertSame(RoomRequestStatus::Rejected, $roomRequest->fresh()->status);
    }

    public function test_a_student_cannot_approve_or_reject_a_room_request(): void
    {
        $student = $this->studentWithProfile();
        $roomRequest = RoomRequest::factory()->create();

        $this->actingAs($student)->post("/room-requests/{$roomRequest->id}/approve")->assertForbidden();
        $this->actingAs($student)->post("/room-requests/{$roomRequest->id}/reject", ['rejection_reason' => 'No.'])->assertForbidden();
    }

    public function test_a_student_only_sees_their_own_room_requests(): void
    {
        $student = $this->studentWithProfile();
        $ownRequest = RoomRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);
        $otherRequest = RoomRequest::factory()->create();

        $response = $this->actingAs($student)->get('/room-requests');

        $response->assertOk();
        $response->assertSee($ownRequest->room->room_number);
        $response->assertDontSee($otherRequest->room->room_number);
    }
}
