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
        <h2>Monthly Report - {{ \Carbon\Carbon::createFromDate($year, $month, 1)->format('F Y') }}</h2>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Sales Count</th>
                <th>Total Sales</th>
                <th>Collections</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['date'] }}</td>
                    <td>{{ $row['sales_count'] }}</td>
                    <td>{{ number_format($row['sales'], 2) }}</td>
                    <td>{{ number_format($row['collections'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>