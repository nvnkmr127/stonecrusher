<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monthly Summary Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form action="{{ route('reports.monthly') }}" method="GET" class="mb-6 flex gap-4 items-end">
                        <div class="w-32">
                            <x-input-label for="month" :value="__('Month')" />
                            <select name="month" id="month"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" {{ $m == $month ? 'selected' : '' }}>
                                        {{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <x-input-label for="year" :value="__('Year')" />
                            <select name="year" id="year"
                                class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(range(date('Y') - 5, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <x-primary-button>{{ __('View Report') }}</x-primary-button>
                        <div class="ml-auto flex gap-2">
                            <a href="{{ route('reports.monthly.export', ['month' => $month, 'year' => $year, 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export CSV</a>
                            <a href="{{ route('reports.monthly.export', ['month' => $month, 'year' => $year, 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export PDF</a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Sales</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sales Count</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total Collections</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Net (Cashflow)</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @php
                                    $monthlySales = 0;
                                    $monthlyCollections = 0;
                                @endphp
                                @forelse($reportData as $date => $data)
                                    @php
                                        $monthlySales += $data['sales'];
                                        $monthlyCollections += $data['collections'];
                                    @endphp
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <a href="{{ route('reports.daily', ['date' => $date]) }}"
                                                class="text-blue-600 hover:underline">
                                                {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                            {{ number_format($data['sales'], 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">
                                            {{ $data['sales_count'] }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600 font-medium">
                                            {{ number_format($data['collections'], 2) }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold {{ ($data['collections'] - $data['sales']) >= 0 ? 'text-green-600' : 'text-red-500' }}">
                                            {{ number_format($data['collections'] - $data['sales'], 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No activity found for
                                            this month.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                            <tfoot class="bg-gray-100 font-bold">
                                <tr>
                                    <td class="px-6 py-4">TOTAL</td>
                                    <td class="px-6 py-4 text-right">{{ number_format($monthlySales, 2) }}</td>
                                    <td class="px-6 py-4 text-right"></td>
                                    <td class="px-6 py-4 text-right text-green-800">
                                        {{ number_format($monthlyCollections, 2) }}</td>
                                    <td class="px-6 py-4 text-right">
                                        {{ number_format($monthlyCollections - $monthlySales, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>