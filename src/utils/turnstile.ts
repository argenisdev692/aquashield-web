/**
 * turnstile.ts
 * Server-side Cloudflare Turnstile verification utility.
 * Docs: https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
 */

export interface TurnstileResult {
  success: boolean;
  message?: string;
}

/**
 * Verify a Cloudflare Turnstile token server-side.
 * @param token  The `cf-turnstile-response` token sent by the client widget.
 * @param ip     Optional: the visitor's IP address (improves accuracy).
 */
export async function verifyTurnstile(
  token: string | undefined | null,
  ip?: string
): Promise<TurnstileResult> {
  const secretKey = import.meta.env.TURNSTILE_SECRET_KEY;

  if (!secretKey) {
    console.error('[turnstile] TURNSTILE_SECRET_KEY env var is missing');
    return { success: false, message: 'Server configuration error' };
  }

  if (!token) {
    return { success: false, message: 'CAPTCHA token is missing' };
  }

  try {
    const body = new URLSearchParams({
      secret: secretKey,
      response: token,
      ...(ip ? { remoteip: ip } : {}),
    });

    const res = await fetch(
      'https://challenges.cloudflare.com/turnstile/v1/siteverify',
      {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: body.toString(),
      }
    );

    const data = await res.json() as { success: boolean; 'error-codes'?: string[] };

    console.log('[turnstile] verification result:', {
      success: data.success,
      errors: data['error-codes'],
    });

    return {
      success: data.success,
      message: data.success
        ? 'Verification successful'
        : `Verification failed: ${(data['error-codes'] ?? []).join(', ')}`,
    };
  } catch (err) {
    console.error('[turnstile] fetch error:', err);
    return { success: false, message: 'CAPTCHA verification request failed' };
  }
}
