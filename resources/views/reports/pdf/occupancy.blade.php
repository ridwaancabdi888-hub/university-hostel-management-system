<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Occupancy Report</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .summary { display: table; width: 100%; margin: 20px 0; }
        .summary .cell { display: table-cell; width: 25%; padding: 10px; border: 1px solid #e5e7eb; text-align: center; }
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
    <p class="muted">Occupancy Report — Generated {{ now()->format('M j, Y') }}</p>

    <div class="summary">
        <div class="cell">
            <div class="label">Total Rooms</div>
            <div class="value">{{ $totalRooms }}</div>
        </div>
        <div class="cell">
            <div class="label">Bed Capacity</div>
            <div class="value">{{ $totalCapacity }}</div>
        </div>
        <div class="cell">
            <div class="label">Occupied Beds</div>
            <div class="value">{{ $totalOccupied }}</div>
        </div>
        <div class="cell">
            <div class="label">Occupancy Rate</div>
            <div class="value">{{ $occupancyRate }}%</div>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Hostel</th>
                <th>Rooms</th>
                <th>Occupied/Capacity</th>
                <th class="amount">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byHostel as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['rooms'] }}</td>
                    <td>{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                    <td class="amount">{{ $row['rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="data">
        <thead>
            <tr>
                <th>Room Type</th>
                <th>Rooms</th>
                <th>Occupied/Capacity</th>
                <th class="amount">Rate</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byRoomType as $row)
                <tr>
                    <td>{{ $row['name'] }}</td>
                    <td>{{ $row['rooms'] }}</td>
                    <td>{{ $row['occupied'] }}/{{ $row['capacity'] }}</td>
                    <td class="amount">{{ $row['rate'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">University Hostel Systems — Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
