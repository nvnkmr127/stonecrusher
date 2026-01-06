<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reports Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Daily Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Daily Closing Report</h3>
                        <p class="text-gray-600 mb-4">View daily sales, collections, and financial closing status.</p>
                        <a href="{{ route('reports.daily') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Daily Report &rarr;</a>
                    </div>
                </div>

                <!-- Monthly Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Monthly Summary</h3>
                        <p class="text-gray-600 mb-4">Consolidated monthly view of sales and collections.</p>
                        <a href="{{ route('reports.monthly') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Monthly Report &rarr;</a>
                    </div>
                </div>

                <!-- Outstanding & Advance Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Result: Outstanding Balances</h3>
                        <p class="text-gray-600 mb-4">Track client receivables and advance payments.</p>
                        <a href="{{ route('reports.outstanding') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Outstanding &rarr;</a>
                    </div>
                </div>

                <!-- Custom Range Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Custom Date Range</h3>
                        <p class="text-gray-600 mb-4">Detailed sales list for any specific date range.</p>
                        <a href="{{ route('reports.custom') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Custom Report &rarr;</a>
                    </div>
                </div>

                <!-- Metal Wise -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Metal-wise Sales</h3>
                        <p class="text-gray-600 mb-4">Breakdown of sales by Metal Type.</p>
                        <a href="{{ route('reports.summary', 'metal') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Breakdown &rarr;</a>
                    </div>
                </div>

                <!-- Client Wise -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Client-wise Sales</h3>
                        <p class="text-gray-600 mb-4">Top clients and sales volume analysis.</p>
                        <a href="{{ route('reports.summary', 'client') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Client Report &rarr;</a>
                    </div>
                </div>

                <!-- Distance & Transport Report -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Distance & Transport</h3>
                        <p class="text-gray-600 mb-4">Transport cost analysis and distance reporting.</p>
                        <a href="{{ route('gate-passes.distance-report') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Distance Report &rarr;</a>
                    </div>
                </div>

                <!-- Vehicle Wise -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Vehicle Performance</h3>
                        <p class="text-gray-600 mb-4">Trip counts and distance covered by vehicles.</p>
                        <a href="{{ route('reports.summary', 'vehicle') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Vehicle Report &rarr;</a>
                    </div>
                </div>

                <!-- Attendance Report Link -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-2">Attendance Report</h3>
                        <p class="text-gray-600 mb-4">Employee attendance logs and status.</p>
                        <a href="{{ route('attendance.report') }}"
                            class="text-blue-600 hover:text-blue-800 font-semibold">View Attendance Log &rarr;</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>