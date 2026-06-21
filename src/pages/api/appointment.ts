import type { APIRoute } from 'astro';
import { type Appointment } from '../../lib/supabase';
import { appointmentSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck, getClientIP } from '../../utils/spam-detection';
import { verifyTurnstile } from '../../utils/turnstile';
import { sendEmail, getNewLeadEmailTemplate, getLeadConfirmationEmailTemplate } from '../../utils/email';

const API_BASE_URL = (
  import.meta.env.PUBLIC_API_URL ||
  'https://backend-aquashield-restoration-production.up.railway.app/api/v1'
).replace(/\/$/, '');

export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();

    // Step 1: Zod Schema Validation
    const validationResult = appointmentSchema.safeParse(body);

    if (!validationResult.success) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Validation errors',
          errors: formatZodErrors(validationResult.error),
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    const validatedData = validationResult.data;

    // Step 2: Spam Check
    const spamCheck = await performSpamCheck({
      request,
      honeypot: '',
      message: validatedData.message || '',
      email: validatedData.email || '',
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: validatedData.phone,
      formType: 'appointment',
    });

    if (spamCheck.isSpam) {
      console.warn('Spam detected in appointment form:', {
        reasons: spamCheck.reasons,
        score: spamCheck.totalScore,
      });
      return new Response(
        JSON.stringify({
          success: false,
          message:
            'Your submission has been flagged. Please contact us directly by phone if this is an error.',
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 3: Verify Cloudflare Turnstile
    const ipAddress = getClientIP(request);

    const turnstileResult = await verifyTurnstile(
      validatedData['cf-turnstile-response'],
      ipAddress === 'unknown' ? undefined : ipAddress
    );

    if (!turnstileResult.success) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'CAPTCHA verification failed. Please try again.',
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 3: Format phone number to E.164
    const formattedPhone = validatedData.phone.replace(/[^0-9]/g, '');
    const finalPhone =
      formattedPhone.length === 10 ? `+1${formattedPhone}` : `+${formattedPhone}`;

    // Keep insurance info in the message since the backend has no dedicated field
    const insuranceNote = `Insurance property: ${validatedData.insurance_property === 'yes' ? 'Yes' : 'No'}`;
    const finalMessage = validatedData.message
      ? `${validatedData.message}\n\n${insuranceNote}`
      : insuranceNote;

    // Local object used only to render the notification emails
    const appointmentData: Partial<Appointment> = {
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: finalPhone,
      email: validatedData.email || null,
      address: validatedData.address,
      address_2: validatedData.address_2 || null,
      city: validatedData.city,
      state: validatedData.state,
      zipcode: validatedData.zipcode,
      country: validatedData.country || 'USA',
      insurance_property: validatedData.insurance_property === 'yes',
      message: validatedData.message || null,
      sms_consent: validatedData.sms_consent ?? false,
      registration_date: new Date().toISOString(),
      status_lead: 'New',
      lead_source: 'Website',
    };

    // Step 4: Send to backend API (POST /public/appointments)
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
        }),
      });
    } catch (fetchError) {
      console.error('Network error contacting backend (appointment):', fetchError);
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

      console.error('Backend error creating appointment:', apiResponse.status);
      return new Response(
        JSON.stringify({ success: false, message }),
        { status: apiResponse.status, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 5: Send emails
    try {
      const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration USA';
      const adminEmail = import.meta.env.ADMIN_EMAIL || 'admin@aquashieldrestorationusa.com';

      // Email 1: New lead alert → admin
      const newLeadHtml = getNewLeadEmailTemplate(appointmentData as Appointment);
      await sendEmail(
        adminEmail,
        `🎉 New Lead Alert! - ${companyName}`,
        newLeadHtml
      );

      // Email 2: Confirmation → customer (only if they provided an email)
      if (appointmentData.email) {
        const confirmationHtml = getLeadConfirmationEmailTemplate(appointmentData as Appointment);
        await sendEmail(
          appointmentData.email,
          `✅ We Received Your Information! - ${companyName}`,
          confirmationHtml
        );
      }
    } catch (emailError) {
      console.error('Email error in appointment API:', emailError);
      // Don't fail the request if email fails
    }

    // Step 6: Success
    return new Response(
      JSON.stringify({
        success: true,
        message:
          'Thank you! We will contact you shortly to schedule your free inspection.',
      }),
      { status: 200, headers: { 'Content-Type': 'application/json' } }
    );
  } catch (error) {
    console.error('Error in appointment API:', error);
    return new Response(
      JSON.stringify({
        success: false,
        message: 'An unexpected error occurred. Please try again later.',
      }),
      { status: 500, headers: { 'Content-Type': 'application/json' } }
    );
  }
};
