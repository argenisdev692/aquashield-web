import type { APIRoute } from 'astro';
import { getServerSupabase, type ContactSupport } from '../../lib/supabase';
import { sendEmail, getContactSupportEmailTemplate } from '../../utils/email';
import { v4 as uuidv4 } from 'uuid';

// Verify reCAPTCHA token
async function verifyRecaptcha(token: string): Promise<boolean> {
  const secretKey = import.meta.env.RECAPTCHA_SECRET_KEY;
  
  const response = await fetch('https://www.google.com/recaptcha/api/siteverify', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `secret=${secretKey}&response=${token}`,
  });

  const data = await response.json();
  
  // Check if score is acceptable (threshold 0.5)
  return data.success && (!data.score || data.score >= 0.5);
}

export const POST: APIRoute = async ({ request }) => {
  try {
    const body = await request.json();
    
    const {
      first_name,
      last_name,
      email,
      phone,
      message,
      sms_consent,
      'g-recaptcha-response': recaptchaToken
    } = body;

    // Validate required fields
    if (!first_name || !last_name || !email || !phone || !message) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'All required fields must be filled',
          errors: {
            first_name: !first_name ? ['First name is required'] : [],
            last_name: !last_name ? ['Last name is required'] : [],
            email: !email ? ['Email is required'] : [],
            phone: !phone ? ['Phone is required'] : [],
            message: !message ? ['Message is required'] : [],
          }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Verify reCAPTCHA
    if (!recaptchaToken) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'reCAPTCHA verification is required',
          errors: { 'g-recaptcha-response': ['Please verify you are not a robot'] }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    const isValidRecaptcha = await verifyRecaptcha(recaptchaToken);
    if (!isValidRecaptcha) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'reCAPTCHA verification failed',
          errors: { 'g-recaptcha-response': ['CAPTCHA verification failed'] }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Format phone number
    const formattedPhone = phone.replace(/[^0-9]/g, '');
    const finalPhone = formattedPhone.length === 10 ? `+1${formattedPhone}` : `+${formattedPhone}`;

    // Create contact support entry in Supabase
    const supabase = getServerSupabase();
    
    const contactData: Partial<ContactSupport> = {
      uuid: uuidv4(),
      first_name,
      last_name,
      email,
      phone: finalPhone,
      message,
      sms_consent: sms_consent === true || sms_consent === 'true' || sms_consent === 'on',
      readed: false,
    };

    const { data, error } = await supabase
      .from('contact_supports')
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

    // Send email notification to admin
    try {
      const emailHtml = getContactSupportEmailTemplate(data as ContactSupport);
      const adminEmail = import.meta.env.ADMIN_EMAIL;
      const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration LLC';
      
      await sendEmail(
        adminEmail,
        `New Contact Support Request - ${companyName}`,
        emailHtml
      );
    } catch (emailError) {
      console.error('Email error:', emailError);
      // Don't fail the request if email fails
    }

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
