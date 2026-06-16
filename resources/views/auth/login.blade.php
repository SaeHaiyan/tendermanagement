<x-guest-layout>
    <div class="mb-8 text-center">
        <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-widest">
            AITO <span class="text-red-600">Portal</span>
        </h2>
        <p class="text-sm text-gray-500 mt-2 font-medium">TENDER MANAGEMENT SYSTEM</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="w-full sm:max-w-md mt-6 px-8 py-8 bg-white shadow-xl sm:rounded-lg border-t-4 border-red-600">
        <form method="POST" action="{{ route('login') }}" id="loginForm">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Corporate Email')" class="text-gray-700 font-bold" />
                <x-text-input id="email" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="name@company.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-bold" />
                <x-text-input id="password" class="block mt-1 w-full border-gray-300 focus:border-red-500 focus:ring-red-500"
                                type="password"
                                name="password"
                                required autocomplete="current-password"
                                placeholder="••••••••" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="mt-8">
                <x-primary-button class="w-full justify-center bg-red-600 hover:bg-red-700 py-3 text-sm tracking-widest uppercase transition-all" onclick="setTabAuth()">
                    {{ ('Sign In') }}
                </x-primary-button>

                @if (Route::has('password.request'))
                    <div class="text-center mt-4">
                        <a class="text-sm text-gray-500 hover:text-red-600 transition-colors" href="{{ route('password.request') }}">
                            {{ ('Forgot your password?') }}
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <script>
        function setTabAuth() {
            sessionStorage.setItem('tab_authorized', 'true');
        }
    </script>
</x-guest-layout>
