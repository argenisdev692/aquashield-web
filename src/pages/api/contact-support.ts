import type { APIRoute } from 'astro';
import { getServerSupabase, type ContactSupport } from '../../lib/supabase';
import { sendEmail, getContactSupportEmailTemplate } from '../../utils/email';
import { contactSupportSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck, getClientIP } from '../../utils/spam-detection';
import { verifyTurnstile } from '../../utils/turnstile';


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

    // Step 5: Create contact support entry in Supabase
    const supabase = getServerSupabase();

    const contactData: Partial<ContactSupport> = {
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      email: validatedData.email,
      phone: finalPhone,
      subject: validatedData.service || 'General Inquiry',
      message: validatedData.message,
    };

    const { data, error } = await supabase
      .from('contact_support')
      .insert([contactData])
      .select()
      .single();

    if (error) {
      console.error('Supabase error:', error);
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Database error occurred',
        }),
        { status: 500, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 6: Send email notification to admin(s)
    try {
      const emailHtml = getContactSupportEmailTemplate(data as ContactSupport);
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
