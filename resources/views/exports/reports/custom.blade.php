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
        <h2>Custom Report ({{ $startDate }} to {{ $endDate }})</h2>
        <p>Total Sales: {{ number_format($totalSales, 2) }} | Total Trips: {{ $totalCount }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>GP#</th>
                <th>Client</th>
                <th>Vehicle</th>
                <th>Metal</th>
                <th>Qty</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $gp)
                <tr>
                    <td>{{ $gp->date->format('Y-m-d') }}</td>
                    <td>{{ $gp->gate_pass_number }}</td>
                    <td>{{ $gp->client->name ?? '-' }}</td>
                    <td>{{ $gp->vehicle->vehicle_number }}</td>
                    <td>{{ $gp->metalType->name }}</td>
                    <td>{{ $gp->loading_quantity }}</td>
                    <td>{{ number_format($gp->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>