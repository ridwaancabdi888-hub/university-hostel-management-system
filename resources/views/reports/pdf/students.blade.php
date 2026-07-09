<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Report</title>
    <style>
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1f2937; }
        h1 { font-size: 20px; margin: 0 0 4px; }
        .muted { color: #6b7280; }
        .summary { display: table; width: 100%; margin: 20px 0; }
        .summary .cell { display: table-cell; width: 100%; padding: 10px; border: 1px solid #e5e7eb; text-align: center; }
        .summary .label { font-size: 10px; text-transform: uppercase; color: #6b7280; }
        .summary .value { font-size: 16px; font-weight: bold; margin-top: 4px; }
        .row { display: table; width: 100%; }
        .row .col { display: table-cell; width: 33.33%; vertical-align: top; padding-right: 12px; }
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
    <p class="muted">Student Report — Generated {{ now()->format('M j, Y') }}</p>

    <div class="summary">
        <div class="cell">
            <div class="label">Total Students</div>
            <div class="value">{{ $total }}</div>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="data">
                <thead>
                    <tr><th>Status</th><th class="amount">Count</th></tr>
                </thead>
                <tbody>
                    @foreach ($byStatus as $row)
                        <tr>
                            <td>{{ \App\Enums\StudentStatus::from($row->status)->label() }}</td>
                            <td class="amount">{{ $row->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col">
            <table class="data">
                <thead>
                    <tr><th>Year Level</th><th class="amount">Count</th></tr>
                </thead>
                <tbody>
                    @foreach ($byYearLevel as $row)
                        <tr>
                            <td>{{ \App\Enums\YearLevel::from($row->year_level)->label() }}</td>
                            <td class="amount">{{ $row->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col">
            <table class="data">
                <thead>
                    <tr><th>Gender</th><th class="amount">Count</th></tr>
                </thead>
                <tbody>
                    @foreach ($byGender as $row)
                        <tr>
                            <td>{{ \App\Enums\Gender::from($row->gender)->label() }}</td>
                            <td class="amount">{{ $row->count }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>Course</th>
                <th class="amount">Students</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($byCourse as $row)
                <tr>
                    <td>{{ $row->course }}</td>
                    <td class="amount">{{ $row->count }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">University Hostel Systems — Generated {{ now()->format('M j, Y') }}</div>
</body>
</html>
