@extends('layouts.main')

@section('title', __('privacy_policy_page_title', ['company' => 'V General Contractors']))
@section('meta_description', __('privacy_policy_meta_description', ['company' => 'V General Contractors']))

@php
    use App\Helpers\PhoneHelper;
@endphp

@section('content')
    <!-- Hero Section -->
    <section class="relative py-24 bg-gray-900 text-white">
        <div class="absolute inset-0">
            <div class="absolute inset-0 bg-black opacity-60"></div>
        </div>
        <div class="relative container mx-auto px-4">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">{!! __('privacy_policy_page') !!}</h1>
            <p class="text-xl text-gray-300">{!! __('privacy_last_updated_january_23_2025') !!}</p>
        </div>
    </section>

    <!-- Breadcrumb -->
    <nav class="bg-gray-100 py-3">
        <div class="container mx-auto px-4">
            <div class="flex items-center space-x-2 text-gray-500">
                <a href="{{ route('home') }}" class="hover:text-yellow-500">{{ __('home') }}</a>
                <span>/</span>
                <span class="text-yellow-500">{!! __('privacy_policy_page') !!}</span>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <section class="py-12 bg-white">
        <div class="container mx-auto px-4 max-w-4xl">
            <div class="prose max-w-none">
                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_general_information') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_data_controller_identification') !!}</h3>
                <p class="mb-6">
                    {!! __('privacy_company_responsibility', [
                        'company' => 'VG General Contractors',
                        'address' => '1302 Waugh Dr., No. 810, Houston, Texas 77019',
                    ]) !!}
                </p>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_contact_section') !!}</h3>
                <p class="mb-6">
                    {!! __('privacy_contact_dpo') !!}<br>
                    {!! __('by_email') !!}: <a href="mailto:info@vgeneralcontractors.com"
                        class="text-yellow-600 hover:text-yellow-700 no-underline">info@vgeneralcontractors.com</a><br>
                    {!! __('by_phone') !!}: <a href="tel:{{ $companyData->phone }}"
                        class="text-yellow-600 hover:text-yellow-700 no-underline">{{ PhoneHelper::format($companyData->phone) }}</a>
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_data_collected') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_types_of_data') !!}</h3>
                <p class="mb-4">{!! __('privacy_company_may_collect') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_full_name_owner') !!}</li>
                    <li>{!! __('privacy_property_address') !!}</li>
                    <li>{!! __('privacy_phone_number') !!}</li>
                    <li>{!! __('privacy_email_address') !!}</li>
                    <li>{!! __('privacy_roof_structure_info') !!}</li>
                    <li>{!! __('privacy_insurance_details') !!}</li>
                    <li>{!! __('privacy_property_photographs') !!}</li>
                </ul>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_collection_methods') !!}</h3>
                <p class="mb-4">{!! __('privacy_data_collected_through') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_roof_inspection_forms') !!}</li>
                    <li>{!! __('privacy_online_quote_requests') !!}</li>
                    <li>{!! __('privacy_onsite_technical_visits') !!}</li>
                    <li>{!! __('privacy_telephone_communications') !!}</li>
                    <li>{!! __('privacy_emails') !!}</li>
                    <li>{!! __('privacy_web_contact_form') !!}</li>
                </ul>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_sms_optin_data') !!}</h3>
                <p class="mb-6">
                    {!! __('privacy_sms_consent_collection') !!}<br><br>
                    {!! __('privacy_sms_no_sharing_assurance') !!}<br><br>
                    {!! __('privacy_sms_service_providers') !!}
                    <br><br>
                    {!! __('privacy_sms_no_sharing_bold') !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_purposes_processing') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_main_objectives') !!}</h3>
                <p class="mb-4">{!! __('privacy_data_used_for') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_scheduling_inspections') !!}</li>
                    <li>{!! __('privacy_preparing_quotes') !!}</li>
                    <li>{!! __('privacy_managing_repair_process') !!}</li>
                    <li>{!! __('privacy_warranty_followup') !!}</li>
                    <li>{!! __('privacy_roofing_communications') !!}</li>
                    <li>{!! __('privacy_admin_accounting') !!}</li>
                    <li>{!! __('privacy_sms_consent_customers') !!}</li>
                </ul>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_legal_basis') !!}</h3>
                <p class="mb-4">{!! __('privacy_processing_legal_bases') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_consent_data_subject') !!}</li>
                    <li>{!! __('privacy_service_contract_performance') !!}</li>
                    <li>{!! __('privacy_legal_obligations_compliance') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_data_subject_rights') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_arco_rights') !!}</h3>
                <p class="mb-4">{!! __('privacy_you_have_right_to') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_access_personal_data') !!}</li>
                    <li>{!! __('privacy_rectify_inaccurate_info') !!}</li>
                    <li>{!! __('privacy_cancel_your_data') !!}</li>
                    <li>{!! __('privacy_object_to_processing') !!}</li>
                    <li>{!! __('privacy_limit_data_use') !!}</li>
                </ul>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_exercising_rights') !!}</h3>
                <p class="mb-4">{!! __('privacy_to_exercise_rights') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_send_written_request') !!}</li>
                    <li>{!! __('privacy_attach_identity_docs') !!}</li>
                    <li>{!! __('privacy_describe_precise_right') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_security_measures') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_data_protection') !!}</h3>
                <p class="mb-4">{!! __('privacy_implement_measures') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_encryption_inspection_info') !!}</li>
                    <li>{!! __('privacy_restricted_access_data') !!}</li>
                    <li>{!! __('privacy_secure_backups') !!}</li>
                    <li>{!! __('privacy_confidentiality_protocols') !!}</li>
                    <li>{!! __('privacy_secure_photo_management') !!}</li>
                    <li>{!! __('privacy_sms_security_measures') !!}</li>
                </ul>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_data_transfers') !!}</h3>
                <p class="mb-6">
                    {!! __('privacy_no_sell_transfer') !!}
                </p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_insurance_companies') !!}</li>
                    <li>{!! __('privacy_material_suppliers') !!}</li>
                    <li>{!! __('privacy_competent_authorities') !!}</li>
                    <li>{!! __('privacy_sms_service_providers_transfer') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_cookies_tracking') !!}</h2>

                <h3 class="text-xl font-semibold mb-4">{!! __('privacy_use_of_cookies') !!}</h3>
                <p class="mb-4">{!! __('privacy_website_uses_cookies') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_improve_browsing') !!}</li>
                    <li>{!! __('privacy_analyze_site_traffic') !!}</li>
                    <li>{!! __('privacy_personalize_quote_content') !!}</li>
                    <li>{!! __('privacy_manage_contact_forms') !!}</li>
                </ul>
                <p class="mb-6">{!! __('privacy_configure_browser_reject') !!}</p>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_data_retention') !!}</h2>
                <p class="mb-4">{!! __('privacy_retain_data') !!}</p>
                <ul class="list-disc pl-6 mb-6">
                    <li>{!! __('privacy_contact_data_5_years') !!}</li>
                    <li>{!! __('privacy_inspection_reports_10_years') !!}</li>
                    <li>{!! __('privacy_billing_data_accounting') !!}</li>
                    <li>{!! __('privacy_sms_data_consent_valid') !!}</li>
                </ul>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_policy_modifications') !!}</h2>
                <p class="mb-6">
                    {!! __('privacy_reserve_right_modify') !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_consent') !!}</h2>
                <p class="mb-6">
                    {!! __('privacy_by_requesting_services') !!}
                </p>

                <h2 class="text-2xl font-bold mb-6">{!! __('privacy_applicable_legislation') !!}</h2>
                <p class="mb-6">
                    {!! __('privacy_governed_by_laws') !!}
                </p>

                <div class="text-sm text-gray-600 mt-12 pt-6 border-t">
                    <p>{!! __('privacy_last_updated_january_23_2025') !!}</p>
                    <p>{!! __('privacy_version_1_1') !!}</p>
                </div>
            </div>
        </div>
    </section>
@endsection
