<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BillingExport implements FromCollection, WithHeadings
{
    public function headings(): array
    {
        return ['Invoice Number', 'Student', 'Student ID', 'Billing Month', 'Rent', 'Utility', 'Late Fee', 'Discount', 'Total', 'Paid', 'Balance', 'Due Date', 'Status'];
    }

    public function collection(): Collection
    {
        return Invoice::with('studentProfile.user')
            ->withSum('payments', 'amount')
            ->latest('billing_month')
            ->get()
            ->map(fn (Invoice $invoice) => [
                $invoice->invoice_number,
                $invoice->studentProfile->user->name,
                $invoice->studentProfile->student_id,
                $invoice->billing_month->format('F Y'),
                $invoice->rent_amount,
                $invoice->utility_amount,
                $invoice->late_fee_amount,
                $invoice->discount_amount,
                $invoice->total_amount,
                $invoice->amountPaid(),
                $invoice->balance(),
                $invoice->due_date->format('Y-m-d'),
                $invoice->status->label(),
            ]);
    }
}
