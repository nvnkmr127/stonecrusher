<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <form action="{{ route('reports.summary', $type) }}" method="GET" class="mb-6 flex gap-4 items-end">
                        <div class="w-48">
                            <x-input-label for="start_date" :value="__('Start Date')" />
                            <x-text-input id="start_date" class="block mt-1 w-full" type="date" name="start_date" :value="$startDate" />
                        </div>
                        <div class="w-48">
                            <x-input-label for="end_date" :value="__('End Date')" />
                            <x-text-input id="end_date" class="block mt-1 w-full" type="date" name="end_date" :value="$endDate" />
                        </div>
                        <x-primary-button>{{ __('Filter') }}</x-primary-button>
                        <div class="ml-auto flex gap-2">
                            <a href="{{ route('reports.summary.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export CSV</a>
                            <a href="{{ route('reports.summary.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-3 rounded text-sm flex items-center">Export PDF</a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                                    
                                    @if($type === 'metal')
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Quantity</th>
                                    @endif
                                    
                                    @if($type === 'vehicle')
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total KM</th>
                                    @endif
                                    
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total Sales</th>
                                    
                                    @if($type === 'client')
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Transport Cost</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($data as $row)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            @if($type === 'metal')
                                                {{ $row->metalType->name ?? 'Unknown' }}
                                            @elseif($type === 'client')
                                                {{ $row->client->name ?? 'Unknown' }}
                                            @elseif($type === 'vehicle')
                                                {{ $row->vehicle->registration_number ?? 'Unknown' }}
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $row->count }}</td>
                                        
                                        @if($type === 'metal')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $row->total_qty }}</td>
                                        @endif
                                        
                                        @if($type === 'vehicle')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ $row->total_km }}</td>
                                        @endif
                                        
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-bold text-blue-600">{{ number_format($row->total_sales, 2) }}</td>
                                        
                                        @if($type === 'client')
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-500">{{ number_format($row->transport, 2) }}</td>
                                        @endif
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">No data found for this period.</td>
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
