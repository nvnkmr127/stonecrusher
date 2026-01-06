<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Audit Logs') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

                    <!-- Filters -->
                    <form method="GET" action="{{ route('audit-logs.index') }}"
                        class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <x-text-input id="description" class="block mt-1 w-full" type="text" name="description"
                                :value="request('description')" placeholder="Search description..." />
                        </div>
                        <div>
                            <x-input-label for="event" :value="__('Event')" />
                            <select name="event" id="event"
                                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">All Events</option>
                                <option value="created" {{ request('event') == 'created' ? 'selected' : '' }}>Created
                                </option>
                                <option value="updated" {{ request('event') == 'updated' ? 'selected' : '' }}>Updated
                                </option>
                                <option value="deleted" {{ request('event') == 'deleted' ? 'selected' : '' }}>Deleted
                                </option>
                                <option value="login" {{ request('event') == 'login' ? 'selected' : '' }}>Login</option>
                                <option value="logout" {{ request('event') == 'logout' ? 'selected' : '' }}>Logout
                                </option>
                            </select>
                        </div>
                        <div>
                            <x-input-label for="causer_id" :value="__('User')" />
                            <select name="causer_id" id="causer_id" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">All Users</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('causer_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <x-input-label for="subject_type" :value="__('Module')" />
                             <select name="subject_type" id="subject_type" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm block mt-1 w-full">
                                <option value="">All Modules</option>
                                @foreach($subjectTypes as $type)
                                    <option value="{{ $type['value'] }}" {{ request('subject_type') == $type['value'] ? 'selected' : '' }}>{{ $type['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                         <div>
                            <x-input-label for="date_from" :value="__('From Date')" />
                            <x-text-input id="date_from" class="block mt-1 w-full" type="date" name="date_from" :value="request('date_from')" />
                        </div>
                         <div>
                            <x-input-label for="date_to" :value="__('To Date')" />
                            <x-text-input id="date_to" class="block mt-1 w-full" type="date" name="date_to" :value="request('date_to')" />
                        </div>
                        <!-- Add more filters as needed -->
                        <div class="flex items-end">
                            <x-primary-button>
                                {{ __('Filter') }}
                            </x-primary-button>
                            <a href="{{ route('audit-logs.index') }}"
                                class="ml-2 text-sm text-gray-600 hover:text-gray-900">Reset</a>
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
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        User</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Event</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subject</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        IP Address</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Description</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Changes</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($activities as $activity)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->created_at->format('d M Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $activity->causer ? $activity->causer->name : 'System' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                    {{ $activity->event === 'created' ? 'bg-green-100 text-green-800' : '' }}
                                                    {{ $activity->event === 'updated' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                                    {{ $activity->event === 'deleted' ? 'bg-red-100 text-red-800' : '' }}
                                                    {{ $activity->event === 'login' ? 'bg-blue-100 text-blue-800' : '' }}
                                                ">
                                                {{ ucfirst($activity->event) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ class_basename($activity->subject_type) }} #{{ $activity->subject_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $activity->ip_address ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500">
                                            {{ $activity->description }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                                            @if($activity->properties->count() > 0)
                                                <div x-data="{ open: false }">
                                                    <button @click="open = !open"
                                                        class="text-indigo-600 hover:text-indigo-900 text-xs">
                                                        View Details
                                                    </button>
                                                    <div x-show="open"
                                                        class="mt-2 text-xs bg-gray-50 p-2 rounded whitespace-pre-wrap font-mono"
                                                        style="display: none;">
                                                        {{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}
                                                    </div>
                                                </div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-4 text-center text-gray-500">No activity logs found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $activities->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>