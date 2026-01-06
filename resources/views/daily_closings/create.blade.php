<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Perform Daily Closing') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form action="{{ route('daily-closings.create') }}" method="GET" class="mb-6 flex gap-4 items-end">
                        <div>
                            <x-input-label for="check_date" :value="__('Select Date')" />
                            <x-text-input id="check_date" class="block mt-1 w-full" type="date" name="date"
                                :value="$date" required />
                        </div>
                        <x-primary-button>
                            {{ __('Fetch Computations') }}
                        </x-primary-button>
                    </form>

                    <div class="bg-gray-50 p-4 rounded-lg mb-6">
                        <h3 class="text-lg font-bold mb-4">Summary for
                            {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white p-4 rounded shadow">
                                <span class="block text-gray-500 text-sm">Total Sales</span>
                                <span
                                    class="text-xl font-bold text-blue-600">{{ number_format($totals['total_sales'], 2) }}</span>
                            </div>
                            <div class="bg-white p-4 rounded shadow">
                                <span class="block text-gray-500 text-sm">Total Collections</span>
                                <span
                                    class="text-xl font-bold text-green-600">{{ number_format($totals['total_cash'], 2) }}</span>
                            </div>
                            <!-- Placeholder for Expenses
                            <div class="bg-white p-4 rounded shadow">
                                <span class="block text-gray-500 text-sm">Total Expenses</span>
                                <span class="text-xl font-bold text-red-600">{{ number_format($totals['total_expenses'], 2) }}</span>
                            </div>
                            -->
                        </div>
                    </div>

                    <form action="{{ route('daily-closings.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="date" value="{{ $date }}">

                        <div class="mb-4">
                            <x-input-label for="notes" :value="__('Closing Notes')" />
                            <textarea id="notes" name="notes"
                                class="w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
                                rows="3"></textarea>
                        </div>

                        <div class="flex items-center gap-2 mb-6">
                            <input type="checkbox" id="confirm_closing" name="confirm_closing"
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                required>
                            <label for="confirm_closing" class="text-sm text-gray-700">
                                I verify these totals are correct and I want to close the day.
                                <span class="font-bold text-red-600">This action will LOCK data for this date.</span>
                            </label>
                        </div>

                        <div class="flex justify-end gap-4">
                            <a href="{{ route('daily-closings.index') }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 transition">
                                Cancel
                            </a>
                            <x-primary-button class="bg-red-600 hover:bg-red-700">
                                {{ __('Close Day') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>