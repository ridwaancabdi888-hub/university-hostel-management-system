<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Hostel Report</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
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
    <p class="muted">Hostel Report — Generated {{ now()->format('M j, Y') }}</p>

    <table class="data">
        <thead>
            <tr>
                <th>Hostel</th>
                <th>Blocks</th>
                <th>Rooms</th>
                <th>Occupied/Capacity</th>
                <th>Rate</th>
                <th>Active Maintenance</th>
                <th class="amount">Revenue</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($hostels as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['blocks'] }}</td>
                    <td>{{ $row['rooms'] }}</td>
                    <td>{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                    <td>{{ $row['rate'] }}%</td>
                    <td>{{ $row['activeMaintenance'] }}</td>
                    <td class="amount">${{ number_format($row['revenue'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">University Hostel Systems — Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
