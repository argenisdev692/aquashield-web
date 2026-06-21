import type { APIRoute } from 'astro';
import { type Appointment } from '../../lib/supabase';
import { sendEmail, getNewLeadEmailTemplate } from '../../utils/email';
import { facebookLeadSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck } from '../../utils/spam-detection';
import { verifyTurnstile } from '../../utils/turnstile';

const API_BASE_URL = (
  import.meta.env.PUBLIC_API_URL ||
  'https://backend-aquashield-restoration-production.up.railway.app/api/v1'
).replace(/\/$/, '');


export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    
    // Step 1: Zod Schema Validation
    const validationResult = facebookLeadSchema.safeParse(body);
    
    if (!validationResult.success) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Validation errors',
          errors: formatZodErrors(validationResult.error)
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    const validatedData = validationResult.data;
    
    // Step 2: Comprehensive Spam Check
    const spamCheck = await performSpamCheck({
      request,
      honeypot: validatedData.website,
      message: validatedData.message,
      email: validatedData.email,
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: validatedData.phone,
      address: validatedData.address,
      formType: 'facebook_lead'
    });

    if (spamCheck.isSpam) {
      console.warn('Spam detected in Facebook lead form:', {
        reasons: spamCheck.reasons,
        score: spamCheck.totalScore,
        ip: request.headers.get('x-forwarded-for') || request.headers.get('x-real-ip')
      });

      return new Response(
        JSON.stringify({
          success: false,
          message: 'Your submission has been flagged. Please contact us directly by phone if this is an error.',
          errors: { general: spamCheck.reasons }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 3: Verify Cloudflare Turnstile
    const ipAddress = request.headers.get('x-forwarded-for')?.split(',')[0] || 
                      request.headers.get('x-real-ip') || 
                      undefined;
    
    const turnstileResult = await verifyTurnstile(
      validatedData['cf-turnstile-response'],
      ipAddress
    );
    
    if (!turnstileResult.success) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'CAPTCHA verification failed. Please try again.',
          errors: { captcha: [turnstileResult.message || 'CAPTCHA verification failed'] }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 4: Format phone number
    const formattedPhone = validatedData.phone.replace(/[^0-9]/g, '');
    const finalPhone = formattedPhone.length === 10 ? `+1${formattedPhone}` : `+${formattedPhone}`;

    // Keep insurance info in the message since the backend has no dedicated field
    const insuranceNote = `Insurance property: ${validatedData.insurance_property === 'yes' ? 'Yes' : 'No'}`;
    const finalMessage = validatedData.message
      ? `${validatedData.message}\n\n${insuranceNote}`
      : insuranceNote;

    // Local object used only to render the notification email
    const appointmentData: Partial<Appointment> = {
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: finalPhone,
      email: validatedData.email,
      address: validatedData.address,
      address_2: validatedData.address_2 || null,
      city: validatedData.city,
      state: validatedData.state,
      zipcode: validatedData.zipcode,
      country: validatedData.country,
      insurance_property: validatedData.insurance_property === 'yes',
      message: validatedData.message || null,
      sms_consent: validatedData.sms_consent,
      registration_date: new Date().toISOString(),
      inspection_status: 'Pending',
      status_lead: 'New',
      latitude: validatedData.latitude,
      longitude: validatedData.longitude,
      lead_source: validatedData.lead_source,
    };

    // Step 5: Send to backend API (POST /public/appointments)
    let apiResponse: Response;
    try {
      apiResponse = await fetch(`${API_BASE_URL}/public/appointments`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          firstName: validatedData.first_name,
          lastName: validatedData.last_name,
          phone: finalPhone,
          email: validatedData.email || undefined,
          address: validatedData.address,
          address2: validatedData.address_2 || undefined,
          city: validatedData.city,
          state: validatedData.state,
          zipcode: validatedData.zipcode,
          country: validatedData.country || 'USA',
          message: finalMessage,
          smsConsent: validatedData.sms_consent ?? false,
          latitude: validatedData.latitude ?? undefined,
          longitude: validatedData.longitude ?? undefined,
        }),
      });
    } catch (fetchError) {
      console.error('Network error contacting backend (facebook-lead):', fetchError);
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Service temporarily unavailable. Please try again or call us directly.',
        }),
        { status: 502, headers: { 'Content-Type': 'application/json' } }
      );
    }

    if (!apiResponse.ok) {
      const message =
        apiResponse.status === 429
          ? 'Too many attempts. Please wait a minute and try again.'
          : apiResponse.status === 403
          ? 'Your submission has been flagged. Please contact us directly by phone if this is an error.'
          : 'We could not process your request. Please review the form and try again.';

      console.error('Backend error creating facebook-lead:', apiResponse.status);
      return new Response(
        JSON.stringify({ success: false, message }),
        { status: apiResponse.status, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 6: Send email notification to admin
    try {
      const emailHtml = getNewLeadEmailTemplate(appointmentData as Appointment);
      const adminEmail = import.meta.env.ADMIN_EMAIL;
      const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration USA';
      
      await sendEmail(
        adminEmail,
        `New Facebook Lead - Free Inspection Request - ${companyName}`,
        emailHtml
      );
    } catch (emailError) {
      console.error('Email error:', emailError);
    }

    // Step 8: Success response
    return new Response(
      JSON.stringify({
        success: true,
        message: 'Thank you for requesting a free inspection! We will contact you within 24 hours.',
      }),
      { status: 200, headers: { 'Content-Type': 'application/json' } }
    );

  } catch (error) {
    console.error('Error in facebook-lead API:', error);
    return new Response(
      JSON.stringify({
        success: false,
        message: 'An unexpected error occurred. Please try again later.',
      }),
      { status: 500, headers: { 'Content-Type': 'application/json' } }
    );
  }
};
