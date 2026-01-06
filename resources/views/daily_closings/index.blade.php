<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Daily Closings') }}
            </h2>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                <a href="{{ route('daily-closings.create') }}"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                    Perform Closing
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if(session('success'))
                        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
                            role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif

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
                                        Total Collections</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Closed By</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notes</th>
                                    <th
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($closings as $closing)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $closing->date->format('d M Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            {{ number_format($closing->total_sales, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            {{ number_format($closing->total_cash, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $closing->closedBy->name ?? 'N/A' }}</td>
                                        <td class="px-6 py-4">{{ Str::limit($closing->notes, 30) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            @if(auth()->user()->hasRole('admin'))
                                                @if($closing->status === 'closed')
                                                    <button type="button" 
                                                        onclick="openReopenModal('{{ $closing->date->format('Y-m-d') }}', '{{ route('daily-closings.reopen', $closing) }}')"
                                                        class="text-red-500 hover:text-red-700 ml-4 font-semibold text-xs uppercase tracking-widest">
                                                        Reopen
                                                    </button>
                                                @else
                                                    <span class="text-gray-400 ml-4 text-xs italic">Reopened</span>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No closings found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $closings->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reopen Modal -->
    <div id="reopenModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full"
        x-data="{ open: false }">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Reopen Day</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">Provide a reason to reopen <span id="modalDate"
                            class="font-bold"></span>.</p>
                    <form id="reopenForm" method="POST">
                        @csrf
                        <textarea name="reason" rows="3" class="mt-2 w-full border rounded-md p-2"
                            placeholder="Reason for reopening..." required minlength="5"></textarea>
                        <div class="items-center px-4 py-3">
                            <button type="submit"
                                class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                                Confirm Reopen
                            </button>
                        </div>
                    </form>
                    <button type="button" onclick="document.getElementById('reopenModal').classList.add('hidden')"
                        class="mt-2 px-4 py-2 bg-gray-100 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openReopenModal(date, actionUrl) {
            document.getElementById('modalDate').innerText = date;
            document.getElementById('reopenForm').action = actionUrl;
            document.getElementById('reopenModal').classList.remove('hidden');
        }
    </script>
</x-app-layout>