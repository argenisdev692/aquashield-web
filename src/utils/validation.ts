/**
 * Validation Schemas with Zod
 * OWASP 2025 Input Validation
 */

import { z } from 'zod';

/**
 * Contact Support Form Schema
 */
export const contactSupportSchema = z.object({
  first_name: z.string()
    .min(2, 'First name must be at least 2 characters')
    .max(50, 'First name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'First name contains invalid characters')
    .refine(val => !/(test|asdf|qwerty|fake|dummy)/i.test(val), {
      message: 'Invalid first name'
    }),
  
  last_name: z.string()
    .min(2, 'Last name must be at least 2 characters')
    .max(50, 'Last name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'Last name contains invalid characters')
    .refine(val => !/(test|asdf|qwerty|fake|dummy)/i.test(val), {
      message: 'Invalid last name'
    }),
  
  email: z.string()
    .email('Invalid email address')
    .min(5, 'Email is too short')
    .max(100, 'Email is too long')
    .refine(val => !/(test@|example@|fake@|sample@|temp@|mailinator|guerrilla)/i.test(val), {
      message: 'Invalid email address'
    }),
  
  phone: z.string()
    .min(10, 'Phone number is too short')
    .max(20, 'Phone number is too long')
    .refine(val => {
      const cleaned = val.replace(/\D/g, '');
      return cleaned.length >= 10;
    }, 'Invalid phone number')
    .refine(val => !/(123|555|0000|1234567890)/.test(val), {
      message: 'Invalid phone number'
    }),
  
  service: z.string()
    .min(1, 'Please select a service')
    .optional(),
  
  address: z.string()
    .min(5, 'Address is too short')
    .max(200, 'Address is too long')
    .optional(),
  
  message: z.string()
    .min(10, 'Message must be at least 10 characters')
    .max(1000, 'Message must not exceed 1000 characters')
    .refine(val => {
      // No URLs allowed
      return !/(https?:\/\/|www\.)/i.test(val);
    }, 'URLs are not allowed in the message'),
  
  sms_consent: z.boolean()
    .or(z.string().transform(val => val === 'true' || val === 'on'))
    .optional()
    .default(false),
  
  consent: z.boolean()
    .or(z.string().transform(val => val === 'true' || val === 'on'))
    .refine(val => val === true, 'You must agree to the terms'),
  
  // Honeypot field - should always be empty
  website: z.string()
    .max(0, 'Invalid submission')
    .optional()
    .default(''),

  // Cloudflare Turnstile token
  'cf-turnstile-response': z.string()
    .min(1, 'CAPTCHA verification is required')
});

export type ContactSupportInput = z.infer<typeof contactSupportSchema>;

/**
 * Facebook Lead Form Schema
 */
export const facebookLeadSchema = z.object({
  first_name: z.string()
    .min(2, 'First name must be at least 2 characters')
    .max(50, 'First name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'First name contains invalid characters')
    .refine(val => !/(test|asdf|qwerty|fake|dummy)/i.test(val), {
      message: 'Invalid first name'
    }),
  
  last_name: z.string()
    .min(2, 'Last name must be at least 2 characters')
    .max(50, 'Last name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'Last name contains invalid characters')
    .refine(val => !/(test|asdf|qwerty|fake|dummy)/i.test(val), {
      message: 'Invalid last name'
    }),
  
  email: z.string()
    .email('Invalid email address')
    .min(5, 'Email is too short')
    .max(100, 'Email is too long')
    .refine(val => !/(test@|example@|fake@|sample@|temp@|mailinator|guerrilla)/i.test(val), {
      message: 'Invalid email address'
    }),
  
  phone: z.string()
    .regex(/^\(\d{3}\)\s\d{3}-\d{4}$/, 'Phone must be in format (XXX) XXX-XXXX')
    .refine(val => !/(123|555|0000)/.test(val), {
      message: 'Invalid phone number'
    }),
  
  address: z.string()
    .min(5, 'Address is too short')
    .max(200, 'Address is too long')
    .refine(val => !/(test|example|fake|asdf)/i.test(val), {
      message: 'Invalid address'
    }),
  
  address_2: z.string()
    .max(100, 'Address line 2 is too long')
    .optional(),
  
  city: z.string()
    .min(2, 'City is too short')
    .max(100, 'City is too long')
    .refine(val => !/(test|example|fake)/i.test(val), {
      message: 'Invalid city'
    }),
  
  state: z.string()
    .min(2, 'State is required')
    .max(2, 'State must be 2 characters'),
  
  zipcode: z.string()
    .regex(/^\d{5}$/, 'Zipcode must be exactly 5 digits'),
  
  country: z.string()
    .min(2, 'Country is required')
    .default('US'),
  
  insurance_property: z.enum(['yes', 'no'], {
    errorMap: () => ({ message: 'Please specify if property has insurance' })
  }),
  
  message: z.string()
    .min(10, 'Message must be at least 10 characters')
    .max(1000, 'Message must not exceed 1000 characters')
    .refine(val => !/(https?:\/\/|www\.)/i.test(val), 'URLs are not allowed')
    .optional(),
  
  sms_consent: z.boolean()
    .or(z.string().transform(val => val === 'true' || val === 'on'))
    .optional()
    .default(false),
  
  latitude: z.number()
    .min(-90, 'Invalid latitude')
    .max(90, 'Invalid latitude')
    .optional()
    .nullable(),
  
  longitude: z.number()
    .min(-180, 'Invalid longitude')
    .max(180, 'Invalid longitude')
    .optional()
    .nullable(),
  
  lead_source: z.enum(['Facebook Ads', 'Website', 'Reference', 'Retell AI'])
    .optional()
    .default('Facebook Ads'),
  
  // Honeypot field
  website: z.string()
    .max(0, 'Invalid submission')
    .optional()
    .default(''),
  
  // Cloudflare Turnstile token
  'cf-turnstile-response': z.string()
    .min(1, 'CAPTCHA verification is required')
});

export type FacebookLeadInput = z.infer<typeof facebookLeadSchema>;

/**
 * Appointment / Free Inspection Form Schema
 */
export const appointmentSchema = z.object({
  first_name: z.string()
    .min(2, 'First name must be at least 2 characters')
    .max(50, 'First name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'First name contains invalid characters'),

  last_name: z.string()
    .min(2, 'Last name must be at least 2 characters')
    .max(50, 'Last name must not exceed 50 characters')
    .regex(/^[A-Za-z\s\'-]+$/, 'Last name contains invalid characters'),

  phone: z.string()
    .min(10, 'Phone number is too short')
    .max(20, 'Phone number is too long')
    .refine(val => val.replace(/\D/g, '').length >= 10, 'Invalid phone number'),

  email: z.string()
    .email('Invalid email address')
    .max(100, 'Email is too long')
    .optional()
    .or(z.literal('')),

  address: z.string()
    .min(5, 'Address is too short')
    .max(200, 'Address is too long'),

  address_2: z.string()
    .max(100, 'Address line 2 is too long')
    .optional(),

  city: z.string()
    .min(2, 'City is required')
    .max(100, 'City is too long'),

  state: z.string()
    .min(2, 'State is required')
    .max(2, 'State must be 2 characters'),

  zipcode: z.string()
    .regex(/^\d{5}$/, 'Zipcode must be exactly 5 digits'),

  country: z.string()
    .min(2, 'Country is required')
    .default('US'),

  insurance_property: z.enum(['yes', 'no'], {
    errorMap: () => ({ message: 'Please specify if property has insurance' }),
  }),

  message: z.string()
    .max(1000, 'Message must not exceed 1000 characters')
    .optional(),

  sms_consent: z.boolean()
    .or(z.string().transform(val => val === 'true' || val === 'on'))
    .optional()
    .default(false),

  // Cloudflare Turnstile token
  'cf-turnstile-response': z.string()
    .min(1, 'CAPTCHA verification is required'),
});

export type AppointmentInput = z.infer<typeof appointmentSchema>;

/**
 * Helper to format Zod errors for API response
 */
export function formatZodErrors(error: z.ZodError) {
  const errors: Record<string, string[]> = {};
  
  for (const issue of error.issues) {
    const path = issue.path.join('.');
    if (!errors[path]) {
      errors[path] = [];
    }
    errors[path].push(issue.message);
  }
  
  return errors;
}
