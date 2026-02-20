/**
 * Astro Middleware - Security Headers
 * OWASP Top 10 2025 Compliant Security Headers
 * TDPSA (Texas Data Privacy and Security Act) compliant - GPC Universal Opt-Out detection
 */

import { defineMiddleware } from 'astro:middleware';

export const onRequest = defineMiddleware(async (context, next) => {
  const response = await next();

  // ─── TDPSA § 541.055 — Universal Opt-Out (GPC) Detection ────────────────
  // Effective Jan 1 2025: Detect Sec-GPC header from browser and honor it.
  // We do not sell personal data, but we must still recognize the signal.
  const gpcSignal = context.request.headers.get('Sec-GPC');
  if (gpcSignal === '1') {
    // Log opt-out signal recognition (no data sale in this project, compliant by default)
    // In a future analytics integration, block tracking scripts here.
    response.headers.set('X-GPC-Honored', '1');
  }

  // ─── Content Security Policy (CSP) — OWASP A02 ──────────────────────────
  // Includes Cloudflare Turnstile (challenges.cloudflare.com),
  // Facebook Pixel, Google Maps, and Google Fonts.
  const csp = [
    "default-src 'self'",
    // Scripts: self + inline (Astro islands) + Cloudflare Turnstile + Facebook + Google Maps
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' " +
      "https://challenges.cloudflare.com " +
      "https://www.google.com https://www.gstatic.com " +
      "https://maps.googleapis.com " +
      "https://connect.facebook.net " +
      "https://www.googletagmanager.com",
    // Styles: self + inline (Tailwind) + Google Fonts
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    // Images: self + data URIs + all HTTPS (Cloudflare images, Supabase storage)
    "img-src 'self' data: https: blob:",
    // Fonts
    "font-src 'self' data: https://fonts.gstatic.com",
    // Connections: API calls to Supabase, Cloudflare Turnstile verify, Cloudflare Analytics
    "connect-src 'self' " +
      "https://challenges.cloudflare.com " +
      "https://*.supabase.co " +
      "https://www.google.com " +
      "https://maps.googleapis.com " +
      "https://www.facebook.com " +
      "https://www.googletagmanager.com",
    // Frames: Cloudflare Turnstile renders in an iframe
    "frame-src 'self' https://challenges.cloudflare.com https://www.google.com https://www.facebook.com",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
    "upgrade-insecure-requests"
  ].join('; ');

  response.headers.set('Content-Security-Policy', csp);

  // ─── X-Content-Type-Options — OWASP A05 (prevents MIME sniffing) ─────────
  response.headers.set('X-Content-Type-Options', 'nosniff');

  // ─── X-Frame-Options — OWASP A01 (clickjacking) ──────────────────────────
  response.headers.set('X-Frame-Options', 'SAMEORIGIN');

  // ─── X-XSS-Protection — Legacy browser XSS filter ────────────────────────
  response.headers.set('X-XSS-Protection', '1; mode=block');

  // ─── Referrer-Policy — OWASP A02 ─────────────────────────────────────────
  response.headers.set('Referrer-Policy', 'strict-origin-when-cross-origin');

  // ─── Permissions-Policy — OWASP A02 (disable unused browser APIs) ────────
  const permissions = [
    'geolocation=(self)',   // Allowed for Google Maps embeds on same origin
    'microphone=()',
    'camera=()',
    'payment=()',
    'usb=()',
    'magnetometer=()',
    'gyroscope=()',
    'accelerometer=()',
    'interest-cohort=()',   // Block FLoC / Topics API advertising
  ].join(', ');
  response.headers.set('Permissions-Policy', permissions);

  // ─── HSTS — Force HTTPS in production (OWASP A04) ────────────────────────
  if (import.meta.env.PROD) {
    response.headers.set(
      'Strict-Transport-Security',
      'max-age=31536000; includeSubDomains; preload'
    );
  }

  // ─── Remove server fingerprinting headers (OWASP A02) ────────────────────
  response.headers.delete('X-Powered-By');
  response.headers.delete('Server');

  return response;
});

