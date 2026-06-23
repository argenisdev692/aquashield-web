/**
 * Meta Conversions API (CAPI) - server-side event tracking
 *
 * Sends conversion events directly from the server to Meta, complementing the
 * browser Pixel. Both events share the same `event_id` so Meta deduplicates
 * them (see https://developers.facebook.com/docs/marketing-api/conversions-api).
 *
 * Improves Event Match Quality and recovers conversions lost to ad-blockers /
 * iOS tracking restrictions.
 */

// Graph API version - bump periodically (Meta deprecates versions after ~2 years)
const GRAPH_API_VERSION = 'v21.0';

/** SHA-256 hex hash using Web Crypto (available in Cloudflare Workers). */
async function sha256(value: string): Promise<string> {
  const data = new TextEncoder().encode(value);
  const hashBuffer = await crypto.subtle.digest('SHA-256', data);
  return Array.from(new Uint8Array(hashBuffer))
    .map((b) => b.toString(16).padStart(2, '0'))
    .join('');
}

/** Normalize then hash. Returns undefined for empty input. */
async function hashField(value?: string | null): Promise<string | undefined> {
  if (!value) return undefined;
  const normalized = value.trim().toLowerCase();
  if (!normalized) return undefined;
  return sha256(normalized);
}

/** Phone must be digits only, including country code, before hashing. */
async function hashPhone(value?: string | null): Promise<string | undefined> {
  if (!value) return undefined;
  const digits = value.replace(/\D/g, '');
  if (!digits) return undefined;
  return sha256(digits);
}

export interface CapiUserData {
  email?: string | null;
  phone?: string | null; // E.164 or any format; normalized internally
  firstName?: string | null;
  lastName?: string | null;
  city?: string | null;
  state?: string | null;
  zipcode?: string | null;
  country?: string | null;
  clientIpAddress?: string | null;
  clientUserAgent?: string | null;
  fbc?: string | null; // _fbc cookie
  fbp?: string | null; // _fbp cookie
}

export interface CapiEventParams {
  eventName: string; // e.g. 'Lead'
  eventId: string; // shared with the browser Pixel for deduplication
  eventSourceUrl?: string;
  userData: CapiUserData;
  customData?: Record<string, unknown>;
}

/**
 * Sends a single event to the Meta Conversions API.
 * Reads FACEBOOK_PIXEL_ID + FACEBOOK_CONVERSIONS_API_TOKEN from env.
 * Never throws: logs and returns a result so it can't break the lead flow.
 */
export async function sendFacebookConversionEvent(
  params: CapiEventParams
): Promise<{ success: boolean; error?: string }> {
  const pixelId =
    import.meta.env.FACEBOOK_PIXEL_ID ||
    import.meta.env.PUBLIC_FACEBOOK_PIXEL_ID;
  const accessToken = import.meta.env.FACEBOOK_CONVERSIONS_API_TOKEN;
  const testEventCode = import.meta.env.FACEBOOK_TEST_EVENT_CODE; // optional

  if (!pixelId || !accessToken) {
    // CAPI not configured - silently skip (Pixel still works on the client)
    return { success: false, error: 'CAPI not configured' };
  }

  try {
    const u = params.userData;

    const [em, ph, fn, ln, ct, st, zp, countryHash] = await Promise.all([
      hashField(u.email),
      hashPhone(u.phone),
      hashField(u.firstName),
      hashField(u.lastName),
      hashField(u.city),
      hashField(u.state),
      hashField(u.zipcode),
      hashField(u.country),
    ]);

    const user_data: Record<string, unknown> = {};
    if (em) user_data.em = [em];
    if (ph) user_data.ph = [ph];
    if (fn) user_data.fn = [fn];
    if (ln) user_data.ln = [ln];
    if (ct) user_data.ct = [ct];
    if (st) user_data.st = [st];
    if (zp) user_data.zp = [zp];
    if (countryHash) user_data.country = [countryHash];
    if (u.clientIpAddress) user_data.client_ip_address = u.clientIpAddress;
    if (u.clientUserAgent) user_data.client_user_agent = u.clientUserAgent;
    if (u.fbc) user_data.fbc = u.fbc;
    if (u.fbp) user_data.fbp = u.fbp;

    const payload: Record<string, unknown> = {
      data: [
        {
          event_name: params.eventName,
          event_time: Math.floor(Date.now() / 1000),
          event_id: params.eventId,
          action_source: 'website',
          event_source_url: params.eventSourceUrl,
          user_data,
          custom_data: params.customData,
        },
      ],
    };

    if (testEventCode) {
      payload.test_event_code = testEventCode;
    }

    const url = `https://graph.facebook.com/${GRAPH_API_VERSION}/${pixelId}/events?access_token=${accessToken}`;

    const response = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload),
    });

    if (!response.ok) {
      const errorText = await response.text();
      console.error('Meta CAPI error:', response.status, errorText);
      return { success: false, error: `CAPI ${response.status}` };
    }

    return { success: true };
  } catch (error) {
    console.error('Meta CAPI exception:', error);
    return { success: false, error: 'CAPI exception' };
  }
}

/** Reads _fbc / _fbp values from a Cookie header string. */
export function parseFbCookies(cookieHeader?: string | null): {
  fbc?: string;
  fbp?: string;
} {
  if (!cookieHeader) return {};
  const result: { fbc?: string; fbp?: string } = {};
  for (const part of cookieHeader.split(';')) {
    const [rawKey, ...rest] = part.trim().split('=');
    const value = rest.join('=');
    if (rawKey === '_fbc') result.fbc = value;
    if (rawKey === '_fbp') result.fbp = value;
  }
  return result;
}
