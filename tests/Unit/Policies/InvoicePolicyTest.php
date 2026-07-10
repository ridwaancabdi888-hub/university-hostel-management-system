<?php

namespace Tests\Unit\Policies;

use App\Enums\Role;
use App\Models\Invoice;
use App\Models\StudentProfile;
use App\Models\User;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicePolicyTest extends TestCase
{
    use RefreshDatabase;

    private InvoicePolicy $policy;

    protected function setUp(): void
    {
        parent::setUp();

        $this->policy = new InvoicePolicy;
    }

    public function test_student_can_only_view_their_own_invoice(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $own = Invoice::factory()->create(['student_profile_id' => $profile->id]);
        $other = Invoice::factory()->create();

        $this->assertTrue($this->policy->view($studentUser, $own));
        $this->assertFalse($this->policy->view($studentUser, $other));
    }

    public function test_staff_can_view_any_invoice(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $accountant = User::factory()->create(['role' => Role::Accountant]);
        $invoice = Invoice::factory()->create();

        $this->assertTrue($this->policy->view($admin, $invoice));
        $this->assertTrue($this->policy->view($accountant, $invoice));
    }
}
