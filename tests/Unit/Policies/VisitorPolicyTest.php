<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\StudentProfile;
use App\Models\User;
use App\Models\Visitor;
use App\Policies\VisitorPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisitorPolicyTest extends TestCase
{
    use RefreshDatabase;

    private VisitorPolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new VisitorPolicy;
    }

    public function test_student_can_only_view_their_own_registration(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $own = Visitor::factory()->create(['student_profile_id' => $profile->id]);
        $other = Visitor::factory()->create();

        $this->assertTrue($this->policy->view($studentUser, $own));
        $this->assertFalse($this->policy->view($studentUser, $other));
    }

    public function test_student_can_only_update_their_own_pending_registration(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $pending = Visitor::factory()->create(['student_profile_id' => $profile->id]);
        $approved = Visitor::factory()->approved()->create(['student_profile_id' => $profile->id]);

        $this->assertTrue($this->policy->update($studentUser, $pending));
        $this->assertFalse($this->policy->update($studentUser, $approved));
    }

    public function test_only_staff_can_approve_or_reject(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $warden = User::factory()->create(['role' => Role::Warden]);
        $accountant = User::factory()->create(['role' => Role::Accountant]);
        $student = User::factory()->create(['role' => Role::Student]);

        $this->assertTrue($this->policy->approve($admin));
        $this->assertTrue($this->policy->approve($warden));
        $this->assertTrue($this->policy->reject($warden));
        $this->assertFalse($this->policy->approve($accountant));
        $this->assertFalse($this->policy->approve($student));
        $this->assertFalse($this->policy->reject($student));
    }
}
