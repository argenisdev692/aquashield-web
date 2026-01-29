# Contributing to AquaShield Restoration

First off, thank you for considering contributing to AquaShield Restoration! It's people like you that make this project better.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [How Can I Contribute?](#how-can-i-contribute)
- [Development Setup](#development-setup)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
- [Commit Guidelines](#commit-guidelines)
- [Pull Request Process](#pull-request-process)
- [Testing Guidelines](#testing-guidelines)
- [Documentation](#documentation)

---

## Code of Conduct

### Our Pledge

We pledge to make participation in our project a harassment-free experience for everyone, regardless of age, body size, disability, ethnicity, gender identity and expression, level of experience, nationality, personal appearance, race, religion, or sexual identity and orientation.

### Our Standards

**Examples of behavior that contributes to a positive environment:**

✅ Using welcoming and inclusive language  
✅ Being respectful of differing viewpoints and experiences  
✅ Gracefully accepting constructive criticism  
✅ Focusing on what is best for the community  
✅ Showing empathy towards other community members

**Examples of unacceptable behavior:**

❌ The use of sexualized language or imagery  
❌ Trolling, insulting/derogatory comments, and personal or political attacks  
❌ Public or private harassment  
❌ Publishing others' private information without explicit permission  
❌ Other conduct which could reasonably be considered inappropriate

---

## How Can I Contribute?

### Reporting Bugs

Before creating bug reports, please check the issue tracker to avoid duplicates. When you are creating a bug report, please include as many details as possible:

**Bug Report Template:**

```markdown
**Describe the bug**
A clear and concise description of what the bug is.

**To Reproduce**
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

**Expected behavior**
A clear description of what you expected to happen.

**Screenshots**
If applicable, add screenshots to help explain your problem.

**Environment:**
 - OS: [e.g. Windows 10]
 - Browser: [e.g. Chrome 120]
 - Node Version: [e.g. 22.20.0]
 - npm Version: [e.g. 10.8.1]

**Additional context**
Add any other context about the problem here.
```

### Suggesting Enhancements

Enhancement suggestions are tracked as GitHub issues. When creating an enhancement suggestion, please include:

**Enhancement Request Template:**

```markdown
**Is your feature request related to a problem?**
A clear description of the problem. Ex. I'm always frustrated when [...]

**Describe the solution you'd like**
A clear description of what you want to happen.

**Describe alternatives you've considered**
A clear description of any alternative solutions or features you've considered.

**Additional context**
Add any other context or screenshots about the feature request here.
```

### Pull Requests

Pull requests are the best way to propose changes to the codebase. We actively welcome your pull requests:

1. Fork the repo and create your branch from `develop`
2. If you've added code that should be tested, add tests
3. If you've changed APIs, update the documentation
4. Ensure the test suite passes
5. Make sure your code lints
6. Issue that pull request!

---

## Development Setup

### Prerequisites

```bash
Node.js >= 22.20.0
npm >= 10.8.1
Git >= 2.40.0
```

### Fork and Clone

```bash
# Fork the repository on GitHub, then:
git clone https://github.com/YOUR-USERNAME/aquashield-web.git
cd aquashield-web

# Add upstream remote
git remote add upstream https://github.com/aquashield/aquashield-web.git
```

### Install Dependencies

```bash
npm install
```

### Configure Environment

```bash
cp env.example .env
# Edit .env with your credentials
```

See [README.md#configuration](README.md#-configuration) for detailed setup.

### Start Development Server

```bash
npm run dev
```

---

## Development Workflow

### Branching Strategy

We use **Git Flow** with the following branches:

```
main          → Production-ready code (protected)
develop       → Integration branch for features
feature/*     → New features
bugfix/*      → Bug fixes
hotfix/*      → Urgent production fixes
release/*     → Release preparation
```

### Creating a New Feature

```bash
# Update develop branch
git checkout develop
git pull upstream develop

# Create feature branch
git checkout -b feature/your-feature-name

# Make changes...

# Commit changes
git add .
git commit -m "feat(scope): description"

# Push to your fork
git push origin feature/your-feature-name
```

### Keeping Your Branch Updated

```bash
# While on your feature branch
git fetch upstream
git rebase upstream/develop

# If there are conflicts, resolve them and:
git rebase --continue

# Force push (only for your own branches!)
git push origin feature/your-feature-name --force-with-lease
```

---

## Coding Standards

### TypeScript/JavaScript

We follow **Airbnb JavaScript Style Guide** with some modifications.

#### Naming Conventions

```typescript
// PascalCase for types, interfaces, classes
interface ContactFormData {}
class EmailService {}
type ValidationResult = {};

// camelCase for variables, functions
const firstName = 'John';
function sendEmail() {}

// UPPER_SNAKE_CASE for constants
const API_BASE_URL = 'https://api.example.com';
const MAX_RETRY_ATTEMPTS = 3;

// kebab-case for file names
// ✅ contact-form.ts
// ✅ email-service.ts
// ❌ ContactForm.ts
// ❌ email_service.ts
```

#### Function Guidelines

```typescript
// ✅ Good: Typed, documented, single responsibility
/**
 * Validates and submits contact form data
 * @param data - Form data to validate
 * @returns Promise resolving to submission result
 * @throws {ValidationError} If data is invalid
 */
export async function submitContactForm(
  data: ContactFormData
): Promise<SubmissionResult> {
  const validatedData = validateContactForm(data);
  return await saveToDatabase(validatedData);
}

// ❌ Bad: No types, unclear purpose, multiple responsibilities
export async function submit(data) {
  if (valid(data)) {
    await save(data);
    await email(data);
    return true;
  }
  return false;
}
```

#### Error Handling

```typescript
// ✅ Good: Specific error types, proper logging
try {
  await processForm(data);
} catch (error) {
  if (error instanceof ValidationError) {
    logger.warn('Validation failed', { error, data });
    throw new BadRequestError(error.message);
  }
  
  logger.error('Unexpected error', { error, data });
  throw new InternalServerError('Failed to process form');
}

// ❌ Bad: Generic catch, no logging
try {
  await process(data);
} catch (e) {
  throw e;
}
```

### Astro Components

```astro
---
// ✅ Good: Clear interface, typed props, organized imports
import Layout from '@/layouts/Layout.astro';
import Button from '@/components/ui/button.tsx';

interface Props {
  title: string;
  description?: string;
  variant?: 'primary' | 'secondary';
  class?: string;
}

const { 
  title, 
  description, 
  variant = 'primary', 
  class: className 
} = Astro.props;
---

<Layout title={title}>
  <section class:list={["section", className]}>
    <h2>{title}</h2>
    {description && <p>{description}</p>}
    <Button variant={variant}>Learn More</Button>
  </section>
</Layout>

<style>
  .section {
    padding: 2rem;
  }
</style>
```

### React Components

```typescript
// ✅ Good: TypeScript, functional component, proper hooks
import { useState, useCallback } from 'react';

interface CounterProps {
  initialValue?: number;
  max?: number;
}

export function Counter({ initialValue = 0, max = 10 }: CounterProps) {
  const [count, setCount] = useState(initialValue);
  
  const increment = useCallback(() => {
    setCount(prev => Math.min(prev + 1, max));
  }, [max]);
  
  return (
    <div className="flex items-center gap-4">
      <button onClick={increment} className="btn-primary">
        Increment
      </button>
      <span className="text-2xl font-bold">{count}</span>
    </div>
  );
}
```

### CSS/Tailwind

```html
<!-- ✅ Good: Utility classes, logical order, responsive -->
<button 
  class="
    px-6 py-3 
    bg-aqua text-white 
    rounded-lg 
    hover:bg-aqua/90 
    transition-colors 
    md:px-8 md:py-4
  "
>
  Submit
</button>

<!-- ❌ Bad: Arbitrary values, no order, not responsive -->
<button class="text-white bg-[#00b5e2] py-3 px-6 rounded-lg">
  Submit
</button>
```

**Utility Class Order:**
1. Layout (flex, grid, display)
2. Spacing (margin, padding)
3. Sizing (width, height)
4. Typography (font, text)
5. Visual (background, border)
6. Effects (shadow, opacity)
7. Interactivity (hover, focus)
8. Responsive modifiers (md:, lg:)

### File Organization

```
src/
├── components/
│   ├── ui/              # Reusable UI components (React)
│   ├── [Feature].astro  # Feature-specific components
│   └── [Layout].astro   # Layout components
│
├── layouts/             # Page layouts
│   └── Layout.astro
│
├── lib/                 # Shared libraries
│   ├── supabase.ts     # External service clients
│   └── utils.ts        # Utility functions
│
├── pages/              # File-based routing
│   ├── api/           # API endpoints
│   └── [route].astro  # Page components
│
├── styles/            # Global styles
│   └── global.css
│
└── utils/             # Business logic utilities
    ├── validation.ts  # Zod schemas
    └── email.ts       # Email service
```

---

## Commit Guidelines

We follow **Conventional Commits** specification.

### Format

```
<type>(<scope>): <subject>

<body>

<footer>
```

### Type

Must be one of:

- **feat**: A new feature
- **fix**: A bug fix
- **docs**: Documentation only changes
- **style**: Changes that don't affect code meaning (formatting, etc.)
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **perf**: Performance improvement
- **test**: Adding missing tests or correcting existing tests
- **build**: Changes to build system or dependencies
- **ci**: Changes to CI configuration
- **chore**: Other changes that don't modify src or test files

### Scope

The scope should specify the place of the commit change:

- **contact**: Contact form
- **lead**: Lead generation
- **email**: Email service
- **auth**: Authentication
- **api**: API endpoints
- **ui**: UI components
- **security**: Security features
- **perf**: Performance improvements

### Subject

- Use imperative, present tense: "change" not "changed" nor "changes"
- Don't capitalize first letter
- No period (.) at the end
- Maximum 72 characters

### Body

- Use imperative, present tense
- Include motivation for the change
- Contrast with previous behavior
- Wrap at 72 characters

### Footer

- Reference issues: `Fixes #123` or `Closes #456`
- Breaking changes: `BREAKING CHANGE: description`

### Examples

```bash
# Feature
feat(contact): add reCAPTCHA validation

- Integrate Google reCAPTCHA v3
- Add score threshold (0.5)
- Update API endpoint validation

Closes #123

# Bug Fix
fix(email): resolve SMTP connection timeout

- Increase connection timeout to 10s
- Add retry logic (3 attempts)
- Improve error logging

Fixes #456

# Documentation
docs(readme): update installation instructions

Add detailed steps for Windows users

# Breaking Change
feat(api)!: change contact API response format

BREAKING CHANGE: Response now uses camelCase instead of snake_case

Before:
{ "first_name": "John" }

After:
{ "firstName": "John" }

Migration guide: ...

Refs #789
```

---

## Pull Request Process

### Before Submitting

1. ✅ Update documentation
2. ✅ Add/update tests
3. ✅ Run linter: `npm run lint`
4. ✅ Run tests: `npm test`
5. ✅ Build successfully: `npm run build`
6. ✅ Test in browser manually
7. ✅ Update CHANGELOG.md (if applicable)

### PR Title

Follow commit message format:

```
feat(contact): add email validation
fix(api): handle network errors gracefully
docs(contributing): add PR guidelines
```

### PR Description

Use this template:

```markdown
## Description
Brief description of what this PR does

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## Motivation and Context
Why is this change required? What problem does it solve?
Fixes #(issue)

## How Has This Been Tested?
- [ ] Unit tests
- [ ] Integration tests
- [ ] Manual testing in browser

## Screenshots (if appropriate)
[Add screenshots here]

## Checklist
- [ ] My code follows the code style of this project
- [ ] I have updated the documentation accordingly
- [ ] I have added tests to cover my changes
- [ ] All new and existing tests passed
- [ ] My changes generate no new warnings
```

### Review Process

1. **Automated Checks**: All CI checks must pass
2. **Code Review**: At least 1 approval from maintainer
3. **Discussion**: Address all review comments
4. **Approval**: PR approved by maintainer
5. **Merge**: Squash and merge into develop

### After Merge

1. Delete your feature branch
2. Pull latest develop: `git pull upstream develop`
3. Update your fork: `git push origin develop`

---

## Testing Guidelines

### Unit Tests

**Location:** `src/**/*.test.ts`

```typescript
// src/utils/validation.test.ts
import { describe, it, expect } from 'vitest';
import { validateEmail } from './validation';

describe('validateEmail', () => {
  it('should accept valid email', () => {
    expect(validateEmail('test@example.com')).toBe(true);
  });
  
  it('should reject invalid email', () => {
    expect(validateEmail('invalid')).toBe(false);
  });
  
  it('should reject empty string', () => {
    expect(validateEmail('')).toBe(false);
  });
});
```

### Integration Tests

**Location:** `tests/**/*.spec.ts`

```typescript
// tests/contact-form.spec.ts
import { test, expect } from '@playwright/test';

test.describe('Contact Form', () => {
  test('should submit successfully with valid data', async ({ page }) => {
    await page.goto('/contact');
    
    await page.fill('#first_name', 'John');
    await page.fill('#last_name', 'Doe');
    await page.fill('#email', 'john@example.com');
    await page.fill('#phone', '5551234567');
    await page.fill('#message', 'Test message');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.success-message')).toBeVisible();
  });
  
  test('should show validation errors for invalid data', async ({ page }) => {
    await page.goto('/contact');
    
    await page.fill('#email', 'invalid-email');
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.error-message')).toContainText('Invalid email');
  });
});
```

### Test Coverage

Aim for:
- **80%+ overall coverage**
- **100% coverage for critical paths**
  - Form validation
  - API endpoints
  - Security functions
  - Payment processing (if applicable)

---

## Documentation

### Code Comments

```typescript
// ✅ Good: Explain WHY, not WHAT
// Use rate limiting to prevent abuse. 
// Threshold: 3 requests per hour per IP
const submissions = await checkRateLimit(ip);

// ❌ Bad: States the obvious
// Get submissions
const submissions = await checkRateLimit(ip);
```

### JSDoc for Public APIs

```typescript
/**
 * Submits contact form data to the database and sends notification email
 * 
 * @param data - Contact form data from user input
 * @param options - Optional configuration
 * @param options.skipNotification - Skip sending email notification
 * @returns Promise resolving to created contact record
 * 
 * @throws {ValidationError} If data fails validation
 * @throws {DatabaseError} If database operation fails
 * @throws {EmailError} If email sending fails
 * 
 * @example
 * ```typescript
 * const contact = await submitContactForm({
 *   firstName: 'John',
 *   lastName: 'Doe',
 *   email: 'john@example.com',
 *   phone: '5551234567',
 *   message: 'Need help'
 * });
 * ```
 */
export async function submitContactForm(
  data: ContactFormData,
  options?: SubmitOptions
): Promise<ContactSupport> {
  // ...
}
```

### README Updates

When adding new features:

1. Update **Features** section
2. Add to **Table of Contents**
3. Update **Configuration** if new env vars
4. Add **Usage Examples**
5. Update **Architecture** diagrams if needed

---

## Questions?

Feel free to:

- 💬 Open a [GitHub Discussion](https://github.com/aquashield/aquashield-web/discussions)
- 📧 Email: dev@aquashieldrestorationusa.com
- 🐛 Report bugs: [GitHub Issues](https://github.com/aquashield/aquashield-web/issues)

---

**Thank you for contributing to AquaShield Restoration!** 🎉

