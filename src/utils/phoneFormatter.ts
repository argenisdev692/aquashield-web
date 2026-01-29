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
 * Convierte un número de teléfono al formato backend +1xxxxxxxxxx
 * @param value - Valor del teléfono (formateado o no)
 * @returns Número en formato E.164 para USA (+1xxxxxxxxxx) o string vacío si incompleto
 */
export function formatPhoneForBackend(value: string): string {
  const numbers = value.replace(/\D/g, '').slice(0, 10);
  return numbers.length === 10 ? `+1${numbers}` : numbers;
}

/**
 * Extrae solo dígitos de un número de teléfono
 * @param value - Valor del teléfono (formateado o no)
 * @returns Solo los dígitos (máximo 10)
 */
export function extractPhoneDigits(value: string): string {
  return value.replace(/\D/g, '').slice(0, 10);
}

/**
 * Aplica formateo automático a un input de teléfono
 * Almacena el valor para backend en data-phone-backend
 * @param input - Elemento HTMLInputElement
 */
export function applyPhoneFormatter(input: HTMLInputElement): void {
  input.addEventListener('input', (e) => {
    const target = e.target as HTMLInputElement;
    const formatted = formatPhoneNumber(target.value);
    target.value = formatted;
    // Store backend value in data attribute for form submission
    target.dataset.phoneBackend = formatPhoneForBackend(target.value);
  });
  
  // Initialize data attribute with current value
  if (input.value) {
    input.dataset.phoneBackend = formatPhoneForBackend(input.value);
  }
}

/**
 * Obtiene el valor para backend de un input de teléfono
 * @param input - Elemento HTMLInputElement con phone formatter aplicado
 * @returns Número en formato +1xxxxxxxxxx
 */
export function getPhoneBackendValue(input: HTMLInputElement): string {
  return input.dataset.phoneBackend || formatPhoneForBackend(input.value);
}

/**
 * Inicializa el formateo de teléfono en todos los inputs con clase 'phone-input'
 */
export function initPhoneFormatters(): void {
  document.querySelectorAll<HTMLInputElement>('input[type="tel"], .phone-input').forEach(input => {
    applyPhoneFormatter(input);
  });
}
