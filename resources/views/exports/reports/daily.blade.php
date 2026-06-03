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

        .section-title {
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 5px;
            margin-top: 15px;
        }

        .summary-box {
            border: 1px solid #000;
            padding: 10px;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>Daily Report - {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h2>
    </div>

    <div class="summary-box">
        <strong>Summary:</strong><br>
        Total Sales: {{ number_format($salesSummary['total_amount'], 2) }} |
        Trips: {{ $salesSummary['count'] }} |
        Volume: {{ $salesSummary['total_volume'] }} |
        Advance: {{ number_format($salesSummary['total_advance'], 2) }} |
        Paid: {{ number_format($salesSummary['total_paid'], 2) }} |
        Collections: {{ number_format($collectionSummary['total_collected'], 2) }}
    </div>

    <div class="section-title">Sales (Gate Passes)</div>
    <table>
        <thead>
            <tr>
                <th>GP#</th>
                <th>Client</th>
                <th>Vehicle</th>
                <th>Metal</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($gatePasses as $gp)
                <tr>
                    <td>{{ $gp->gate_pass_number }}</td>
                    <td>{{ $gp->client->name ?? '-' }}</td>
                    <td>{{ $gp->vehicle->vehicle_number }}</td>
                    <td>{{ $gp->metalType->name }}</td>
                    <td>{{ $gp->loading_quantity }}</td>
                    <td>{{ number_format($gp->total_amount, 2) }}</td>
                    <td>{{ number_format($gp->paid_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Collections</div>
    <table>
        <thead>
            <tr>
                <th>Client</th>
                <th>Amount</th>
                <th>Mode</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collections as $col)
                <tr>
                    <td>{{ $col->client->name ?? '-' }}</td>
                    <td>{{ number_format($col->amount, 2) }}</td>
                    <td>{{ $col->payment_mode }}</td>
                    <td>{{ $col->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>