/**
 * Formatea un número de teléfono al formato (xxx) xxx-xxxx
 * @param value - Valor del input (puede contener caracteres no numéricos)
 * @returns Número formateado o string vacío
 */
export function formatPhoneNumber(value: string): string {
  const numbers = value.replace(/\D/g, '');
  const limited = numbers.slice(0, 10);
  
  if (limited.length === 0) return '';
  if (limited.length <= 3) return `(${limited}`;
  if (limited.length <= 6) return `(${limited.slice(0, 3)}) ${limited.slice(3)}`;
  return `(${limited.slice(0, 3)}) ${limited.slice(3, 6)}-${limited.slice(6)}`;
}

/**
 * Aplica formateo automático a un input de teléfono
 * @param input - Elemento HTMLInputElement
 */
export function applyPhoneFormatter(input: HTMLInputElement): void {
  input.addEventListener('input', (e) => {
    const target = e.target as HTMLInputElement;
    const formatted = formatPhoneNumber(target.value);
    target.value = formatted;
  });
}

/**
 * Inicializa el formateo de teléfono en todos los inputs con clase 'phone-input'
 */
export function initPhoneFormatters(): void {
  document.querySelectorAll<HTMLInputElement>('input[type="tel"], .phone-input').forEach(input => {
    applyPhoneFormatter(input);
  });
}
