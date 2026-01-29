// Global utilities for forms - loaded inline
window.AquaShieldUtils = {
  // Format phone number to (xxx) xxx-xxxx
  formatPhoneNumber(value) {
    const numbers = value.replace(/\D/g, '');
    const limited = numbers.slice(0, 10);
    
    if (limited.length === 0) return '';
    if (limited.length <= 3) return `(${limited}`;
    if (limited.length <= 6) return `(${limited.slice(0, 3)}) ${limited.slice(3)}`;
    return `(${limited.slice(0, 3)}) ${limited.slice(3, 6)}-${limited.slice(6)}`;
  },

  // Apply phone formatting to an input
  applyPhoneFormatter(input) {
    if (!input) return;
    input.addEventListener('input', (e) => {
      const formatted = window.AquaShieldUtils.formatPhoneNumber(e.target.value);
      e.target.value = formatted;
    });
  },

  // Get backend phone format (+1xxxxxxxxxx)
  getPhoneBackendValue(input) {
    if (!input) return '';
    const numbers = input.value.replace(/\D/g, '').slice(0, 10);
    return numbers.length === 10 ? `+1${numbers}` : '';
  },

  // Show modern alert
  showAlert({ message, type, duration = 5000 }) {
    const existingContainer = document.getElementById('alert-container');
    if (existingContainer) {
      existingContainer.remove();
    }

    const container = document.createElement('div');
    container.id = 'alert-container';
    container.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      z-index: 9999;
      max-width: 400px;
      width: calc(100% - 40px);
    `;

    const colors = {
      success: { bg: '#10B981', icon: '✓' },
      error: { bg: '#EF4444', icon: '✕' },
      info: { bg: '#3B82F6', icon: 'ℹ' },
      warning: { bg: '#F59E0B', icon: '⚠' }
    };

    const { bg, icon } = colors[type];

    const alert = document.createElement('div');
    alert.style.cssText = `
      background: ${bg};
      color: white;
      padding: 16px 20px;
      border-radius: 12px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.2);
      display: flex;
      align-items: center;
      gap: 12px;
      animation: slideIn 0.3s ease-out;
      font-family: system-ui, -apple-system, sans-serif;
      font-size: 15px;
      line-height: 1.5;
    `;

    alert.innerHTML = `
      <div style="
        width: 24px;
        height: 24px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
      ">${icon}</div>
      <div style="flex: 1;">${message}</div>
      <button onclick="this.parentElement.parentElement.remove()" style="
        width: 24px;
        height: 24px;
        border: none;
        background: rgba(255,255,255,0.2);
        color: white;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        line-height: 1;
        flex-shrink: 0;
        transition: background 0.2s;
      " onmouseover="this.style.background='rgba(255,255,255,0.3)'" onmouseout="this.style.background='rgba(255,255,255,0.2)'">×</button>
    `;

    container.appendChild(alert);
    document.body.appendChild(container);

    if (!document.getElementById('alert-animations')) {
      const style = document.createElement('style');
      style.id = 'alert-animations';
      style.textContent = `
        @keyframes slideIn {
          from {
            transform: translateX(400px);
            opacity: 0;
          }
          to {
            transform: translateX(0);
            opacity: 1;
          }
        }
        @keyframes slideOut {
          from {
            transform: translateX(0);
            opacity: 1;
          }
          to {
            transform: translateX(400px);
            opacity: 0;
          }
        }
      `;
      document.head.appendChild(style);
    }

    if (duration > 0) {
      setTimeout(() => {
        alert.style.animation = 'slideOut 0.3s ease-out';
        setTimeout(() => container.remove(), 300);
      }, duration);
    }
  }
};
