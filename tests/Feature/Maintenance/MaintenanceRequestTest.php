<?php

namespace Tests\Feature\Maintenance;

use App\Enums\MaintenanceStatus;
use App\Enums\Role;
use App\Models\MaintenanceRequest;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRequestTest extends TestCase
{
    use RefreshDatabase;

    private function studentWithProfile(): User
    {
        $user = User::factory()->create(['role' => Role::Student]);
        StudentProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_a_student_can_view_their_own_ticket(): void
    {
        $student = $this->studentWithProfile();
        $ticket = MaintenanceRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->get("/maintenance/{$ticket->id}")->assertOk();
    }

    public function test_a_student_cannot_view_another_students_ticket(): void
    {
        $student = $this->studentWithProfile();
        $otherStudent = StudentProfile::factory()->create();
        $ticket = MaintenanceRequest::factory()->create(['student_profile_id' => $otherStudent->id]);

        $this->actingAs($student)->get("/maintenance/{$ticket->id}")->assertForbidden();
    }

    public function test_staff_can_view_any_ticket(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $ticket = MaintenanceRequest::factory()->create();

        $this->actingAs($admin)->get("/maintenance/{$ticket->id}")->assertOk();
    }

    public function test_a_student_can_edit_their_own_pending_ticket_but_not_once_in_progress(): void
    {
        $student = $this->studentWithProfile();
        $pending = MaintenanceRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);
        $inProgress = MaintenanceRequest::factory()->inProgress()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->get("/maintenance/{$pending->id}/edit")->assertOk();
        $this->actingAs($student)->get("/maintenance/{$inProgress->id}/edit")->assertForbidden();
    }

    public function test_only_staff_can_assign_a_ticket(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $warden = User::factory()->create(['role' => Role::Warden]);
        $student = $this->studentWithProfile();
        $ticket = MaintenanceRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->post("/maintenance/{$ticket->id}/assign", ['assigned_to' => $warden->id])
            ->assertForbidden();

        $this->actingAs($admin)->post("/maintenance/{$ticket->id}/assign", ['assigned_to' => $warden->id])
            ->assertRedirect("/maintenance/{$ticket->id}");

        $this->assertSame($warden->id, $ticket->fresh()->assigned_to);
    }

    public function test_a_student_may_only_confirm_or_reopen_a_ticket_awaiting_verification(): void
    {
        $student = $this->studentWithProfile();
        $ticket = MaintenanceRequest::factory()->inProgress()->create(['student_profile_id' => $student->studentProfile->id]);

        // Not yet at the Verification stage — student cannot jump straight to Completed.
        $this->actingAs($student)
            ->post("/maintenance/{$ticket->id}/status", ['status' => MaintenanceStatus::Completed->value])
            ->assertSessionHasErrors('status');

        $ticket->update(['status' => MaintenanceStatus::Verification]);

        $this->actingAs($student)
            ->post("/maintenance/{$ticket->id}/status", ['status' => MaintenanceStatus::Completed->value])
            ->assertSessionHasNoErrors();

        $this->assertSame(MaintenanceStatus::Completed, $ticket->fresh()->status);
    }

    public function test_a_student_can_comment_on_their_own_ticket_but_not_anothers(): void
    {
        $student = $this->studentWithProfile();
        $ownTicket = MaintenanceRequest::factory()->create(['student_profile_id' => $student->studentProfile->id]);
        $otherTicket = MaintenanceRequest::factory()->create();

        $this->actingAs($student)
            ->post("/maintenance/{$ownTicket->id}/comments", ['body' => 'Any update?'])
            ->assertRedirect("/maintenance/{$ownTicket->id}");

        $this->actingAs($student)
            ->post("/maintenance/{$otherTicket->id}/comments", ['body' => 'Any update?'])
            ->assertForbidden();
    }
}
