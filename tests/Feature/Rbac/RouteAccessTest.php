<?php

namespace Tests\Feature\Rbac;

use App\Enums\Role;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Boundary-tests every `role:...` middleware group in routes/web.php. The
 * app's entire access-control model is route-middleware-driven, so this is
 * the single highest-value test in the suite.
 */
class RouteAccessTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    private User $warden;

    private User $accountant;

    private User $student;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => Role::Admin]);
        $this->warden = User::factory()->create(['role' => Role::Warden]);
        $this->accountant = User::factory()->create(['role' => Role::Accountant]);

        $studentUser = User::factory()->create(['role' => Role::Student]);
        StudentProfile::factory()->create(['user_id' => $studentUser->id]);
        $this->student = $studentUser;
    }

    public function test_room_management_is_restricted_to_admin_and_warden(): void
    {
        $this->actingAs($this->admin)->get('/rooms')->assertOk();
        $this->actingAs($this->warden)->get('/rooms')->assertOk();
        $this->actingAs($this->accountant)->get('/rooms')->assertForbidden();
        $this->actingAs($this->student)->get('/rooms')->assertForbidden();
    }

    public function test_student_directory_is_restricted_to_staff(): void
    {
        $this->actingAs($this->admin)->get('/students')->assertOk();
        $this->actingAs($this->warden)->get('/students')->assertOk();
        $this->actingAs($this->accountant)->get('/students')->assertOk();
        $this->actingAs($this->student)->get('/students')->assertForbidden();
    }

    public function test_maintenance_is_open_to_admin_warden_and_student_but_not_accountant(): void
    {
        $this->actingAs($this->admin)->get('/maintenance')->assertOk();
        $this->actingAs($this->warden)->get('/maintenance')->assertOk();
        $this->actingAs($this->student)->get('/maintenance')->assertOk();
        $this->actingAs($this->accountant)->get('/maintenance')->assertForbidden();
    }

    public function test_visitors_is_open_to_admin_warden_and_student_but_not_accountant(): void
    {
        $this->actingAs($this->admin)->get('/visitors')->assertOk();
        $this->actingAs($this->warden)->get('/visitors')->assertOk();
        $this->actingAs($this->student)->get('/visitors')->assertOk();
        $this->actingAs($this->accountant)->get('/visitors')->assertForbidden();
    }

    public function test_billing_is_open_to_admin_accountant_and_student_but_not_warden(): void
    {
        $this->actingAs($this->admin)->get('/invoices')->assertOk();
        $this->actingAs($this->accountant)->get('/invoices')->assertOk();
        $this->actingAs($this->student)->get('/invoices')->assertOk();
        $this->actingAs($this->warden)->get('/invoices')->assertForbidden();
    }

    public function test_invoice_management_actions_remain_restricted_to_admin_and_accountant(): void
    {
        $this->actingAs($this->student)->get('/invoices/create')->assertForbidden();
        $this->actingAs($this->student)->get('/invoices/generate')->assertForbidden();
        $this->actingAs($this->warden)->get('/invoices/create')->assertForbidden();
    }

    public function test_reports_center_is_open_to_admin_warden_and_accountant_but_not_student(): void
    {
        $this->actingAs($this->admin)->get('/reports')->assertRedirect();
        $this->actingAs($this->warden)->get('/reports')->assertRedirect();
        $this->actingAs($this->accountant)->get('/reports')->assertRedirect();
        $this->actingAs($this->student)->get('/reports')->assertForbidden();
    }

    public function test_activity_log_is_restricted_to_admin(): void
    {
        $this->actingAs($this->admin)->get('/activity-log')->assertOk();
        $this->actingAs($this->warden)->get('/activity-log')->assertForbidden();
        $this->actingAs($this->accountant)->get('/activity-log')->assertForbidden();
        $this->actingAs($this->student)->get('/activity-log')->assertForbidden();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_is_open_to_every_authenticated_role(): void
    {
        foreach ([$this->admin, $this->warden, $this->accountant, $this->student] as $user) {
            $this->actingAs($user)->get('/dashboard')->assertOk();
        }
    }
}
