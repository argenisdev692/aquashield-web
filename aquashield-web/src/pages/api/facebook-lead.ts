import type { APIRoute } from 'astro';
import { getServerSupabase, type Appointment } from '../../lib/supabase';
import { sendEmail, getNewLeadEmailTemplate } from '../../utils/email';
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
      phone,
      email,
      address,
      address_2,
      city,
      state,
      zipcode,
      country,
      insurance_property,
      message,
      sms_consent,
      latitude,
      longitude,
      'g-recaptcha-response': recaptchaToken
    } = body;

    // Validate required fields
    if (!first_name || !last_name || !phone || !email || !address || !city || !state || !zipcode || !country) {
      return new Response(
        JSON.stringify({
          success: false,
          message: 'All required fields must be filled',
          errors: {
            first_name: !first_name ? ['First name is required'] : [],
            last_name: !last_name ? ['Last name is required'] : [],
            phone: !phone ? ['Phone is required'] : [],
            email: !email ? ['Email is required'] : [],
            address: !address ? ['Address is required'] : [],
            city: !city ? ['City is required'] : [],
            state: !state ? ['State is required'] : [],
            zipcode: !zipcode ? ['ZIP code is required'] : [],
            country: !country ? ['Country is required'] : [],
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

    // Check if email already exists
    const supabase = getServerSupabase();
    
    const { data: existingAppointment } = await supabase
      .from('appointments')
      .select('id')
      .eq('email', email)
      .single();

    if (existingAppointment) {
      return new Response(
        JSON.stringify({
          success: false,
          duplicate_email: true,
          message: 'This email is already registered in our system. Please contact our support team.',
          errors: { email: ['This email is already registered'] }
        }),
        { status: 422, headers: { 'Content-Type': 'application/json' } }
      );
    }

    // Create appointment entry
    const appointmentData: Partial<Appointment> = {
      uuid: uuidv4(),
      first_name,
      last_name,
      phone: finalPhone,
      email,
      address,
      address_2: address_2 || null,
      city,
      state,
      zipcode,
      country,
      insurance_property: insurance_property === 'yes' || insurance_property === true,
      message: message || null,
      sms_consent: sms_consent === true || sms_consent === 'true' || sms_consent === 'on',
      registration_date: new Date().toISOString(),
      inspection_status: 'Pending',
      status_lead: 'New',
      latitude: latitude ? parseFloat(latitude) : null,
      longitude: longitude ? parseFloat(longitude) : null,
      lead_source: 'Facebook Ads',
    };

    const { data, error } = await supabase
      .from('appointments')
      .insert([appointmentData])
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
      const emailHtml = getNewLeadEmailTemplate(data as Appointment);
      const adminEmail = import.meta.env.ADMIN_EMAIL;
      
      await sendEmail(
        adminEmail,
        '🎉 New Lead Alert! 🔔',
        emailHtml
      );
    } catch (emailError) {
      console.error('Email error:', emailError);
      // Don't fail the request if email fails
    }

    return new Response(
      JSON.stringify({
        success: true,
        message: 'Your request has been submitted successfully!',
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
