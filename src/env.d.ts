/// <reference types="astro/client" />

// Alpine.js global type declarations
interface Alpine {
  initTree: (el: HTMLElement) => void;
  start: () => void;
  store: (name: string, value?: unknown) => unknown;
  data: (name: string, callback: () => object) => void;
  // Add other Alpine methods you use as needed
  [key: string]: unknown;
}

declare global {
  const Alpine: Alpine | undefined;
  interface Window {
    Alpine: Alpine | undefined;
    openInspectionModal: () => void;
    closeInspectionModal: () => void;
    AquaShieldUtils: {
      applyPhoneFormatter: (input: HTMLInputElement) => void;
      showAlert: (options: { message: string; type: string }) => void;
    } | undefined;
  }
}

export {};
