<?php

namespace Tests\Feature\Visitors;

use App\Enums\Role;
use App\Enums\VisitorStatus;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorTest extends TestCase
{
    use RefreshDatabase;

    private function studentWithProfile(): User
    {
        $user = User::factory()->create(['role' => Role::Student]);
        StudentProfile::factory()->create(['user_id' => $user->id]);

        return $user;
    }

    public function test_a_student_can_view_their_own_visitor_registration(): void
    {
        $student = $this->studentWithProfile();
        $visitor = Visitor::factory()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->get("/visitors/{$visitor->id}")->assertOk();
    }

    public function test_a_student_cannot_view_another_students_visitor_registration(): void
    {
        $student = $this->studentWithProfile();
        $otherStudent = StudentProfile::factory()->create();
        $visitor = Visitor::factory()->create(['student_profile_id' => $otherStudent->id]);

        $this->actingAs($student)->get("/visitors/{$visitor->id}")->assertForbidden();
    }

    public function test_a_student_can_edit_a_pending_registration_but_not_once_approved(): void
    {
        $student = $this->studentWithProfile();
        $pending = Visitor::factory()->create(['student_profile_id' => $student->studentProfile->id]);
        $approved = Visitor::factory()->approved()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->get("/visitors/{$pending->id}/edit")->assertOk();
        $this->actingAs($student)->get("/visitors/{$approved->id}/edit")->assertForbidden();
    }

    /**
     * Regression test: VisitorController::approve()/reject() previously had
     * zero in-controller authorization, relying solely on route middleware.
     * The new VisitorPolicy closes that gap.
     */
    public function test_a_student_cannot_approve_or_reject_a_visitor_even_their_own(): void
    {
        $student = $this->studentWithProfile();
        $visitor = Visitor::factory()->create(['student_profile_id' => $student->studentProfile->id]);

        $this->actingAs($student)->post("/visitors/{$visitor->id}/approve")->assertForbidden();
        $this->actingAs($student)->post("/visitors/{$visitor->id}/reject", ['rejection_reason' => 'No.'])->assertForbidden();
    }

    public function test_staff_can_approve_a_pending_visitor(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $visitor = Visitor::factory()->create();

        $this->actingAs($warden)->post("/visitors/{$visitor->id}/approve")
            ->assertRedirect("/visitors/{$visitor->id}");

        $this->assertSame(VisitorStatus::Approved, $visitor->fresh()->status);
    }

    public function test_staff_can_reject_a_pending_visitor(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $visitor = Visitor::factory()->create();

        $this->actingAs($warden)->post("/visitors/{$visitor->id}/reject", ['rejection_reason' => 'Conflicts with maintenance.'])
            ->assertRedirect("/visitors/{$visitor->id}");

        $this->assertSame(VisitorStatus::Rejected, $visitor->fresh()->status);
    }
}
