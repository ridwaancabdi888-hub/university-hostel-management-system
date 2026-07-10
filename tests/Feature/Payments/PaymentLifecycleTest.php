<?php

namespace Tests\Feature\Payments;

use App\Enums\InvoiceStatus;
use App\Enums\Role;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentLifecycleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Regression test for the bug fixed in PaymentObserver: the old
     * MySQL-only raw SQL sequence generator would have thrown against
     * SQLite (this suite's driver) the moment two payments were created in
     * the same year.
     */
    public function test_receipt_numbers_are_sequential_and_distinct(): void
    {
        $invoice = Invoice::factory()->create();

        $first = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 50]);
        $second = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 50]);

        $this->assertNotSame($first->receipt_number, $second->receipt_number);
        $this->assertStringStartsWith('RCT-'.now()->year.'-', $first->receipt_number);
        $this->assertStringStartsWith('RCT-'.now()->year.'-', $second->receipt_number);
    }

    public function test_invoice_is_marked_paid_once_payments_cover_the_total(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $invoice = Invoice::factory()->create([
            'rent_amount' => 300,
            'utility_amount' => 0,
            'status' => InvoiceStatus::Unpaid,
        ]);

        $this->actingAs($admin)->post("/invoices/{$invoice->id}/payments", [
            'amount' => 300,
            'payment_method' => 'cash',
            'paid_at' => now()->toDateString(),
        ])->assertRedirect("/invoices/{$invoice->id}");

        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);
    }

    public function test_invoice_reverts_to_unpaid_when_its_only_payment_is_deleted(): void
    {
        $admin = User::factory()->create(['role' => Role::Admin]);
        $invoice = Invoice::factory()->create([
            'rent_amount' => 300,
            'utility_amount' => 0,
        ]);
        $payment = Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 300]);

        $this->assertSame(InvoiceStatus::Paid, $invoice->fresh()->status);

        $this->actingAs($admin)->delete("/payments/{$payment->id}")
            ->assertRedirect("/invoices/{$invoice->id}");

        $this->assertSame(InvoiceStatus::Unpaid, $invoice->fresh()->status);
    }

    public function test_cancelled_invoices_are_not_reactivated_by_a_payment(): void
    {
        $invoice = Invoice::factory()->cancelled()->create([
            'rent_amount' => 300,
            'utility_amount' => 0,
        ]);

        Payment::factory()->create(['invoice_id' => $invoice->id, 'amount' => 300]);

        $this->assertSame(InvoiceStatus::Cancelled, $invoice->fresh()->status);
    }
}
