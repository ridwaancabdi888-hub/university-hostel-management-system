<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Report</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .summary { display: table; width: 100%; margin: 20px 0; }
        .summary .cell { display: table-cell; width: 33.33%; padding: 10px; border: 1px solid #e5e7eb; text-align: center; }
        .summary .label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .summary .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 20px; }
        table.data th, table.data td { padding: 6px 8px; text-align: left; }
        table.data th { border-bottom: 2px solid #1f2937; font-size: 10px; text-transform: uppercase; }
        table.data td { border-bottom: 1px solid #e5e7eb; }
        table.data .amount { text-align: right; }
        .footer { margin-top: 30px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <h1>University Hostel Systems</h1>
    <p class="muted">Payment Report — {{ $filters['date_from'] }} to {{ $filters['date_to'] }} — Generated {{ now()->format('M j, Y') }}</p>

    <div class="summary">
        <div class="cell">
            <div class="label">Today's Income</div>
            <div class="value">${{ number_format($todayIncome, 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">This Month's Income</div>
            <div class="value">${{ number_format($monthIncome, 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">Outstanding Balance</div>
            <div class="value">${{ number_format($pendingBalance, 2) }}</div>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Method</th>
                <th>Payments</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byMethod as $row)
                <tr>
                    <td>{{ \App\Enums\PaymentMethod::from($row->payment_method)->label() }}</td>
                    <td>{{ $row->count }}</td>
                    <td class="amount">${{ number_format($row->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Receipt</th>
                <th>Student</th>
                <th>Invoice</th>
                <th>Method</th>
                <th>Date</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($payments as $payment)
                <tr>
                    <td>{{ $payment->receipt_number }}</td>
                    <td>{{ $payment->invoice->studentProfile->user->name }}</td>
                    <td>{{ $payment->invoice->invoice_number }}</td>
                    <td>{{ $payment->payment_method->label() }}</td>
                    <td>{{ $payment->paid_at->format('M j, Y') }}</td>
                    <td class="amount">${{ number_format($payment->amount, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No payments in this range.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">University Hostel Systems — Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
