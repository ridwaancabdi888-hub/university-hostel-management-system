<?php

namespace Tests\Feature\Reports;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_see_every_report(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        foreach (['occupancy', 'billing', 'payments', 'students', 'hostels'] as $type) {
            $this->actingAs($admin)->get("/reports/{$type}")->assertOk();
        }
    }

    public function test_warden_can_only_see_occupancy_students_and_hostels(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);

        $this->actingAs($warden)->get('/reports/occupancy')->assertOk();
        $this->actingAs($warden)->get('/reports/students')->assertOk();
        $this->actingAs($warden)->get('/reports/hostels')->assertOk();
        $this->actingAs($warden)->get('/reports/billing')->assertForbidden();
        $this->actingAs($warden)->get('/reports/payments')->assertForbidden();
    }

    public function test_accountant_can_only_see_billing_payments_and_students(): void
    {
        $accountant = User::factory()->create(['role' => Role::Accountant]);

        $this->actingAs($accountant)->get('/reports/billing')->assertOk();
        $this->actingAs($accountant)->get('/reports/payments')->assertOk();
        $this->actingAs($accountant)->get('/reports/students')->assertOk();
        $this->actingAs($accountant)->get('/reports/occupancy')->assertForbidden();
        $this->actingAs($accountant)->get('/reports/hostels')->assertForbidden();
    }

    public function test_index_redirects_to_the_first_report_the_role_can_see(): void
    {
        $warden = User::factory()->create(['role' => Role::Warden]);
        $accountant = User::factory()->create(['role' => Role::Accountant]);

        $this->actingAs($warden)->get('/reports')->assertRedirect('/reports/occupancy');
        $this->actingAs($accountant)->get('/reports')->assertRedirect('/reports/billing');
    }

    public function test_pdf_export_succeeds_for_every_report_type(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        foreach (['occupancy', 'billing', 'payments', 'students', 'hostels'] as $type) {
            $response = $this->actingAs($admin)->get("/reports/{$type}/pdf");
            $response->assertOk();
            $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        }
    }

    public function test_excel_export_succeeds_for_every_report_type(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        foreach (['occupancy', 'billing', 'payments', 'students', 'hostels'] as $type) {
            $response = $this->actingAs($admin)->get("/reports/{$type}/excel");
            $response->assertOk();
            $this->assertStringContainsString(
                'spreadsheetml',
                $response->headers->get('Content-Type')
            );
        }
    }
}
