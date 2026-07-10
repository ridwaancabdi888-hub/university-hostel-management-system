<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\MaintenanceRequest;
use App\Models\StudentProfile;
use App\Models\User;
use App\Policies\MaintenanceRequestPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MaintenanceRequestPolicyTest extends TestCase
{
    use RefreshDatabase;

    private MaintenanceRequestPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new MaintenanceRequestPolicy;
    }

    public function test_staff_can_view_any_ticket(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $warden = User::factory()->create(['role' => Role::Warden]);
        $ticket = MaintenanceRequest::factory()->create();

        $this->assertTrue($this->policy->view($admin, $ticket));
        $this->assertTrue($this->policy->view($warden, $ticket));
    }

    public function test_student_can_only_view_their_own_ticket(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $own = MaintenanceRequest::factory()->create(['student_profile_id' => $profile->id]);
        $other = MaintenanceRequest::factory()->create();

        $this->assertTrue($this->policy->view($studentUser, $own));
        $this->assertFalse($this->policy->view($studentUser, $other));
    }

    public function test_student_can_only_update_their_own_pending_ticket(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $pending = MaintenanceRequest::factory()->create(['student_profile_id' => $profile->id]);
        $inProgress = MaintenanceRequest::factory()->inProgress()->create(['student_profile_id' => $profile->id]);

        $this->assertTrue($this->policy->update($studentUser, $pending));
        $this->assertFalse($this->policy->update($studentUser, $inProgress));
    }

    public function test_staff_can_update_a_ticket_at_any_stage(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $completed = MaintenanceRequest::factory()->completed()->create();

        $this->assertTrue($this->policy->update($admin, $completed));
    }

    public function test_only_staff_can_assign(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $warden = User::factory()->create(['role' => Role::Warden]);
        $accountant = User::factory()->create(['role' => Role::Accountant]);
        $student = User::factory()->create(['role' => Role::Student]);

        $this->assertTrue($this->policy->assign($admin));
        $this->assertTrue($this->policy->assign($warden));
        $this->assertFalse($this->policy->assign($accountant));
        $this->assertFalse($this->policy->assign($student));
    }
}
