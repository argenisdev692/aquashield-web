@php
    use App\Helpers\PhoneHelper;
@endphp
<header x-data="{ isScrolled: false, isDrawerOpen: false }" @scroll.window="isScrolled = (window.pageYOffset > 20)"
    :class="{ 'bg-white shadow-md': isScrolled, 'bg-transparent': !isScrolled }"
    class="fixed w-full top-0 z-40 transition-all duration-300">
    <div class="container mx-auto px-4 py-4 flex justify-between items-center">
        <div class="flex items-center">
            <a href="{{ route('home') }}">
                <img :src="isScrolled ? '{{ asset('assets/logo/logo3.webp') }}' : '{{ asset('assets/logo/logo4-white.webp') }}'"
                    alt="V General Contractors Logo" class="h-14 md:h-12 transition-all duration-300">
            </a>
            <span class="ml-2 text-xl font-bold"
                :class="{ 'text-gray-800': isScrolled, 'text-white': !isScrolled }"></span>
        </div>

        <!-- Mobile menu button -->
        <button @click="isDrawerOpen = !isDrawerOpen"
            :class="{ 'text-gray-500 hover:text-gray-700': isScrolled, 'text-white hover:text-gray-200': !isScrolled }"
            class="md:hidden">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path x-show="!isDrawerOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"></path>
                <path x-show="isDrawerOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>

        <nav class="hidden md:flex space-x-6">
            <a href="{{ route('home') }}"
                :class="{
                    'text-white': !isScrolled && '{{ request()->routeIs('home') }}',
                    'text-yellow-400': isScrolled && '{{ request()->routeIs('home') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !'{{ request()->routeIs('home') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !'{{ request()->routeIs('home') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('home') }}</a>

            <a href="{{ route('about') }}"
                :class="{
                    'text-white': !isScrolled && '{{ request()->routeIs('about') }}',
                    'text-yellow-400': isScrolled && '{{ request()->routeIs('about') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !'{{ request()->routeIs('about') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !'{{ request()->routeIs('about') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('about_us') }}</a>

            <!-- Services Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false"
                    :class="{
                        'text-gray-700 hover:text-gray-900': isScrolled,
                        'text-yellow-400 hover:text-yellow-300': !isScrolled
                    }"
                    class="font-semibold transition-colors duration-300 ease-in-out flex items-center">
                    {{ __('services') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="absolute z-50 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                    <a href="{{ route('new-roof') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('new_roof') }}</a>
                    <a href="{{ route('roof-repair') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('roof_repair') }}</a>
                    <a href="{{ route('storm-damage') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('storm_damage') }}</a>
                    <a href="{{ route('hail-damage') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('hail_damage') }}</a>
                </div>
            </div>

            <!-- Resources Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false"
                    :class="{
                        'text-gray-700 hover:text-gray-900': isScrolled,
                        'text-yellow-400 hover:text-yellow-300': !isScrolled
                    }"
                    class="font-semibold transition-colors duration-300 ease-in-out flex items-center">
                    {{ __('resources') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="absolute z-50 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                    <a href="{{ route('products') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('products') }}</a>
                    <a href="{{ route('financing') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('financing') }}</a>
                    <a href="{{ route('virtual-remodeler') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('virtual_remodeler') }}</a>
                    <a href="{{ route('insurance-claims') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('insurance_claims') }}</a>
                </div>
            </div>

            <a href="{{ route('warranties') }}"
                :class="{
                    'text-white': !isScrolled && '{{ request()->routeIs('warranties') }}',
                    'text-yellow-400': isScrolled && '{{ request()->routeIs('warranties') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !'{{ request()->routeIs('warranties') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !'{{ request()->routeIs('warranties') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('warranties') }}</a>

            <a href="{{ route('portfolio') }}"
                :class="{
                    'text-white': !isScrolled && '{{ request()->routeIs('portfolio') }}',
                    'text-yellow-400': isScrolled && '{{ request()->routeIs('portfolio') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !'{{ request()->routeIs('portfolio') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !'{{ request()->routeIs('portfolio') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('portfolio') }}</a>

            <a href="{{ route('faqs') }}"
                :class="{
                    'text-white': !isScrolled && '{{ request()->routeIs('faqs') }}',
                    'text-yellow-400': isScrolled && '{{ request()->routeIs('faqs') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !'{{ request()->routeIs('faqs') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !'{{ request()->routeIs('faqs') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('faqs') }}</a>

            <!-- Blog Link -->
            <a href="{{ route('blog.index') }}"
                :class="{
                    'text-white': !isScrolled &&
                        '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}',
                    'text-yellow-400': isScrolled &&
                        '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}',
                    'text-gray-700 hover:text-gray-900': isScrolled && !
                        '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}',
                    'text-yellow-400 hover:text-yellow-300': !isScrolled && !
                        '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}'
                }"
                class="font-semibold transition-colors duration-300 ease-in-out relative after:absolute after:bottom-0 after:left-0 after:h-0.5 after:w-0 hover:after:w-full after:bg-current after:transition-all after:duration-300">{{ __('blog') }}</a>

            <!-- Contact Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false"
                    :class="{
                        'text-gray-700 hover:text-gray-900': isScrolled,
                        'text-yellow-400 hover:text-yellow-300': !isScrolled
                    }"
                    class="font-semibold transition-colors duration-300 ease-in-out flex items-center">
                    {{ __('contact') }}
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" class="absolute z-50 mt-2 w-48 bg-white rounded-md shadow-lg py-1">
                    <button @click="$dispatch('open-appointment-modal'); open = false"
                        class="block w-full text-left px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">
                        {{ __('schedule_appointment') }}
                    </button>

                    <a href="{{ route('contact-support') }}"
                        class="block px-4 py-2 text-sm text-gray-700 font-semibold hover:bg-gray-100">{{ __('support') }}</a>
                </div>
            </div>

            <!-- Language Switcher -->
            <div class="ml-4">
                <x-language-switcher-public />
            </div>
        </nav>

        <!-- Fixed Phone Button -->
        <a href="tel:{{ $companyData->phone }}" class="hidden md:inline-flex items-center">
            <button
                class="bg-yellow-500 text-white text-xs font-bold px-4 py-2 rounded hover:bg-yellow-600 flex items-center space-x-2 transform transition-all duration-300 ease-in-out hover:scale-105 hover:shadow-lg active:scale-95">
                <svg class="w-4 h-4 transition-transform duration-300 group-hover:rotate-12" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                <span>{{ PhoneHelper::format($companyData->phone) }}</span>
            </button>
        </a>
    </div>

    <!-- Overlay for drawer -->
    <div x-show="isDrawerOpen" x-transition:enter="transition-opacity ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-in duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 z-40 md:hidden"
        @click="isDrawerOpen = false">
    </div>

    <!-- Mobile Drawer -->
    <div x-show="isDrawerOpen" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg transform md:hidden z-50"
        @click.away="isDrawerOpen = false">
        <div class="p-6 space-y-4">
            <!-- Logo centered at top -->
            <div class="flex justify-center mb-8">
                <img src="{{ asset('assets/logo/logo3.webp') }}" alt="V General Contractors Logo" class="h-12">
            </div>

            <!-- Navigation items -->
            <a href="{{ route('home') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('home') }}',
                    'text-gray-800 hover:bg-gray-100': !'{{ request()->routeIs('home') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('home') }}</a>

            <a href="{{ route('about') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('about') }}',
                    'text-gray-800 hover:bg-gray-100': !'{{ request()->routeIs('about') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('about_us') }}</a>

            <!-- Services Dropdown in Drawer -->
            <div x-data="{ isOpen: false }" class="relative">
                <button @click="isOpen = !isOpen"
                    class="flex items-center justify-between w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 text-gray-800 font-semibold">
                    <span>{{ __('services') }}</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
                <div x-show="isOpen" class="pl-4">
                    <a href="{{ route('new-roof') }}"
                        :class="{
                            'text-yellow-400': '{{ request()->routeIs('new-roof') }}',
                            'text-gray-700 hover:bg-gray-100': !'{{ request()->routeIs('new-roof') }}'
                        }"
                        class="block py-2 px-4 rounded">{{ __('new_roof') }}</a>
                    <a href="{{ route('roof-repair') }}"
                        :class="{
                            'text-yellow-400': '{{ request()->routeIs('roof-repair') }}',
                            'text-gray-700 hover:bg-gray-100': !'{{ request()->routeIs('roof-repair') }}'
                        }"
                        class="block py-2 px-4 rounded">{{ __('roof_repair') }}</a>
                    <a href="{{ route('storm-damage') }}"
                        :class="{
                            'text-yellow-400': '{{ request()->routeIs('storm-damage') }}',
                            'text-gray-700 hover:bg-gray-100': !'{{ request()->routeIs('storm-damage') }}'
                        }"
                        class="block py-2 px-4 rounded">{{ __('storm_damage') }}</a>
                    <a href="{{ route('hail-damage') }}"
                        :class="{
                            'text-yellow-400': '{{ request()->routeIs('hail-damage') }}',
                            'text-gray-700 hover:bg-gray-100': !'{{ request()->routeIs('hail-damage') }}'
                        }"
                        class="block py-2 px-4 rounded">{{ __('hail_damage') }}</a>
                </div>
            </div>

            <a href="{{ route('warranties') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('warranties') }}',
                    'text-gray-800 hover:bg-gray-100': !'{{ request()->routeIs('warranties') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('warranties') }}</a>

            <a href="{{ route('products') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('products') }}',
                    'text-gray-800 hover:bg-gray-100': !'{{ request()->routeIs('products') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('products') }}</a>

            <a href="{{ route('faqs') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('faqs') }}',
                    'text-gray-800 hover:bg-gray-100': !'{{ request()->routeIs('faqs') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('faqs') }}</a>

            <!-- Mover este enlace ANTES del Contact Dropdown en el Mobile Drawer -->
            <a href="{{ route('blog.index') }}"
                :class="{
                    'text-yellow-400': '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}',
                    'text-gray-800 hover:bg-gray-100': !
                        '{{ request()->routeIs('blog.index') || request()->routeIs('blog.show') || request()->routeIs('blog.category') || request()->routeIs('blog.search') }}'
                }"
                class="block py-2.5 px-4 rounded transition duration-200 font-semibold">{{ __('blog') }}</a>

            <!-- Contact Dropdown -->
            <div x-data="{ isOpen: false }" class="relative">
                <button @click="isOpen = !isOpen"
                    class="flex items-center justify-between w-full py-2.5 px-4 rounded transition duration-200 hover:bg-gray-100 text-gray-800 font-semibold">
                    <span>{{ __('contact') }}</span>
                    <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': isOpen }"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>
                <div x-show="isOpen" class="pl-4">
                    <button @click="$dispatch('open-appointment-modal'); isOpen = false; isDrawerOpen = false"
                        class="block w-full text-left py-2 px-4 rounded text-gray-700 hover:bg-gray-100">
                        {{ __('schedule_appointment') }}
                    </button>
                    <a href="{{ route('contact-support') }}"
                        :class="{
                            'text-yellow-400': '{{ request()->routeIs('contact-support') }}',
                            'text-gray-700 hover:bg-gray-100': !'{{ request()->routeIs('contact-support') }}'
                        }"
                        class="block py-2 px-4 rounded">{{ __('support') }}</a>
                </div>
            </div>

            <!-- Language Switcher Mobile -->
            <div class="mt-6">
                <div class="text-xs text-gray-500 uppercase tracking-wide px-4 mb-2">
                    {{ __('language') }}
                </div>
                <div class="px-4">
                    <x-language-switcher-public />
                </div>
            </div>

            <!-- Mobile Call Button -->
            <div class="mt-6 px-4">
                <a href="tel:{{ $companyData->phone }}" class="block w-full">
                    <button
                        class="w-full bg-yellow-500 text-white text-sm font-bold px-4 py-3 rounded hover:bg-yellow-600 flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                        </svg>
                        <span> {{ PhoneHelper::format($companyData->phone) }}</span>
                    </button>
                </a>
            </div>
        </div>
    </div>
</header>
