<!-- Services Section -->
<section class="py-16 bg-gray-50 fade-in-section">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-yellow-500 font-semibold">{{ __('our_services') }}</span>
            <h2 class="text-4xl font-bold mt-2 mb-4">{{ __('expert_roofing_solutions') }}</h2>
            <p class="text-gray-600 max-w-3xl mx-auto">{!! __('services_description') !!}</p>
        </div>

        <div class="flex flex-col md:flex-row gap-8 items-start">
            <!-- Left Side: Single Image -->
            <div class="w-full md:w-1/2">
                <div class="relative h-[400px] rounded-lg overflow-hidden shadow-lg">
                    <img src="{{ asset('assets/img/services-roofing.webp') }}"
                        alt="{{ __('professional_roofing_services_alt') }}"
                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-500 hover:scale-105"
                        width="800" height="600" loading="lazy">
                </div>
            </div>

            <!-- Right Side: Services List with Expand/Collapse -->
            <div class="w-full md:w-1/2" x-data="{ openItems: {} }" x-init="openItems = { 'roof-replacement': false, 'roof-restoration': false, 'storm-damage': false, 'hail-damage': false }">
                <div class="space-y-6">
                    <h3 class="text-3xl font-bold text-gray-900">{{ __('comprehensive_roofing_solutions') }}</h3>
                    <p class="text-gray-600 mb-6">{!! __('services_intro_description') !!}</p>

                    <!-- Service 1: Roof Replacement -->
                    <div class="border-b border-gray-200">
                        <button @click="openItems['roof-replacement'] = !openItems['roof-replacement']"
                            class="w-full text-left flex items-start gap-4 py-4 focus:outline-none">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white font-semibold">01</span>
                            <h4 class="text-xl font-semibold text-gray-900">{{ __('professional_roof_replacement') }}
                            </h4>
                            <svg x-show="!openItems['roof-replacement']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="openItems['roof-replacement']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <div x-show="openItems['roof-replacement']" x-collapse x-cloak
                            class="text-gray-600 mt-2 pl-12 pr-4 pb-4">
                            {!! __('roof_replacement_description') !!}
                        </div>
                    </div>

                    <!-- Service 2: Insurance Claims -->
                    <div class="border-b border-gray-200">
                        <button @click="openItems['roof-restoration'] = !openItems['roof-restoration']"
                            class="w-full text-left flex items-start gap-4 py-4 focus:outline-none">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white font-semibold">02</span>
                            <h4 class="text-xl font-semibold text-gray-900">{{ __('insurance_claim_assistance') }}</h4>
                            <svg x-show="!openItems['roof-restoration']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="openItems['roof-restoration']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <div x-show="openItems['roof-restoration']" x-collapse x-cloak
                            class="text-gray-600 mt-2 pl-12 pr-4 pb-4">
                            {!! __('insurance_assistance_description') !!}
                        </div>
                    </div>

                    <!-- Service 3: Storm Damage -->
                    <div class="border-b border-gray-200">
                        <button @click="openItems['storm-damage'] = !openItems['storm-damage']"
                            class="w-full text-left flex items-start gap-4 py-4 focus:outline-none">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white font-semibold">03</span>
                            <h4 class="text-xl font-semibold text-gray-900">{{ __('storm_damage_repair') }}</h4>
                            <svg x-show="!openItems['storm-damage']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="openItems['storm-damage']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <div x-show="openItems['storm-damage']" x-collapse x-cloak
                            class="text-gray-600 mt-2 pl-12 pr-4 pb-4">
                            {!! __('storm_damage_description') !!}
                        </div>
                    </div>

                    <!-- Service 4: Hail Damage -->
                    <div class="border-b border-gray-200">
                        <button @click="openItems['hail-damage'] = !openItems['hail-damage']"
                            class="w-full text-left flex items-start gap-4 py-4 focus:outline-none">
                            <span
                                class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-500 text-white font-semibold">04</span>
                            <h4 class="text-xl font-semibold text-gray-900">{{ __('hail_damage_expertise') }}</h4>
                            <svg x-show="!openItems['hail-damage']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                            <svg x-show="openItems['hail-damage']" x-cloak
                                class="ml-auto w-5 h-5 text-gray-500 transform transition-transform duration-300"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                        </button>
                        <div x-show="openItems['hail-damage']" x-collapse x-cloak
                            class="text-gray-600 mt-2 pl-12 pr-4 pb-4">
                            {!! __('hail_damage_description') !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
