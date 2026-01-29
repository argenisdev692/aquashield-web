// @ts-check
import { defineConfig } from 'astro/config';

import react from '@astrojs/react';

import tailwindcss from '@tailwindcss/vite';

import cloudflare from '@astrojs/cloudflare';

// https://astro.build/config
export default defineConfig({
  integrations: [react()],
  
  // Output mode for API endpoints
  output: 'server',
  
  // Cloudflare Pages adapter with optimizations
  adapter: cloudflare({
    platformProxy: {
      enabled: true
    },
    imageService: 'compile', // Use compile-time image optimization
  }),
  
  // Disable dev toolbar
  devToolbar: {
    enabled: false
  },

  // Build optimizations
  build: {
    inlineStylesheets: 'auto', // Inline small CSS automatically
  },

  // Security headers via middleware
  // Configured in src/middleware.ts

  vite: {
    plugins: [tailwindcss()],
    build: {
      cssCodeSplit: true, // Split CSS per route
      rollupOptions: {
        output: {
          manualChunks: {
            // Split vendor code
            'react-vendor': ['react', 'react-dom'],
          }
        }
      }
    }
  }
});