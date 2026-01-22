<!-- Video Section - Optimized -->
@php
    use App\Helpers\PhoneHelper;
@endphp
<section class="py-16 bg-gray-900 fade-in-section" x-data="{ showAppointmentModal: false }">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <span class="text-yellow-500 font-semibold">{{ __('watch_our_story') }}</span>
            <h2 class="text-4xl font-bold mt-2 mb-4 text-white">{!! __('see_how_we_transform_homes') !!}</h2>
            <p class="text-gray-300 max-w-3xl mx-auto">{{ __('video_section_description') }}</p>
        </div>

        <div class="max-w-4xl mx-auto relative rounded-xl overflow-hidden shadow-2xl">
            <div class="aspect-w-16 aspect-h-9">
                <video class="w-full h-full object-cover" controls preload="metadata"
                    poster="{{ asset('assets/video/thumbnail.webp') }}" loading="lazy">
                    <source src="{{ asset('assets/video/VIDEO_VGENERALCONTRACTORS.COM_1080p.webm') }}"
                        type="video/webm">
                    <source src="{{ asset('assets/video/VIDEO_VGENERALCONTRACTORS.COM_1080p.mp4') }}" type="video/mp4">
                    {{ __('video_not_supported') }}
                </video>
            </div>
        </div>

        <!-- Call to Action bajo el video -->
        <div class="text-center mt-12">
            <p class="text-gray-300 text-lg mb-6">{{ __('ready_transform_roof_free_inspection') }}</p>
            <div class="flex justify-center gap-4">
                <x-primary-button @click="showAppointmentModal = true" class="text-lg px-8 py-4">
                    {{ __('schedule_free_inspection') }}
                </x-primary-button>
                <a href="tel:{{ $companyData->phone }}"
                    class="inline-flex items-center bg-transparent border-2 border-yellow-500 text-yellow-500 px-8 py-4 rounded hover:bg-yellow-500 hover:text-white transition-all duration-300">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    {{ __('call_phone_number') }} {{ PhoneHelper::format($companyData->phone) }}
                </a>
            </div>
        </div>
    </div>

    <!-- Appointment Modal -->
    <div x-show="showAppointmentModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showAppointmentModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <!-- Modal panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="showAppointmentModal = false">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <livewire:appointment-form />
                </div>
            </div>
        </div>
    </div>
</section>
