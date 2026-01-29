/**
 * Phone Formatter Utilities
 * 
 * Funciones para formatear números de teléfono:
 * - Display: (xxx) xxx-xxxx
 * - Backend: +1xxxxxxxxxx
 */

/**
 * Formatea un número de teléfono para mostrar en pantalla
 * @param input - Número de teléfono sin formato o parcialmente formateado
 * @returns Número formateado como (xxx) xxx-xxxx
 * 
 * @example
 * formatPhoneDisplay('7135876423')    // '(713) 587-6423'
 * formatPhoneDisplay('(713) 587-64')  // '(713) 587-64'
 */
export function formatPhoneDisplay(input: string): string {
  // Elimina todo excepto números
  const numbers = input.replace(/\D/g, '');
  
  // Limita a 10 dígitos
  const limited = numbers.slice(0, 10);
  
  // Aplica formato (xxx) xxx-xxxx
  if (limited.length === 0) return '';
  if (limited.length <= 3) return `(${limited}`;
  if (limited.length <= 6) return `(${limited.slice(0, 3)}) ${limited.slice(3)}`;
  return `(${limited.slice(0, 3)}) ${limited.slice(3, 6)}-${limited.slice(6)}`;
}

/**
 * Formatea un número de teléfono para enviar al backend
 * @param input - Número de teléfono en cualquier formato
 * @returns Número formateado como +1xxxxxxxxxx o cadena vacía si no hay 10 dígitos
 * 
 * @example
 * formatPhoneBackend('(713) 587-6423')  // '+17135876423'
 * formatPhoneBackend('713-587-6423')    // '+17135876423'
 * formatPhoneBackend('713')             // '' (incompleto)
 */
export function formatPhoneBackend(input: string): string {
  const numbers = input.replace(/\D/g, '').slice(0, 10);
  return numbers.length === 10 ? `+1${numbers}` : '';
}

/**
 * Extrae solo los números de un string
 * @param input - String que puede contener números y otros caracteres
 * @returns Solo los números del string
 * 
 * @example
 * extractNumbers('(713) 587-6423')  // '7135876423'
 */
export function extractNumbers(input: string): string {
  return input.replace(/\D/g, '');
}

/**
 * Valida si un número de teléfono tiene 10 dígitos
 * @param input - Número de teléfono en cualquier formato
 * @returns true si el número tiene exactamente 10 dígitos
 * 
 * @example
 * isValidPhone('(713) 587-6423')  // true
 * isValidPhone('713-587')         // false
 */
export function isValidPhone(input: string): boolean {
  const numbers = extractNumbers(input);
  return numbers.length === 10;
}

/**
 * Inicializa el formateo automático de teléfono en un input HTML
 * Esta función debe llamarse después de que el DOM esté listo
 * 
 * @param inputElement - Elemento input HTML o selector
 * @param onValueChange - Callback opcional que se ejecuta cuando el valor cambia
 * 
 * @example
 * // En un script de Astro
 * initPhoneInput('#phone-input', (formatted, backend) => {
 *   console.log('Display:', formatted);
 *   console.log('Backend:', backend);
 * });
 * 
 * @example
 * // Con elemento directo
 * const input = document.querySelector('#phone');
 * initPhoneInput(input);
 */
export function initPhoneInput(
  inputElement: HTMLInputElement | string,
  onValueChange?: (formattedDisplay: string, backendValue: string) => void
): void {
  const input = typeof inputElement === 'string' 
    ? document.querySelector<HTMLInputElement>(inputElement)
    : inputElement;

  if (!input) {
    console.error('Phone input element not found');
    return;
  }

  // Configurar atributos del input
  input.setAttribute('type', 'tel');
  input.setAttribute('autocomplete', 'tel');
  
  // Agregar placeholder si no tiene
  if (!input.placeholder) {
    input.placeholder = '(___) ___-____';
  }

  // Handler para el evento de input
  const handleInput = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const cursorPosition = target.selectionStart || 0;
    const oldValue = target.value;
    const oldLength = oldValue.length;

    // Formatear el valor
    const formatted = formatPhoneDisplay(target.value);
    target.value = formatted;

    // Calcular nueva posición del cursor
    const newLength = formatted.length;
    const lengthDiff = newLength - oldLength;
    let newCursorPosition = cursorPosition + lengthDiff;

    // Ajustar cursor para que no quede en los caracteres de formato
    if (formatted[newCursorPosition - 1] === ')' || formatted[newCursorPosition - 1] === ' ') {
      newCursorPosition += 1;
    }
    if (formatted[newCursorPosition - 1] === '-') {
      newCursorPosition += 1;
    }

    // Establecer la posición del cursor
    target.setSelectionRange(newCursorPosition, newCursorPosition);

    // Ejecutar callback si existe
    if (onValueChange) {
      const backendValue = formatPhoneBackend(formatted);
      onValueChange(formatted, backendValue);
    }
  };

  // Agregar event listener
  input.addEventListener('input', handleInput);

  // Formatear valor inicial si existe
  if (input.value) {
    input.value = formatPhoneDisplay(input.value);
  }
}

/**
 * Crea un hidden input con el valor formateado para backend
 * Útil para formularios que necesitan enviar el valor en formato +1xxxxxxxxxx
 * 
 * @param sourceInput - Input visible para el usuario
 * @param hiddenInputName - Nombre del hidden input (por defecto: nombre del source + '_formatted')
 * 
 * @example
 * // En un formulario Astro
 * <script>
 *   import { createHiddenPhoneInput } from '@/utils/phoneFormatter';
 *   
 *   document.addEventListener('DOMContentLoaded', () => {
 *     const phoneInput = document.querySelector('#phone');
 *     createHiddenPhoneInput(phoneInput, 'phone_backend');
 *   });
 * </script>
 */
export function createHiddenPhoneInput(
  sourceInput: HTMLInputElement | string,
  hiddenInputName?: string
): HTMLInputElement | null {
  const input = typeof sourceInput === 'string'
    ? document.querySelector<HTMLInputElement>(sourceInput)
    : sourceInput;

  if (!input || !input.form) {
    console.error('Source input not found or not part of a form');
    return null;
  }

  // Crear hidden input
  const hiddenInput = document.createElement('input');
  hiddenInput.type = 'hidden';
  hiddenInput.name = hiddenInputName || `${input.name}_formatted`;
  
  // Insertar después del input visible
  input.parentNode?.insertBefore(hiddenInput, input.nextSibling);

  // Actualizar hidden input cuando cambie el visible
  const updateHidden = () => {
    hiddenInput.value = formatPhoneBackend(input.value);
  };

  input.addEventListener('input', updateHidden);
  
  // Valor inicial
  updateHidden();

  return hiddenInput;
}
