<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        .header { display: table; width: 100%; margin-bottom: 24px; }
        .header .left, .header .right { display: table-cell; vertical-align: top; }
        .header .right { text-align: right; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        table.charges { width: 100%; border-collapse: collapse; margin-top: 24px; }
        table.charges th, table.charges td { padding: 8px 0; text-align: left; }
        table.charges th { border-bottom: 2px solid #1f2937; font-size: 11px; text-transform: uppercase; }
        table.charges td { border-bottom: 1px solid #e5e7eb; }
        table.charges .amount { text-align: right; }
        .total-row td { border-bottom: none; border-top: 2px solid #1f2937; font-weight: bold; font-size: 14px; padding-top: 12px; }
        .discount { color: #dc2626; }
        .status { display: inline-block; padding: 4px 10px; border-radius: 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .status-unpaid { background: #fef3c7; color: #92400e; }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-overdue { background: #fee2e2; color: #991b1b; }
        .status-cancelled { background: #f3f4f6; color: #4b5563; }
        .footer { margin-top: 40px; font-size: 10px; color: #9ca3af; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <div class="left">
            <h1>University Hostel Systems</h1>
            <p class="muted">Invoice {{ $invoice->invoice_number }}</p>
        </div>
        <div class="right">
            @php
                $state = $invoice->isOverdue() ? 'overdue' : $invoice->status->value;
                $labels = ['unpaid' => 'Unpaid', 'paid' => 'Paid', 'cancelled' => 'Cancelled', 'overdue' => 'Overdue'];
            @endphp
            <span class="status status-{{ $state }}">{{ $labels[$state] }}</span>
            <p class="muted">Billing month: {{ $invoice->billing_month->format('F Y') }}</p>
            <p class="muted">Due date: {{ $invoice->due_date->format('M j, Y') }}</p>
        </div>
    </div>

    <div>
        <strong>Billed to</strong><br>
        {{ $invoice->studentProfile->user->name }}<br>
        Student ID: {{ $invoice->studentProfile->student_id }}<br>
        {{ $invoice->studentProfile->user->email }}
        @if ($invoice->roomAllocation)
            <br>Room {{ $invoice->roomAllocation->room->room_number }} — {{ $invoice->roomAllocation->room->floor->block->name }} ({{ $invoice->roomAllocation->room->floor->block->hostel->name }})
        @endif
    </div>

    <table class="charges">
        <thead>
            <tr>
                <th>Description</th>
                <th class="amount">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Monthly Rent</td>
                <td class="amount">${{ number_format($invoice->rent_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Utility Charges</td>
                <td class="amount">${{ number_format($invoice->utility_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Late Fee</td>
                <td class="amount">${{ number_format($invoice->late_fee_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Discount</td>
                <td class="amount discount">-${{ number_format($invoice->discount_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td>Total Due</td>
                <td class="amount">${{ number_format($invoice->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    @if ($invoice->notes)
        <p class="muted">{{ $invoice->notes }}</p>
    @endif

    @if ($invoice->status->value === 'paid')
        <p class="muted">Paid on {{ $invoice->paid_at->format('M j, Y') }}</p>
    @endif

    <div class="footer">
        University Hostel Systems — Generated {{ now()->format('M j, Y') }}
    </div>
</body>
</html>
