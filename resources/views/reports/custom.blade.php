<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Custom Date Range Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    <form action="{{ route('reports.custom') }}" method="GET" class="mb-6 flex gap-4 items-end">
                        <div class="w-48">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date"
                                :value="$startDate" />
                        </div>
                        <div class="w-48">
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date"
                                :value="$endDate" />
                        </div>
                        <x-primary-button>{{ __('Fetch Report') }}</x-primary-button>
                        <div class="ml-auto flex gap-2">
                            <a href="{{ route('reports.custom.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export CSV</a>
                            <a href="{{ route('reports.custom.export', ['start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export PDF</a>
                        </div>
                    </form>

                    <div class="mb-6 p-4 bg-gray-50 rounded-md flex justify-between">
                        <div>
                            <span class="block text-sm text-gray-500">Total Count</span>
                            <span class="text-xl font-bold">{{ $totalCount }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500">Total Sales Amount</span>
                            <span class="text-xl font-bold text-blue-600">{{ number_format($totalSales, 2) }}</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">GP#</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Client
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Material
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($sales as $sale)
                                    <tr>
                                        <td class="px-6 py-4">{{ $sale->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4">{{ $sale->gate_pass_number }}</td>
                                        <td class="px-6 py-4">{{ $sale->client->name }}</td>
                                        <td class="px-6 py-4">{{ $sale->metalType->name }}</td>
                                        <td class="px-6 py-4 text-right font-medium">
                                            {{ number_format($sale->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No sales found in this
                                            range.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>