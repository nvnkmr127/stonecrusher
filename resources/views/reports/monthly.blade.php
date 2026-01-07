<x-tabler-layout>
    <x-slot name="header">
        <h2 class="page-title">
            {{ __('Monthly Summary Report') }}
        </h2>
    </x-slot>

    <div class="row row-cards">
        <div class="col-12">
            <div class="card">
                <div class="card-body border-bottom py-3">

                    <form action="{{ route('reports.monthly') }}" method="GET" class="d-flex gap-2 align-items-end">
                        <div class="mb-3">
                            <label class="form-label">{{ __('Month') }}</label>
                            <select name="month" id="month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">{{ __('Year') }}</label>
                            <select name="year" id="year" class="form-select">
                                @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <button type="submit" class="btn btn-primary">{{ __('View Report') }}</button>
                        </div>
                        <div class="ms-auto mb-3 d-flex gap-2">
                            <a href="{{ route('reports.monthly.export', ['month' => $month, 'year' => $year, 'format' => 'csv']) }}"
                                class="btn btn-success">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-spreadsheet" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z">
                                    </path>
                                    <path d="M8 11h8v7h-8z"></path>
                                    <path d="M8 15h8"></path>
                                    <path d="M11 11v7"></path>
                                </svg>
                                Export CSV
                            </a>
                            <a href="{{ route('reports.monthly.export', ['month' => $month, 'year' => $year, 'format' => 'pdf']) }}"
                                class="btn btn-danger">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="icon icon-tabler icon-tabler-file-type-pdf" width="24" height="24"
                                    viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                    <path d="M14 3v4a1 1 0 0 0 1 1h4"></path>
                                    <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4"></path>
                                    <path d="M5 18h1.5a1.5 1.5 0 0 0 0 -3h-1.5v6"></path>
                                    <path d="M17 18h2"></path>
                                    <path d="M20 15h-3v6"></path>
                                    <path d="M11 15v6h1a2 2 0 0 0 2 -2v-2a2 2 0 0 0 -2 -2h-1z"></path>
                                </svg>
                                Export PDF
                            </a>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-vcenter card-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th class="text-end">Total Sales</th>
                                    <th class="text-end">Sales Count</th>
                                    <th class="text-end">Total Collections</th>
                                    <th class="text-end">Net (Cashflow)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reportData as $date => $data)
                                    <tr>
                                        <td>
                                            <a href="{{ route('reports.daily', ['date' => $date]) }}">
                                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                            </a>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($data['sales'], 2) }}
                                        </td>
                                        <td class="text-end">
                                            {{ $data['sales_count'] }}
                                        </td>
                                        <td class="text-end text-success">
                                            {{ number_format($data['collections'], 2) }}
                                        </td>
                                        <td
                                            class="text-end fw-bold {{ ($data['collections'] - $data['sales']) >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ number_format($data['collections'] - $data['sales'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No activity found for
                                            this month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="fw-bold bg-light">
                                <tr>
                                    <td>TOTAL</td>
                                    <td class="text-end">{{ number_format($totalSales, 2) }}</td>
                                    <td class="text-end"></td>
                                    <td class="text-end text-success">
                                        {{ number_format($totalCollections, 2) }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format($totalCollections - $totalSales, 2) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-tabler-layout>