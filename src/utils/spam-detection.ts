/**
 * Anti-Spam Utilities for Astro + Supabase
 * OWASP 2025 Compliant Spam Detection
 */

import { getServerSupabase } from '../lib/supabase';

/**
 * Spam keywords to detect in messages
 */
const SPAM_KEYWORDS = [
  'buy', 'sale', 'offer', 'discount', 'promo', 'click here',
  'visit', 'website', 'seo', 'marketing', 'viagra', 'cialis',
  'pharmacy', 'casino', 'poker', 'loan', 'crypto', 'bitcoin',
  'investment', 'earn money', 'work from home', 'make money',
  'free money', 'win prize', 'congratulations', 'winner',
];

/**
 * Test/fake patterns to block
 */
const TEST_PATTERNS = {
  email: /test@|example@|fake@|sample@|temp@|mailinator|guerrilla/i,
  name: /test|asdf|qwerty|fake|dummy|lorem|ipsum/i,
  phone: /\(123\)|^(555)|0000|1234567890/,
  address: /test|example|fake|asdf|lorem|ipsum/i,
};

/**
 * Suspicious User-Agent strings
 */
const SUSPICIOUS_AGENTS = [
  'curl', 'wget', 'python-requests', 'go-http-client', 'scrapy',
  'bot', 'spider', 'crawler', 'selenium', 'phantomjs', 'headless'
];

interface SpamCheckResult {
  isSpam: boolean;
  reason?: string;
  score: number; // 0-100, higher = more likely spam
}

/**
 * Check if honeypot field was filled (indicates bot)
 */
export function checkHoneypot(honeypotValue: any): SpamCheckResult {
  if (honeypotValue && honeypotValue !== '') {
    return {
      isSpam: true,
      reason: 'Honeypot field filled',
      score: 100
    };
  }
  return { isSpam: false, score: 0 };
}

/**
 * Detect spam keywords in message content
 */
export function detectSpamContent(message: string): SpamCheckResult {
  if (!message || message.trim().length === 0) {
    return { isSpam: false, score: 0 };
  }

  const messageLower = message.toLowerCase();
  let score = 0;
  const foundKeywords: string[] = [];

  // Check for spam keywords
  for (const keyword of SPAM_KEYWORDS) {
    if (messageLower.includes(keyword)) {
      foundKeywords.push(keyword);
      score += 15;
    }
  }

  // Check for URLs in message
  if (/(https?:\/\/|www\.)/i.test(message)) {
    score += 25;
    foundKeywords.push('URL');
  }

  // Check for excessive special characters
  const specialChars = (message.match(/[^a-zA-Z0-9\s\.,!?\-]/g) || []).length;
  const specialCharRatio = specialChars / message.length;
  if (specialCharRatio > 0.3) {
    score += 20;
    foundKeywords.push('excessive special chars');
  }

  // Check for excessive capitalization (SPAM!)
  const upperCaseRatio = (message.match(/[A-Z]/g) || []).length / message.length;
  if (upperCaseRatio > 0.5 && message.length > 20) {
    score += 15;
    foundKeywords.push('excessive caps');
  }

  // Check for repeated characters (!!!!, ????)
  if (/(.)\1{4,}/.test(message)) {
    score += 10;
    foundKeywords.push('repeated chars');
  }

  return {
    isSpam: score >= 50,
    reason: foundKeywords.length > 0 
      ? `Spam indicators: ${foundKeywords.join(', ')}`
      : undefined,
    score
  };
}

/**
 * Validate input against test/fake patterns
 */
export function detectTestData(data: {
  email?: string;
  first_name?: string;
  last_name?: string;
  phone?: string;
  address?: string;
}): SpamCheckResult {
  let score = 0;
  const reasons: string[] = [];

  if (data.email && TEST_PATTERNS.email.test(data.email)) {
    score += 40;
    reasons.push('test email');
  }

  if (data.first_name && TEST_PATTERNS.name.test(data.first_name)) {
    score += 30;
    reasons.push('test first name');
  }

  if (data.last_name && TEST_PATTERNS.name.test(data.last_name)) {
    score += 30;
    reasons.push('test last name');
  }

  if (data.phone && TEST_PATTERNS.phone.test(data.phone)) {
    score += 35;
    reasons.push('test phone');
  }

  if (data.address && TEST_PATTERNS.address.test(data.address)) {
    score += 25;
    reasons.push('test address');
  }

  return {
    isSpam: score >= 40,
    reason: reasons.length > 0 ? `Test data detected: ${reasons.join(', ')}` : undefined,
    score
  };
}

/**
 * Check User-Agent for suspicious patterns
 */
export function checkUserAgent(userAgent: string | null): SpamCheckResult {
  if (!userAgent) {
    return {
      isSpam: false,
      reason: 'No User-Agent provided',
      score: 10
    };
  }

  const userAgentLower = userAgent.toLowerCase();
  
  for (const agent of SUSPICIOUS_AGENTS) {
    if (userAgentLower.includes(agent)) {
      return {
        isSpam: true,
        reason: `Suspicious User-Agent: ${agent}`,
        score: 90
      };
    }
  }

  return { isSpam: false, score: 0 };
}

/**
 * Rate limiting using Supabase
 * Check if IP has exceeded submission limit
 */
export async function checkRateLimit(
  ipAddress: string,
  maxSubmissions: number = 3,
  windowMinutes: number = 60
): Promise<{ allowed: boolean; remaining: number; resetAt?: Date }> {
  try {
    const supabase = getServerSupabase();
    const now = new Date();
    const windowStart = new Date(now.getTime() - windowMinutes * 60 * 1000);

    // Count submissions from this IP in the time window
    const { count, error } = await supabase
      .from('form_submissions')
      .select('*', { count: 'exact', head: true })
      .eq('ip_address', ipAddress)
      .gte('created_at', windowStart.toISOString());

    if (error) {
      console.error('Rate limit check error:', error);
      // Fail open - allow the request if we can't check
      return { allowed: true, remaining: maxSubmissions };
    }

    const submissionCount = count || 0;
    const remaining = Math.max(0, maxSubmissions - submissionCount);
    const allowed = submissionCount < maxSubmissions;

    return {
      allowed,
      remaining,
      resetAt: new Date(now.getTime() + windowMinutes * 60 * 1000)
    };
  } catch (error) {
    console.error('Rate limit error:', error);
    // Fail open
    return { allowed: true, remaining: maxSubmissions };
  }
}

/**
 * Log submission attempt to Supabase for rate limiting and analysis
 */
export async function logSubmission(data: {
  ipAddress: string;
  userAgent: string | null;
  formType: string;
  isSpam: boolean;
  spamScore: number;
  spamReason?: string;
  email?: string;
}): Promise<void> {
  try {
    const supabase = getServerSupabase();
    
    await supabase
      .from('form_submissions')
      .insert([{
        ip_address: data.ipAddress,
        user_agent: data.userAgent,
        form_type: data.formType,
        is_spam: data.isSpam,
        spam_score: data.spamScore,
        spam_reason: data.spamReason,
        email: data.email,
        created_at: new Date().toISOString()
      }]);
  } catch (error) {
    console.error('Failed to log submission:', error);
    // Don't throw - logging failure shouldn't block the request
  }
}

/**
 * Get client IP address from request
 */
export function getClientIP(request: Request): string {
  // Check common headers for real IP
  const headers = request.headers;
  
  const cfConnectingIP = headers.get('cf-connecting-ip'); // Cloudflare
  if (cfConnectingIP) {
    return cfConnectingIP;
  }

  const realIP = headers.get('x-real-ip');
  if (realIP) {
    return realIP;
  }
  
  const forwardedFor = headers.get('x-forwarded-for');
  if (forwardedFor) {
    return forwardedFor.split(',')[0].trim();
  }

  return 'unknown';
}

/**
 * Comprehensive spam check combining all methods
 */
export async function performSpamCheck(data: {
  request: Request;
  honeypot?: any;
  message?: string;
  email?: string;
  first_name?: string;
  last_name?: string;
  phone?: string;
  address?: string;
  formType: string;
}): Promise<{
  isSpam: boolean;
  totalScore: number;
  reasons: string[];
  rateLimit: { allowed: boolean; remaining: number };
}> {
  const ipAddress = getClientIP(data.request);
  const userAgent = data.request.headers.get('user-agent');
  
  const checks = {
    honeypot: checkHoneypot(data.honeypot),
    content: data.message ? detectSpamContent(data.message) : { isSpam: false, score: 0 },
    testData: detectTestData({
      email: data.email,
      first_name: data.first_name,
      last_name: data.last_name,
      phone: data.phone,
      address: data.address
    }),
    userAgent: checkUserAgent(userAgent)
  };

  const rateLimit = await checkRateLimit(ipAddress);

  const totalScore = 
    checks.honeypot.score +
    checks.content.score +
    checks.testData.score +
    checks.userAgent.score;

  const reasons = [
    checks.honeypot.reason,
    checks.content.reason,
    checks.testData.reason,
    checks.userAgent.reason
  ].filter(Boolean) as string[];

  if (!rateLimit.allowed) {
    reasons.push('Rate limit exceeded');
  }

  const isSpam = totalScore >= 50 || !rateLimit.allowed || checks.honeypot.isSpam;

  // Log the submission attempt
  await logSubmission({
    ipAddress,
    userAgent,
    formType: data.formType,
    isSpam,
    spamScore: totalScore,
    spamReason: reasons.join('; '),
    email: data.email
  });

  return {
    isSpam,
    totalScore,
    reasons,
    rateLimit
  };
}
