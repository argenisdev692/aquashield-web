<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('language') }} {{ __('demo') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <div class="mt-6 text-gray-500 dark:text-gray-400">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-6">
                            {{ __('welcome_title') }}
                        </h3>

                        <p class="mb-4">
                            {{ __('welcome_description') }}
                        </p>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
                            <!-- Navigation Section -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    {{ __('navigation') }}
                                </h4>
                                <ul class="space-y-2">
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        {{ __('home') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        {{ __('about_us') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        {{ __('services') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-blue-500 rounded-full mr-3"></span>
                                        {{ __('contact') }}
                                    </li>
                                </ul>
                            </div>

                            <!-- Services Section -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                    {{ __('services') }}
                                </h4>
                                <ul class="space-y-2">
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        {{ __('new_roof') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        {{ __('roof_repair') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        {{ __('storm_damage') }}
                                    </li>
                                    <li class="flex items-center">
                                        <span class="w-2 h-2 bg-green-500 rounded-full mr-3"></span>
                                        {{ __('insurance_claims') }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Current Language Info -->
                        <div class="mt-8 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-500 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-blue-700 dark:text-blue-300">
                                    <strong>{{ __('current_language') }}:</strong>
                                    {{ app()->getLocale() === 'en' ? __('english') : __('spanish') }}
                                </span>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-8 flex flex-wrap gap-4">
                            <button
                                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                {{ __('schedule_free_inspection') }}
                            </button>
                            <button
                                class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                                {{ __('get_quote') }}
                            </button>
                            <button
                                class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                {{ __('call_us') }}
                            </button>
                        </div>

                        <!-- Business Hours -->
                        <div class="mt-8 bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                                {{ __('business_hours') }}
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span>{{ __('monday_friday') }}:</span>
                                    <span class="font-medium">8:00 AM - 6:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>{{ __('saturday') }}:</span>
                                    <span class="font-medium">9:00 AM - 4:00 PM</span>
                                </div>
                                <div class="flex justify-between">
                                    <span>{{ __('sunday') }}:</span>
                                    <span class="font-medium">{{ __('closed') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
