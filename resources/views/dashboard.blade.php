<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">Welcome back, {{ Auth::user()->name }}!</h3>
                    <p class="mt-1 text-gray-600">This is your personal dashboard. Track your activity and manage your
                        profile here.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Profile Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-4">Your Profile</h4>
                        <div class="space-y-3">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-24">Name:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->name }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-24">Email:</span>
                                <span class="font-medium text-gray-900">{{ Auth::user()->email }}</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-500 w-24">Role:</span>
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ ucfirst(Auth::user()->getRoleNames()->first() ?? 'User') }}
                                </span>
                            </div>
                            <div class="mt-6">
                                <x-primary-button onclick="window.location='{{ route('profile.edit') }}'">
                                    Edit Profile
                                </x-primary-button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h4 class="text-base font-semibold text-gray-900 mb-4">Quick Actions</h4>
                        <div class="grid grid-cols-1 gap-4">
                            <a href="#"
                                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="font-medium text-indigo-600">My Orders</div>
                                <div class="text-sm text-gray-500 mt-1">View your order history and status</div>
                            </a>
                            <a href="#"
                                class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-150">
                                <div class="font-medium text-indigo-600">Support</div>
                                <div class="text-sm text-gray-500 mt-1">Contact admin for assistance</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>