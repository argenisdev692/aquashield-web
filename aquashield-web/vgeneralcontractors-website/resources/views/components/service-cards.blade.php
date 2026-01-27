<!-- Service Cards Section -->
<section class="py-16 bg-white fade-in-section">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-yellow-500 font-semibold">{{ __('our_solutions') }}</span>
            <h2 class="text-4xl font-bold mt-2 mb-4">{!! __('comprehensive_roofing_services') !!}</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">{{ __('service_cards_description') }}</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- New Roof Card -->
            <div
                class="group relative overflow-hidden rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="relative h-[300px] w-full">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <img src="{{ asset('assets/img/new-roof-1.webp') }}" alt="{{ __('new_roof_process_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                        width="600" height="400" loading="lazy">
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>
                    <img src="{{ asset('assets/img/new-roof-2.webp') }}" alt="{{ __('new_roof_completed_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        width="600" height="400" loading="lazy">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                    <div class="absolute bottom-0 p-6 text-white">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ __('new_roof_installation') }}</h3>
                        <p
                            class="mb-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('new_roof_installation_description') }}
                        </p>
                        <x-primary-button href="{{ route('new-roof') }}" class="inline-flex items-center">
                            {{ __('read_more') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Roof Repair Card -->
            <div
                class="group relative overflow-hidden rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="relative h-[300px] w-full">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <img src="{{ asset('assets/img/repair-1.webp') }}" alt="{{ __('roof_repair_service_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                        width="600" height="400" loading="lazy">
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>
                    <img src="{{ asset('assets/img/repair-2.webp') }}" alt="{{ __('roof_repair_completed_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        width="600" height="400" loading="lazy">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                    <div class="absolute bottom-0 p-6 text-white">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 4a2 2 0 114 0v1a1 1 0 001 1h3a1 1 0 011 1v3a1 1 0 01-1 1h-1a2 2 0 100 4h1a1 1 0 011 1v3a1 1 0 01-1 1h-3a1 1 0 01-1-1v-1a2 2 0 10-4 0v1a1 1 0 01-1 1H7a1 1 0 01-1-1v-3a1 1 0 00-1-1H4a2 2 0 110-4h1a1 1 0 001-1V7a1 1 0 011-1h3a1 1 0 001-1V4z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ __('roof_repair_replacement') }}</h3>
                        <p
                            class="mb-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('roof_repair_replacement_description') }}
                        </p>
                        <x-primary-button href="{{ route('roof-repair') }}" class="inline-flex items-center">
                            {{ __('read_more') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Storm Damage Card -->
            <div
                class="group relative overflow-hidden rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="relative h-[300px] w-full">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <img src="{{ asset('assets/img/storm-1.webp') }}" alt="{{ __('storm_damage_assessment_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                        width="600" height="400" loading="lazy">
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>
                    <img src="{{ asset('assets/img/storm-2.webp') }}" alt="{{ __('storm_damage_completion_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        width="600" height="400" loading="lazy">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                    <div class="absolute bottom-0 p-6 text-white">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ __('storm_damage_repair_replacement') }}</h3>
                        <p
                            class="mb-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('storm_damage_repair_replacement_description') }}
                        </p>
                        <x-primary-button href="{{ route('storm-damage') }}" class="inline-flex items-center">
                            {{ __('read_more') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>

            <!-- Hail Damage Card -->
            <div
                class="group relative overflow-hidden rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="relative h-[300px] w-full">
                    <div class="absolute inset-0 bg-black/40"></div>
                    <img src="{{ asset('assets/img/hail-1.webp') }}" alt="{{ __('hail_damage_inspection_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0"
                        width="600" height="400" loading="lazy">
                    <div
                        class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                    </div>
                    <img src="{{ asset('assets/img/hail-2.webp') }}" alt="{{ __('hail_damage_completed_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100"
                        width="600" height="400" loading="lazy">
                </div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/50 to-transparent">
                    <div class="absolute bottom-0 p-6 text-white">
                        <div class="w-12 h-12 bg-yellow-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold mb-2">{{ __('hail_damage_repair_replacement') }}</h3>
                        <p
                            class="mb-4 opacity-0 group-hover:opacity-100 transform translate-y-4 group-hover:translate-y-0 transition-all duration-300">
                            {{ __('hail_damage_repair_replacement_description') }}
                        </p>
                        <x-primary-button href="{{ route('hail-damage') }}" class="inline-flex items-center">
                            {{ __('read_more') }}
                        </x-primary-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
