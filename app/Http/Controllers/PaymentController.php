<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Models\Invoice;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Record a payment against the given invoice.
     */
    public function store(PaymentRequest $request, Invoice $invoice): RedirectResponse
    {
        DB::transaction(function () use ($request, $invoice) {
            $invoice->payments()->create([
                ...$request->validated(),
                'recorded_by' => $request->user()->id,
            ]);
        });

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment recorded.');
    }

    /**
     * Remove a recorded payment, correcting a mistaken entry.
     */
    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $payment->invoice;
        $payment->delete();

        return redirect()->route('invoices.show', $invoice)->with('status', 'Payment removed.');
    }

    /**
     * Download the payment receipt as a PDF.
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['invoice.studentProfile.user', 'recordedBy']);

        return Pdf::loadView('payments.receipt', ['payment' => $payment])
            ->download("{$payment->receipt_number}.pdf");
    }
}
