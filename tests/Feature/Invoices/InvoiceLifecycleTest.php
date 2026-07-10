<?php

namespace Tests\Feature\Invoices;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\RoomAllocation;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceLifecycleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * This is the regression test for the bug fixed in InvoiceObserver: the
     * old MySQL-only raw SQL sequence generator would have thrown against
     * SQLite (this suite's driver) the moment two invoices were created in
     * the same year.
     */
    public function test_invoice_numbers_are_sequential_and_distinct(): void
    {
        $student = StudentProfile::factory()->create();

        $first = Invoice::factory()->create(['student_profile_id' => $student->id]);
        $second = Invoice::factory()->create([
            'student_profile_id' => $student->id,
            'billing_month' => now()->subMonth()->startOfMonth(),
        ]);

        $this->assertNotSame($first->invoice_number, $second->invoice_number);
        $this->assertStringStartsWith('INV-'.now()->year.'-', $first->invoice_number);
        $this->assertStringStartsWith('INV-'.now()->year.'-', $second->invoice_number);
    }

    public function test_total_amount_is_always_recalculated_from_charges(): void
    {
        $invoice = Invoice::factory()->create([
            'rent_amount' => 300,
            'utility_amount' => 25,
            'late_fee_amount' => 10,
            'discount_amount' => 5,
        ]);

        $this->assertSame('330.00', $invoice->total_amount);
    }

    public function test_generate_skips_students_already_billed_for_the_month(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $billingMonth = now()->startOfMonth();

        $student = StudentProfile::factory()->create();
        RoomAllocation::factory()->create(['student_profile_id' => $student->id]);

        Invoice::factory()->create([
            'student_profile_id' => $student->id,
            'billing_month' => $billingMonth,
        ]);

        $response = $this->actingAs($admin)->post('/invoices/generate', [
            'billing_month' => $billingMonth->format('Y-m'),
            'due_date' => $billingMonth->copy()->addDays(25)->toDateString(),
        ]);

        $response->assertRedirect('/invoices');
        $response->assertSessionHas('status');
        $this->assertStringContainsString('Skipped 1 student', session('status'));
        $this->assertSame(1, Invoice::where('student_profile_id', $student->id)
            ->whereYear('billing_month', $billingMonth->year)
            ->whereMonth('billing_month', $billingMonth->month)
            ->count());
    }

    public function test_late_fee_can_only_be_applied_to_an_overdue_invoice(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);

        $current = Invoice::factory()->create([
            'due_date' => now()->addDays(10),
            'status' => InvoiceStatus::Unpaid,
        ]);

        $response = $this->actingAs($admin)->post("/invoices/{$current->id}/apply-late-fee");

        $response->assertRedirect("/invoices/{$current->id}");
        $response->assertSessionHas('error');
        $this->assertSame('0.00', $current->fresh()->late_fee_amount);
    }

    public function test_invoice_with_payments_cannot_be_deleted(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $invoice = Invoice::factory()->create();
        Payment::factory()->create(['invoice_id' => $invoice->id]);

        $response = $this->actingAs($admin)->delete("/invoices/{$invoice->id}");

        $response->assertRedirect('/invoices');
        $response->assertSessionHas('error');
        $this->assertNotNull($invoice->fresh());
    }

    public function test_a_student_can_view_their_own_invoice_but_not_another_students(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $own = Invoice::factory()->create(['student_profile_id' => $profile->id]);
        $other = Invoice::factory()->create();

        $this->actingAs($studentUser)->get("/invoices/{$own->id}")->assertOk();
        $this->actingAs($studentUser)->get("/invoices/{$other->id}")->assertForbidden();
    }

    public function test_a_student_only_sees_their_own_invoices_in_the_billing_list(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);

        $own = Invoice::factory()->create(['student_profile_id' => $profile->id]);
        $other = Invoice::factory()->create();

        $response = $this->actingAs($studentUser)->get('/invoices');

        $response->assertOk();
        $response->assertSee($own->invoice_number);
        $response->assertDontSee($other->invoice_number);
    }

    public function test_a_student_cannot_create_edit_or_delete_invoices(): void
    {
        $studentUser = User::factory()->create(['role' => Role::Student]);
        $profile = StudentProfile::factory()->create(['user_id' => $studentUser->id]);
        $invoice = Invoice::factory()->create(['student_profile_id' => $profile->id]);

        $this->actingAs($studentUser)->get('/invoices/create')->assertForbidden();
        $this->actingAs($studentUser)->get("/invoices/{$invoice->id}/edit")->assertForbidden();
        $this->actingAs($studentUser)->delete("/invoices/{$invoice->id}")->assertForbidden();
    }
}
