<nav x-data="{ open: false, blogMenuOpen: false, appointmentMenuOpen: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-mark />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
                        {{ __('dashboard') }}
                    </x-nav-link>

                    <!-- Administration Dropdown Menu -->
                    @if (auth()->check() && (auth()->user()->can('READ_COMPANY_DATA') || auth()->user()->can('READ_USER')))
                        <div class="hidden sm:flex sm:items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 
                                    {{ request()->routeIs('company-data') || request()->routeIs('users')
                                        ? 'border-gray-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}
                                    text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('administration') }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                    style="display: none;">
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                        @can('READ_COMPANY_DATA')
                                            <a href="{{ route('company-data') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('company-data') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('company_data') }}
                                            </a>
                                        @endcan

                                        @can('READ_USER')
                                            <a href="{{ route('users') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('users') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('users') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Services Dropdown Menu -->
                    @if (auth()->check() && (auth()->user()->can('READ_EMAIL_DATA') || auth()->user()->can('READ_SERVICE_CATEGORY')))
                        <div class="hidden sm:flex sm:items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 
                                    {{ request()->routeIs('email-datas') || request()->routeIs('service-categories')
                                        ? 'border-gray-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}
                                    text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('services') }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                    style="display: none;">
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                        @can('READ_EMAIL_DATA')
                                            <a href="{{ route('email-datas') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('email-datas') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('emails') }}
                                            </a>
                                        @endcan

                                        @can('READ_SERVICE_CATEGORY')
                                            <a href="{{ route('service-categories') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('service-categories') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('service_categories') }}
                                            </a>
                                        @endcan

                                        @can('READ_USER')
                                            <a href="{{ route('call-records') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('call-records') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('Call Records') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Retell AI Dropdown Menu -->
                    @if (auth()->check() && auth()->user()->can('READ_USER'))
                        <div class="hidden sm:flex sm:items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 
                                    {{ request()->routeIs('call-records')
                                        ? 'border-gray-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}
                                    text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('retell_ai') }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                    style="display: none;">
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                        <a href="{{ route('call-records') }}"
                                            class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('call-records') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            {{ __('Call Records') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Appointments Dropdown Menu -->
                    @if (auth()->check() && auth()->user()->can('READ_APPOINTMENT'))
                        <div class="hidden sm:flex sm:items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 
                                    {{ request()->routeIs('appointments') || request()->routeIs('appointment-calendar')
                                        ? 'border-gray-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}
                                    text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('appointments') }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                    style="display: none;">
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                        <a href="{{ route('appointments.index') }}"
                                            class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('appointments.index') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            {{ __('manage_appointments') }}
                                        </a>
                                        <a href="{{ route('appointment-calendar') }}"
                                            class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('appointment-calendar') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                            {{ __('calendar_view') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endcan

                    <!-- Blog Dropdown Menu -->
                    @if (auth()->check() && (auth()->user()->can('READ_POST') || auth()->user()->can('READ_BLOG_CATEGORY')))
                        <div class="hidden sm:flex sm:items-center">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 
                                    {{ request()->routeIs('admin.posts') || request()->routeIs('blog-categories')
                                        ? 'border-gray-400 dark:border-indigo-600 text-gray-900 dark:text-gray-100'
                                        : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-700' }}
                                    text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out">
                                    {{ __('blog_management') }}
                                    <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>

                                <div x-show="open" @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="transform opacity-0 scale-95"
                                    x-transition:enter-end="transform opacity-100 scale-100"
                                    x-transition:leave="transition ease-in duration-75"
                                    x-transition:leave-start="transform opacity-100 scale-100"
                                    x-transition:leave-end="transform opacity-0 scale-95"
                                    class="absolute z-50 mt-2 w-48 rounded-md shadow-lg origin-top-right right-0"
                                    style="display: none;">
                                    <div
                                        class="rounded-md ring-1 ring-black ring-opacity-5 py-1 bg-white dark:bg-gray-700">
                                        @can('READ_POST')
                                            <a href="{{ route('admin.posts') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('admin.posts') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('posts') }}
                                            </a>
                                        @endcan

                                        @can('READ_BLOG_CATEGORY')
                                            <a href="{{ route('blog-categories') }}"
                                                class="block px-4 py-2 text-sm leading-5 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-600 {{ request()->routeIs('blog-categories') ? 'bg-gray-100 dark:bg-gray-600' : '' }}">
                                                {{ __('blog_categories') }}
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (auth()->check() && auth()->user()->can('READ_PORTFOLIO'))
                        <x-nav-link href="{{ route('portfolios') }}" :active="request()->routeIs('portfolios')">
                            {{ __('portfolio') }}
                        </x-nav-link>
                    @endif

                    <!-- Translation Demo Link -->
                    @auth
                        <x-nav-link href="{{ route('translation-demo') }}" :active="request()->routeIs('translation-demo')">
                            {{ __('language') }} {{ __('demo') }}
                        </x-nav-link>
                    @endauth
            </div>
        </div>

        <div class="hidden sm:flex sm:items-center sm:ms-6">
            <!-- Language Switcher -->
            <div class="me-3">
                <x-language-switcher />
            </div>

            <!-- Teams Dropdown -->
            @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
                <div class="ms-3 relative">
                    <x-dropdown align="right" width="60">
                        <x-slot name="trigger">
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ Auth::user()->currentTeam->name }}

                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                    </svg>
                                </button>
                            </span>
                        </x-slot>

                        <x-slot name="content">
                            <div class="w-60">
                                <!-- Team Management -->
                                <div class="block px-4 py-2 text-xs text-gray-400">
                                    {{ __('Manage Team') }}
                                </div>

                                <!-- Team Settings -->
                                <x-dropdown-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}">
                                    {{ __('team_settings') }}
                                </x-dropdown-link>

                                @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
                                    <x-dropdown-link href="{{ route('teams.create') }}">
                                        {{ __('create_new_team') }}
                                    </x-dropdown-link>
                                @endcan

                                <!-- Team Switcher -->
                                @if (Auth::user()->allTeams()->count() > 1)
                                    <div class="border-t border-gray-200 dark:border-gray-600"></div>

                                    <div class="block px-4 py-2 text-xs text-gray-400">
                                        {{ __('Switch Teams') }}
                                    </div>

                                    @foreach (Auth::user()->allTeams() as $team)
                                        <x-switchable-team :team="$team" />
                                    @endforeach
                                @endif
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>
            @endif

            <!-- Settings Dropdown -->
            <div class="ms-3 relative">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <button
                                class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                <img class="size-8 rounded-full object-cover"
                                    src="{{ Auth::user()->profile_photo_url }}"
                                    alt="{{ Auth::user()->name }}" />
                            </button>
                        @else
                            <span class="inline-flex rounded-md">
                                <button type="button"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none focus:bg-gray-50 dark:focus:bg-gray-700 active:bg-gray-50 dark:active:bg-gray-700 transition ease-in-out duration-150">
                                    {{ Auth::user()->name }}

                                    <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>
                            </span>
                        @endif
                    </x-slot>

                    <x-slot name="content">
                        <!-- Account Management -->
                        <div class="block px-4 py-2 text-xs text-gray-400">
                            {{ __('manage_account') }}
                        </div>

                        <x-dropdown-link href="{{ route('profile.show') }}">
                            {{ __('profile') }}
                        </x-dropdown-link>

                        @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                            <x-dropdown-link href="{{ route('api-tokens.index') }}">
                                {{ __('api_tokens') }}
                            </x-dropdown-link>
                        @endif

                        <div class="border-t border-gray-200 dark:border-gray-600"></div>

                        <!-- Authentication -->
                        <form method="POST" action="{{ secure_url(route('logout', [], false)) }}" x-data>
                            @csrf

                            <x-dropdown-link href="{{ secure_url(route('logout', [], false)) }}"
                                @click.prevent="$root.submit();">
                                {{ __('log_out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        </div>

        <!-- Hamburger -->
        <div class="-me-2 flex items-center sm:hidden">
            <button @click="open = ! open"
                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                <svg class="size-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                        stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
</div>
</div>

<!-- Responsive Navigation Menu -->
<div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
    <div class="pt-2 pb-3 space-y-1">
        <x-responsive-nav-link href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
            {{ __('dashboard') }}
        </x-responsive-nav-link>

        <!-- Administration Dropdown (Mobile) -->
        @if (auth()->check() && (auth()->user()->can('READ_COMPANY_DATA') || auth()->user()->can('READ_USER')))
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 
                        {{ request()->routeIs('company-data') || request()->routeIs('users')
                            ? 'border-gray-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                        text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    <span>{{ __('Administration') }}</span>
                    <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="mt-2 space-y-1" style="display: none;">
                    @can('READ_COMPANY_DATA')
                        <x-responsive-nav-link href="{{ route('company-data') }}" :active="request()->routeIs('company-data')" class="pl-8">
                            {{ __('Company Data') }}
                        </x-responsive-nav-link>
                    @endcan

                    @can('READ_USER')
                        <x-responsive-nav-link href="{{ route('users') }}" :active="request()->routeIs('users')" class="pl-8">
                            {{ __('Users') }}
                        </x-responsive-nav-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Services Dropdown (Mobile) -->
        @if (auth()->check() && (auth()->user()->can('READ_EMAIL_DATA') || auth()->user()->can('READ_SERVICE_CATEGORY')))
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 
                        {{ request()->routeIs('email-datas') || request()->routeIs('service-categories')
                            ? 'border-gray-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                        text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    <span>{{ __('Services') }}</span>
                    <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="mt-2 space-y-1" style="display: none;">
                    @can('READ_EMAIL_DATA')
                        <x-responsive-nav-link href="{{ route('email-datas') }}" :active="request()->routeIs('email-datas')" class="pl-8">
                            {{ __('Emails') }}
                        </x-responsive-nav-link>
                    @endcan

                    @can('READ_SERVICE_CATEGORY')
                        <x-responsive-nav-link href="{{ route('service-categories') }}" :active="request()->routeIs('service-categories')"
                            class="pl-8">
                            {{ __('Service Categories') }}
                        </x-responsive-nav-link>
                    @endcan
                </div>
            </div>
        @endif

        <!-- Retell AI Dropdown (Mobile) -->
        @if (auth()->check() && auth()->user()->can('READ_USER'))
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 
                        {{ request()->routeIs('call-records')
                            ? 'border-gray-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                        text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    <span>{{ __('Retell AI') }}</span>
                    <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="mt-2 space-y-1" style="display: none;">
                    <x-responsive-nav-link href="{{ route('call-records') }}" :active="request()->routeIs('call-records')" class="pl-8">
                        {{ __('call_records') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endif

        <!-- Appointments Dropdown (Mobile) -->
        @if (auth()->check() && auth()->user()->can('READ_APPOINTMENT'))
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 
                        {{ request()->routeIs('appointments.index') || request()->routeIs('appointment-calendar')
                            ? 'border-gray-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                        text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    <span>{{ __('Appointments') }}</span>
                    <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="mt-2 space-y-1" style="display: none;">
                    <x-responsive-nav-link href="{{ route('appointments.index') }}" :active="request()->routeIs('appointments.index')"
                        class="pl-8">
                        {{ __('Manage Appointments') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link href="{{ route('appointment-calendar') }}" :active="request()->routeIs('appointment-calendar')"
                        class="pl-8">
                        {{ __('Calendar View') }}
                    </x-responsive-nav-link>
                </div>
            </div>
        @endcan

        <!-- Blog Management Dropdown (Mobile) -->
        @if (auth()->check() && (auth()->user()->can('READ_POST') || auth()->user()->can('READ_BLOG_CATEGORY')))
            <div x-data="{ open: false }">
                <button @click="open = !open"
                    class="w-full flex items-center pl-3 pr-4 py-2 border-l-4 
                        {{ request()->routeIs('admin.posts') || request()->routeIs('blog-categories')
                            ? 'border-gray-400 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-900/50'
                            : 'border-transparent text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700' }}
                        text-base font-medium focus:outline-none transition duration-150 ease-in-out">
                    <span>{{ __('Blog Management') }}</span>
                    <svg class="ml-auto h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>

                <div x-show="open" class="mt-2 space-y-1" style="display: none;">
                    @can('READ_POST')
                        <x-responsive-nav-link href="{{ route('admin.posts') }}" :active="request()->routeIs('admin.posts')"
                            class="pl-8">
                            {{ __('Posts') }}
                        </x-responsive-nav-link>
                    @endcan

                    @can('READ_BLOG_CATEGORY')
                        <x-responsive-nav-link href="{{ route('blog-categories') }}" :active="request()->routeIs('blog-categories')"
                            class="pl-8">
                            {{ __('Blog Categories') }}
                        </x-responsive-nav-link>
                    @endcan
                </div>
            </div>
        @endif

        @if (auth()->check() && auth()->user()->can('READ_PORTFOLIO'))
            <x-responsive-nav-link href="{{ route('portfolios') }}" :active="request()->routeIs('portfolios')">
                {{ __('Portfolio') }}
            </x-responsive-nav-link>
        @endcan

        <!-- Translation Demo Link (Mobile) -->
        @auth
            <x-responsive-nav-link href="{{ route('translation-demo') }}" :active="request()->routeIs('translation-demo')">
                {{ __('language') }} {{ __('demo') }}
            </x-responsive-nav-link>
        @endauth
</div>

<!-- Responsive Settings Options -->
<div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
<div class="flex items-center px-4">
    @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
        <div class="shrink-0 me-3">
            <img class="size-10 rounded-full object-cover" src="{{ Auth::user()->profile_photo_url }}"
                alt="{{ Auth::user()->name }}" />
        </div>
    @endif

    <div>
        <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}
        </div>
        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
    </div>
</div>

<div class="mt-3 space-y-1">
    <!-- Language Switcher (Mobile) -->
    <div class="px-4 py-2">
        <div class="text-xs text-gray-400 uppercase tracking-wide">
            {{ __('language') }}
        </div>
        <div class="mt-2">
            <x-language-switcher />
        </div>
    </div>

    <div class="border-t border-gray-200 dark:border-gray-600 my-2"></div>

    <!-- Account Management -->
    <x-responsive-nav-link href="{{ route('profile.show') }}" :active="request()->routeIs('profile.show')">
        {{ __('profile') }}
    </x-responsive-nav-link>

    @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
        <x-responsive-nav-link href="{{ route('api-tokens.index') }}" :active="request()->routeIs('api-tokens.index')">
            {{ __('api_tokens') }}
        </x-responsive-nav-link>
    @endif

    <!-- Authentication -->
    <form method="POST" action="{{ secure_url(route('logout', [], false)) }}" x-data>
        @csrf

        <x-responsive-nav-link href="{{ secure_url(route('logout', [], false)) }}"
            @click.prevent="$root.submit();">
            {{ __('log_out') }}
        </x-responsive-nav-link>
    </form>

    <!-- Team Management -->
    @if (Laravel\Jetstream\Jetstream::hasTeamFeatures())
        <div class="border-t border-gray-200 dark:border-gray-600"></div>

        <div class="block px-4 py-2 text-xs text-gray-400">
            {{ __('manage_team') }}
        </div>

        <!-- Team Settings -->
        <x-responsive-nav-link href="{{ route('teams.show', Auth::user()->currentTeam->id) }}"
            :active="request()->routeIs('teams.show')">
            {{ __('Team Settings') }}
        </x-responsive-nav-link>

        @can('create', Laravel\Jetstream\Jetstream::newTeamModel())
            <x-responsive-nav-link href="{{ route('teams.create') }}" :active="request()->routeIs('teams.create')">
                {{ __('Create New Team') }}
            </x-responsive-nav-link>
        @endcan

        <!-- Team Switcher -->
        @if (Auth::user()->allTeams()->count() > 1)
            <div class="border-t border-gray-200 dark:border-gray-600"></div>

            <div class="block px-4 py-2 text-xs text-gray-400">
                {{ __('switch_teams') }}
            </div>

            @foreach (Auth::user()->allTeams() as $team)
                <x-switchable-team :team="$team" component="responsive-nav-link" />
            @endforeach
        @endif
    @endif
</div>
</div>
</div>
</nav>
