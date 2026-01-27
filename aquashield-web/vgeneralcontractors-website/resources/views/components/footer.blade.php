<!-- Footer Section -->
<div x-data="{ showAppointmentModal: false }">
    @php
        use App\Helpers\PhoneHelper;
    @endphp
    <footer class="bg-gray-900 text-white pt-16 pb-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                <!-- Contact Information -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ __('contact_us') }}</h4>
                    <div class="space-y-3">
                        <p class="flex items-start justify-center md:justify-start">
                            <svg class="w-5 h-5 mr-2 mt-1 text-yellow-500 flex-shrink-0" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span class="text-sm sm:text-base md:text-lg">{{ $companyData->address }}</span>
                        </p>
                        <p class="flex items-center justify-center md:justify-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $companyData->phone }}"
                                class="text-sm sm:text-base md:text-lg hover:text-yellow-500 transition-colors">
                                {{ PhoneHelper::format($companyData->phone) }}
                            </a>
                        </p>
                        <p class="flex items-center justify-center md:justify-start">
                            <svg class="w-5 h-5 mr-2 text-yellow-500 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                            <span class="text-sm sm:text-base md:text-lg">{{ $companyData->email }}</span>
                        </p>
                        <a href="javascript:void(0)" @click="showAppointmentModal = true"
                            class="inline-flex items-center justify-center md:justify-start text-yellow-500 hover:text-yellow-400 transition-colors text-sm sm:text-base md:text-lg cursor-pointer">
                            <svg class="w-5 h-5 mr-2 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            {{ __('schedule_appointment') }}
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ __('quick_links') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('about') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('about_us') }}</a>
                        </li>
                        <li><a href="{{ route('portfolio') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('portfolio') }}</a>
                        </li>
                        <li><a href="{{ route('new-roof') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('new_roof') }}</a>
                        </li>
                        <li><a href="{{ route('roof-repair') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('roof_repair') }}</a>
                        </li>
                        <li><a href="{{ route('storm-damage') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('storm_damage') }}</a>
                        </li>
                        <li><a href="{{ route('hail-damage') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('hail_damage') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Legal & Help -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ __('legal_and_help') }}</h4>
                    <ul class="space-y-2">
                        <li><a href="{{ route('faqs') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('faqs') }}</a>
                        </li>
                        <li><a href="{{ route('privacy-policy') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('privacy_policy') }}</a>
                        </li>
                        <li><a href="{{ route('terms-and-conditions') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('terms_and_conditions') }}</a>
                        </li>
                        <li><a href="{{ route('cookies-policy') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('cookie_policy') }}</a>
                        </li>
                        <li><a href="#"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('sitemap') }}</a>
                        </li>
                        <li><a href="{{ route('contact-support') }}"
                                class="hover:text-yellow-500 transition-colors text-sm sm:text-base md:text-lg">{{ __('contact_support') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Google Map -->
                <div class="text-center md:text-left">
                    <h4 class="text-lg sm:text-xl md:text-2xl font-bold mb-4">{{ __('find_us') }}</h4>
                    <div class="h-48 rounded-lg overflow-hidden">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3463.6617472292933!2d-95.40117182572651!3d29.758501132064385!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8640c0a6728f8879%3A0x87e6d62cceb4acef!2s810%20Waugh%20Dr%2C%20Houston%2C%20TX%2077019%2C%20EE.%20UU.!5e0!3m2!1ses!2spt!4v1741305902297!5m2!1ses!2spt"
                            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <!-- Social Media -->
            <div class="border-t border-gray-800 pt-8 pb-4">
                <div class="flex justify-center space-x-6">
                    <a href="https://facebook.com/vgeneralcontractors"
                        class="text-gray-400 hover:text-yellow-500 transition-colors">
                        <span class="sr-only">{{ __('facebook') }}</span>
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M18.77,7.46H14.5v-1.9c0-.9.6-1.1,1-1.1h3V.5h-4.33C10.24.5,9.5,3.44,9.5,5.32v2.15h-3v4h3v12h5v-12h3.85l.42-4Z" />
                        </svg>
                    </a>
                    <a href="https://instagram.com/vgeneralcontractors"
                        class="text-gray-400 hover:text-yellow-500 transition-colors">
                        <span class="sr-only">{{ __('instagram') }}</span>
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12,2.2c3.2,0,3.6,0,4.9,0.1c3.3,0.1,4.8,1.7,4.9,4.9c0.1,1.3,0.1,1.6,0.1,4.8c0,3.2,0,3.6-0.1,4.8c-0.1,3.2-1.7,4.8-4.9,4.9c-1.3,0.1-1.6,0.1-4.9,0.1c-3.2,0-3.6,0-4.9-0.1c-3.3-0.1-4.8-1.7-4.9-4.9c-0.1-1.3-0.1-1.6-0.1-4.8c0-3.2,0-3.6,0.1-4.8c0.1-3.2,1.7-4.8,4.9-4.9C8.4,2.2,8.8,2.2,12,2.2z M12,0C8.7,0,8.3,0,7.1,0.1c-4.4,0.2-6.8,2.6-7,7C0,8.3,0,8.7,0,12s0,3.7,0.1,4.9c0.2,4.4,2.6,6.8,7,7C8.3,24,8.7,24,12,24s3.7,0,4.9-0.1c4.4-0.2,6.8-2.6,7-7C24,15.7,24,15.3,24,12s0-3.7-0.1-4.9c-0.2-4.4-2.6-6.8-7-7C15.7,0,15.3,0,12,0z M12,5.8c-3.4,0-6.2,2.8-6.2,6.2s2.8,6.2,6.2,6.2s6.2-2.8,6.2-6.2S15.4,5.8,12,5.8z M12,16c-2.2,0-4-1.8-4-4s1.8-4,4-4s4,1.8,4,4S14.2,16,12,16z" />
                        </svg>
                    </a>
                    <a href="https://twitter.com/vgeneralcontractors"
                        class="text-gray-400 hover:text-yellow-500 transition-colors">
                        <span class="sr-only">{{ __('twitter') }}</span>
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                        </svg>
                    </a>
                    <a href="https://linkedin.com/company/vgeneralcontractors"
                        class="text-gray-400 hover:text-yellow-500 transition-colors">
                        <span class="sr-only">{{ __('linkedin') }}</span>
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 md:w-8 md:h-8" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Copyright -->
            <div class="border-t border-gray-800 pt-8 mt-4 text-center">
                <p class="text-sm sm:text-base md:text-lg text-gray-400">
                    {{ __('copyright_text', ['year' => date('Y'), 'company' => $companyData->company_name]) }}
                </p>
            </div>
        </div>
    </footer>

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
</div>
