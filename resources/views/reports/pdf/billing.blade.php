<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Billing Report</title>
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
    <p class="muted">Billing Report — Generated {{ now()->format('M j, Y') }}</p>

    <div class="summary">
        <div class="cell">
            <div class="label">Total Billed</div>
            <div class="value">${{ number_format($totalBilled, 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">Total Collected</div>
            <div class="value">${{ number_format($totalCollected, 2) }}</div>
        </div>
        <div class="cell">
            <div class="label">Outstanding</div>
            <div class="value">${{ number_format($totalOutstanding, 2) }}</div>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Status</th>
                <th>Invoices</th>
                <th class="amount">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byStatus as $row)
                <tr>
                    <td>{{ \App\Enums\InvoiceStatus::from($row->status)->label() }}</td>
                    <td>{{ $row->count }}</td>
                    <td class="amount">${{ number_format($row->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Month</th>
                <th class="amount">Billed</th>
                <th class="amount">Collected</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($monthlyBreakdown as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td class="amount">${{ number_format($row['billed'], 2) }}</td>
                    <td class="amount">${{ number_format($row['collected'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">University Hostel Systems — Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
