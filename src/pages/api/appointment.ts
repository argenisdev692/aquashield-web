import type { APIRoute } from 'astro';
import { getServerSupabase, type Appointment } from '../../lib/supabase';
import { v4 as uuidv4 } from 'uuid';
import { appointmentSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck } from '../../utils/spam-detection';
import { verifyTurnstile } from '../../utils/turnstile';
import { sendEmail, getNewLeadEmailTemplate, getLeadConfirmationEmailTemplate } from '../../utils/email';

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
    const ipAddress =
      request.headers.get('x-forwarded-for')?.split(',')[0] ||
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
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 3: Format phone number to E.164
    const formattedPhone = validatedData.phone.replace(/[^0-9]/g, '');
    const finalPhone =
      formattedPhone.length === 10 ? `+1${formattedPhone}` : `+${formattedPhone}`;

    // Step 4: Insert into Supabase appointments table (service role bypasses RLS)
    const supabase = getServerSupabase();

    const appointmentData: Partial<Appointment> = {
      uuid: uuidv4(),
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      phone: finalPhone,
      email: validatedData.email || null,
      address: validatedData.address,
      address_2: validatedData.address_2 || null,
      city: validatedData.city,
      state: validatedData.state,
      zipcode: validatedData.zipcode,
      country: validatedData.country || 'US',
      insurance_property: validatedData.insurance_property === 'yes',
      message: validatedData.message || null,
      sms_consent: validatedData.sms_consent ?? false,
      registration_date: new Date().toISOString(),
      status_lead: 'New',
      lead_source: 'Website',
    };

    const { error } = await supabase
      .from('appointments')
      .insert([appointmentData]);

    if (error) {
      console.error('Supabase error inserting appointment:', error);
      return new Response(
        JSON.stringify({
          success: false,
          message: 'Database error occurred. Please try again or call us directly.',
        }),
        { status: 500, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Step 5: Send emails
    try {
      const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration LLC';
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
