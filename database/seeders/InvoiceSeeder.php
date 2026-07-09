<?php

namespace Database\Seeders;

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

            // Two months ago: PaymentSeeder will pay this one off in full.
            $this->makeInvoice($student, $rate, 2);

            // Last month: past its due date — left unpaid/partial so it
            // shows up as overdue until PaymentSeeder settles some of them.
            $this->makeInvoice($student, $rate, 1, dueInPast: true);

            // Current month: freshly billed, not yet due.
            $this->makeInvoice($student, $rate, 0);
        }
    }

    private function makeInvoice(StudentProfile $student, float $rate, int $monthsAgo, bool $dueInPast = false): void
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
        ]);
    }
}
