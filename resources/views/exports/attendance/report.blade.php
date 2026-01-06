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
    </div>

    <table>
        <thead>
            <tr>
                <th>Employee</th>
                <th>Present</th>
                <th>Late</th>
                <th>Half Day</th>
                <th>Absent</th>
                <th>Leave</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['user']->name }}</td>
                    <td>{{ $row['present'] }}</td>
                    <td>{{ $row['late'] }}</td>
                    <td>{{ $row['half_day'] }}</td>
                    <td>{{ $row['absent'] }}</td>
                    <td>{{ $row['leave'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>