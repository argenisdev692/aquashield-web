@php
    $customStyle = 'background-color: #2C2E36;';
    $customHoverStyle = 'background-color: rgba(44, 46, 54, 0.5);';
@endphp

<div x-data="{ sidebarOpen: false, mobileSearchOpen: false }" x-init="// Sidebar Store
$store.sidebar = {
    open: false,
    toggle() {
        this.open = !this.open;
        sidebarOpen = this.open
    }
};

// Dark Mode Store
$store.darkMode = {
    on: JSON.parse(localStorage.getItem('darkMode')) || false,
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        this.updateTheme();
    },
    updateTheme() {
        if (this.on) {
            document.documentElement.classList.add('dark');
            document.body.style.backgroundColor = '#141414';
        } else {
            document.documentElement.classList.remove('dark');
            document.body.style.backgroundColor = '#f9fafb';
        }
    }
};

// Initialize theme on load
$store.darkMode.updateTheme();" x-effect="sidebarOpen = $store.sidebar.open">
    <!-- Top Header -->
    <nav class="fixed top-0 left-0 right-0 z-50 border-b" style="background-color: #141414; border-color: #2C2E36;">
        <!-- Normal Header -->
        <div x-show="!mobileSearchOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-250"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform -translate-y-4" class="px-2 sm:px-4 lg:px-8">
            <div class="flex justify-between items-center h-14 sm:h-16">
                <!-- Left side - Logo and search -->
                <div class="flex items-center space-x-2 sm:space-x-4 flex-1">
                    <!-- Mobile Menu Button -->
                    <button @click="$store.sidebar.toggle()"
                        class="lg:hidden text-gray-400 hover:text-white p-1 sm:p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    <!-- Logo -->
                    <div class="flex items-center flex-shrink-0">
                        <a href="{{ route('dashboard') }}" class="flex items-center">
                            <img src="{{ asset('assets/logo/logo4-white.webp') }}" alt="V General Contractors Logo"
                                class="h-8 sm:h-10 mr-2 sm:mr-3">
                        </a>
                    </div>

                    <!-- Search Bar - Desktop -->
                    <div class="relative hidden md:block flex-1 max-w-md">
                        <input type="text" placeholder="{{ __('search') }}"
                            class="text-gray-300 placeholder-gray-500 rounded-lg px-4 py-2 pl-10 w-full focus:outline-none focus:ring-2 focus:ring-yellow-500 border-0"
                            style="background-color: #2C2E36;">
                        <svg class="w-5 h-5 text-gray-500 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Right side - Theme Toggle, Language, Notifications, Messages, User menu -->
                <div class="flex items-center space-x-1 sm:space-x-2 md:space-x-4">
                    <!-- Theme Toggle - Desktop Only -->
                    <div class="hidden md:block">
                        <button @click="$store.darkMode.toggle()"
                            class="flex items-center justify-center w-8 h-8 text-gray-400 hover:text-white transition-colors duration-200 rounded-lg hover:bg-gray-700">
                            <svg x-show="!$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            <svg x-show="$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24" style="display: none;">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Language Switcher -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" type="button"
                            class="inline-flex items-center px-2 sm:px-3 py-1 sm:py-2 text-xs sm:text-sm leading-4 font-medium rounded-md text-gray-400 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            @php
                                $currentLocale = app()->getLocale();
                                $languages = [
                                    'en' => [
                                        'name' => __('english'),
                                        'flag' => '<svg class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                       <rect width="20" height="15" fill="#B22234"/>
                                                       <rect width="20" height="1.15" y="1.15" fill="white"/>
                                                       <rect width="20" height="1.15" y="3.46" fill="white"/>
                                                       <rect width="20" height="1.15" y="5.77" fill="white"/>
                                                       <rect width="20" height="1.15" y="8.08" fill="white"/>
                                                       <rect width="20" height="1.15" y="10.39" fill="white"/>
                                                       <rect width="20" height="1.15" y="12.69" fill="white"/>
                                                       <rect width="8" height="8.08" fill="#3C3B6E"/>
                                                   </svg>',
                                        'code' => 'en',
                                    ],
                                    'es' => [
                                        'name' => __('spanish'),
                                        'flag' => '<svg class="h-3 w-3 sm:h-4 sm:w-4" viewBox="0 0 20 15" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                       <rect width="20" height="5" fill="#C60B1E"/>
                                                       <rect width="20" height="5" y="5" fill="#FFC400"/>
                                                       <rect width="20" height="5" y="10" fill="#C60B1E"/>
                                                   </svg>',
                                        'code' => 'es',
                                    ],
                                ];
                            @endphp
                            <span class="mr-1 sm:mr-2">{!! $languages[$currentLocale]['flag'] !!}</span>
                            <span class="hidden sm:inline">{{ substr($languages[$currentLocale]['name'], 0, 2) }}</span>
                            <svg class="ml-1 sm:ml-2 -mr-0.5 h-3 w-3 sm:h-4 sm:w-4" xmlns="http://www.w3.org/2000/svg"
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
                            class="absolute z-50 mt-2 w-44 rounded-md shadow-lg origin-top-right right-0 ring-1 ring-black ring-opacity-5 focus:outline-none"
                            style="background-color: #2C2E36; display: none;">
                            <div class="py-1">
                                @foreach ($languages as $code => $language)
                                    <a href="{{ route('lang.switch', $code) }}"
                                        class="group flex items-center px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 {{ $currentLocale === $code ? 'bg-gray-700' : '' }}">
                                        <span class="mr-3">{!! $language['flag'] !!}</span>
                                        <span>{{ $language['name'] }}</span>
                                        @if ($currentLocale === $code)
                                            <svg class="ml-auto h-4 w-4 text-green-500" fill="currentColor"
                                                viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        @endif
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Search Icon - Mobile -->
                    <div class="md:hidden">
                        <button @click="mobileSearchOpen = !mobileSearchOpen"
                            class="text-gray-400 hover:text-white p-1 sm:p-2 transition-colors">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>

                    <!-- Notifications -->
                    <button class="text-gray-400 hover:text-white p-1 sm:p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-5 5v-5zM10.5 3.5a6 6 0 0 1 6 6v2l1.5 3h-15l1.5-3v-2a6 6 0 0 1 6-6z" />
                        </svg>
                    </button>

                    <!-- Messages -->
                    <button class="text-gray-400 hover:text-white p-1 sm:p-2">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                        </svg>
                    </button>

                    <!-- User Profile -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-1 sm:space-x-2 text-gray-300 hover:text-white">
                            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                                <img class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover border-2 border-gray-700"
                                    src="{{ Auth::user()->profile_photo_url }}" alt="{{ Auth::user()->name }}" />
                            @else
                                <div
                                    class="w-6 h-6 sm:w-8 sm:h-8 bg-gray-700 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-xs sm:text-sm font-medium text-white">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                </div>
                            @endif
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg border z-50"
                            style="background-color: #2C2E36; border-color: #2C2E36; display: none;">
                            <div class="py-2">
                                <div class="px-4 py-2 border-b" style="border-color: #2C2E36;">
                                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-gray-400">{{ Auth::user()->email }}</p>
                                </div>

                                <a href="{{ route('profile.show') }}"
                                    class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                    {{ __('profile') }}
                                </a>

                                @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                    <a href="{{ route('api-tokens.index') }}"
                                        class="block px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                        {{ __('api_tokens') }}
                                    </a>
                                @endif

                                <div class="border-t mt-2" style="border-color: #2C2E36;"></div>

                                <form method="POST" action="{{ secure_url(route('logout', [], false)) }}" x-data>
                                    @csrf
                                    <button type="submit"
                                        class="block w-full text-left px-4 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white">
                                        {{ __('log_out') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Search Header -->
        <div x-show="mobileSearchOpen" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform -translate-y-4"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-250"
            x-transition:leave-start="opacity-100 transform translate-y-0"
            x-transition:leave-end="opacity-0 transform translate-y-4" class="md:hidden px-2 sm:px-4"
            style="display: none;">
            <div class="flex items-center h-14 sm:h-16 space-x-3">
                <!-- Back/Close Button -->
                <button @click="mobileSearchOpen = false"
                    class="text-gray-400 hover:text-white p-2 rounded-full hover:bg-gray-800 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </button>

                <!-- Search Input -->
                <form class="flex-1 relative" onsubmit="handleMobileSearch(event)">
                    <input type="text" placeholder="{{ __('search') }}"
                        class="w-full text-gray-100 placeholder-gray-400 bg-transparent border-0 outline-none focus:outline-none focus:ring-2 focus:ring-yellow-500 text-base py-2 pr-12"
                        style="background-color: transparent;" x-ref="mobileSearchInput"
                        x-effect="if (mobileSearchOpen) { $nextTick(() => $refs.mobileSearchInput?.focus()) }">

                    <!-- Search Submit Button -->
                    <button type="submit"
                        class="absolute right-0 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-yellow-400 p-2 rounded-full hover:bg-gray-800 transition-all duration-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:block fixed left-2 sm:left-4 lg:left-6 top-16 bottom-0 w-16 z-40"
        style="background-color: #141414;">
        <div class="flex flex-col items-center py-4 space-y-4">
            <!-- Dashboard -->
            <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                <a href="{{ route('dashboard') }}"
                    class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('dashboard') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                    onmouseover="{{ !request()->routeIs('dashboard') ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                    onmouseout="{{ !request()->routeIs('dashboard') ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v4" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5v4" />
                    </svg>
                </a>
                @if (!request()->routeIs('dashboard'))
                    <div x-show="open" x-transition
                        class="absolute left-12 top-0 text-white px-3 py-2 rounded-lg shadow-lg whitespace-nowrap z-50"
                        style="background-color: #2C2E36;">
                        {{ __('dashboard') }}
                        <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 rotate-45"
                            style="background-color: #2C2E36;"></div>
                    </div>
                @endif
            </div>

            <!-- Administration Group -->
            @if (auth()->check() &&
                    (auth()->user()->can('READ_COMPANY_DATA') ||
                        auth()->user()->can('READ_USER') ||
                        auth()->user()->can('READ_INSURANCE_COMPANY')))
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <div class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('company-data.*') || request()->routeIs('users.*') || request()->routeIs('insurance-companies.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !(request()->routeIs('company-data.*') || request()->routeIs('users.*') || request()->routeIs('insurance-companies.*')) ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !(request()->routeIs('company-data.*') || request()->routeIs('users.*') || request()->routeIs('insurance-companies.*')) ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>

                    <!-- Submenu -->
                    <div x-show="open" x-transition
                        class="absolute left-12 top-0 rounded-lg shadow-lg z-50 min-w-max"
                        style="background-color: #2C2E36;">
                        <div class="p-2 space-y-1">
                            <div class="text-xs text-yellow-400 px-3 py-1 font-medium uppercase tracking-wide">
                                {{ __('administration') }}</div>
                            @can('READ_COMPANY_DATA')
                                <a href="{{ route('company-data.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('company-data.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('company_data_title') }}
                                </a>
                            @endcan
                            @can('READ_USER')
                                <a href="{{ route('users.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('users.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('users') }}
                                </a>
                            @endcan
                            @can('READ_INSURANCE_COMPANY')
                                <a href="{{ route('insurance-companies.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('insurance-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('insurance_companies') }}
                                </a>
                            @endcan
                            @can('READ_PUBLIC_COMPANY')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('public-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('public_companies') }}
                                </a>
                            @endcan
                            @can('READ_ROLE')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('roles.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('roles') }}
                                </a>
                            @endcan
                            @can('READ_PERMISSION')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('permissions.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('permissions') }}
                                </a>
                            @endcan
                            @can('READ_CUSTOMER')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('customers.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('customers') }}
                                </a>
                            @endcan

                            @can('READ_PUBLIC_ADJUSTER')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('public-adjusters.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('public_adjusters') }}
                                </a>
                            @endcan
                            @can('READ_SCOPE_SHEET')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('scope-sheets.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('scope_sheets') }}
                                </a>
                            @endcan
                            @can('READ_MORTGAGE_COMPANY')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('mortgage-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('mortgage_companies') }}
                                </a>
                            @endcan

                            @can('READ_ALLIANCE_COMPANY')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('alliance-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('alliance_companies') }}
                                </a>
                            @endcan
                            @can('READ_ZONE')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('zones.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('zones') }}
                                </a>
                            @endcan
                            @can('READ_PROPERTIES')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('properties.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('properties') }}
                                </a>
                            @endcan
                            @can('READ_TYPE_DAMAGE')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('damage-types.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('type_damage') }}
                                </a>
                            @endcan
                            @can('READ_CAUSE_OF_LOSS')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('cause-of-losses.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('cause_of_loss') }}
                                </a>
                            @endcan
                            @can('READ_PRODUCT')
                                <a href=""
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('products.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('products') }}
                                </a>
                            @endcan
                        </div>
                        <div class="absolute left-0 top-4 transform -translate-x-1 w-2 h-2 rotate-45"
                            style="background-color: #2C2E36;"></div>
                    </div>
                </div>
            @endif

            <!-- Services Group -->
            @if (auth()->check() && (auth()->user()->can('READ_EMAIL_DATA') || auth()->user()->can('READ_SERVICE_CATEGORY')))
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <div class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('email-datas.*') || request()->routeIs('service-categories.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !(request()->routeIs('email-datas.*') || request()->routeIs('service-categories.*')) ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !(request()->routeIs('email-datas.*') || request()->routeIs('service-categories.*')) ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>

                    <!-- Submenu -->
                    <div x-show="open" x-transition
                        class="absolute left-12 top-0 rounded-lg shadow-lg z-50 min-w-max"
                        style="background-color: #2C2E36;">
                        <div class="p-2 space-y-1">
                            <div class="text-xs text-yellow-400 px-3 py-1 font-medium uppercase tracking-wide">
                                {{ __('services') }}</div>
                            @can('READ_EMAIL_DATA')
                                <a href="{{ route('email-datas.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('email-datas.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('emails') }}
                                </a>
                            @endcan
                            @can('READ_SERVICE_CATEGORY')
                                <a href="{{ route('service-categories.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('service-categories.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('service_categories') }}
                                </a>
                            @endcan
                        </div>
                        <div class="absolute left-0 top-4 transform -translate-x-1 w-2 h-2 rotate-45"
                            style="background-color: #2C2E36;"></div>
                    </div>
                </div>
            @endif

            <!-- Appointments Group -->
            @if (auth()->check() && auth()->user()->can('READ_APPOINTMENT'))
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <div class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('appointments.index') || request()->routeIs('appointment-calendar') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !(request()->routeIs('appointments.index') || request()->routeIs('appointment-calendar')) ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !(request()->routeIs('appointments.index') || request()->routeIs('appointment-calendar')) ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>

                    <!-- Submenu -->
                    <div x-show="open" x-transition
                        class="absolute left-12 top-0 rounded-lg shadow-lg z-50 min-w-max"
                        style="background-color: #2C2E36;">
                        <div class="p-2 space-y-1">
                            <div class="text-xs text-yellow-400 px-3 py-1 font-medium uppercase tracking-wide">
                                {{ __('appointments') }}</div>
                            <a href="{{ route('appointments.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('appointments.index') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('manage_appointments') }}
                            </a>
                            <a href="{{ route('appointment-calendar') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('appointment-calendar') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('calendar_view') }}
                            </a>
                        </div>
                        <div class="absolute left-0 top-4 transform -translate-x-1 w-2 h-2 rotate-45"
                            style="background-color: #2C2E36;"></div>
                    </div>
                </div>
            @endif

            <!-- Call Records -->
            @if (auth()->check() && auth()->user()->can('READ_CALL_RECORD'))
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <a href="{{ route('call-records') }}"
                        class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('call-records') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !request()->routeIs('call-records') ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !request()->routeIs('call-records') ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                    </a>
                    @if (!request()->routeIs('call-records'))
                        <div x-show="open" x-transition
                            class="absolute left-12 top-0 text-white px-3 py-2 rounded-lg shadow-lg whitespace-nowrap z-50"
                            style="background-color: #2C2E36;">
                            {{ __('call_records_title') }}
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 rotate-45"
                                style="background-color: #2C2E36;"></div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Invoice Management -->
            @if (auth()->check() && auth()->user()->can('READ_INVOICE_DEMO'))
                <div class="relative group">
                    <a href="{{ route('invoices.index') }}"
                        class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('invoices.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !request()->routeIs('invoices.*') ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !request()->routeIs('invoices.*') ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </a>
                    @if (!request()->routeIs('invoices.*'))
                        <div class="absolute left-12 top-0 text-white px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 whitespace-nowrap z-50"
                            style="background-color: #2C2E36;">
                            {{ __('invoices_demo_traduccion_title') }}
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 rotate-45"
                                style="background-color: #2C2E36;"></div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Blog Management Group -->
            @if (auth()->check() && (auth()->user()->can('READ_POST') || auth()->user()->can('READ_BLOG_CATEGORY')))
                <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                    <div class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('posts-crud.*') || request()->routeIs('blog-categories.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !(request()->routeIs('posts-crud.*') || request()->routeIs('blog-categories.*')) ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !(request()->routeIs('posts-crud.*') || request()->routeIs('blog-categories.*')) ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>

                    <!-- Submenu -->
                    <div x-show="open" x-transition
                        class="absolute left-12 top-0 rounded-lg shadow-lg z-50 min-w-max"
                        style="background-color: #2C2E36;">
                        <div class="p-2 space-y-1">
                            <div class="text-xs text-yellow-400 px-3 py-1 font-medium uppercase tracking-wide">
                                {{ __('blog_management') }}</div>
                            @can('READ_POST')
                                <a href="{{ route('posts-crud.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('posts-crud.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('posts') }}
                                </a>
                            @endcan
                            @can('READ_BLOG_CATEGORY')
                                <a href="{{ route('blog-categories.index') }}"
                                    class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('blog-categories.*') ? 'bg-gray-700 text-white' : '' }}">
                                    {{ __('blog_categories') }}
                                </a>
                            @endcan
                        </div>
                        <div class="absolute left-0 top-4 transform -translate-x-1 w-2 h-2 rotate-45"
                            style="background-color: #2C2E36;"></div>
                    </div>
                </div>
            @endif

            <!-- Portfolio -->
            @if (auth()->check() && auth()->user()->can('READ_PORTFOLIO'))
                <div class="relative group">
                    <a href="{{ route('portfolios-crud.index') }}"
                        class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('portfolios-crud.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !request()->routeIs('portfolios-crud.*') ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !request()->routeIs('portfolios-crud.*') ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </a>
                    @if (!request()->routeIs('portfolios-crud.*'))
                        <div class="absolute left-12 top-0 text-white px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 whitespace-nowrap z-50"
                            style="background-color: #2C2E36;">
                            {{ __('Portfolios') }}
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 rotate-45"
                                style="background-color: #2C2E36;"></div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- AI Models -->
            @if (auth()->check() && auth()->user()->can('READ_MODEL_AI'))
                <div class="relative group">
                    <a href="{{ route('model-ais.index') }}"
                        class="flex items-center justify-center w-10 h-10 transition-all duration-300 cursor-pointer {{ request()->routeIs('model-ais.*') ? 'bg-yellow-400 text-gray-900 rounded-full' : 'text-gray-400 hover:text-white border border-gray-600/30 hover:border-yellow-400/50 bg-transparent rounded-full' }}"
                        onmouseover="{{ !request()->routeIs('model-ais.*') ? 'this.style.backgroundColor=\'rgba(44, 46, 54, 0.5)\'' : '' }}"
                        onmouseout="{{ !request()->routeIs('model-ais.*') ? 'this.style.backgroundColor=\'transparent\'' : '' }}">
                        <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </a>
                    @if (!request()->routeIs('model-ais.*'))
                        <div class="absolute left-12 top-0 text-white px-3 py-2 rounded-lg shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-150 whitespace-nowrap z-50"
                            style="background-color: #2C2E36;">
                            {{ __('model_ai_title') }}
                            <div class="absolute left-0 top-1/2 transform -translate-y-1/2 -translate-x-1 w-2 h-2 rotate-45"
                                style="background-color: #2C2E36;"></div>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>

    <!-- Mobile Drawer -->

    <!-- Overlay -->
    <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" @click="$store.sidebar.toggle()"
        class="lg:hidden fixed inset-0 z-40 bg-black bg-opacity-50" style="display: none;"></div>

    <!-- Mobile Drawer -->
    <div x-show="sidebarOpen" x-transition:enter="transition ease-in-out duration-300 transform"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in-out duration-300 transform" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="lg:hidden fixed left-0 top-14 sm:top-16 bottom-0 w-64 z-50 overflow-y-auto"
        style="background-color: #141414; display: none;">

        <div class="p-4 space-y-4">
            <!-- Theme Toggle - Mobile Only -->
            <div class="md:hidden pb-4 border-b border-gray-700">
                <div class="flex items-center justify-between">
                    <span class="text-sm font-medium text-gray-300">{{ __('Theme') }}</span>
                    <button @click="$store.darkMode.toggle()"
                        class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 focus:ring-offset-gray-800"
                        :class="$store.darkMode.on ? 'bg-yellow-400' : 'bg-gray-600'">
                        <span class="sr-only">Toggle theme</span>
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition"
                            :class="$store.darkMode.on ? 'translate-x-6' : 'translate-x-1'">
                            <svg x-show="!$store.darkMode.on" class="w-3 h-3 text-gray-600 absolute top-0.5 left-0.5"
                                fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"
                                    clip-rule="evenodd" />
                            </svg>
                            <svg x-show="$store.darkMode.on" class="w-3 h-3 text-gray-600 absolute top-0.5 left-0.5"
                                fill="currentColor" viewBox="0 0 20 20" style="display: none;">
                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z" />
                            </svg>
                        </span>
                    </button>
                </div>
            </div>

            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}"
                class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('dashboard') ? 'bg-yellow-400 text-gray-900' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 5v4" />
                </svg>
                <span class="font-medium">{{ __('dashboard') }}</span>
            </a>

            <!-- Administration Group -->
            @if (auth()->check() &&
                    (auth()->user()->can('READ_COMPANY_DATA') ||
                        auth()->user()->can('READ_USER') ||
                        auth()->user()->can('READ_INSURANCE_COMPANY')))
                <div x-data="{ adminOpen: false }">
                    <button @click="adminOpen = !adminOpen"
                        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="font-medium text-yellow-400">{{ __('administration') }}</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="adminOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="adminOpen" x-transition class="ml-9 mt-2 space-y-2">
                        @can('READ_COMPANY_DATA')
                            <a href="{{ route('company-data.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('company-data.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('company_data_title') }}
                            </a>
                        @endcan
                        @can('READ_USER')
                            <a href="{{ route('users.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('users.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('users') }}
                            </a>
                        @endcan
                        @can('READ_INSURANCE_COMPANY')
                            <a href="{{ route('insurance-companies.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('insurance-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('insurance_companies') }}
                            </a>
                        @endcan
                        @can('READ_PUBLIC_COMPANY')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('public-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('public_companies') }}
                            </a>
                        @endcan
                        @can('READ_ROLE')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('roles.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('roles') }}
                            </a>
                        @endcan
                        @can('READ_PERMISSION')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('permissions.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('permissions') }}
                            </a>
                        @endcan
                        @can('READ_CUSTOMER')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('customers.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('customers') }}
                            </a>
                        @endcan

                        @can('READ_PUBLIC_ADJUSTER')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('public-adjusters.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('public_adjusters') }}
                            </a>
                        @endcan
                        @can('READ_SCOPE_SHEET')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('scope-sheets.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('scope_sheets') }}
                            </a>
                        @endcan
                        @can('READ_MORTGAGE_COMPANY')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('mortgage-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('mortgage_companies') }}
                            </a>
                        @endcan

                        @can('READ_ALLIANCE_COMPANY')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('alliance-companies.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('alliance_companies') }}
                            </a>
                        @endcan
                        @can('READ_ZONE')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('zones.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('zones') }}
                            </a>
                        @endcan
                        @can('READ_PROPERTIES')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('properties.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('properties') }}
                            </a>
                        @endcan
                        @can('READ_TYPE_DAMAGE')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('type-damage.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('type_damage') }}
                            </a>
                        @endcan
                        @can('READ_CAUSE_OF_LOSS')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('cause-of-losses.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('cause_of_loss') }}
                            </a>
                        @endcan
                        @can('READ_PRODUCT')
                            <a href=""
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('products.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('products') }}
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Services Group -->
            @if (auth()->check() && (auth()->user()->can('READ_EMAIL_DATA') || auth()->user()->can('READ_SERVICE_CATEGORY')))
                <div x-data="{ servicesOpen: false }">
                    <button @click="servicesOpen = !servicesOpen"
                        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="font-medium text-yellow-400">{{ __('services') }}</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="servicesOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="servicesOpen" x-transition class="ml-9 mt-2 space-y-2">
                        @can('READ_EMAIL_DATA')
                            <a href="{{ route('email-datas.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('email-datas.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('emails') }}
                            </a>
                        @endcan
                        @can('READ_SERVICE_CATEGORY')
                            <a href="{{ route('service-categories.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:bg-gray-700 hover:text-white rounded {{ request()->routeIs('service-categories.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('service_categories') }}
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Appointments Group -->
            @if (auth()->check() && auth()->user()->can('READ_APPOINTMENT'))
                <div x-data="{ appointmentsOpen: false }">
                    <button @click="appointmentsOpen = !appointmentsOpen"
                        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span class="font-medium text-yellow-400">{{ __('appointments') }}</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="appointmentsOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="appointmentsOpen" x-transition class="ml-9 mt-2 space-y-2">
                        <a href="{{ route('appointments.index') }}"
                            class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('appointments.index') ? 'bg-gray-700 text-white' : '' }}">
                            {{ __('manage_appointments') }}
                        </a>
                        <a href="{{ route('appointment-calendar') }}"
                            class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('appointment-calendar') ? 'bg-gray-700 text-white' : '' }}">
                            {{ __('calendar_view') }}
                        </a>
                    </div>
                </div>
            @endif

            <!-- Call Records -->
            @if (auth()->check() && auth()->user()->can('READ_CALL_RECORD'))
                <a href="{{ route('call-records') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('call-records') ? 'bg-yellow-400 text-gray-900' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span class="font-medium">{{ __('call_records_title') }}</span>
                </a>
            @endif

            <!-- Invoice Management -->
            @if (auth()->check() && auth()->user()->can('READ_INVOICE_DEMO'))
                <a href="{{ route('invoices.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('invoices.*') ? 'bg-yellow-400 text-gray-900' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="font-medium">{{ __('invoices_demo_traduccion_title') }}</span>
                </a>
            @endif

            <!-- Blog Management Group -->
            @if (auth()->check() && (auth()->user()->can('READ_POST') || auth()->user()->can('READ_BLOG_CATEGORY')))
                <div x-data="{ blogOpen: false }">
                    <button @click="blogOpen = !blogOpen"
                        class="w-full flex items-center justify-between p-3 rounded-lg text-gray-400 hover:text-white hover:bg-gray-800">
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                            </svg>
                            <span class="font-medium text-yellow-400">{{ __('blog_management') }}</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform" :class="blogOpen ? 'rotate-180' : ''" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="blogOpen" x-transition class="ml-9 mt-2 space-y-2">
                        @can('READ_POST')
                            <a href="{{ route('posts-crud.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('posts-crud.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('posts') }}
                            </a>
                        @endcan
                        @can('READ_BLOG_CATEGORY')
                            <a href="{{ route('blog-categories.index') }}"
                                class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-gray-700 rounded {{ request()->routeIs('blog-categories.*') ? 'bg-gray-700 text-white' : '' }}">
                                {{ __('blog_categories') }}
                            </a>
                        @endcan
                    </div>
                </div>
            @endif

            <!-- Portfolio -->
            @if (auth()->check() && auth()->user()->can('READ_PORTFOLIO'))
                <a href="{{ route('portfolios-crud.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('portfolios-crud.*') ? 'bg-yellow-400 text-gray-900' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <span class="font-medium">{{ __('Portfolios') }}</span>
                </a>
            @endif

            <!-- AI Models -->
            @if (auth()->check() && auth()->user()->can('READ_MODEL_AI'))
                <a href="{{ route('model-ais.index') }}"
                    class="flex items-center space-x-3 p-3 rounded-lg {{ request()->routeIs('model-ais.*') ? 'bg-yellow-400 text-gray-900' : 'text-gray-400 hover:text-white hover:bg-gray-800' }}">
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                    </svg>
                    <span class="font-medium">{{ __('model_ai_title') }}</span>
                </a>
            @endif
        </div>
    </div>
</div>

<script>
    function handleMobileSearch(event) {
        event.preventDefault();
        const searchInput = event.target.querySelector('input[type="text"]');
        const searchTerm = searchInput.value.trim();

        if (searchTerm) {
            // Aquí puedes agregar la lógica de búsqueda
            console.log('Searching for:', searchTerm);

            // Ejemplo: redirigir a una página de búsqueda
            // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;

            // Por ahora, simplemente mostraremos un mensaje
            alert(`Buscando: ${searchTerm}`);

            // Cerrar el modal de búsqueda después de buscar
            // Si usas Alpine.js store para manejar el estado
            // Alpine.store('mobileSearch', false);
        }
    }
</script>
