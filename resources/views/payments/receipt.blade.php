<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $payment->receipt_number }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: table; width: 100%; margin-bottom: 24px; }
        .header .left, .header .right { display: table-cell; vertical-align: top; }
        .header .right { text-align: right; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        table.details { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.details td { padding: 8px 0; border-bottom: 1px solid #e5e7eb; }
        table.details td.label { color: #6b7280; width: 40%; }
        table.details td.value { text-align: right; font-weight: bold; }
        .amount-row td { border-bottom: none; border-top: 2px solid #1f2937; font-size: 16px; padding-top: 12px; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <h1>University Hostel Systems</h1>
            <p class="muted">Payment Receipt {{ $payment->receipt_number }}</p>
        </div>
        <div class="right">
            <p class="muted">Date: {{ $payment->paid_at->format('M j, Y') }}</p>
            <p class="muted">Invoice: {{ $payment->invoice->invoice_number }}</p>
        </div>
    </div>

    <div>
        <strong>Received from</strong><br>
        {{ $payment->invoice->studentProfile->user->name }}<br>
        Student ID: {{ $payment->invoice->studentProfile->student_id }}
    </div>

    <table class="details">
        <tr>
            <td class="label">Billing Month</td>
            <td class="value">{{ $payment->invoice->billing_month->format('F Y') }}</td>
        </tr>
        <tr>
            <td class="label">Payment Method</td>
            <td class="value">{{ $payment->payment_method->label() }}</td>
        </tr>
        @if ($payment->reference_number)
            <tr>
                <td class="label">Reference Number</td>
                <td class="value">{{ $payment->reference_number }}</td>
            </tr>
        @endif
        @if ($payment->recordedBy)
            <tr>
                <td class="label">Recorded By</td>
                <td class="value">{{ $payment->recordedBy->name }}</td>
            </tr>
        @endif
        <tr class="amount-row">
            <td class="label">Amount Paid</td>
            <td class="value">${{ number_format($payment->amount, 2) }}</td>
        </tr>
    </table>

    @if ($payment->notes)
        <p class="muted">{{ $payment->notes }}</p>
    @endif

    <div class="footer">
        University Hostel Systems — Generated {{ now()->format('M j, Y') }}
    </div>
</body>
</html>
