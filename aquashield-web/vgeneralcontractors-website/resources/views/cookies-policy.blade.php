@extends('layouts.main')

@php
    use App\Helpers\PhoneHelper;
@endphp

@section('title', __('cookies_policy_page_title', ['company' => $companyData->company_name]))
@section('meta_description', __('cookies_policy_meta_description', ['company' => $companyData->company_name]))

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>
        <div class="relative container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{!! __('cookies_policy_page') !!}</h1>
            <p class="text-xl text-gray-300">{!! __('cookies_last_updated_march_10_2025') !!}</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">{{ __('home') }}</a>
                <span>/</span>
                <span class="text-yellow-500">{!! __('cookies_policy_page') !!}</span>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="prose max-w-none">
                <p class="mb-6">
                    {!! __('cookies_policy_intro') !!}
                </p>

                <p class="mb-6">
                    {!! __('cookies_personal_info_disclaimer') !!}
                </p>

                <p class="mb-6">
                    {!! __('cookies_sensitive_info_disclaimer') !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('interpretation_and_definitions') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('interpretation') !!}</h3>
                <p class="mb-6">
                    {!! __('words_capitalized_meanings_defined') !!}
                </p>

                <h3 class="text-xl font-semibold mb-4">{!! __('definitions') !!}</h3>
                <p class="mb-6">{!! __('cookies_definitions_intro') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('cookies_definition_company', [
                        'company' => $companyData->company_name,
                        'address' => $companyData->address,
                    ]) !!}</li>
                    <li>{!! __('cookies_definition_cookies') !!}</li>
                    <li>{!! __('cookies_definition_website', ['company' => $companyData->company_name]) !!}</li>
                    <li>{!! __('cookies_definition_you') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('cookies_use_title') !!}</h2>
                <h3 class="text-xl font-semibold mb-4">{!! __('cookies_types_we_use') !!}</h3>
                <p class="mb-6">
                    {!! __('cookies_persistent_session_explanation') !!}
                </p>

                <p class="mb-6">{!! __('cookies_both_types_usage') !!}</p>

                <h4 class="text-lg font-semibold mb-4">{!! __('cookies_necessary_essential') !!}</h4>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('cookies_necessary_type') !!}</li>
                    <li>{!! __('cookies_necessary_administered') !!}</li>
                    <li>{!! __('cookies_necessary_purpose') !!}</li>
                </ul>

                <h4 class="text-lg font-semibold mb-4">{!! __('cookies_functionality') !!}</h4>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('cookies_functionality_type') !!}</li>
                    <li>{!! __('cookies_functionality_administered') !!}</li>
                    <li>{!! __('cookies_functionality_purpose') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('cookies_your_choices') !!}</h2>
                <p class="mb-6">
                    {!! __('cookies_disable_instructions') !!}
                </p>

                <p class="mb-6">
                    {!! __('cookies_inconvenience_warning') !!}
                </p>

                <p class="mb-6">
                    {!! __('cookies_browser_help_intro') !!}
                </p>

                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('cookies_chrome_help') !!} <a href="https://support.google.com/accounts/answer/32050"
                            class="text-yellow-600 hover:text-yellow-700 no-underline">https://support.google.com/accounts/answer/32050</a>
                    </li>
                    <li>{!! __('cookies_ie_help') !!} <a href="https://support.microsoft.com/kb/278835"
                            class="text-yellow-600 hover:text-yellow-700 no-underline">https://support.microsoft.com/kb/278835</a>
                    </li>
                    <li>{!! __('cookies_firefox_help') !!} <a
                            href="https://support.mozilla.org/en-US/kb/delete-cookies-remove-info-websites-stored"
                            class="text-yellow-600 hover:text-yellow-700 no-underline">https://support.mozilla.org/en-US/kb/delete-cookies-remove-info-websites-stored</a>
                    </li>
                    <li>{!! __('cookies_safari_help') !!} <a
                            href="https://support.apple.com/guide/safari/manage-cookies-and-website-data-sfri11471/mac"
                            class="text-yellow-600 hover:text-yellow-700 no-underline">https://support.apple.com/guide/safari/manage-cookies-and-website-data-sfri11471/mac</a>
                    </li>
                </ul>

                <p class="mb-6">{!! __('cookies_other_browsers') !!}</p>

                <h2 class="text-2xl font-bold mb-6">{!! __('cookies_more_info') !!}</h2>
                <p class="mb-6">{!! __('cookies_learn_more') !!} <a href="#"
                        class="text-yellow-600 hover:text-yellow-700 no-underline">{!! __('cookies_what_do_they_do') !!}</a>
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('contact_us') !!}</h2>
                <p class="mb-6">{!! __('cookies_contact_intro') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('by_email') !!}: <a href="mailto:{{ $companyData->email }}"
                            class="text-yellow-600 hover:text-yellow-700 no-underline">{{ $companyData->email }}</a></li>
                    <li>{!! __('cookies_by_phone') !!}: {{ PhoneHelper::format($companyData->phone) }}</li>
                </ul>

                <div class="text-sm text-gray-600 mt-12 pt-6 border-t">
                    <p>{!! __('cookies_last_updated_march_10_2025') !!}</p>
                    <p>{!! __('cookies_version_1_1') !!}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
