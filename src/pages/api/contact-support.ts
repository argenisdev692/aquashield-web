import type { APIRoute } from 'astro';
import { type ContactSupport } from '../../lib/supabase';
import { sendEmail, getContactSupportEmailTemplate } from '../../utils/email';
import { contactSupportSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck, getClientIP } from '../../utils/spam-detection';
import { verifyTurnstile } from '../../utils/turnstile';

const API_BASE_URL = (
  import.meta.env.PUBLIC_API_URL ||
  'https://backend-aquashield-restoration-production.up.railway.app/api/v1'
).replace(/\/$/, '');


export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    
    // Step 1: Zod Schema Validation
    const validationResult = contactSupportSchema.safeParse(body);
    
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
    
    // Step 2: Comprehensive Spam Check (honeypot, content, rate limit, etc.)
    const spamCheck = await performSpamCheck({
      request,
      honeypot: validatedData.website,
      message: validatedData.message,
      email: validatedData.email,
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: validatedData.phone,
      formType: 'contact_support'
    });

    if (spamCheck.isSpam) {
      console.warn('Spam detected in contact form:', {
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
          errors: { captcha: [turnstileResult.message || 'CAPTCHA verification failed'] },
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 4: Format phone number
    const formattedPhone = validatedData.phone.replace(/[^0-9]/g, '');
    const finalPhone = formattedPhone.length === 10 ? `+1${formattedPhone}` : `+${formattedPhone}`;

    // Step 5: Local object used only to render the notification email
    const contactData: Partial<ContactSupport> = {
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      email: validatedData.email,
      phone: finalPhone,
      subject: validatedData.service || 'General Inquiry',
      message: validatedData.message,
      created_at: new Date().toISOString(),
    };

    // Send to backend API (POST /public/contact-support)
    let apiResponse: Response;
    try {
      apiResponse = await fetch(`${API_BASE_URL}/public/contact-support`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          firstName: validatedData.first_name,
          lastName: validatedData.last_name,
          email: validatedData.email,
          phone: validatedData.phone,
          subject: validatedData.service || 'General Inquiry',
          message: validatedData.message,
          smsConsent: validatedData.sms_consent ?? false,
        }),
      });
    } catch (fetchError) {
      console.error('Network error contacting backend (contact-support):', fetchError);
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

      console.error('Backend error creating contact-support:', apiResponse.status);
      return new Response(
        JSON.stringify({ success: false, message }),
        { status: apiResponse.status, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 6: Send email notification to admin(s)
    try {
      const emailHtml = getContactSupportEmailTemplate(contactData as ContactSupport);
      const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration USA';

      // Collect all admin recipients (deduplicated)
      const adminEmail = import.meta.env.ADMIN_EMAIL || 'admin@aquashieldrestorationusa.com';
      const infoEmail = import.meta.env.COMPANY_EMAIL || 'info@aquashieldrestorationusa.com';
      const recipients = [...new Set([adminEmail, infoEmail].filter(Boolean))];

      await sendEmail(
        recipients,
        `New Contact Support Request - ${companyName}`,
        emailHtml
      );
    } catch (emailError) {
      console.error('Email error:', emailError);
      // Don't fail the request if email fails
    }

    // Step 7: Success response
    return new Response(
      JSON.stringify({
        success: true,
        message: 'Thank you for contacting us! We will get back to you shortly.',
      }),
      { status: 200, headers: { 'Content-Type': 'application/json' } }
    );

  } catch (error) {
    console.error('Error in contact-support API:', error);
    return new Response(
      JSON.stringify({
        success: false,
        message: 'An unexpected error occurred. Please try again later.',
      }),
      { status: 500, headers: { 'Content-Type': 'application/json' } }
    );
  }
};
