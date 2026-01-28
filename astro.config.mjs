// @ts-check
import { defineConfig } from 'astro/config';

import react from '@astrojs/react';

import tailwindcss from '@tailwindcss/vite';

// https://astro.build/config
export default defineConfig({
  integrations: [react()],
  
  // Output mode for API endpoints
  output: 'server',
  
  // Disable dev toolbar
  devToolbar: {
    enabled: false
  },

  // Security headers via middleware
  // Configured in src/middleware.ts

  vite: {
    plugins: [tailwindcss()]
  }
});