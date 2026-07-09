<?php

namespace App\Exports;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PaymentExport implements FromCollection, WithHeadings
{
    public function __construct(private readonly Request $request) {}

    public function headings(): array
    {
        return ['Receipt Number', 'Student', 'Invoice Number', 'Amount', 'Method', 'Reference', 'Date', 'Recorded By'];
    }

    public function collection(): Collection
    {
        $dateFrom = $this->request->date('date_from')?->startOfDay() ?? now()->startOfMonth();
        $dateTo = $this->request->date('date_to')?->endOfDay() ?? now()->endOfMonth();

        return Payment::with(['invoice.studentProfile.user', 'recordedBy'])
            ->whereBetween('paid_at', [$dateFrom, $dateTo])
            ->latest('paid_at')
            ->get()
            ->map(fn (Payment $payment) => [
                $payment->receipt_number,
                $payment->invoice->studentProfile->user->name,
                $payment->invoice->invoice_number,
                $payment->amount,
                $payment->payment_method->label(),
                $payment->reference_number,
                $payment->paid_at->format('Y-m-d'),
                $payment->recordedBy?->name,
            ]);
    }
}
