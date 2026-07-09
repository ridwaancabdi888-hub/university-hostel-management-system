<?php

namespace Database\Seeders;

use App\Enums\InvoiceStatus;
use App\Models\Invoice;
use App\Models\StudentProfile;
use Illuminate\Database\Seeder;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $students = StudentProfile::with('activeAllocation.room.roomType')
            ->whereHas('activeAllocation')
            ->get();

        foreach ($students as $student) {
            $rate = $student->activeAllocation->room->roomType->monthly_rate;

            // Two months ago: paid in full.
            $this->makeInvoice($student, $rate, 2, InvoiceStatus::Paid);

            // Last month: still unpaid and past its due date (overdue).
            $this->makeInvoice($student, $rate, 1, InvoiceStatus::Unpaid, dueInPast: true);

            // Current month: freshly billed, not yet due.
            $this->makeInvoice($student, $rate, 0, InvoiceStatus::Unpaid);
        }
    }

    private function makeInvoice(StudentProfile $student, float $rate, int $monthsAgo, InvoiceStatus $status, bool $dueInPast = false): void
    {
        $billingMonth = now()->subMonths($monthsAgo)->startOfMonth();
        $dueDate = $dueInPast ? $billingMonth->copy()->addDays(10) : $billingMonth->copy()->addDays(25);

        Invoice::create([
            'student_profile_id' => $student->id,
            'room_allocation_id' => $student->activeAllocation->id,
            'billing_month' => $billingMonth,
            'rent_amount' => $rate,
            'utility_amount' => 25.00,
            'due_date' => $dueDate,
            'status' => $status,
            'paid_at' => $status === InvoiceStatus::Paid ? $billingMonth->copy()->addDays(5) : null,
        ]);
    }
}
