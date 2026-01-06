<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Filters -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200 flex items-center gap-4">
                    <form action="{{ route('reports.daily') }}" method="GET" class="flex gap-4 items-end">
                        <div class="w-48">
                            <x-input-label for="date" :value="__('Select Date')" />
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" :value="$date"
                                required />
                        </div>
                        <x-primary-button>{{ __('Fetch Report') }}</x-primary-button>
                    </form>

                    <div class="ml-auto">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ \App\Services\DayClosureService::isClosed($date) ? 'green' : 'gray' }}-100 text-{{ \App\Services\DayClosureService::isClosed($date) ? 'green' : 'gray' }}-800">
                            Status: {{ \App\Services\DayClosureService::isClosed($date) ? 'Closed' : 'Open' }}
                        </span>
                    </div>
                    <div class="flex gap-2 ml-4">
                        <a href="{{ route('reports.daily.export', ['date' => $date, 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm">CSV</a>
                        <a href="{{ route('reports.daily.export', ['date' => $date, 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm">PDF</a>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Sales Summary Block -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4">Sales Activity</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm block">Total Gate Passes</span>
                                <span class="text-2xl font-bold">{{ $salesSummary['count'] }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm block">Total Sales</span>
                                <span class="text-2xl font-bold text-blue-600">{{ number_format($salesSummary['total_amount'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm block">Diesel Cost</span>
                                <span class="text-xl font-bold text-red-500">{{ number_format($salesSummary['total_diesel'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm block">Advances Adjusted</span>
                                <span class="text-xl font-bold text-orange-500">{{ number_format($salesSummary['total_advance'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm block">Outstanding</span>
                                <span class="text-xl font-bold text-indigo-600">{{ number_format($salesSummary['outstanding'], 2) }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm block">Volume/Qty</span>
                                <span class="text-xl font-bold">{{ number_format($salesSummary['total_volume'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Collections Summary Block -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 bg-white border-b border-gray-200">
                        <h3 class="text-lg font-bold mb-4">Collections (Cash In)</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">Total Collected</span>
                                <div class="text-2xl font-bold text-green-600">
                                    {{ number_format($collectionSummary['total_collected'], 2) }}</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <span class="text-sm font-semibold text-gray-700">Breakdown by Mode:</span>
                            <ul class="mt-1 text-sm text-gray-600">
                                @forelse($collectionSummary['by_mode'] as $mode => $amount)
                                    <li class="flex justify-between">
                                        <span>{{ $mode ?: 'Unknown' }}</span>
                                        <span>{{ number_format($amount, 2) }}</span>
                                    </li>
                                @empty
                                    <li>No collections today.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            </div>
            
            <!-- Metal Wise Summary (Use Case 6.1) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">Metal-wise Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Metal Type</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Quantity</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @forelse($metalStats as $stat)
                                    <tr>
                                        <td class="px-4 py-2 font-medium">{{ $stat['name'] }}</td>
                                        <td class="px-4 py-2 text-right">{{ $stat['count'] }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($stat['quantity'], 2) }}</td>
                                        <td class="px-4 py-2 text-right">{{ number_format($stat['amount'], 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-4 py-2 text-center text-gray-500">No data.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Detailed Lists -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold mb-4">Completed Sales (Gate Passes)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead>
                                <tr>
                                    <th class="px-4 py-2 text-left">GP#</th>
                                    <th class="px-4 py-2 text-left">Client</th>
                                    <th class="px-4 py-2 text-left">Vehicle / Driver</th>
                                    <th class="px-4 py-2 text-left">Material</th>
                                    <th class="px-4 py-2 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($gatePasses as $gp)
                                    <tr>
                                        <td class="px-4 py-2">{{ $gp->gate_pass_number }}</td>
                                        <td class="px-4 py-2">{{ $gp->client->name }}</td>
                                        <td class="px-4 py-2">{{ $gp->vehicle->registration_number }} /
                                            {{ $gp->driver_name }}</td>
                                        <td class="px-4 py-2">{{ $gp->metalType->name }}
                                            ({{ $gp->loading_quantity ?: $gp->net_weight }})</td>
                                        <td class="px-4 py-2 text-right font-medium">
                                            {{ number_format($gp->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-2 text-center text-gray-500">No sales found.</td>
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