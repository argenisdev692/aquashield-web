import type { APIRoute } from 'astro';
import { getServerSupabase, type ContactSupport } from '../../lib/supabase';
import { sendEmail, getContactSupportEmailTemplate } from '../../utils/email';
import { v4 as uuidv4 } from 'uuid';
import { contactSupportSchema, formatZodErrors } from '../../utils/validation';
import { performSpamCheck } from '../../utils/spam-detection';

// Verify reCAPTCHA token with enhanced logging
async function verifyRecaptcha(token: string, ipAddress: string): Promise<{ success: boolean; score?: number; message?: string }> {
  const secretKey = import.meta.env.RECAPTCHA_SECRET_KEY;
  
  try {
    const response = await fetch('https://www.google.com/recaptcha/api/siteverify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
      },
      body: `secret=${secretKey}&response=${token}&remoteip=${ipAddress}`,
    });

    const data = await response.json();
    
    console.log('reCAPTCHA verification:', {
      success: data.success,
      score: data.score,
      action: data.action,
      hostname: data.hostname,
      'error-codes': data['error-codes']
    });
    
    // Check if score is acceptable (threshold 0.5)
    const isValid = data.success && (!data.score || data.score >= 0.5);
    
    return {
      success: isValid,
      score: data.score,
      message: isValid ? 'Verification successful' : 'Low confidence score or failed verification'
    };
  } catch (error) {
    console.error('reCAPTCHA verification error:', error);
    return {
      success: false,
      message: 'reCAPTCHA server error'
    };
  }
}

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

    // Step 3: Verify reCAPTCHA
    const ipAddress = request.headers.get('x-forwarded-for')?.split(',')[0] || 
                      request.headers.get('x-real-ip') || 
                      'unknown';
    
    const recaptchaResult = await verifyRecaptcha(validatedData['g-recaptcha-response'], ipAddress);
    
    if (!recaptchaResult.success) {
      return new Response(
        JSON.stringify({
          success: false,
          message: `reCAPTCHA verification failed: ${recaptchaResult.message}`,
          errors: { 'g-recaptcha-response': [recaptchaResult.message || 'CAPTCHA verification failed'] }
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
      uuid: uuidv4(),
      first_name: validatedData.first_name,
      last_name: validatedData.last_name,
      email: validatedData.email,
      phone: finalPhone,
      message: validatedData.message,
      sms_consent: validatedData.sms_consent || false,
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

    // Step 6: Send email notification to admin
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
