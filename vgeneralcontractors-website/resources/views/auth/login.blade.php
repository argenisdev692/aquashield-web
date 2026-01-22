<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        @session('status')
            <div class="mb-4 font-medium text-sm text-green-600 dark:text-green-400">
                {{ $value }}
            </div>
        @endsession

        <!-- Botón de Google con animación -->
        <div class="mb-6">
            <a href="{{ secure_url('/google-auth/redirect') }}"
                class="rounded-md flex items-center border border-slate-300 py-3 px-6 text-center text-sm transition-all duration-200 shadow-sm hover:shadow-lg text-slate-600 hover:text-white hover:bg-slate-800 hover:border-slate-800 focus:text-white focus:bg-slate-800 focus:border-slate-800 active:border-slate-800 active:text-white active:bg-slate-800 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none w-full justify-center">
                <img src="https://docs.material-tailwind.com/icons/google.svg" alt="google" class="h-5 w-5" />
                <span class="font-semibold ml-3">Continue with Google</span>
            </a>
        </div>

        <div class="relative flex items-center justify-center my-4">
            <span class="absolute inset-x-0 h-px bg-gray-300"></span>
            <span class="relative bg-white px-4 text-sm text-gray-600">Or log in with email</span>
        </div>

        <form method="POST" action="{{ secure_url(route('login', [], false)) }}">
            @csrf

            <div>
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')"
                    required autofocus autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password" required
                    autocomplete="current-password" />
            </div>

            <div class="block mt-4">
                <label for="remember_me" class="flex items-center">
                    <x-checkbox id="remember_me" name="remember" />
                    <span style="margin-left:10px" class="ml-2 text-sm text-gray-600">{{ __('Remember me') }}</span>
                </label>
            </div>

            <div class="flex items-center justify-end mt-4">
                @if (Route::has('password.request'))
                    <a class="no-underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        href="{{ secure_url(route('password.request', [], false)) }}" style="margin-right:10px">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif

                <x-button class="ml-6">
                    {{ __('Log in') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
