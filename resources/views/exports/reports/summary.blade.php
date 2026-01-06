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
        <h2>{{ $title }} ({{ $startDate }} to {{ $endDate }})</h2>
    </div>

    <table>
        <thead>
            @if($type === 'metal')
                <tr>
                    <th>Metal Type</th>
                    <th>Count</th>
                    <th>Total Qty</th>
                    <th>Total Sales</th>
                </tr>
            @elseif($type === 'client')
                <tr>
                    <th>Client</th>
                    <th>Trip Count</th>
                    <th>Transport Cost</th>
                    <th>Total Purchase</th>
                </tr>
            @elseif($type === 'vehicle')
                <tr>
                    <th>Vehicle</th>
                    <th>Trip Count</th>
                    <th>Total KM</th>
                    <th>Total Revenue</th>
                </tr>
            @endif
        </thead>
        <tbody>
            @foreach($data as $row)
                <tr>
                    @if($type === 'metal')
                        <td>{{ $row->metalType->name ?? 'Unknown' }}</td>
                        <td>{{ $row->count }}</td>
                        <td>{{ $row->total_qty }}</td>
                        <td>{{ number_format($row->total_sales, 2) }}</td>
                    @elseif($type === 'client')
                        <td>{{ $row->client->name ?? 'Unknown' }}</td>
                        <td>{{ $row->count }}</td>
                        <td>{{ number_format($row->transport, 2) }}</td>
                        <td>{{ number_format($row->total_sales, 2) }}</td>
                    @elseif($type === 'vehicle')
                        <td>{{ $row->vehicle->vehicle_number ?? 'Unknown' }}</td>
                        <td>{{ $row->count }}</td>
                        <td>{{ $row->total_km }}</td>
                        <td>{{ number_format($row->total_sales, 2) }}</td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>