@extends('layouts.app')

@section('title', 'Invoice Management')






@section('content')
    <style>
        /* Modern Animated Gradient Header with Particles */
        .animated-gradient-header {
            background: linear-gradient(-45deg, #8b5cf6, #6366f1, #3b82f6, #1d4ed8, #7c3aed, #f59e0b, #eab308);
            background-size: 500% 500%;
            animation: gradientShift 10s ease-in-out infinite;
            position: relative;
            overflow: hidden;
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

        /* Floating White Particles */
        .animated-gradient-header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: floatPattern 20s linear infinite;
            pointer-events: none;
        }

        .animated-gradient-header::after {
            content: '';
            position: absolute;
            top: 20%;
            right: 10%;
            width: 100px;
            height: 100px;
            background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
            border-radius: 50%;
            animation: floatBubble 6s ease-in-out infinite;
            pointer-events: none;
        }

        @keyframes floatPattern {
            0% {
                transform: translate(0, 0) rotate(0deg);
            }

            100% {
                transform: translate(-50px, -50px) rotate(360deg);
            }
        }

        @keyframes floatBubble {

            0%,
            100% {
                transform: translateY(0) scale(1);
                opacity: 0.7;
            }

            50% {
                transform: translateY(-20px) scale(1.1);
                opacity: 0.9;
            }
        }

        /* Modern Glass Container with Animated Border */
        .glass-container {
            position: relative;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0.05) 100%);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 1.5rem;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.1), 0 4px 16px rgba(99, 102, 241, 0.05);
            overflow: hidden;
            /* Important for pseudo-element */
        }

        .glass-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1rem;
            border: 1px solid transparent;
            background: linear-gradient(90deg, #f59e0b, #eab308, #8b5cf6, #d946ef, #f59e0b) border-box;
            background-size: 200% auto;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            z-index: 0;
            animation: rotateGradient 3s linear infinite;
        }

        .table-container {
            position: relative;
            border-radius: 1rem;
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.2);
            overflow: hidden;
            /* Ensures the child table respects the border radius */
        }

        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: 1rem;
            border: 1px solid transparent;
            background: linear-gradient(90deg, #f59e0b, #eab308, #8b5cf6, #d946ef, #f59e0b) border-box;
            background-size: 200% auto;
            -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: destination-out;
            mask-composite: exclude;
            z-index: 1;
            animation: rotateGradient 3s linear infinite;
            pointer-events: none;
            /* Allows interaction with the table */
        }

        /* Modern Glass Input */
        .glass-input {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgb(243, 244, 246);
            transition: all 0.3s ease;
        }

        .glass-input::placeholder {
            color: rgb(209, 213, 219);
        }

        .glass-input:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(139, 92, 246, 0.4);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            color: rgb(255, 255, 255);
        }

        /* Modern Date Picker Custom Styles - Litepicker Alternative */
        .litepicker {
            background: rgba(30, 30, 30, 0.95) !important;
            backdrop-filter: blur(20px) !important;
            border: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 12px !important;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3) !important;
            z-index: 99999 !important;
            position: fixed !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        .litepicker .container__main {
            background: rgba(30, 30, 30, 0.95) !important;
            border-radius: 12px !important;
        }

        .litepicker .container__months {
            background: transparent !important;
        }

        .litepicker .month-item {
            background: transparent !important;
            color: rgb(243, 244, 246) !important;
        }

        .litepicker .month-item-header {
            color: rgb(243, 244, 246) !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
            font-weight: 600 !important;
        }

        .litepicker .month-item-weekdays-row > div {
            color: rgba(156, 163, 175, 0.8) !important;
            font-weight: 600 !important;
            font-size: 0.75rem !important;
        }

        .litepicker .day-item {
            color: rgb(229, 231, 235) !important;
            border-radius: 6px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-weight: 500 !important;
        }

        .litepicker .day-item:hover {
            background: rgba(147, 51, 234, 0.5) !important;
            color: white !important;
            transform: scale(1.05) !important;
            box-shadow: 0 2px 8px rgba(147, 51, 234, 0.3) !important;
        }

        .litepicker .day-item.is-start-date,
        .litepicker .day-item.is-end-date {
            background: rgba(147, 51, 234, 0.8) !important;
            color: white !important;
            font-weight: 700 !important;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.4) !important;
        }

        .litepicker .day-item.is-in-range {
            background: rgba(147, 51, 234, 0.3) !important;
            color: white !important;
        }

        .litepicker .day-item.is-today {
            background: rgba(255, 255, 255, 0.1) !important;
            font-weight: 700 !important;
            border: 1px solid rgba(147, 51, 234, 0.5) !important;
        }

        .litepicker .container__footer {
            background: rgba(30, 30, 30, 0.9) !important;
            border-top: 1px solid rgba(255, 255, 255, 0.1) !important;
            border-radius: 0 0 12px 12px !important;
        }

        .litepicker .container__footer .preview-date-range {
            color: rgb(243, 244, 246) !important;
            font-weight: 500 !important;
        }

        .litepicker .container__footer .button-cancel,
        .litepicker .container__footer .button-apply {
            background: rgba(255, 255, 255, 0.1) !important;
            color: rgb(229, 231, 235) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            border-radius: 6px !important;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
            font-weight: 500 !important;
        }

        .litepicker .container__footer .button-apply {
            background: rgba(147, 51, 234, 0.8) !important;
            border-color: rgba(147, 51, 234, 1) !important;
            color: white !important;
        }

        .litepicker .container__footer .button-cancel:hover,
        .litepicker .container__footer .button-apply:hover {
            transform: translateY(-1px) !important;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
        }

        .litepicker .container__footer .button-apply:hover {
            background: rgba(147, 51, 234, 1) !important;
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.4) !important;
        }

        /* Fallback native date input styles */
        .modern-date-picker-fallback {
            background: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2) !important;
            color: rgb(243, 244, 246) !important;
            border-radius: 8px !important;
            padding: 8px 12px !important;
            backdrop-filter: blur(10px) !important;
        }

        .modern-date-picker-fallback:focus {
            border-color: rgba(147, 51, 234, 0.8) !important;
            box-shadow: 0 0 0 3px rgba(147, 51, 234, 0.25) !important;
            outline: none !important;
        }

        /* Responsive adjustments for Modern Date Picker */
        @media (max-width: 768px) {
            .litepicker {
                width: 90vw !important;
                max-width: 350px !important;
                left: 50% !important;
                transform: translateX(-50%) !important;
            }
            
            .litepicker .container__months {
                flex-direction: column !important;
            }
        }

        /* Modern Button Styles */
        .modern-button {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .modern-button:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(147, 51, 234, 0.3);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(147, 51, 234, 0.15);
        }

        .modern-button:active {
            transform: translateY(0);
        }

        .glass-input:hover {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Modern Toggle */
        .glass-toggle {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }

        .glass-toggle.active {
            background: linear-gradient(135deg, #8b5cf6, #6366f1);
        }

        .glass-toggle-thumb {
            background: rgba(255, 255, 255, 0.95);
        }

        /* Quick Date Range Label Styles */
        .date-range-label {
            padding: 0.5rem 1rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: rgb(209, 213, 219);
            position: relative;
            z-index: 1;
        }

        input[type="radio"]:checked+.date-range-label {
            background: rgba(139, 92, 246, 0.3);
            color: white;
            border-color: rgba(139, 92, 246, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.2);
        }

        @keyframes rotateGradient {
            0% {
                background-position: 0% 50%;
            }

            100% {
                background-position: 200% 50%;
            }
        }

        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        transition: all 0.3s ease;
        }

        /* Text Styling */
        .glass-text {
            color: rgb(243, 244, 246);
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        }

        .glass-label {
            color: rgb(229, 231, 235);
            font-weight: 600;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
        }

        /* Enhanced Modern Button with Circle Effect */
        .modern-button {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow:
                0 8px 32px rgba(139, 92, 246, 0.2),
                0 4px 16px rgba(99, 102, 241, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.4);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .modern-button::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.2), rgba(139, 92, 246, 0.1));
            border-radius: 50%;
            transform: translate(-50%, -50%);
            transition: all 0.6s ease;
            z-index: 0;
        }

        .modern-button:hover::before {
            width: 300px;
            height: 300px;
        }

        .modern-button:hover {
            transform: translateY(-2px);
            box-shadow:
                0 12px 40px rgba(139, 92, 246, 0.3),
                0 6px 20px rgba(245, 158, 11, 0.2),
                0 4px 12px rgba(99, 102, 241, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            background: rgba(255, 255, 255, 0.98);
        }

        .modern-button>* {
            position: relative;
            z-index: 1;
        }

        /* Option Styling for Dark Theme */
        .glass-input option {
            background: rgb(31, 41, 55);
            color: rgb(243, 244, 246);
        }

        /* ========== MARQUEE ANIMATION ========== */
        @keyframes marquee {
            0% {
                transform: translateX(100%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .animate-marquee {
            animation: marquee 15s linear infinite;
        }

        .marquee-container:hover .animate-marquee {
            animation-play-state: paused;
        }

        /* ========== GLASSMORPHIC FILTER STYLES ========== */
        .glass-container-filter {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.12) 0%, rgba(255, 255, 255, 0.06) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(139, 92, 246, 0.15), 0 4px 16px rgba(99, 102, 241, 0.1);
        }

        .glass-input-filter {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Estilos para hacer visibles las flechas de los selectores */
        select.glass-input-filter {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1rem;
            padding-right: 2.5rem;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        .glass-input-filter:hover {
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.25);
            transform: translateY(-1px);
        }

        .glass-input-filter:focus {
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(139, 92, 246, 0.4);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.15);
            transform: translateY(-1px);
        }

        .glass-button-filter {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(12px);
        }

        .glass-button-filter:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.25);
        }

        .glass-button-filter:active {
            transform: translateY(-1px);
        }

        /* ========== DELETED ITEMS STYLING ========== */
        .deleted-item {
            background: linear-gradient(135deg, rgba(185, 28, 28, 0.3) 0%, rgba(153, 27, 27, 0.2) 100%) !important;
        }

        /* ========== IMPROVED GLASS INPUT STYLES ========== */
        .glass-input-filter {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
    <div class="min-h-screen py-8" x-data="invoiceDemoData()" x-init="init()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="bg-white rounded-2xl shadow-xl border border-purple-100 mb-8 overflow-hidden">
                <div class="animated-gradient-header px-8 py-6 relative">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h1 class="text-3xl font-bold text-white mb-2"
                                style="text-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);">
                                {{ __('invoices_demo_traduccion_title') }}</h1>
                            <p class="text-purple-100 opacity-90 glass-text">{{ __('invoices_demo_traduccion_subtitle') }}</p>
                        </div>
                        <!-- Motivational Quote Section with Icon -->
                        <div class="mt-4 sm:mt-0">
                            <div class="flex items-center space-x-4">
                                <!-- Animated Invoice Icon -->
                                <div class="relative">
                                    <div
                                        class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-xl flex items-center justify-center animate-pulse">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </svg>
                                    </div>
                                </div>

                                <!-- Marquee Motivational Text -->
                                <div
                                    class="hidden sm:block bg-white/10 backdrop-blur-md rounded-lg px-4 py-2 border border-white/20">
                                    <div class="marquee-container overflow-hidden w-64">
                                        <div
                                            class="marquee-text animate-marquee whitespace-nowrap text-white/90 text-sm font-medium">
                                            ✨ {{ __('invoices_demo_traduccion_create_professional') }} • 🚀 {{ __('invoices_demo_traduccion_boost_business') }} •
                            💼 {{ __('invoices_demo_traduccion_stay_organized') }} • 📊 {{ __('invoices_demo_traduccion_track_progress') }} •
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Glassmorphic Filters Section -->
            <div
                class="glass-container-filter backdrop-blur-xl bg-white/10 border border-white/20 rounded-2xl shadow-2xl mb-8 overflow-hidden">
                <div class="p-6">
                    <!-- Main Filter Row -->
                    <!-- First Row: Search and Show Per Page -->
                    <div class="grid grid-cols-1 lg:grid-cols-7 gap-4 items-end mb-4">
                        <!-- Search - Takes more space -->
                        <div class="lg:col-span-5">
                            <label class="block text-sm font-medium text-white/90 mb-2">🔍 {{ __('invoices_demo_traduccion_search') }}</label>
                            <div class="relative">
                                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-5 h-5 text-white/60"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <input type="text" x-model="search" @input.debounce.500ms="searchInvoices()"
                                    placeholder="{{ __('invoices_demo_traduccion_search') }} invoices..."
                                    class="glass-input-filter w-full h-11 pl-10 pr-4 text-sm rounded-lg backdrop-blur-md bg-white/10 border border-white/30 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-transparent transition-all duration-200">
                            </div>
                        </div>

                        <!-- Items Per Page -->
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-white/90 mb-2">📄 {{ __('invoices_demo_traduccion_show') }}</label>
                            <select x-model="perPage" @change="changePerPage()"
                                class="glass-input-filter w-full h-11 px-3 text-sm rounded-lg backdrop-blur-md bg-white/10 border border-white/30 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-transparent transition-all duration-200">
                                <option value="10" class="bg-gray-800 text-white">10</option>
                                <option value="25" class="bg-gray-800 text-white">25</option>
                                <option value="50" class="bg-gray-800 text-white">50</option>
                                <option value="100" class="bg-gray-800 text-white">100</option>
                            </select>
                        </div>
                    </div>

                    <!-- Second Row: All other controls -->
                    <div class="grid grid-cols-1 lg:grid-cols-5 gap-4 items-end">
                        <!-- Status Filter -->
                        <div class="lg:col-span-1">
                            <label class="block text-sm font-medium text-white/90 mb-2">📋 {{ __('invoices_demo_traduccion_status') }}</label>
                            <select x-model="statusFilter" @change="filterByStatus()"
                                class="glass-input-filter w-full h-11 px-3 text-sm rounded-lg backdrop-blur-md bg-white/10 border border-white/30 text-white focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-transparent transition-all duration-200">
                                 <option value="print_pdf" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_print_pdf') }}</option>
                                <option value="" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_all_statuses') }}</option>
                    <option value="draft" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_draft') }}</option>
                    <option value="sent" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_sent') }}</option>
                    <option value="paid" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_paid') }}</option>
                    <option value="cancelled" class="bg-gray-800 text-white">{{ __('invoices_demo_traduccion_cancelled') }}</option>
                   
                            </select>
                        </div>

                        <!-- Advanced Filters Toggle -->
                        <div class="lg:col-span-1">
                            <button @click="showAdvancedFilters = !showAdvancedFilters"
                                class="w-full h-11 px-4 glass-button-filter backdrop-blur-md bg-gradient-to-r from-purple-500/30 to-blue-500/30 border border-white/30 text-white text-sm font-medium rounded-lg hover:from-purple-600/40 hover:to-blue-600/40 transition-all duration-200 flex items-center justify-center relative overflow-hidden group"
                                :class="{ 'ring-2 ring-purple-400/50': showAdvancedFilters }">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-purple-600/20 to-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                </div>
                                <svg class="w-4 h-4 mr-2 relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4">
                                    </path>
                                </svg>
                                <span x-text="showAdvancedFilters ? '{{ __('invoices_demo_traduccion_hide_filters') }}' : '{{ __('invoices_demo_traduccion_more_filters') }}'"
                                    class="relative z-10"></span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 relative z-10"
                                    :class="{ 'rotate-180': showAdvancedFilters }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Export Dropdown -->
                        <div class="lg:col-span-1 relative" x-data="{ exportOpen: false, buttonRect: null }"
                             x-init="$watch('exportOpen', value => {
                                 if (value) {
                                     buttonRect = $el.querySelector('button').getBoundingClientRect();
                                 }
                             })"
                             @click.away="exportOpen = false">
                            <button @click="exportOpen = !exportOpen" 
                                class="w-full h-11 px-4 glass-button-filter backdrop-blur-md bg-gradient-to-r from-orange-500/30 to-yellow-500/30 border border-white/30 text-white text-sm font-medium rounded-lg hover:from-orange-600/40 hover:to-yellow-600/40 transition-all duration-200 flex items-center justify-center relative overflow-hidden group">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-orange-600/20 to-yellow-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                </div>
                                <svg class="w-4 h-4 mr-2 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <span class="relative z-10">{{ __('invoices_demo_traduccion_export') }}</span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 relative z-10"
                                    :class="{ 'rotate-180': exportOpen }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            
                            <!-- Teleported Export Dropdown Menu -->
                            <template x-teleport="body">
                                <div x-show="exportOpen" 
                                     class="fixed backdrop-blur-md bg-gray-800/90 border border-white/20 rounded-lg shadow-xl z-[9999] overflow-hidden"
                                     x-bind:style="buttonRect ? `top: ${buttonRect.bottom + 8}px; left: ${buttonRect.left}px; width: ${buttonRect.width}px;` : ''"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95 transform -translate-y-2"
                                     x-transition:enter-end="opacity-100 scale-100 transform translate-y-0"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100 transform translate-y-0"
                                     x-transition:leave-end="opacity-0 scale-95 transform -translate-y-2"
                                     @click.stop>
                                    <div class="py-2">
                                        <button @click="exportToExcel(); exportOpen = false" 
                                                data-export="excel"
                                                class="w-full text-left px-4 py-3 text-white hover:bg-white/10 transition-colors duration-200 flex items-center group">
                                            <svg class="w-5 h-5 text-green-400 mr-3 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                                            </svg>
                                            <div>
                                                <div class="font-medium">{{ __('invoices_demo_traduccion_export_excel') }}</div>
                                                <div class="text-xs text-gray-400">{{ __('invoices_demo_traduccion_download_xlsx') }}</div>
                                            </div>
                                        </button>
                                        <button @click="exportToPdf(); exportOpen = false" 
                                                data-export="pdf"
                                                class="w-full text-left px-4 py-3 text-white hover:bg-white/10 transition-colors duration-200 flex items-center group">
                                            <svg class="w-5 h-5 text-red-400 mr-3 group-hover:scale-110 transition-transform duration-200" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M14,2H6A2,2 0 0,0 4,4V20A2,2 0 0,0 6,22H18A2,2 0 0,0 20,20V8L14,2M18,20H6V4H13V9H18V20Z" />
                                            </svg>
                                            <div>
                                                <div class="font-medium">{{ __('invoices_demo_traduccion_export_pdf') }}</div>
                                                <div class="text-xs text-gray-400">{{ __('invoices_demo_traduccion_download_pdf') }}</div>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Create Button -->
                        <div class="lg:col-span-2">
                            <button @click="openCreateModal()"
                                class="w-full h-11 px-4 glass-button-filter backdrop-blur-md bg-gradient-to-r from-green-500/30 to-emerald-500/30 border border-white/30 text-white text-sm font-medium rounded-lg hover:from-green-600/40 hover:to-emerald-600/40 transition-all duration-200 flex items-center justify-center relative overflow-hidden group">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-green-600/20 to-emerald-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                </div>
                                <svg class="w-4 h-4 mr-2 relative z-10" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="relative z-10">{{ __('invoices_demo_traduccion_new_invoice') }}</span>
                            </button>
                        </div>
                    </div>

                    <!-- Advanced Filters - Collapsible -->
                    <div x-show="showAdvancedFilters" x-transition:enter="transition ease-out duration-300"
                        x-transition:enter-start="opacity-0 transform -translate-y-4"
                        x-transition:enter-end="opacity-100 transform translate-y-0"
                        x-transition:leave="transition ease-in duration-200"
                        x-transition:leave-start="opacity-100 transform translate-y-0"
                        x-transition:leave-end="opacity-0 transform -translate-y-4"
                        class="mt-6 pt-6 border-t border-white/20">

                        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 items-end">
                            <!-- Date Range -->
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-medium text-white/90 mb-2">📅 {{ __('invoices_demo_traduccion_date_range') }}</label>
                                <input type="text" id="dateRangePicker" x-model="dateRangeDisplay"
                                    placeholder="{{ __('invoices_demo_traduccion_select_date_range') }}..."
                                    class="glass-input-filter w-full h-11 px-4 text-sm rounded-lg backdrop-blur-md bg-white/10 border border-white/30 text-white placeholder-white/60 focus:outline-none focus:ring-2 focus:ring-purple-400/50 focus:border-transparent transition-all duration-200 cursor-pointer"
                                    readonly>
                            </div>

                            <!-- Quick Date Filters -->
                            <div class="lg:col-span-5">
                                <label class="block text-sm font-medium text-white/90 mb-2">⚡ {{ __('invoices_demo_traduccion_quick_filters') }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <button @click="setDateRange('today')"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-white/10 border border-white/20 text-white/80 rounded-md hover:bg-purple-500/30 hover:text-white transition-colors duration-200"
                                        :class="{ 'bg-purple-500/40 text-white border-purple-400/50': activeQuickFilter === 'today' }">
                                        {{ __('invoices_demo_traduccion_today') }}
                                    </button>
                                    <button @click="setDateRange('last7days')"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-white/10 border border-white/20 text-white/80 rounded-md hover:bg-purple-500/30 hover:text-white transition-colors duration-200"
                                        :class="{ 'bg-purple-500/40 text-white border-purple-400/50': activeQuickFilter === 'last7days' }">
                                        {{ __('invoices_demo_traduccion_7_days') }}
                                    </button>
                                    <button @click="setDateRange('last30days')"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-white/10 border border-white/20 text-white/80 rounded-md hover:bg-purple-500/30 hover:text-white transition-colors duration-200"
                                        :class="{ 'bg-purple-500/40 text-white border-purple-400/50': activeQuickFilter === 'last30days' }">
                                        {{ __('invoices_demo_traduccion_30_days') }}
                                    </button>
                                    <button @click="setDateRange('thisMonth')"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-white/10 border border-white/20 text-white/80 rounded-md hover:bg-purple-500/30 hover:text-white transition-colors duration-200"
                                        :class="{ 'bg-purple-500/40 text-white border-purple-400/50': activeQuickFilter === 'thisMonth' }">
                                        {{ __('invoices_demo_traduccion_this_month') }}
                                    </button>
                                    <button @click="setDateRange('thisYear')"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-white/10 border border-white/20 text-white/80 rounded-md hover:bg-purple-500/30 hover:text-white transition-colors duration-200"
                                        :class="{ 'bg-purple-500/40 text-white border-purple-400/50': activeQuickFilter === 'thisYear' }">
                                        {{ __('invoices_demo_traduccion_this_year') }}
                                    </button>
                                    <!-- Clear Filters Button -->
                                    <button @click="clearAllFilters()"
                                        class="px-3 py-2 text-xs backdrop-blur-md bg-red-500/25 border border-red-400/40 text-red-200 rounded-md hover:bg-red-500/40 hover:text-white hover:border-red-300/60 transition-all duration-200 hover:scale-105 shadow-sm hover:shadow-md"
                                        title="Clear all filters and reset"
                                        :class="{ 'animate-pulse': hasActiveFilters() }">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            <span class="hidden sm:inline text-xs">{{ __('invoices_demo_traduccion_clear') }} </span>
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <!-- Show Deleted Toggle Switch -->
                            <div class="lg:col-span-2 flex items-end justify-start">
                                <!-- Toggle Switch for Show Deleted -->
                                <div class="flex items-center space-x-3">
                                    <label class="text-sm text-white/90 font-medium">🗑️ {{ __('invoices_demo_traduccion_show_deleted') }}</label>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" x-model="showDeleted" @change="toggleDeleted()"
                                            class="sr-only peer">
                                        <div
                                            class="relative w-11 h-6 bg-white/20 backdrop-blur-md border border-white/30 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-purple-300/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-gradient-to-r peer-checked:from-purple-500 peer-checked:to-blue-500">
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Active Filters Indicator -->
                        <div x-show="hasActiveFilters()"
                            class="mt-4 flex items-center gap-2 text-sm text-white/70 bg-white/5 backdrop-blur-md rounded-lg px-4 py-2 border border-white/10">
                            <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.707A1 1 0 013 7V4z">
                                </path>
                            </svg>
                            <span>Active filters applied</span>
                            <span
                                class="inline-flex items-center px-2 py-1 text-xs bg-purple-500/30 text-purple-200 rounded-full border border-purple-400/30"
                                x-text="getActiveFiltersCount()"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table Section -->
            <div class="bg-gray-900 rounded-2xl shadow-2xl border border-gray-700 overflow-visible">
                <!-- Loading State -->
                <div x-show="loading" class="flex items-center justify-center py-12 bg-gray-900">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-purple-400"></div>
                    <span class="ml-3 text-gray-300">{{ __('invoices_demo_traduccion_loading_invoices') }}</span>
                </div>

                <!-- Table -->
                <div x-show="!loading" class="table-container overflow-visible">
                    <div class="overflow-x-auto rounded-lg overflow-y-visible">
                        <table class="min-w-full text-sm text-left text-gray-300 bg-gray-800/50 divide-y divide-gray-700">
                            <thead class="bg-gradient-to-r from-gray-800 to-gray-700">
                                <tr>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('nro') }}
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-700 transition-colors duration-200"
                                        @click="sortInvoices('invoice_number')"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('invoice_number') }}
                                        <svg x-show="sortBy === 'invoice_number'"
                                            class="inline w-4 h-4 ml-1 text-gray-300"
                                            :class="sortOrder === 'asc' ? 'transform rotate-180' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-700 transition-colors duration-200"
                                        @click="sortInvoices('bill_to_name')"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('bill_to') }}
                                        <svg x-show="sortBy === 'bill_to_name'" class="inline w-4 h-4 ml-1 text-gray-300"
                                            :class="sortOrder === 'asc' ? 'transform rotate-180' : ''" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-700 transition-colors duration-200"
                                        @click="sortInvoices('balance_due')"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('balance_due') }}
                                        <svg x-show="sortBy === 'balance_due'" class="inline w-4 h-4 ml-1 text-gray-300"
                                            :class="sortOrder === 'asc' ? 'transform rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('Status') }}</th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-700 transition-colors duration-200"
                                        @click="sortInvoices('invoice_date')"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('invoice_date') }}
                                        <svg x-show="sortBy === 'invoice_date'" class="inline w-4 h-4 ml-1 text-gray-300"
                                            :class="sortOrder === 'asc' ? 'transform rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider cursor-pointer hover:bg-gray-700 transition-colors duration-200"
                                        @click="sortInvoices('date_of_loss')"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('date_of_loss') }}
                                        <svg x-show="sortBy === 'date_of_loss'" class="inline w-4 h-4 ml-1 text-gray-300"
                                            :class="sortOrder === 'asc' ? 'transform rotate-180' : ''"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </th>
                                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-200 uppercase tracking-wider"
                                        style="text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);">
                                        {{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700">
                                <template x-for="(invoice, index) in invoices" :key="invoice.uuid">
                                    <tr class="transition-colors duration-200 hover:bg-slate-700"
                                        :class="[
                                            invoice.deleted_at ? 'deleted-item' : '',
                                            index % 2 === 0 ? 'bg-gray-800 bg-opacity-30' : 'bg-transparent'
                                        ]">
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium"
                                                :class="invoice.deleted_at ? 'text-red-200' : 'text-gray-100'"
                                                x-text="((currentPage - 1) * perPage) + index + 1">
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="flex items-center justify-center">
                                                <div class="text-sm font-medium"
                                                    :class="invoice.deleted_at ? 'text-red-200 line-through' : 'text-gray-100'"
                                                    x-text="invoice.invoice_number">
                                                </div>
                                                <div x-show="invoice.pdf_url" class="ml-2 text-green-400"
                                                    title="PDF Available">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                        </path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium"
                                                :class="invoice.deleted_at ? 'text-red-200' : 'text-gray-100'"
                                                x-text="invoice.bill_to_name">
                                            </div>
                                            <div class="text-sm"
                                                :class="invoice.deleted_at ? 'text-red-300' : 'text-gray-400'"
                                                x-text="invoice.bill_to_phone_formatted || invoice.bill_to_phone"></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <div class="text-sm font-medium"
                                                :class="invoice.deleted_at ? 'text-red-200' : 'text-gray-100'"
                                                x-text="formatCurrency(invoice.balance_due)"></div>
                                            <div class="text-xs"
                                                :class="invoice.deleted_at ? 'text-red-300' : 'text-gray-400'">Subtotal:
                                                <span x-text="formatCurrency(invoice.subtotal)"></span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="inline-flex px-3 py-1 text-xs font-semibold rounded-full"
                                                :class="[
                                                    getStatusBadgeClass(invoice.status),
                                                    invoice.deleted_at ? 'opacity-60' : ''
                                                ]"
                                                x-text="(invoice.deleted_at ? 'DELETED - ' : '') + (invoice.status === 'print_pdf' ? 'Print PDF' : (invoice.status.replace('_', ' ').charAt(0).toUpperCase() + invoice.status.replace('_', ' ').slice(1)))"></span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm"
                                            :class="invoice.deleted_at ? 'text-red-200' : 'text-gray-100'"
                                            x-text="formatDate(invoice.invoice_date)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm"
                                            :class="invoice.deleted_at ? 'text-red-200' : 'text-gray-100'"
                                            x-text="formatDate(invoice.date_of_loss)"></td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <div class="flex items-center justify-center space-x-2">
                                                <template x-if="!invoice.deleted_at">
                                                    <div class="flex space-x-2">
                                                        <!-- PDF Actions -->
                                                        <div class="relative" x-data="{ showPdfMenu: false, buttonRect: null }" 
                                                             x-init="$watch('showPdfMenu', value => {
                                                                 if (value) {
                                                                     buttonRect = $el.querySelector('button').getBoundingClientRect();
                                                                 }
                                                             })"
                                                             @click.away="showPdfMenu = false">
                                                            <button @click="showPdfMenu = !showPdfMenu"
                                                                class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                                title="PDF Actions">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                                                                    </path>
                                                                </svg>
                                                            </button>
                                                            
                                                            <!-- Teleported Dropdown -->
                                                            <template x-teleport="body">
                                                                <div x-show="showPdfMenu" 
                                                                     class="fixed w-48 bg-gray-800 rounded-md shadow-lg z-[9999] border border-gray-600"
                                                                     x-bind:style="buttonRect ? `top: ${buttonRect.bottom + 8}px; left: ${buttonRect.right - 192}px;` : ''"
                                                                     x-transition:enter="transition ease-out duration-100"
                                                                     x-transition:enter-start="transform opacity-0 scale-95"
                                                                     x-transition:enter-end="transform opacity-100 scale-100"
                                                                     x-transition:leave="transition ease-in duration-75"
                                                                     x-transition:leave-start="transform opacity-100 scale-100"
                                                                     x-transition:leave-end="transform opacity-0 scale-95"
                                                                     @click.stop>
                                                                    <div class="py-1">
                                                                        <a :href="window.invoiceDemoManager.getPdfViewUrl(invoice
                                                                            .uuid)"
                                                                            target="_blank"
                                                                            class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">
                                                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round" stroke-width="2"
                                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round" stroke-width="2"
                                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                                                </path>
                                                                            </svg>
                                                                            View PDF
                                                                        </a>
                                                                        <a :href="window.invoiceDemoManager.getPdfDownloadUrl(invoice
                                                                            .uuid)"
                                                                            class="flex items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white">
                                                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round" stroke-width="2"
                                                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4">
                                                                                </path>
                                                                            </svg>
                                                                            Download PDF
                                                                        </a>
                                                                        <button
                                                                            @click="generatePdf(invoice.uuid); showPdfMenu = false"
                                                                            class="flex w-full items-center px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 hover:text-white"
                                                                            :class="{ 'opacity-50 cursor-not-allowed': pdfGenerating }"
                                                                            :disabled="pdfGenerating">
                                                                            <svg class="w-4 h-4 mr-2" fill="none"
                                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round"
                                                                                    stroke-linejoin="round" stroke-width="2"
                                                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                                                </path>
                                                                            </svg>
                                                                            <span
                                                                                x-text="pdfGenerating ? 'Generating...' : 'Regenerate PDF'"></span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                        <button @click="openEditModal(invoice)"
                                                            class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                            title="Edit Invoice">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                        <button @click="deleteInvoice(invoice)"
                                                            class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                            title="Delete Invoice">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </template>
                                                <template x-if="invoice.deleted_at">
                                                    <button @click="restoreInvoice(invoice)"
                                                        class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                                                        title="Restore Invoice">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- Empty State -->
                        <div x-show="invoices.length === 0" class="text-center py-12 bg-gray-900">
                            <svg class="mx-auto h-12 w-12 text-gray-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-200">No invoices found</h3>
                            <p class="mt-1 text-sm text-gray-400">Get started by creating a new invoice.</p>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div x-show="totalPages > 1" class="bg-gray-800 px-6 py-4 border-t border-gray-700">
                        <div class="flex items-center justify-between">
                            <div class="text-sm text-gray-300">
                                Showing <span x-text="((currentPage - 1) * perPage) + 1"></span> to
                                <span x-text="Math.min(currentPage * perPage, total)"></span> of
                                <span x-text="total"></span> results
                            </div>
                            <div class="flex space-x-2">
                                <button @click="goToPage(currentPage - 1)" :disabled="currentPage === 1"
                                    class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    Previous
                                </button>
                                <template
                                    x-for="page in Array.from({length: Math.min(5, totalPages)}, (_, i) => Math.max(1, currentPage - 2) + i).filter(p => p <= totalPages)"
                                    :key="page">
                                    <button @click="goToPage(page)"
                                        :class="page === currentPage ? 'bg-purple-600 text-white border-purple-600' :
                                            'bg-gray-700 text-gray-300 hover:bg-gray-600 border-gray-600'"
                                        class="px-3 py-2 text-sm font-medium border rounded-lg transition-colors duration-200"
                                        x-text="page"></button>
                                </template>
                                <button @click="goToPage(currentPage + 1)" :disabled="currentPage === totalPages"
                                    class="px-3 py-2 text-sm font-medium text-gray-300 bg-gray-700 border border-gray-600 rounded-lg hover:bg-gray-600 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                                    Next
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Modal -->
            @include('invoice-demos.partials.invoice-modal')

            <!-- Success/Error Messages -->
            <div x-show="message" x-transition class="fixed top-4 right-4 z-50">
                <div class="bg-white border-l-4 p-4 rounded-lg shadow-lg"
                    :class="messageType === 'success' ? 'border-green-500' : 'border-red-500'">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg x-show="messageType === 'success'" class="h-5 w-5 text-green-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                    clip-rule="evenodd"></path>
                            </svg>
                            <svg x-show="messageType === 'error'" class="h-5 w-5 text-red-400" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm" :class="messageType === 'success' ? 'text-green-700' : 'text-red-700'"
                                x-text="message"></p>
                        </div>
                        <div class="ml-auto pl-3">
                            <button @click="message = ''" class="text-gray-400 hover:text-gray-600">
                                <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @push('scripts')
            <!-- Translations for JavaScript -->
            <script>
                window.translations = {
                    invoices_demo_traduccion_success: @json(__('invoices_demo_traduccion_success')),
                    invoices_demo_traduccion_error: @json(__('invoices_demo_traduccion_error')),
                    invoices_demo_traduccion_are_you_sure: @json(__('invoices_demo_traduccion_are_you_sure')),
                    invoices_demo_traduccion_delete_invoice_confirm: @json(__('invoices_demo_traduccion_delete_invoice_confirm')),
                    invoices_demo_traduccion_yes_delete: @json(__('invoices_demo_traduccion_yes_delete')),
                    invoices_demo_traduccion_cancel: @json(__('invoices_demo_traduccion_cancel')),
                    invoices_demo_traduccion_deleted: @json(__('invoices_demo_traduccion_deleted')),
                    invoices_demo_traduccion_invoice_deleted_successfully: @json(__('invoices_demo_traduccion_invoice_deleted_successfully')),
                    invoices_demo_traduccion_restore_invoice_question: @json(__('invoices_demo_traduccion_restore_invoice_question')),
                    invoices_demo_traduccion_restore_invoice_confirm: @json(__('invoices_demo_traduccion_restore_invoice_confirm')),
                    invoices_demo_traduccion_yes_restore: @json(__('invoices_demo_traduccion_yes_restore')),
                    invoices_demo_traduccion_restored: @json(__('invoices_demo_traduccion_restored')),
                    invoices_demo_traduccion_invoice_restored_successfully: @json(__('invoices_demo_traduccion_invoice_restored_successfully')),
                    invoices_demo_traduccion_error_deleting_invoice: @json(__('invoices_demo_traduccion_error_deleting_invoice')),
                    invoices_demo_traduccion_error_restoring_invoice: @json(__('invoices_demo_traduccion_error_restoring_invoice')),
                    invoices_demo_traduccion_view_deleted_invoices: @json(__('invoices_demo_traduccion_view_deleted_invoices')),
                    invoices_demo_traduccion_showing_deleted_invoices: @json(__('invoices_demo_traduccion_showing_deleted_invoices')),
                    invoices_demo_traduccion_showing_active_invoices: @json(__('invoices_demo_traduccion_showing_active_invoices')),
                    invoices_demo_traduccion_switching_to_active_view: @json(__('invoices_demo_traduccion_switching_to_active_view')),
                    invoices_demo_traduccion_invoice_restored_active_list: @json(__('invoices_demo_traduccion_invoice_restored_active_list')),
                    invoices_demo_traduccion_exporting: @json(__('invoices_demo_traduccion_exporting')),
                    invoices_demo_traduccion_excel_export_started: @json(__('invoices_demo_traduccion_excel_export_started')),
                    invoices_demo_traduccion_pdf_export_started: @json(__('invoices_demo_traduccion_pdf_export_started')),
                    invoices_demo_traduccion_failed_export_excel: @json(__('invoices_demo_traduccion_failed_export_excel')),
                    invoices_demo_traduccion_failed_export_pdf: @json(__('invoices_demo_traduccion_failed_export_pdf')),
                    invoices_demo_traduccion_pdf_generated_successfully: @json(__('invoices_demo_traduccion_pdf_generated_successfully')),
                    invoices_demo_traduccion_failed_generate_pdf: @json(__('invoices_demo_traduccion_failed_generate_pdf')),
                    invoices_demo_traduccion_filters_cleared_successfully: @json(__('invoices_demo_traduccion_filters_cleared_successfully')),
                    invoices_demo_traduccion_select_date_range: @json(__('invoices_demo_traduccion_select_date_range')),
                    invoices_demo_traduccion_export_excel: @json(__('invoices_demo_traduccion_export_excel')),
                    invoices_demo_traduccion_export_pdf: @json(__('invoices_demo_traduccion_export_pdf'))
                };
            </script>
            <!-- Main Invoice Demo Script -->
            <script src="{{ asset('js/invoice-demos.js') }}"></script>
        @endpush
    @endsection
