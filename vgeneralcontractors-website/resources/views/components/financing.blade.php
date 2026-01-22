<!-- Parallax Financing Section -->
<section class="relative py-24 bg-fixed bg-center bg-cover fade-in-section"
    style="background-image: url('{{ asset('assets/img/bg-financial-1024x690.webp') }}');">
    <!-- Overlay -->
    <div class="absolute inset-0 bg-black/50"></div>

    <!-- Content -->
    <div class="relative container mx-auto px-4 text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-4xl font-bold text-white mb-6">{{ __('financing') }}</h2>
            <p class="text-xl text-gray-100 mb-2">{{ __('financing_description_p1') }}</p>
            <p class="text-xl text-gray-100 mb-8">{{ __('financing_description_p2') }}</p>

        </div>
    </div>
</section>
@php
    use App\Helpers\PhoneHelper;
@endphp
<!-- Call Us Now Section -->
<section class="bg-yellow-500 py-12">
    <div class="container mx-auto px-4">
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-8">
            <h3 class="text-2xl sm:text-3xl font-bold text-black">{{ __('call_us_now') }}</h3>
            <a href="tel:{{ $companyData->phone }}"
                class="inline-flex items-center bg-black text-white text-xl sm:text-2xl font-bold px-6 sm:px-8 py-3 sm:py-4 rounded-lg hover:bg-gray-900 transition-colors duration-300">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                {{ PhoneHelper::format($companyData->phone) }}
            </a>
        </div>
    </div>
</section>
