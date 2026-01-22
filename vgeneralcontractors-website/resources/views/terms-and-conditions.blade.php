@extends('layouts.main')


@section('title', __('terms_conditions_page_title', ['company' => $companyData->company_name]))
@section('meta_description', __('terms_conditions_meta_description', ['company' => $companyData->company_name]))

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>
        <div class="relative container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ __('terms_and_conditions_page') }}</h1>
            <p class="text-xl text-gray-300">{{ __('effective_date_january_24_2025') }}</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">{{ __('home') }}</a>
                <span>/</span>
                <span class="text-yellow-500">{{ __('terms_and_conditions_page') }}</span>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold mb-6">{!! __('sms_messaging_service_terms') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{{ __('consent_to_receive_text_messages') }}</h3>
                <p class="mb-6">
                    {{ __('by_checking_consent_box_agree') }}
                    <br>
                    <em>"{!! __('sms_consent_full_text', [
                        'company' => $companyData->company_name,
                        'phone' => \App\Helpers\PhoneHelper::format($companyData->phone),
                        'privacy_url' => route('privacy-policy'),
                        'terms_url' => route('terms-and-conditions'),
                    ]) !!}"</em>
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('interpretation_and_definitions') }}</h2>

                <h3 class="text-xl font-semibold mb-4">{{ __('interpretation') }}</h3>
                <p class="mb-6">
                    {{ __('words_capitalized_meanings_defined') }}
                </p>

                <h3 class="text-xl font-semibold mb-4">{{ __('definitions') }}</h3>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('definition_affiliate') !!}</li>
                    <li>{!! __('definition_country') !!}</li>
                    <li>{!! __('definition_company', ['company' => $companyData->company_name, 'address' => $companyData->address]) !!}</li>
                    <li>{!! __('definition_device') !!}</li>
                    <li>{!! __('definition_service') !!}</li>
                    <li>{!! __('definition_terms_conditions') !!}</li>
                    <li>{!! __('definition_third_party_social_media') !!}</li>
                    <li>{!! __('definition_website', ['company' => $companyData->company_name]) !!}</li>
                    <li>{!! __('definition_you') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{{ __('acknowledgment') }}</h2>
                <p class="mb-6">
                    {{ __('acknowledgment_paragraph_1') }}
                </p>
                <p class="mb-6">
                    {!! __('acknowledgment_paragraph_2') !!}
                </p>
                <p class="mb-6">
                    {{ __('acknowledgment_paragraph_3') }}
                </p>
                <p class="mb-6">
                    {!! __('acknowledgment_paragraph_4') !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('frequency_of_sms_messages') !!}</h2>
                <p class="mb-6">
                    {!! __('frequency_sms_messages_vary', ['company' => $companyData->company_name]) !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('message_data_rates_sms_service') !!}</h2>
                <p class="mb-6">
                    {!! __('message_data_rates_apply', ['company' => $companyData->company_name]) !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('opt_out_sms_messages') !!}</h2>
                <p class="mb-6">
                    {!! __('unsubscribe_sms_service', ['company' => $companyData->company_name]) !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('help_support_sms_service') !!}</h2>
                <p class="mb-6">
                    {!! __('sms_assistance_help', [
                        'phone' => \App\Helpers\PhoneHelper::format($companyData->phone),
                        'privacy_url' => route('privacy-policy'),
                        'terms_url' => route('terms-and-conditions'),
                    ]) !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('links_to_other_websites') }}</h2>
                <p class="mb-6">
                    {{ __('service_may_contain_links') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('termination') }}</h2>
                <p class="mb-6">
                    {{ __('may_terminate_suspend_access') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('limitation_of_liability') }}</h2>
                <p class="mb-6">
                    {{ __('liability_limited_amount_paid') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('as_is_as_available_disclaimer') }}</h2>
                <p class="mb-6">
                    {{ __('service_provided_as_is') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('privacy_policy_additional_terms') }}</h2>
                <p class="mb-6">
                    {{ __('privacy_important_see_policy') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('changes_to_terms_conditions') }}</h2>
                <p class="mb-6">
                    {{ __('reserve_right_modify_terms') }}
                </p>

                <h2 class="text-2xl font-bold mb-6">{{ __('contact_us') }}</h2>
                <p class="mb-6">
                    {{ __('questions_terms_conditions_contact') }}
                </p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{{ __('by_email') }}: <a href="mailto:admin@vgeneralcontractors.com"
                            class="text-yellow-600 hover:text-yellow-700">
                            admin@vgeneralcontractors.com</a></li>
                    <li>{{ __('by_visiting_contact_section') }}:
                        <a href="https://vgeneralcontractors.com/" class="text-yellow-600 hover:text-yellow-700">
                            https://vgeneralcontractors.com/</a>
                    </li>
                </ul>

                <div class="text-sm text-gray-600 mt-12 pt-6 border-t">
                    <p>{{ __('last_updated_january_24_2025') }}</p>
                    <p>{{ __('version_1_1') }}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
