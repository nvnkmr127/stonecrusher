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
    </style>
</head>

<body>
    <div class="header">
        <h2>Distance Report ({{ $startDate }} to {{ $endDate }})</h2>
        <p>Trips: {{ $summary['total_trips'] }} | Dist: {{ $summary['total_distance'] }} km | Cost:
            {{ number_format($summary['total_cost'], 2) }}</p>
    </div>

    <div class="section-title">Location Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Location</th>
                <th>Trips</th>
                <th>Distance</th>
                <th>Cost</th>
                <th>Cost/km</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row->delivery_location ?? 'Unknown' }}</td>
                    <td>{{ $row->trip_count }}</td>
                    <td>{{ $row->total_distance }}</td>
                    <td>{{ number_format($row->total_cost, 2) }}</td>
                    <td>{{ number_format($row->cost_per_km, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Range Analysis</div>
    <table>
        <thead>
            <tr>
                <th>Range</th>
                <th>Count</th>
                <th>Total Dist</th>
                <th>Total Cost</th>
                <th>Avg Cost/km</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rangeStats as $row)
                <tr>
                    <td>{{ $row->range_label }}</td>
                    <td>{{ $row->count }}</td>
                    <td>{{ $row->total_dist }}</td>
                    <td>{{ number_format($row->total_cost, 2) }}</td>
                    <td>{{ number_format($row->avg_cost_per_km, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>