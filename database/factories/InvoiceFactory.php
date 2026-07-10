<?php

namespace Database\Factories;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\StudentProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * Deliberately does not set `invoice_number` — InvoiceObserver generates
     * it on creation, so factories exercise the same code path production
     * traffic does.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $billingMonth = now()->startOfMonth();

        return [
            'student_profile_id' => StudentProfile::factory(),
            'room_allocation_id' => null,
            'billing_month' => $billingMonth,
            'rent_amount' => fake()->randomElement([180, 225, 300, 450]),
            'utility_amount' => 25.00,
            'late_fee_amount' => 0,
            'discount_amount' => 0,
            'due_date' => $billingMonth->copy()->addDays(25),
            'status' => InvoiceStatus::Unpaid,
        ];
    }

    /**
     * Fully paid and settled (sets status/paid_at directly rather than via
     * a real Payment record — fine for invoice-only tests, but tests that
     * assert against the payments relationship should create a matching
     * Payment explicitly instead of relying on this state).
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    /**
     * Billed last month and past its due date, still unpaid.
     */
    public function overdue(): static
    {
        $billingMonth = now()->subMonth()->startOfMonth();

        return $this->state(fn (array $attributes) => [
            'billing_month' => $billingMonth,
            'due_date' => $billingMonth->copy()->addDays(10),
            'status' => InvoiceStatus::Unpaid,
        ]);
    }

    /**
     * Cancelled invoice, excluded from billing totals.
     */
    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => InvoiceStatus::Cancelled,
        ]);
    }
}
