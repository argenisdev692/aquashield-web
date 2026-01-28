/**
 * Astro Middleware - Security Headers
 * OWASP 2025 Compliant Security Headers
 */

import { defineMiddleware } from 'astro:middleware';

export const onRequest = defineMiddleware(async (context, next) => {
  const response = await next();

  // Content Security Policy (CSP) - OWASP 2025 Critical Control
  // Allows Google reCAPTCHA, Google Maps, and necessary assets
  const csp = [
    "default-src 'self'",
    "script-src 'self' 'unsafe-inline' 'unsafe-eval' https://www.google.com https://www.gstatic.com https://maps.googleapis.com https://connect.facebook.net",
    "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
    "img-src 'self' data: https: blob:",
    "font-src 'self' data: https://fonts.gstatic.com",
    "connect-src 'self' https://www.google.com https://maps.googleapis.com https://www.facebook.com",
    "frame-src 'self' https://www.google.com https://www.facebook.com",
    "object-src 'none'",
    "base-uri 'self'",
    "form-action 'self'",
    "frame-ancestors 'self'",
    "upgrade-insecure-requests"
  ].join('; ');

  response.headers.set('Content-Security-Policy', csp);

  // X-Content-Type-Options - Prevent MIME sniffing
  response.headers.set('X-Content-Type-Options', 'nosniff');

  // X-Frame-Options - Clickjacking protection
  response.headers.set('X-Frame-Options', 'SAMEORIGIN');

  // X-XSS-Protection - Enable browser XSS filtering
  response.headers.set('X-XSS-Protection', '1; mode=block');

  // Referrer-Policy - Control referrer information
  response.headers.set('Referrer-Policy', 'strict-origin-when-cross-origin');

  // Permissions-Policy - Control browser features
  const permissions = [
    'geolocation=(self)',
    'microphone=()',
    'camera=()',
    'payment=()',
    'usb=()',
    'magnetometer=()',
    'gyroscope=()',
    'accelerometer=()'
  ].join(', ');
  
  response.headers.set('Permissions-Policy', permissions);

  // Strict-Transport-Security (HSTS) - Force HTTPS in production
  if (import.meta.env.PROD) {
    response.headers.set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
  }

  // Remove identifying headers
  response.headers.delete('X-Powered-By');
  response.headers.delete('Server');

  return response;
});
