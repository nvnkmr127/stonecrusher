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
        <h2>Client Outstanding Report</h2>
        <p>Total Sales: {{ number_format($totalSales, 2) }} | Total O/S: {{ number_format($totalOutstanding, 2) }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Phone</th>
                <th>Total Sales</th>
                <th>Total Advance</th>
                <th>Net Balance</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($clients as $client)
                <tr>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>{{ number_format($client->total_debit, 2) }}</td>
                    <td>{{ number_format($client->total_credit, 2) }}</td>
                    <td>{{ number_format(abs($client->balance), 2) }}</td>
                    <td>{{ $client->balance >= 0 ? 'Advance' : 'Outstanding' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>