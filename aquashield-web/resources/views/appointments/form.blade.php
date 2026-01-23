@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 sm:px-8">
        <div class="py-8">
            {{-- Enhanced Animated Header Section --}}
            <div class="p-4 sm:p-6 mb-8">
                <div class="animated-header-card bg-white rounded-2xl shadow-2xl mb-8 overflow-hidden">
                    <div class="animated-gradient-header px-8 py-6 relative">
                        {{-- Floating particles background --}}
                        <div class="absolute inset-0 overflow-hidden pointer-events-none">
                            <div class="absolute top-10 left-20 w-2 h-2 bg-white/20 rounded-full animate-float-slow"></div>
                            <div class="absolute top-20 right-32 w-1 h-1 bg-purple-200/30 rounded-full animate-float-medium">
                            </div>
                            <div class="absolute bottom-16 left-40 w-3 h-3 bg-blue-200/20 rounded-full animate-float-fast">
                            </div>
                            <div class="absolute bottom-8 right-20 w-2 h-2 bg-white/15 rounded-full animate-float-slow">
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between relative z-10">
                            <div class="mb-4 sm:mb-0 w-full">
                                <h1 class="text-2xl xs:text-2xl sm:text-3xl md:text-4xl font-bold text-white mb-2 tracking-tight text-center sm:text-left"
                                    style="text-shadow: 0 4px 8px rgba(0, 0, 0, 0.4), 0 2px 4px rgba(0, 0, 0, 0.3);">
                                    {{ isset($appointment->uuid) ? __('edit_appointment') : __('create_appointment') }}
                                </h1>

                                {{-- Animated subtitle with marquee effect --}}
                                <div
                                    class="bg-white/10 backdrop-blur-md rounded-lg px-2 py-2 border border-white/20 max-w-full sm:max-w-md mx-auto sm:mx-0 text-center">
                                    <div class="marquee-container overflow-hidden w-full">
                                        <div
                                            class="marquee-text animate-marquee whitespace-nowrap text-purple-100/90 text-xs xs:text-sm sm:text-sm font-medium text-center">
                                            ✨
                                            {{ isset($appointment->uuid) ? __('update_your_appointment_details') : __('schedule_your_perfect_appointment') }}
                                            • 🚀 {{ __('efficient_scheduling') }} • 💼
                                            {{ __('professional_service') }} • 📅 {{ __('complete_management') }} •
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Animated icon section --}}
                            <div class="flex items-center space-x-4">
                                {{-- Primary animated icon --}}
                                <div class="relative">
                                    <div
                                        class="w-14 h-14 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center animate-pulse-soft border border-white/30">
                                        <svg class="w-7 h-7 text-white animate-bounce-subtle" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M8 7V3a1 1 0 011-1h6a1 1 0 011 1v4m-6 0h6m-6 0v13a1 1 0 001 1h4a1 1 0 001-1V7M5 7h14l-1 11a1 1 0 01-1 1H7a1 1 0 01-1-1L5 7z">
                                            </path>
                                        </svg>
                                    </div>
                                    {{-- Animated ring --}}
                                    <div class="absolute inset-0 border-2 border-white/30 rounded-xl animate-ping-slow">
                                    </div>
                                </div>

                                {{-- Back to list button --}}
                                <a href="{{ route('appointments.index') }}"
                                    class="inline-flex items-center px-4 py-2 bg-white/20 backdrop-blur-md border border-white/30 rounded-lg font-semibold text-sm text-white uppercase tracking-wide hover:bg-white/30 focus:bg-white/30 active:bg-white/40 focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 transition-all duration-300 transform hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18">
                                        </path>
                                    </svg>
                                    {{ __('back_to_list') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="my-4 overflow-hidden sm:rounded-md">
                <form id="{{ isset($appointment->uuid) ? 'appointmentEditForm' : 'appointmentCreateForm' }}"
                    action="{{ isset($appointment->uuid) ? secure_url(route('appointments.update', $appointment->uuid, false)) : secure_url(route('appointments.store', [], false)) }}"
                    method="POST" class="glassmorphism-form-container shadow-md rounded-lg p-6">
                    @csrf
                    @if (isset($appointment->uuid))
                        @method('PUT')
                    @endif
                    @include('appointments._form')
                    <div class="mt-10 mb-3 flex justify-center">
                        <button type="submit" id="submit-button" disabled
                            class="glassmorphism-submit-btn inline-flex items-center justify-center px-6 py-3 border border-white/10 rounded-lg font-semibold text-sm text-white uppercase tracking-widest focus:outline-none focus:ring disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-300 relative overflow-hidden group"
                            title="Submit Form">

                            {{-- Glass overlay effect --}}
                            <span
                                class="absolute inset-0 w-full h-full bg-white/5 backdrop-filter backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity duration-300"></span>

                            {{-- Content container --}}
                            <span class="relative z-10 flex items-center">
                                {{-- Spinner (hidden initially) --}}
                                <svg id="submit-spinner" class="hidden animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>

                                <span
                                    id="submit-button-text">{{ isset($appointment->uuid) ? __('update_appointment_btn') : __('create_appointment_btn') }}</span>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        /* Modern Dark Crystal Form 2025 with Purple Accents */
        .glassmorphism-form-container {
            position: relative;
            border-radius: 16px;
            overflow: hidden;

            /* Dark Crystal Background */
            background: linear-gradient(135deg,
                    rgba(17, 17, 17, 0.95) 0%,
                    rgba(30, 30, 30, 0.92) 50%,
                    rgba(20, 20, 20, 0.95) 100%);

            /* Elegant Border */
            border: 1px solid rgba(139, 69, 190, 0.3);

            /* Modern Shadow System */
            box-shadow:
                0 8px 32px rgba(139, 69, 190, 0.15),
                0 4px 16px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);

            /* Subtle Blur */
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);

            /* Smooth Animation */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glassmorphism-form-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg,
                    transparent 0%,
                    rgba(139, 69, 190, 0.8) 25%,
                    rgba(168, 85, 247, 0.9) 50%,
                    rgba(139, 69, 190, 0.8) 75%,
                    transparent 100%);
            opacity: 0.8;
        }

        .glassmorphism-form-container:hover {
            transform: translateY(-2px);
            border-color: rgba(168, 85, 247, 0.5);
            box-shadow:
                0 12px 40px rgba(139, 69, 190, 0.25),
                0 6px 20px rgba(0, 0, 0, 0.5),
                inset 0 1px 0 rgba(255, 255, 255, 0.15);
        }

        /* Input Fields Enhancement */
        .glassmorphism-form-container input,
        .glassmorphism-form-container select,
        .glassmorphism-form-container textarea {
            background: rgba(40, 40, 40, 0.8) !important;
            border: 1px solid rgba(139, 69, 190, 0.3) !important;
            color: #ffffff !important;
            border-radius: 8px !important;
            transition: all 0.3s ease !important;
        }

        .glassmorphism-form-container input:focus,
        .glassmorphism-form-container select:focus,
        .glassmorphism-form-container textarea:focus {
            border-color: rgba(168, 85, 247, 0.6) !important;
            box-shadow: 0 0 0 3px rgba(139, 69, 190, 0.2) !important;
            background: rgba(50, 50, 50, 0.9) !important;
        }

        /* Labels Enhancement */
        .glassmorphism-form-container label {
            color: #e5e7eb !important;
            font-weight: 500;
        }

        /* Custom styles for form validation feedback */
        .field-invalid {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.2) !important;
        }

        .field-valid {
            border-color: #10b981 !important;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
        }

        /* Glassmorphism Submit Button - Dark Blue Crystal Style */
        .glassmorphism-submit-btn {
            /* Dark Blue Crystal Background */
            background: linear-gradient(135deg,
                    rgba(30, 58, 138, 0.85) 0%,
                    rgba(37, 99, 235, 0.80) 50%,
                    rgba(29, 78, 216, 0.85) 100%);

            /* Premium Blue Box Shadow System */
            box-shadow:
                0 8px 32px 0 rgba(37, 99, 235, 0.25),
                0 16px 64px 0 rgba(59, 130, 246, 0.18),
                0 4px 16px 0 rgba(30, 58, 138, 0.3),
                0 2px 8px 0 rgba(37, 99, 235, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15),
                inset 0 -1px 0 rgba(255, 255, 255, 0.08);

            /* Enhanced Blur Effects */
            backdrop-filter: blur(16px) saturate(1.2);
            -webkit-backdrop-filter: blur(16px) saturate(1.2);

            /* Refined Border for Glass Effect */
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-top: 1px solid rgba(255, 255, 255, 0.25);

            /* Smooth Transitions */
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Shimmer effect overlay */
        .glassmorphism-submit-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 0.5rem;
            background: linear-gradient(135deg,
                    rgba(255, 255, 255, 0.1) 0%,
                    rgba(255, 255, 255, 0.05) 25%,
                    transparent 50%,
                    rgba(37, 99, 235, 0.08) 75%,
                    rgba(59, 130, 246, 0.12) 100%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        /* Shimmer animation */
        .glassmorphism-submit-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.15), transparent);
            transition: left 0.5s ease;
            pointer-events: none;
        }

        /* Hover effects */
        .glassmorphism-submit-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            background: linear-gradient(135deg,
                    rgba(37, 99, 235, 0.9) 0%,
                    rgba(59, 130, 246, 0.85) 50%,
                    rgba(37, 99, 235, 0.9) 100%);
            box-shadow:
                0 12px 48px 0 rgba(37, 99, 235, 0.35),
                0 24px 80px 0 rgba(59, 130, 246, 0.25),
                0 6px 24px 0 rgba(30, 58, 138, 0.4),
                0 3px 12px 0 rgba(37, 99, 235, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1);
            border-color: rgba(59, 130, 246, 0.5);
        }

        .glassmorphism-submit-btn:hover:not(:disabled)::before {
            opacity: 1;
        }

        .glassmorphism-submit-btn:hover:not(:disabled)::after {
            left: 100%;
        }

        /* Active state */
        .glassmorphism-submit-btn:active:not(:disabled) {
            transform: translateY(-1px);
            box-shadow:
                0 6px 24px 0 rgba(37, 99, 235, 0.3),
                0 12px 48px 0 rgba(59, 130, 246, 0.2),
                0 3px 12px 0 rgba(30, 58, 138, 0.35),
                inset 0 1px 0 rgba(255, 255, 255, 0.25);
        }

        /* Focus state */
        .glassmorphism-submit-btn:focus:not(:disabled) {
            border-color: rgba(59, 130, 246, 0.6);
            box-shadow:
                0 8px 32px 0 rgba(37, 99, 235, 0.3),
                0 16px 64px 0 rgba(59, 130, 246, 0.2),
                0 4px 16px 0 rgba(30, 58, 138, 0.3),
                0 0 20px 0 rgba(37, 99, 235, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.2),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1);
        }

        .submit-button-disabled {
            background: linear-gradient(135deg,
                    rgba(55, 65, 81, 0.7) 0%,
                    rgba(75, 85, 99, 0.65) 50%,
                    rgba(55, 65, 81, 0.7) 100%) !important;
            box-shadow:
                0 4px 16px 0 rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.05) !important;
            border-color: rgba(255, 255, 255, 0.05) !important;
            opacity: 0.5 !important;
            cursor: not-allowed !important;
            pointer-events: none;
            transform: none !important;
        }

        .submit-button-disabled::before,
        .submit-button-disabled::after {
            display: none !important;
        }

        .submit-button-enabled {
            opacity: 1 !important;
            cursor: pointer !important;
            pointer-events: auto;
        }



        /* Validation Message Styles */
        .realtime-validation-message {
            font-size: 0.75rem;
            margin-top: 0.25rem;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Enhanced Glassmorphism Insurance Radio Label Styles */
        .glassmorphism-form-container .insurance-label {
            /* Dark glassmorphic background */
            background: rgba(40, 40, 40, 0.8) !important;

            /* Dark border matching form inputs */
            border: 1px solid rgba(139, 69, 190, 0.3) !important;
            color: #e5e7eb !important;
            /* Light gray text */

            /* Enhanced shadow for depth */
            box-shadow:
                0 2px 8px 0 rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;

            /* Smooth transitions */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;

            /* Typography */
            font-weight: 500;
            position: relative;
            overflow: hidden;
        }

        .glassmorphism-form-container .insurance-label::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(139, 69, 190, 0.1), transparent);
            transition: left 0.5s ease;
            pointer-events: none;
        }

        .glassmorphism-form-container .insurance-label:hover {
            /* Hover state with purple accent */
            background: linear-gradient(135deg,
                    rgba(139, 69, 190, 0.8) 0%,
                    rgba(168, 85, 247, 0.7) 100%) !important;
            color: #ffffff !important;
            border-color: rgba(168, 85, 247, 0.6) !important;

            /* Enhanced hover shadow */
            box-shadow:
                0 6px 16px 0 rgba(139, 69, 190, 0.25),
                0 3px 8px 0 rgba(168, 85, 247, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;

            /* Subtle elevation */
            transform: translateY(-2px) !important;
        }

        .glassmorphism-form-container .insurance-label:hover::before {
            left: 100%;
        }

        .glassmorphism-form-container .insurance-label.selected {
            /* Selected state with vibrant gradient */
            background: linear-gradient(135deg,
                    rgba(168, 85, 247, 0.9) 0%,
                    rgba(139, 69, 190, 0.8) 50%,
                    rgba(168, 85, 247, 0.9) 100%) !important;
            color: #ffffff !important;
            border-color: rgba(168, 85, 247, 0.8) !important;

            /* Enhanced selected shadow */
            box-shadow:
                0 8px 20px 0 rgba(139, 69, 190, 0.3),
                0 4px 12px 0 rgba(168, 85, 247, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -1px 0 rgba(255, 255, 255, 0.15) !important;

            transform: translateY(-2px) !important;
        }

        .glassmorphism-form-container .insurance-label:active {
            transform: translateY(-1px) !important;
            box-shadow:
                0 4px 12px 0 rgba(139, 69, 190, 0.2),
                0 2px 6px 0 rgba(168, 85, 247, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.2) !important;
        }

        .glassmorphism-form-container .insurance-label:focus-within {
            /* Focus state for accessibility */
            outline: none !important;
            border-color: rgba(168, 85, 247, 0.6) !important;

            /* Focus ring */
            box-shadow:
                0 0 0 3px rgba(139, 69, 190, 0.2),
                0 4px 12px 0 rgba(139, 69, 190, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        }

        /* Animation for selected state */
        .glassmorphism-form-container .insurance-label.selected {
            animation: pulseSelected 2s infinite;
        }

        @keyframes pulseSelected {

            0%,
            100% {
                box-shadow:
                    0 8px 20px 0 rgba(139, 69, 190, 0.3),
                    0 4px 12px 0 rgba(168, 85, 247, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.25),
                    inset 0 -1px 0 rgba(255, 255, 255, 0.15);
            }

            50% {
                box-shadow:
                    0 10px 25px 0 rgba(139, 69, 190, 0.4),
                    0 6px 16px 0 rgba(168, 85, 247, 0.25),
                    inset 0 1px 0 rgba(255, 255, 255, 0.3),
                    inset 0 -1px 0 rgba(255, 255, 255, 0.2);
            }
        }

        /* Enhanced Glassmorphism Additional Options Checkbox Styles */
        .glassmorphism-form-container input[type="checkbox"].checkbox-field {
            /* Override any conflicting styles with higher specificity */
            background: rgba(40, 40, 40, 0.8) !important;
            border: 1px solid rgba(139, 69, 190, 0.3) !important;

            /* Remove default text colors */
            color: transparent !important;

            /* Enhanced positioning and sizing */
            width: 1.25rem !important;
            height: 1.25rem !important;
            border-radius: 0.375rem !important;

            /* Remove default appearance completely */
            appearance: none !important;
            -webkit-appearance: none !important;
            -moz-appearance: none !important;

            /* Enhanced shadow system */
            box-shadow:
                0 2px 8px 0 rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1) !important;

            /* Smooth transitions */
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;

            /* Positioning */
            position: relative !important;
            cursor: pointer !important;
        }

        .glassmorphism-form-container input[type="checkbox"].checkbox-field:hover {
            /* Enhanced hover state */
            border-color: rgba(168, 85, 247, 0.5) !important;
            background: rgba(50, 50, 50, 0.9) !important;

            /* Enhanced hover shadow */
            box-shadow:
                0 4px 12px 0 rgba(139, 69, 190, 0.15),
                0 2px 8px 0 rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;

            /* Subtle elevation */
            transform: translateY(-1px) !important;
        }

        .glassmorphism-form-container input[type="checkbox"].checkbox-field:focus {
            /* Enhanced focus state */
            outline: none !important;
            border-color: rgba(168, 85, 247, 0.6) !important;

            /* Focus ring with purple accent */
            box-shadow:
                0 0 0 3px rgba(139, 69, 190, 0.2),
                0 4px 12px 0 rgba(139, 69, 190, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.15) !important;
        }

        .glassmorphism-form-container input[type="checkbox"].checkbox-field:checked {
            /* Enhanced checked state with gradient */
            background: linear-gradient(135deg,
                    rgba(139, 69, 190, 0.9) 0%,
                    rgba(168, 85, 247, 0.8) 50%,
                    rgba(139, 69, 190, 0.9) 100%) !important;

            border-color: rgba(168, 85, 247, 0.8) !important;

            /* Enhanced checked shadow */
            box-shadow:
                0 6px 16px 0 rgba(139, 69, 190, 0.3),
                0 3px 8px 0 rgba(168, 85, 247, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2),
                inset 0 -1px 0 rgba(255, 255, 255, 0.1) !important;
        }

        .glassmorphism-form-container input[type="checkbox"].checkbox-field:checked:hover {
            /* Enhanced checked hover state */
            background: linear-gradient(135deg,
                    rgba(168, 85, 247, 0.95) 0%,
                    rgba(139, 69, 190, 0.85) 50%,
                    rgba(168, 85, 247, 0.95) 100%) !important;

            box-shadow:
                0 8px 20px 0 rgba(139, 69, 190, 0.4),
                0 4px 12px 0 rgba(168, 85, 247, 0.25),
                inset 0 1px 0 rgba(255, 255, 255, 0.25),
                inset 0 -1px 0 rgba(255, 255, 255, 0.15) !important;

            transform: translateY(-2px) !important;
        }

        /* Enhanced Additional Options Label Styles */
        .glassmorphism-form-container label[for="sms_consent"] span,
        .glassmorphism-form-container label[for="intent_to_claim"] span {
            color: #e5e7eb !important;
            /* Light gray text */
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .glassmorphism-form-container label[for="sms_consent"]:hover span,
        .glassmorphism-form-container label[for="intent_to_claim"]:hover span {
            color: #ffffff !important;
            /* White on hover */
        }

        .glassmorphism-form-container label[for="sms_consent"],
        .glassmorphism-form-container label[for="intent_to_claim"] {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.375rem 0.5rem;
            border-radius: 0.5rem;
            border: 1px solid transparent;
        }

        .glassmorphism-form-container label[for="sms_consent"]:hover,
        .glassmorphism-form-container label[for="intent_to_claim"]:hover {
            background: rgba(139, 69, 190, 0.1);
            border-color: rgba(139, 69, 190, 0.2);
        }

        /* Loading spinner for real-time validation */
        .validation-loading {
            position: relative;
        }

        .validation-loading::after {
            content: '';
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid rgba(139, 69, 190, 0.3);
            border-top: 2px solid #8b45be;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: translateY(-50%) rotate(0deg);
            }

            100% {
                transform: translateY(-50%) rotate(360deg);
            }
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById(
                '{{ isset($appointment->uuid) ? 'appointmentEditForm' : 'appointmentCreateForm' }}');
            const submitButton = document.getElementById('submit-button');
            const submitSpinner = document.getElementById('submit-spinner');
            const submitButtonText = document.getElementById('submit-button-text');

            // Function to set loading state
            function setLoadingState(isLoading) {
                submitButton.disabled = isLoading;
                if (isLoading) {
                    submitSpinner.classList.remove('hidden');
                    submitButtonText.textContent = '{{ __('sending') }}...';
                } else {
                    submitSpinner.classList.add('hidden');
                    submitButtonText.textContent =
                        '{{ isset($appointment->uuid) ? __('update_appointment_btn') : __('create_appointment_btn') }}';
                }
            }

            // Form validation function
            function validateForm() {
                const requiredFields = [
                    'first_name',
                    'last_name',
                    'email',
                    'phone',
                    'address_map_input',
                    'city',
                    'state',
                    'zipcode',
                    'country',
                    'lead_source',
                    'inspection_status',
                    'status_lead'
                ];

                let isValid = true;

                // Check text/email/select fields
                requiredFields.forEach(fieldName => {
                    const field = document.getElementById(fieldName);
                    if (field && (!field.value.trim() || field.value === '')) {
                        isValid = false;
                    }
                });

                // Check radio buttons for insurance_property
                const insuranceRadios = document.querySelectorAll('input[name="insurance_property"]');
                const insuranceChecked = Array.from(insuranceRadios).some(radio => radio.checked);
                if (!insuranceChecked) {
                    isValid = false;
                }

                // Special validation for names (letters only)
                const firstName = document.getElementById('first_name');
                const lastName = document.getElementById('last_name');
                const namePattern = /^[A-Za-z\s\'-]+$/;

                if (firstName && firstName.value.trim() && !namePattern.test(firstName.value.trim())) {
                    isValid = false;
                }

                if (lastName && lastName.value.trim() && !namePattern.test(lastName.value.trim())) {
                    isValid = false;
                }

                // Email validation
                const email = document.getElementById('email');
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && email.value.trim() && !emailPattern.test(email.value.trim())) {
                    isValid = false;
                }

                // Check for duplicate indicators
                const emailField = document.getElementById('email');
                const phoneField = document.getElementById('phone');

                if (emailField && emailField.classList.contains('field-invalid')) {
                    isValid = false;
                }

                if (phoneField && phoneField.classList.contains('field-invalid')) {
                    isValid = false;
                }

                // Special date/time validation logic
                const inspectionDate = document.getElementById('inspection_date');
                const inspectionTimeHour = document.getElementById('inspection_time_hour');
                const inspectionTimeMinute = document.getElementById('inspection_time_minute');

                // If inspection date is selected, both hour and minute must be selected
                if (inspectionDate && inspectionDate.value) {
                    if (!inspectionTimeHour || !inspectionTimeHour.value ||
                        !inspectionTimeMinute || !inspectionTimeMinute.value) {
                        isValid = false;
                    }
                }

                return isValid;
            }

            // Function to update submit button state
            function updateSubmitButton() {
                const isFormValid = validateForm();
                submitButton.disabled = !isFormValid;

                if (isFormValid) {
                    submitButton.classList.remove('submit-button-disabled');
                    submitButton.classList.add('submit-button-enabled');
                } else {
                    submitButton.classList.remove('submit-button-enabled');
                    submitButton.classList.add('submit-button-disabled');
                }
            }

            // Function to validate individual field and provide visual feedback
            function validateField(fieldElement, value = null) {
                if (!fieldElement) return true;

                const fieldValue = value !== null ? value : fieldElement.value.trim();
                const fieldName = fieldElement.name || fieldElement.id;
                let isValid = true;

                // Remove existing validation classes
                fieldElement.classList.remove('field-valid', 'field-invalid');

                // Skip validation for optional fields or if field is empty and not required
                if (!fieldElement.hasAttribute('required') && !fieldValue) {
                    return true;
                }

                // Validate based on field type and name
                switch (fieldName) {
                    case 'first_name':
                    case 'last_name':
                        const namePattern = /^[A-Za-z\s\'-]+$/;
                        isValid = fieldValue && namePattern.test(fieldValue);
                        break;

                    case 'email':
                        const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                        isValid = fieldValue && emailPattern.test(fieldValue);
                        break;

                    case 'phone':
                        isValid = fieldValue && fieldValue.length >= 10;
                        break;

                    default:
                        // For other required fields, just check if they have a value
                        if (fieldElement.hasAttribute('required')) {
                            isValid = fieldValue !== '' && fieldValue !== null;
                        }
                        break;
                }

                // Apply visual feedback
                if (fieldValue) { // Only apply visual feedback if field has content
                    if (isValid) {
                        fieldElement.classList.add('field-valid');
                    } else {
                        fieldElement.classList.add('field-invalid');
                    }
                }

                return isValid;
            }

            // Function to check if email exists in real-time
            function checkEmailExists(email, excludeUuid = null) {
                if (!email || email.trim() === '') return Promise.resolve(false);

                const formData = new FormData();
                formData.append('email', email);
                if (excludeUuid) {
                    formData.append('exclude_uuid', excludeUuid);
                }

                return fetch('{{ secure_url(route('appointments.check-email', [], false)) }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            return data.exists;
                        }
                        return false;
                    })
                    .catch(error => {
                        console.error('Error checking email:', error);
                        return false;
                    });
            }

            // Function to check if phone exists in real-time
            function checkPhoneExists(phone, excludeUuid = null) {
                if (!phone || phone.trim() === '') return Promise.resolve(false);

                const formData = new FormData();
                formData.append('phone', phone);
                if (excludeUuid) {
                    formData.append('exclude_uuid', excludeUuid);
                }

                return fetch('{{ secure_url(route('appointments.check-phone', [], false)) }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                                'content')
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            return data.exists;
                        }
                        return false;
                    })
                    .catch(error => {
                        console.error('Error checking phone:', error);
                        return false;
                    });
            }

            // Function to show validation message
            function showValidationMessage(fieldElement, message, isError = true) {
                // Remove existing messages
                const existingMessage = fieldElement.parentNode.querySelector('.realtime-validation-message');
                if (existingMessage) {
                    existingMessage.remove();
                }

                if (message) {
                    const messageElement = document.createElement('div');
                    messageElement.className =
                        `realtime-validation-message text-xs mt-1 ${isError ? 'text-red-500' : 'text-green-500'}`;
                    messageElement.textContent = message;
                    fieldElement.parentNode.appendChild(messageElement);
                }
            }

            // Add event listeners to all form fields
            const allInputs = form.querySelectorAll('input, select, textarea');
            allInputs.forEach(input => {
                // Real-time validation on input/change
                input.addEventListener('input', function(e) {
                    validateField(e.target);
                    updateSubmitButton();
                });

                input.addEventListener('change', function(e) {
                    validateField(e.target);
                    updateSubmitButton();
                });

                input.addEventListener('blur', function(e) {
                    validateField(e.target);
                    updateSubmitButton();
                });
            });

            // Special handling for email field - Real-time duplicate check
            const emailField = document.getElementById('email');
            if (emailField) {
                let emailTimeout;
                emailField.addEventListener('input', function(e) {
                    clearTimeout(emailTimeout);
                    const email = e.target.value.trim();

                    // Clear previous messages
                    showValidationMessage(e.target, '');
                    e.target.classList.remove('validation-loading');

                    if (email && email.includes('@')) {
                        // Show loading indicator
                        e.target.classList.add('validation-loading');

                        emailTimeout = setTimeout(() => {
                            const excludeUuid =
                                '{{ isset($appointment->uuid) ? $appointment->uuid : null }}';
                            checkEmailExists(email, excludeUuid).then(exists => {
                                e.target.classList.remove('validation-loading');
                                if (exists) {
                                    showValidationMessage(e.target,
                                        '{{ __('This email is already registered') }}',
                                        true);
                                    e.target.classList.add('field-invalid');
                                    e.target.classList.remove('field-valid');
                                } else {
                                    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                                    if (emailPattern.test(email)) {
                                        showValidationMessage(e.target,
                                            '{{ __('Email is available') }}', false);
                                        e.target.classList.add('field-valid');
                                        e.target.classList.remove('field-invalid');
                                    }
                                }
                                updateSubmitButton();
                            }).catch(() => {
                                e.target.classList.remove('validation-loading');
                                updateSubmitButton();
                            });
                        }, 800); // Wait 800ms after user stops typing
                    }
                });
            }

            // Special handling for phone field - Real-time duplicate check
            const phoneField = document.getElementById('phone');
            if (phoneField) {
                let phoneTimeout;
                phoneField.addEventListener('input', function(e) {
                    clearTimeout(phoneTimeout);
                    const phone = e.target.value.trim();

                    // Clear previous messages
                    showValidationMessage(e.target, '');
                    e.target.classList.remove('validation-loading');

                    if (phone && phone.length >= 10) {
                        // Show loading indicator
                        e.target.classList.add('validation-loading');

                        phoneTimeout = setTimeout(() => {
                            const excludeUuid =
                                '{{ isset($appointment->uuid) ? $appointment->uuid : null }}';
                            checkPhoneExists(phone, excludeUuid).then(exists => {
                                e.target.classList.remove('validation-loading');
                                if (exists) {
                                    showValidationMessage(e.target,
                                        '{{ __('This phone number is already registered') }}',
                                        true);
                                    e.target.classList.add('field-invalid');
                                    e.target.classList.remove('field-valid');
                                } else {
                                    showValidationMessage(e.target,
                                        '{{ __('Phone number is available') }}', false);
                                    e.target.classList.add('field-valid');
                                    e.target.classList.remove('field-invalid');
                                }
                                updateSubmitButton();
                            }).catch(() => {
                                e.target.classList.remove('validation-loading');
                                updateSubmitButton();
                            });
                        }, 800); // Wait 800ms after user stops typing
                    }
                });
            } // Special handling for radio buttons (insurance_property)
            const insuranceRadios = document.querySelectorAll('input[name="insurance_property"]');
            insuranceRadios.forEach(radio => {
                radio.addEventListener('change', updateSubmitButton);
            });

            // Special handling for time fields that might be added dynamically
            document.addEventListener('change', function(e) {
                if (e.target.id === 'inspection_time_hour' || e.target.id === 'inspection_time_minute') {
                    updateSubmitButton();
                }
            });

            // Initial validation check
            updateSubmitButton();

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Final validation check before submission
                if (!validateForm()) {
                    Swal.fire({
                        title: '{{ __('validation_error') }}',
                        text: '{{ __('please_fill_required_fields') }}',
                        icon: 'warning',
                        confirmButtonText: '{{ __('swal_ok') }}'
                    });
                    return;
                }

                // Show spinner and disable button
                setLoadingState(true);

                fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: new FormData(form)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reset form before showing success message
                            form.reset();

                            // Reset button state as well
                            setLoadingState(false);

                            Swal.fire({
                                title: '{{ __('success_title') }}',
                                text: data.message,
                                icon: 'success',
                                confirmButtonText: '{{ __('swal_ok') }}'
                            }).then(() => {
                                // Use redirectUrl from response if available
                                window.location.href = data.redirectUrl ||
                                    "{{ route('appointments.index') }}";
                            });
                        } else {
                            // Check specifically for scheduling conflicts
                            if (data.errors && data.errors.schedule_conflict) {
                                Swal.fire({
                                    title: '{{ __('scheduling_conflict') }}',
                                    text: data.errors.schedule_conflict,
                                    icon: 'warning',
                                    confirmButtonText: '{{ __('swal_ok') }}'
                                });
                            } else {
                                let errorMessage = data.message;
                                if (data.errors) {
                                    errorMessage += '\n';
                                    Object.values(data.errors).forEach(error => {
                                        errorMessage += '\n• ' + error;
                                    });
                                }

                                Swal.fire({
                                    title: '{{ __('error_title') }}',
                                    text: errorMessage,
                                    icon: 'error',
                                    confirmButtonText: '{{ __('swal_ok') }}'
                                });
                            }

                            // Hide spinner and enable button on error
                            setLoadingState(false);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            title: '{{ __('error_title') }}',
                            text: '{{ __('unexpected_error_occurred') }}',
                            icon: 'error',
                            confirmButtonText: '{{ __('swal_ok') }}'
                        });

                        // Hide spinner and enable button on error
                        setLoadingState(false);
                    });
            });

            // Funcionalidad de compartir ubicación
            const shareWhatsApp = document.getElementById('share-whatsapp');
            const shareEmail = document.getElementById('share-email');
            const shareMaps = document.getElementById('share-maps');
            const copyAddress = document.getElementById('copy-address');

            if (shareWhatsApp && shareEmail && shareMaps && copyAddress) {
                const updateShareLinks = () => {
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;
                    const address = document.getElementById('address_map_input').value;

                    if (!lat || !lng) {
                        // Deshabilitar botones si no hay coordenadas
                        [shareWhatsApp, shareEmail, shareMaps, copyAddress].forEach(btn => {
                            btn.classList.add('opacity-50', 'cursor-not-allowed');
                            btn.setAttribute('disabled', 'disabled');
                        });
                        return;
                    }

                    // Habilitar botones
                    [shareWhatsApp, shareEmail, shareMaps, copyAddress].forEach(btn => {
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        btn.removeAttribute('disabled');
                    });

                    const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;

                    // WhatsApp
                    shareWhatsApp.href =
                        `https://wa.me/?text={{ __('location_for_inspection') }}: ${encodeURIComponent(address)} - ${encodeURIComponent(mapsUrl)}`;
                    shareWhatsApp.target = '_blank';

                    // Email
                    const subject = encodeURIComponent('{{ __('location_for_inspection') }}');
                    const body = encodeURIComponent(
                        `{{ __('location_for_inspection') }}: ${address}\n\n{{ __('view_google_maps') }}: ${mapsUrl}`
                    );
                    shareEmail.href = `mailto:?subject=${subject}&body=${body}`;

                    // Maps
                    shareMaps.href = mapsUrl;
                    shareMaps.target = '_blank';

                    // Copy link
                    copyAddress.addEventListener('click', function(e) {
                        e.preventDefault();
                        navigator.clipboard.writeText(mapsUrl).then(() => {
                            // Mostrar mensaje de confirmación
                            const originalText = this.innerHTML;
                            this.innerHTML =
                                '<svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> {{ __('copied') }}';
                            setTimeout(() => {
                                this.innerHTML = originalText;
                            }, 2000);
                        });
                    });
                };

                // Actualizar enlaces cuando cambie la dirección
                document.getElementById('address_map_input').addEventListener('change', updateShareLinks);

                // Inicializar enlaces
                updateShareLinks();

                // Actualizar enlaces cuando cambie el mapa
                if (typeof google !== 'undefined' && typeof google.maps !== 'undefined') {
                    // Si se usa autocomplete, escuchar ese evento también
                    if (typeof autocomplete !== 'undefined') {
                        google.maps.event.addListener(autocomplete, 'place_changed', updateShareLinks);
                    }
                }
            }
        });
    </script>

    {{-- Enhanced Animated Header Styles --}}
    <style>
        /* Animated gradient background */
        .animated-gradient-header {
            background: linear-gradient(135deg, #1e3c72 0%, #2a5298 25%, #1e3c72 50%, #6a11cb 75%, #2575fc 100%);
            background-size: 300% 300%;
            animation: gradientShift 8s ease infinite;
            position: relative;
        }

        .animated-header-card {
            border: 2px solid #34d399;
            /* border-emerald-400 */
            box-shadow: 0 0 30px rgba(16, 185, 129, 0.4), 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .animated-gradient-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg,
                    rgba(255, 255, 255, 0.1) 0%,
                    rgba(255, 255, 255, 0.05) 50%,
                    rgba(255, 255, 255, 0.1) 100%);
            pointer-events: none;
        }

        @keyframes gradientShift {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }
        }

        /* Marquee animation */
        .marquee-container {
            width: 100%;
            max-width: 400px;
        }

        .marquee-text {
            display: inline-block;
            animation: marquee 20s linear infinite;
        }

        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        /* Floating animations */
        @keyframes float-slow {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-10px) rotate(180deg);
            }
        }

        @keyframes float-medium {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-15px) rotate(90deg);
            }
        }

        @keyframes float-fast {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            50% {
                transform: translateY(-20px) rotate(270deg);
            }
        }

        .animate-float-slow {
            animation: float-slow 6s ease-in-out infinite;
        }

        .animate-float-medium {
            animation: float-medium 4s ease-in-out infinite;
        }

        .animate-float-fast {
            animation: float-fast 3s ease-in-out infinite;
        }

        /* Pulse animations */
        @keyframes pulse-soft {

            0%,
            100% {
                transform: scale(1);
                opacity: 1;
            }

            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }

        .animate-pulse-soft {
            animation: pulse-soft 3s ease-in-out infinite;
        }

        /* Bounce subtle animation */
        @keyframes bounce-subtle {

            0%,
            100% {
                transform: translateY(0);
                animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
            }

            50% {
                transform: translateY(-5px);
                animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
            }
        }

        .animate-bounce-subtle {
            animation: bounce-subtle 2s infinite;
        }

        /* Ping slow animation */
        @keyframes ping-slow {
            0% {
                transform: scale(1);
                opacity: 1;
            }

            75%,
            100% {
                transform: scale(2);
                opacity: 0;
            }
        }

        .animate-ping-slow {
            animation: ping-slow 3s cubic-bezier(0, 0, 0.2, 1) infinite;
        }

        /* Enhanced hover effects for header elements */
        .animated-header-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 0 40px rgba(16, 185, 129, 0.5), 0 25px 50px rgba(0, 0, 0, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Responsive adjustments */
        @media (max-width: 640px) {
            .marquee-container {
                max-width: 280px;
            }

            .marquee-text {
                font-size: 0.75rem;
            }

            .animated-gradient-header {
                padding: 1rem;
            }
        }
    </style>
@endpush
