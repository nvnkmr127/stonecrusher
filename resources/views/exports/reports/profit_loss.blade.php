<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Profit & Loss Statement</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11px;
            color: #333333;
            line-height: 1.5;
            margin: 0;
            padding: 0;
        }

        .header {
            margin-bottom: 30px;
            border-bottom: 2px solid #1a365d;
            padding-bottom: 10px;
        }

        .header h1 {
            font-size: 20px;
            color: #1a365d;
            margin: 0 0 5px 0;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header p {
            margin: 0;
            color: #4a5568;
            font-size: 12px;
        }

        .company-info {
            float: right;
            text-align: right;
            font-size: 10px;
            color: #718096;
            margin-top: -45px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #2c5282;
            margin-bottom: 10px;
            text-transform: uppercase;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 8px 10px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background-color: #f7fafc;
            color: #2d3748;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
        }

        .text-right {
            text-align: right;
        }

        .amount-col {
            font-weight: 500;
            font-family: Courier, monospace;
            text-align: right;
        }

        .highlight-row {
            background-color: #ebf8ff;
            font-weight: bold;
        }

        .highlight-row td {
            border-top: 1px solid #bee3f8;
            border-bottom: 2px solid #3182ce;
        }

        .total-row {
            font-weight: bold;
            background-color: #edf2f7;
        }

        .total-row td {
            border-top: 2px solid #cbd5e0;
            border-bottom: 2px double #4a5568;
        }

        .profit-positive {
            color: #2f855a;
        }

        .profit-negative {
            color: #c53030;
        }

        .summary-box {
            background-color: #f7fafc;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 25px;
        }

        .summary-title {
            font-size: 11px;
            color: #718096;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 22px;
            font-weight: bold;
            color: #1a365d;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 9px;
            color: #a0aec0;
            border-top: 1px solid #edf2f7;
            padding-top: 10px;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Profit & Loss Statement</h1>
        @if($type === 'range')
            <p>Period: <strong>{{ \Carbon\Carbon::parse($data['period']['start'])->format('d M Y') }}</strong> to <strong>{{ \Carbon\Carbon::parse($data['period']['end'])->format('d M Y') }}</strong></p>
        @else
            <p>Year: <strong>{{ $data['year'] }}</strong></p>
        @endif
        <div class="company-info">
            <strong>Stone Crusher ERP</strong><br>
            Financial Reporting Engine<br>
            Generated: {{ \Carbon\Carbon::now()->format('d-M-Y H:i') }}
        </div>
    </div>

    @if($type === 'range')
        <!-- Summary Box -->
        <div class="summary-box">
            <div class="summary-title">Net Profit / (Loss)</div>
            <div class="summary-value {{ $data['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                {{ $data['net_profit'] >= 0 ? '' : '-' }}INR {{ number_format(abs($data['net_profit']), 2) }}
            </div>
        </div>

        <div class="section-title">Operational Revenue & Expenses</div>
        <table>
            <thead>
                <tr>
                    <th>Particulars</th>
                    <th class="text-right">Amount (INR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Sales Revenue (Metal Sales)</strong></td>
                    <td class="amount-col profit-positive">{{ number_format($data['sales'], 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Crusher Operational Expenses</td>
                    <td class="amount-col">{{ number_format($data['crusher_expense'], 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Quarry Operational Expenses</td>
                    <td class="amount-col">{{ number_format($data['quarry_expense'], 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Contractor & Internal Labour</td>
                    <td class="amount-col">{{ number_format($data['labour'], 2) }}</td>
                </tr>
                <tr>
                    <td>Less: Fuel & Diesel Consumption</td>
                    <td class="amount-col">{{ number_format($data['diesel'], 2) }}</td>
                </tr>
                @if($data['other_expense'] > 0)
                    <tr>
                        <td>Less: Other Operational Expenses</td>
                        <td class="amount-col">{{ number_format($data['other_expense'], 2) }}</td>
                    </tr>
                @endif
                <tr class="highlight-row">
                    <td>NET OPERATING PROFIT / (LOSS)</td>
                    <td class="amount-col {{ $data['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ number_format($data['net_profit'], 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @else
        <!-- Monthly breakdown table -->
        <div class="section-title">Monthly Breakdowns</div>
        <table>
            <thead>
                <tr>
                    <th>Month</th>
                    <th class="text-right">Sales</th>
                    <th class="text-right">Crusher Exp.</th>
                    <th class="text-right">Quarry Exp.</th>
                    <th class="text-right">Labour</th>
                    <th class="text-right">Diesel</th>
                    <th class="text-right">Other</th>
                    <th class="text-right">Net Profit</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalSales = 0;
                    $totalCrusher = 0;
                    $totalQuarry = 0;
                    $totalLabour = 0;
                    $totalDiesel = 0;
                    $totalOther = 0;
                    $totalNet = 0;
                @endphp
                @foreach($data['monthly_breakdown'] as $row)
                    @php
                        $totalSales += $row['sales'];
                        $totalCrusher += $row['crusher_expense'];
                        $totalQuarry += $row['quarry_expense'];
                        $totalLabour += $row['labour'];
                        $totalDiesel += $row['diesel'];
                        $totalOther += $row['other_expense'];
                        $totalNet += $row['net_profit'];
                    @endphp
                    <tr>
                        <td>{{ $row['month_name'] }}</td>
                        <td class="amount-col">{{ number_format($row['sales'], 2) }}</td>
                        <td class="amount-col">{{ number_format($row['crusher_expense'], 2) }}</td>
                        <td class="amount-col">{{ number_format($row['quarry_expense'], 2) }}</td>
                        <td class="amount-col">{{ number_format($row['labour'], 2) }}</td>
                        <td class="amount-col">{{ number_format($row['diesel'], 2) }}</td>
                        <td class="amount-col">{{ number_format($row['other_expense'], 2) }}</td>
                        <td class="amount-col {{ $row['net_profit'] >= 0 ? 'profit-positive' : 'profit-negative' }}">
                            <strong>{{ number_format($row['net_profit'], 2) }}</strong>
                        </td>
                    </tr>
                @endforeach
                <tr class="total-row">
                    <td>TOTAL YTD</td>
                    <td class="amount-col">{{ number_format($totalSales, 2) }}</td>
                    <td class="amount-col">{{ number_format($totalCrusher, 2) }}</td>
                    <td class="amount-col">{{ number_format($totalQuarry, 2) }}</td>
                    <td class="amount-col">{{ number_format($totalLabour, 2) }}</td>
                    <td class="amount-col">{{ number_format($totalDiesel, 2) }}</td>
                    <td class="amount-col">{{ number_format($totalOther, 2) }}</td>
                    <td class="amount-col {{ $totalNet >= 0 ? 'profit-positive' : 'profit-negative' }}">
                        {{ number_format($totalNet, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>This statement is auto-generated by the Stone Crusher ERP platform. Confidential - Internal Use Only.</p>
        <p>&copy; {{ date('Y') }} Stone Crusher ERP. All rights reserved.</p>
    </div>

</body>
</html>
