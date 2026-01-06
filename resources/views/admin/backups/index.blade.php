<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Backups') }}
            </h2>
            <form action="{{ route('backups.create') }}" method="POST">
                @csrf
                <x-primary-button
                    onclick="return confirm('Are you sure you want to start a new backup? This might take a while.')">
                    {{ __('Create New Backup') }}
                </x-primary-button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Messages -->
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        File Name</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Size</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($backups as $backup)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            {{ $backup['name'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $backup['size'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $backup['date'] }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <div class="flex justify-end space-x-2">
                                                <div class="flex items-center space-x-2">
                                                    <a href="{{ route('backups.download', ['disk' => $backup['disk'], 'path' => $backup['path']]) }}"
                                                        class="text-indigo-600 hover:text-indigo-900 text-sm">Download</a>

                                                    <!-- Restore Button -->
                                                    <form action="{{ route('backups.restore') }}" method="POST"
                                                        onsubmit="return confirm('DANGER: This will overwrite your current database with the backup. Are you sure?');">
                                                        @csrf
                                                        <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                                        <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 text-sm">
                                                            Restore
                                                        </button>
                                                    </form>

                                                    <form action="{{ route('backups.destroy') }}" method="POST"
                                                        onsubmit="return confirm('Are you sure you want to delete this backup?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="disk" value="{{ $backup['disk'] }}">
                                                        <input type="hidden" name="path" value="{{ $backup['path'] }}">
                                                        <button type="submit" class="text-red-600 hover:text-red-900 text-sm ml-2">
                                                            Delete
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-4 text-center text-gray-500">No backups found.</td>
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