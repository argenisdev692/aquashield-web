<!-- Certifications Section -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Certification Card -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/img/v-constructor-certificated-02.webp') }}"
                        alt="{{ __('certification_badge_alt') }}" class="h-24 w-auto" width="200" height="96"
                        loading="lazy">
                </div>
                <h3 class="text-2xl font-bold text-gray-900 text-center mb-4">{{ __('certified_excellence') }}</h3>
                <p class="text-gray-600 text-center">{{ __('certified_excellence_description') }}</p>
            </div>

            <!-- Financial Options Card -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/img/v-constructor-financial-02.webp') }}"
                        alt="{{ __('financial_options_alt') }}" class="h-24 w-auto" width="200" height="96"
                        loading="lazy">
                </div>
                <h3 class="text-2xl font-bold text-gray-900 text-center mb-4">{{ __('flexible_financing') }}</h3>
                <p class="text-gray-600 text-center">{{ __('flexible_financing_description') }}</p>
            </div>

            <!-- Warranty Card -->
            <div class="bg-white p-8 rounded-lg shadow-lg transform transition-transform duration-300 hover:scale-105">
                <div class="flex justify-center mb-6">
                    <img src="{{ asset('assets/img/v-constructor-roof-02-warranty.webp') }}"
                        alt="{{ __('gaf_warranty_badge_alt') }}" class="h-24 w-auto" width="200" height="96"
                        loading="lazy">
                </div>
                <h3 class="text-2xl font-bold text-gray-900 text-center mb-4">{{ __('gaf_certified_warranty') }}</h3>
                <p class="text-gray-600 text-center">{{ __('gaf_warranty_description') }}</p>
            </div>
        </div>
    </div>
</section>

<!-- Certifications Carousel -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto" x-data="{
            currentSlide: 0,
            slides: [
                '{{ asset('assets/img/logo-google-verified-business.webp') }}',
                '{{ asset('assets/img/gaf-certified.webp') }}',
                '{{ asset('assets/img/gaf-system-plus.webp') }}'
            ]
        }" x-init="setInterval(() => { currentSlide = currentSlide === 2 ? 0 : currentSlide + 1 }, 3000)">

            <div class="relative h-40 overflow-hidden rounded-lg">
                <!-- Slides -->
                <template x-for="(slide, index) in slides" :key="index">
                    <div x-show="currentSlide === index" x-transition:enter="transition transform duration-500"
                        x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="transition transform duration-500" x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="-translate-x-full"
                        class="absolute inset-0 flex justify-center items-center">
                        <img :src="slide" :alt="'{{ __('certification_alt') }} ' + (index + 1)"
                            class="h-32 object-contain">
                    </div>
                </template>

                <!-- Navigation Buttons -->
                <button @click="currentSlide = currentSlide === 0 ? slides.length - 1 : currentSlide - 1"
                    class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-black/30 text-white p-2 rounded-r hover:bg-black/50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </button>
                <button @click="currentSlide = currentSlide === slides.length - 1 ? 0 : currentSlide + 1"
                    class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-black/30 text-white p-2 rounded-l hover:bg-black/50 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>

                <!-- Indicators -->
                <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                    <template x-for="(slide, index) in slides" :key="index">
                        <button @click="currentSlide = index"
                            :class="{ 'bg-yellow-500': currentSlide === index, 'bg-gray-300': currentSlide !== index }"
                            class="w-3 h-3 rounded-full transition-colors duration-300"></button>
                    </template>
                </div>
            </div>
        </div>
    </div>
</section>
