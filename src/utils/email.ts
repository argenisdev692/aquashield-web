import { Resend } from 'resend';
import type { ContactSupport, Appointment } from '../lib/supabase';

// Email provider type
type EmailProvider = 'resend' | 'smtp';

// Get email provider from env
const EMAIL_PROVIDER: EmailProvider = (import.meta.env.EMAIL_PROVIDER || 'resend') as EmailProvider;

// Initialize Resend client (only if using Resend)
let resendClient: Resend | null = null;
if (EMAIL_PROVIDER === 'resend' && import.meta.env.RESEND_API_KEY) {
  resendClient = new Resend(import.meta.env.RESEND_API_KEY);
}

// SMTP transporter is not available in Cloudflare Workers
// Only Resend API works in Cloudflare Workers runtime
// If you need SMTP, deploy to a platform that supports Node.js (Vercel, Netlify, etc.)

// Format phone number
function formatPhone(phone: string): string {
  const digitsOnly = phone.replace(/[^0-9]/g, '');
  
  if (digitsOnly.length === 10) {
    return `(${digitsOnly.slice(0, 3)}) ${digitsOnly.slice(3, 6)}-${digitsOnly.slice(6)}`;
  } else if (digitsOnly.length === 11 && digitsOnly[0] === '1') {
    return `(${digitsOnly.slice(1, 4)}) ${digitsOnly.slice(4, 7)}-${digitsOnly.slice(7)}`;
  }
  
  return phone;
}

// Contact Support Email Template
export function getContactSupportEmailTemplate(contact: ContactSupport): string {
  const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration LLC';
  const companyPhone = import.meta.env.COMPANY_PHONE;
  const companyEmail = import.meta.env.COMPANY_EMAIL;
  const companyAddress = import.meta.env.COMPANY_ADDRESS;
  const facebook = import.meta.env.COMPANY_FACEBOOK;
  const instagram = import.meta.env.COMPANY_INSTAGRAM;
  const linkedin = import.meta.env.COMPANY_LINKEDIN;
  const twitter = import.meta.env.COMPANY_TWITTER;

  const submittedDate = contact.created_at 
    ? new Date(contact.created_at).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZoneName: 'short'
      })
    : 'N/A';

  return `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Contact Support Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            border-radius: 10px;
            background-color: #ffffff;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
        }
        .header img {
            max-width: 180px;
            height: auto;
        }
        .content h2 {
            color: #00b8d4;
            text-align: center;
            border-bottom: 2px solid #00b8d4;
            padding-bottom: 10px;
        }
        .details {
            margin-top: 15px;
        }
        .details strong {
            display: inline-block;
            width: 100px;
            color: #555;
        }
        .details p {
            margin: 8px 0;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            font-size: 14px;
            color: #666666;
        }
        .message-box {
            white-space: pre-wrap;
            background-color: #f9f9f9;
            padding: 15px;
            border: 1px solid #eee;
            border-radius: 4px;
            margin-top: 5px;
        }
        .social-icons {
            margin: 20px 0;
            text-align: center;
        }
        .social-icons a {
            margin: 0 10px;
            display: inline-block;
        }
        a {
            color: #00b8d4;
            text-decoration: none;
        }
        hr {
            border: none;
            border-top: 1px solid #eee;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="color: #001f3f; margin: 0;">${companyName}</h1>
        </div>

        <div class="content">
            <h2>New Contact Support Request</h2>
            <p>A new contact support request has been submitted via the website:</p>

            <div class="details">
                <p><strong>Name:</strong> ${contact.first_name} ${contact.last_name}</p>
                <p><strong>Email:</strong> <a href="mailto:${contact.email}">${contact.email}</a></p>
                <p><strong>Phone:</strong> <a href="tel:${contact.phone ? contact.phone.replace(/[^0-9]/g, '') : ''}">${contact.phone ? formatPhone(contact.phone) : 'N/A'}</a></p>
                <p><strong>Subject:</strong> ${contact.subject}</p>
                <p><strong>Submitted:</strong> ${submittedDate}</p>
                <hr>
                <p><strong>Message:</strong></p>
                <div class="message-box">${contact.message}</div>
            </div>
        </div>

        <div class="social-icons">
            ${facebook ? `<a href="${facebook}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" width="30" alt="Facebook"></a>` : ''}
            ${instagram ? `<a href="${instagram}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" width="30" alt="Instagram"></a>` : ''}
            ${linkedin ? `<a href="${linkedin}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" width="30" alt="LinkedIn"></a>` : ''}
            ${twitter ? `<a href="${twitter}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="30" alt="Twitter"></a>` : ''}
        </div>

        <div class="footer">
            <p>Business Hours:<br>Monday to Friday: 9:00 AM - 5:00 PM</p>
            <p style="margin-top: 10px; font-size: 12px;">© ${new Date().getFullYear()} ${companyName}. All rights reserved.</p>
            ${companyAddress ? `<p style="font-size: 10px; color: #999;">${companyAddress}</p>` : ''}
            <div style="margin-top: 5px; font-size: 12px; color: #777;">
                <p style="margin: 3px 0;">
                    ${companyPhone ? `<span>${companyPhone}</span>` : ''}
                    ${companyPhone && companyEmail ? `<span style="margin: 0 5px;">|</span>` : ''}
                    ${companyEmail ? `<a href="mailto:${companyEmail}" style="color: #666; text-decoration: none;">${companyEmail}</a>` : ''}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
  `;
}

// New Lead Email Template
export function getNewLeadEmailTemplate(appointment: Appointment): string {
  const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration LLC';
  const companyPhone = import.meta.env.COMPANY_PHONE;
  const companyEmail = import.meta.env.COMPANY_EMAIL;
  const companyAddress = import.meta.env.COMPANY_ADDRESS;
  const facebook = import.meta.env.COMPANY_FACEBOOK;
  const instagram = import.meta.env.COMPANY_INSTAGRAM;
  const linkedin = import.meta.env.COMPANY_LINKEDIN;
  const twitter = import.meta.env.COMPANY_TWITTER;

  const submittedDate = appointment.registration_date 
    ? new Date(appointment.registration_date).toLocaleString('en-US', {
        month: 'short',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true,
        timeZoneName: 'short'
      })
    : 'N/A';

  return `
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>🎉 New Lead Alert! 🔔</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .details {
            line-height: 1.6;
            color: #333333;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #666666;
            font-size: 14px;
        }
        .social-icons {
            margin: 20px 0;
            text-align: center;
        }
        .social-icons a {
            margin: 0 10px;
            display: inline-block;
        }
        .highlight {
            color: #00b8d4;
            font-weight: bold;
        }
        .lead-banner {
            background: #e6f7fb;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .lead-icon {
            display: inline-block;
            margin-right: 5px;
            font-size: 1.2em;
            color: #00b8d4;
        }
        .details td {
            padding: 5px 0;
            vertical-align: top;
        }
        .details strong {
            display: inline-block;
            width: 150px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1 style="color: #001f3f; margin: 0;">${companyName}</h1>
        </div>

        <div class="details">
            <h2 style="color: #00b8d4; text-align: center; border-bottom: 2px solid #00b8d4; padding-bottom: 10px;">🎉 New Lead Alert! 🔔</h2>

            <div class="lead-banner">
                <p style="text-align: center; margin: 0;">
                    You have received a <span class="highlight">new potential customer</span> inquiry for
                    <strong>${companyName}</strong>!
                </p>
            </div>

            <h3 style="margin-top: 25px; margin-bottom: 15px; color: #333;">Lead Details:</h3>
            <table style="width: 100%; margin: 0 0 20px 0; border-collapse: collapse;">
                <tr>
                    <td><span class="lead-icon">👤</span> <strong>Client Name:</strong></td>
                    <td>${appointment.first_name} ${appointment.last_name}</td>
                </tr>
                <tr>
                    <td><span class="lead-icon">📧</span> <strong>Email:</strong></td>
                    <td><a href="mailto:${appointment.email}">${appointment.email}</a></td>
                </tr>
                <tr>
                    <td><span class="lead-icon">📞</span> <strong>Phone:</strong></td>
                    <td>${formatPhone(appointment.phone)}</td>
                </tr>
                <tr>
                    <td><span class="lead-icon">📍</span> <strong>Address:</strong></td>
                    <td>
                        ${appointment.address}<br>
                        ${appointment.address_2 ? `${appointment.address_2}<br>` : ''}
                        ${appointment.city}, ${appointment.state} ${appointment.zipcode}<br>
                        ${appointment.country}
                    </td>
                </tr>
                <tr>
                    <td><span class="lead-icon">🛡️</span> <strong>Has Insurance?:</strong></td>
                    <td>${appointment.insurance_property ? 'Yes' : 'No'}</td>
                </tr>
                <tr>
                    <td><span class="lead-icon">💬</span> <strong>SMS Consent:</strong></td>
                    <td>${appointment.sms_consent ? 'Yes' : 'No'}</td>
                </tr>
                <tr>
                    <td><span class="lead-icon">📝</span> <strong>Message:</strong></td>
                    <td>${appointment.message || 'N/A'}</td>
                </tr>
                <tr>
                    <td><span class="lead-icon">📅</span> <strong>Submitted:</strong></td>
                    <td>${submittedDate}</td>
                </tr>
            </table>

            <div style="padding: 15px; border-radius: 8px; margin-top: 25px; text-align: center; background: #e6f7fb;">
                <p>Act quickly to connect with this potential client!</p>
                <p>Contact them from: <strong>📞 ${companyPhone}</strong></p>
            </div>
        </div>

        <div class="social-icons">
            ${facebook ? `<a href="${facebook}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" width="30" alt="Facebook"></a>` : ''}
            ${instagram ? `<a href="${instagram}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" width="30" alt="Instagram"></a>` : ''}
            ${linkedin ? `<a href="${linkedin}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" width="30" alt="LinkedIn"></a>` : ''}
            ${twitter ? `<a href="${twitter}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="30" alt="Twitter"></a>` : ''}
        </div>

        <div class="footer">
            <p>Business Hours:<br>Monday to Friday: 9:00 AM - 5:00 PM</p>
            <p style="margin-top: 10px; font-size: 12px;">© ${new Date().getFullYear()} ${companyName}. All rights reserved.</p>
            ${companyAddress ? `<p style="font-size: 10px; color: #999;">${companyAddress}</p>` : ''}
            <div style="margin-top: 5px; font-size: 12px; color: #777;">
                <p style="margin: 3px 0;">
                    ${companyPhone ? `<span>${companyPhone}</span>` : ''}
                    ${companyPhone && companyEmail ? `<span style="margin: 0 5px;">|</span>` : ''}
                    ${companyEmail ? `<a href="mailto:${companyEmail}" style="color: #666; text-decoration: none;">${companyEmail}</a>` : ''}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
  `;
}

// Lead Confirmation Email Template (sent to customer after submitting appointment)
export function getLeadConfirmationEmailTemplate(appointment: Appointment): string {
  const companyName = import.meta.env.COMPANY_NAME || 'AquaShield Restoration LLC';
  const companyPhone = import.meta.env.COMPANY_PHONE || '(713) 587-6423';
  const companyEmail = import.meta.env.COMPANY_EMAIL;
  const companyAddress = import.meta.env.COMPANY_ADDRESS;
  const companyWebsite = 'https://aquashieldrestorationusa.com';
  const facebook = import.meta.env.COMPANY_FACEBOOK;
  const instagram = import.meta.env.COMPANY_INSTAGRAM;
  const linkedin = import.meta.env.COMPANY_LINKEDIN;
  const twitter = import.meta.env.COMPANY_TWITTER;

  const fullName = `${appointment.first_name} ${appointment.last_name}`;

  return `
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>✅ We Received Your Information! - ${companyName}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
        }
        .logo {
            text-align: center;
            margin-bottom: 25px;
        }
        .details {
            line-height: 1.6;
            color: #333333;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #666666;
            font-size: 14px;
        }
        .social-icons {
            margin: 20px 0;
            text-align: center;
        }
        .social-icons a {
            margin: 0 10px;
            display: inline-block;
        }
        .highlight-blue {
            color: #00b8d4;
            font-weight: bold;
        }
        .confirmation-banner {
            background: #e6f7fb;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            text-align: center;
        }
        a { color: #00b8d4; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <h1 style="color: #001f3f; margin: 0;">${companyName}</h1>
            <p style="color: #00b8d4; margin: 5px 0 0;"><a href="${companyWebsite}">${companyWebsite}</a></p>
        </div>

        <div class="details">
            <h2 style="color: #00b8d4; text-align: center; border-bottom: 2px solid #00b8d4; padding-bottom: 10px;">
                ✅ We Received Your Information!
            </h2>

            <div class="confirmation-banner">
                <p style="margin: 0;"><strong>Your information has been successfully received.</strong></p>
            </div>

            <p style="margin: 20px 0;">Hello <strong>${fullName}</strong>, thank you for contacting us! 🙌</p>

            <p>By completing this form, you authorize <strong>${companyName}</strong> to contact you
                to coordinate your free inspection. An agent or virtual assistant will call you within
                <strong>1 business day</strong> from the number:</p>

            <p style="text-align: center; font-size: 1.2em; margin: 25px 0;">
                <span class="highlight-blue"><strong>📞 ${companyPhone}</strong></span>
            </p>

            <p style="margin-bottom: 25px;">Your information is confidential and will be used exclusively
                for this purpose. Please keep your phone available to receive our call.</p>

            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <p style="margin: 0; font-size: 0.9em;">📌 <strong>Reminder:</strong><br>
                    If we cannot reach you, we will leave a voicemail with instructions to reschedule.</p>
            </div>
        </div>

        <div class="social-icons">
            ${facebook ? `<a href="${facebook}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/124/124010.png" width="30" alt="Facebook"></a>` : ''}
            ${instagram ? `<a href="${instagram}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174855.png" width="30" alt="Instagram"></a>` : ''}
            ${linkedin ? `<a href="${linkedin}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/174/174857.png" width="30" alt="LinkedIn"></a>` : ''}
            ${twitter ? `<a href="${twitter}" target="_blank"><img src="https://cdn-icons-png.flaticon.com/512/733/733579.png" width="30" alt="Twitter"></a>` : ''}
        </div>

        <div class="footer">
            <p>Business Hours:<br>Monday to Friday: 9:00 AM - 5:00 PM</p>
            <p style="margin-top: 10px; font-size: 12px;">© ${new Date().getFullYear()} ${companyName}. All rights reserved.</p>
            ${companyAddress ? `<p style="font-size: 10px; color: #999;">${companyAddress}</p>` : ''}
            <div style="margin-top: 5px; font-size: 12px; color: #777;">
                <p style="margin: 3px 0;">
                    ${companyPhone ? `<span>${companyPhone}</span>` : ''}
                    ${companyPhone && companyEmail ? `<span style="margin: 0 5px;">|</span>` : ''}
                    ${companyEmail ? `<a href="mailto:${companyEmail}" style="color: #666; text-decoration: none;">${companyEmail}</a>` : ''}
                </p>
            </div>
        </div>
    </div>
</body>
</html>
  `;
}

// Send email function with provider support
// `to` can be a single email string or an array of email addresses
export async function sendEmail(to: string | string[], subject: string, html: string) {
  const fromEmail = import.meta.env.EMAIL_FROM;
  const fromName = import.meta.env.EMAIL_FROM_NAME || 'AquaShield Restoration LLC';
  const toArray = Array.isArray(to) ? to : [to];
  
  // Cloudflare Workers only supports Resend API
  if (EMAIL_PROVIDER === 'resend' && resendClient) {
    try {
      const { data, error } = await resendClient.emails.send({
        from: `${fromName} <${fromEmail}>`,
        to: toArray,
        subject,
        html,
      });

      if (error) {
        console.error('Resend error:', error);
        throw new Error(`Failed to send email via Resend: ${error.message}`);
      }

      return data;
    } catch (error) {
      console.error('Resend send error:', error);
      throw error;
    }
  }
  
  // SMTP is not supported in Cloudflare Workers
  throw new Error('SMTP is not supported in Cloudflare Workers. Please use Resend API by setting EMAIL_PROVIDER=resend');
}
