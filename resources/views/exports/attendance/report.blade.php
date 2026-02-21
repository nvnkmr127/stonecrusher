<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 4px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Attendance Report - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
        <p style="color: #666;">Salary Payable in: <strong>{{ $payoutMonthName }}</strong> (2-month hold policy)</p>
        <p style="font-size: 9px;">* 4 days of leave per month are allowed without deduction.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Work Month</th>
                <th>Payout Month</th>
                <th>Base Salary</th>
                <th>Leave</th>
                <th>Absent</th>
                <th>Advances</th>
                <th>Deductions</th>
                <th>CF Balance</th>
                <th>Net Salary</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['user']->name }}</td>
                    <td>{{ \Carbon\Carbon::create($year, $month, 1)->format('M Y') }}</td>
                    <td>{{ $payoutMonthName }}</td>
                    <td>{{ number_format($row['base_salary'], 0) }}</td>
                    <td>{{ $row['leave'] }}</td>
                    <td>{{ $row['absent'] }}</td>
                    <td>{{ number_format($row['advances'], 0) }}</td>
                    <td>{{ number_format($row['absent_deduction'], 0) }}</td>
                    <td>{{ number_format($row['carry_forward'], 0) }}</td>
                    <td><strong>{{ number_format($row['remaining'], 0) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>