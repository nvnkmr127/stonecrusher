<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required
                autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />

            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required
                autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox"
                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif

            <x-primary-button class="ms-3">
                {{ __('Log in') }}
            </x-primary-button>
        </div>
    </form>

    @if(app()->isLocal())
        <div class="mt-8 border-t pt-6">
            <h3 class="text-sm font-medium text-gray-500 mb-3 text-center">Test Auto Login</h3>
            <div class="grid grid-cols-2 gap-2">
                <button onclick="autoLogin('admin@example.com', 'password')"
                    class="px-3 py-2 bg-gray-800 text-white text-xs rounded hover:bg-gray-700 transition">Admin</button>
                <button onclick="autoLogin('manager@example.com', 'password')"
                    class="px-3 py-2 bg-blue-600 text-white text-xs rounded hover:bg-blue-500 transition">Manager</button>
                <button onclick="autoLogin('accountant@example.com', 'password')"
                    class="px-3 py-2 bg-green-600 text-white text-xs rounded hover:bg-green-500 transition">Accountant</button>
                <button onclick="autoLogin('user@example.com', 'password')"
                    class="px-3 py-2 bg-gray-500 text-white text-xs rounded hover:bg-gray-400 transition">User</button>
            </div>
            <script>
                function autoLogin(email, password) {
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = password;
                    document.querySelector('form').submit();
                }
            </script>
        </div>
    @endif
</x-guest-layout>