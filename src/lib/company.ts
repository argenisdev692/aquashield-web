/**
 * company.ts
 * Server-side helper to fetch the active company_data row from Supabase.
 * Uses the service role key to bypass RLS.
 * Safe to import only in Astro component frontmatter or API routes (server-side).
 */

import { getServerSupabase, type CompanyData } from './supabase';

/** Default fallback values so the UI never breaks if the DB is unreachable */
export const DEFAULT_COMPANY: CompanyData = {
  company_name: 'AquaShield Restoration USA',
  name: 'Victor Lara',
  email: 'info@aquashieldrestorationusa.com',
  phone: '+17135876423',
  address: '3733 Westheimer Rd. Ste 1-4583, Houston, TX 77027',
  website: 'https://aquashieldrestorationusa.com',
  facebook_link: 'https://www.facebook.com/aquashieldrestoration/',
  instagram_link: 'https://www.instagram.com/aquashieldrestoration/',
  linkedin_link: 'https://www.linkedin.com/company/aquashieldrestoration/',
  twitter_link: 'https://twitter.com/aquashieldrestoration',
};

/**
 * Format a raw phone number string to `(713) 587-6423` display format.
 * Handles E.164 (+1XXXXXXXXXX) and plain 10-digit numbers.
 */
export function formatPhone(raw: string | null | undefined): string {
  if (!raw) return '';
  const digits = raw.replace(/\D/g, '');
  // Strip leading country code 1 if present
  const local = digits.length === 11 && digits.startsWith('1') ? digits.slice(1) : digits;
  if (local.length !== 10) return raw;
  return `(${local.slice(0, 3)}) ${local.slice(3, 6)}-${local.slice(6)}`;
}

/**
 * Fetch the first active company_data row from Supabase.
 * Falls back to DEFAULT_COMPANY on any error.
 */
export async function getCompanyData(): Promise<CompanyData> {
  try {
    const supabase = getServerSupabase();
    const { data, error } = await supabase
      .from('company_data')
      .select('*')
      .is('deleted_at', null)
      .order('id', { ascending: true })
      .limit(1)
      .single();

    if (error || !data) {
      console.warn('[company.ts] Could not fetch company_data from Supabase:', error?.message);
      return DEFAULT_COMPANY;
    }

    return data as CompanyData;
  } catch (err) {
    console.error('[company.ts] Unexpected error fetching company_data:', err);
    return DEFAULT_COMPANY;
  }
}
