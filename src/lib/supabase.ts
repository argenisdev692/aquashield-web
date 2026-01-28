import { createClient } from '@supabase/supabase-js';

const supabaseUrl = import.meta.env.PUBLIC_SUPABASE_URL;
const supabaseAnonKey = import.meta.env.PUBLIC_SUPABASE_ANON_KEY;

if (!supabaseUrl || !supabaseAnonKey) {
  throw new Error('Missing Supabase environment variables');
}

export const supabase = createClient(supabaseUrl, supabaseAnonKey);

// Server-side client with service role key (for API endpoints)
export function getServerSupabase() {
  const serviceRoleKey = import.meta.env.SUPABASE_SERVICE_ROLE_KEY;
  
  if (!serviceRoleKey) {
    throw new Error('Missing Supabase service role key');
  }
  
  return createClient(supabaseUrl, serviceRoleKey, {
    auth: {
      autoRefreshToken: false,
      persistSession: false
    }
  });
}

// Database types
export interface ContactSupport {
  id?: number;
  uuid: string;
  first_name: string;
  last_name: string;
  email: string;
  phone: string;
  message: string;
  sms_consent: boolean;
  readed: boolean;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}

export interface Appointment {
  id?: number;
  uuid: string;
  first_name: string;
  last_name: string;
  phone: string;
  email: string | null;
  address: string;
  address_2: string | null;
  city: string;
  state: string;
  zipcode: string;
  country: string;
  insurance_property: boolean;
  message: string | null;
  sms_consent: boolean;
  registration_date: string | null;
  inspection_date: string | null;
  inspection_time: string | null;
  inspection_status: 'Confirmed' | 'Completed' | 'Pending' | 'Declined' | null;
  status_lead: 'New' | 'Called' | 'Pending' | 'Declined' | null;
  lead_source: 'Website' | 'Facebook Ads' | 'Reference' | 'Retell AI' | null;
  follow_up_calls: any | null;
  notes: string | null;
  owner: string | null;
  damage_detail: string | null;
  intent_to_claim: boolean | null;
  follow_up_date: string | null;
  additional_note: string | null;
  latitude: number | null;
  longitude: number | null;
  created_at?: string;
  updated_at?: string;
  deleted_at?: string | null;
}
