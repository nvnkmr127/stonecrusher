<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Outstanding & Advance Report') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Outstanding Receivables (Values are Negative) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-red-600 mb-4">Outstanding Receivables (Clients Owe Us)</h3>

                    @if($outstandingClients->isEmpty())
                        <p class="text-gray-500 italic">No outstanding balances.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contact</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Outstanding Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($outstandingClients as $client)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                {{ $client->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $client->phone }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-red-600 font-bold">
                                                {{ number_format(abs($client->current_balance), 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <th colspan="2"
                                            class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total
                                            Outstanding</th>
                                        <th class="px-6 py-3 text-right text-md font-bold text-red-700">
                                            {{ number_format(abs($outstandingClients->sum('current_balance')), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Advance Balances (Values are Positive) -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-bold text-green-600 mb-4">Advance Balances (Credit Available)</h3>

                    @if($advanceClients->isEmpty())
                        <p class="text-gray-500 italic">No advance balances.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Client Name</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Contact</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Credit Balance</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($advanceClients as $client)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                                {{ $client->name }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">{{ $client->phone }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-green-600 font-bold">
                                                {{ number_format($client->current_balance, 2) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <th colspan="2"
                                            class="px-6 py-3 text-right text-xs font-bold text-gray-700 uppercase">Total
                                            Advances</th>
                                        <th class="px-6 py-3 text-right text-md font-bold text-green-700">
                                            {{ number_format($advanceClients->sum('current_balance'), 2) }}
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>