import { useState, useEffect, type ChangeEvent } from 'react';

interface PhoneInputProps {
  value?: string;
  onChange?: (formattedValue: string, rawValue: string) => void;
  name?: string;
  id?: string;
  className?: string;
  placeholder?: string;
  required?: boolean;
  disabled?: boolean;
}

/**
 * PhoneInput Component
 * 
 * Formateo visual: (xxx) xxx-xxxx
 * Valor para backend: +1xxxxxxxxxx
 * 
 * @example
 * // Uso básico en un formulario
 * <PhoneInput 
 *   name="phone"
 *   onChange={(formatted, raw) => {
 *     console.log('Display:', formatted); // (713) 587-6423
 *     console.log('Backend:', raw);      // +17135876423
 *   }}
 * />
 */
export default function PhoneInput({
  value = '',
  onChange,
  name = 'phone',
  id,
  className = '',
  placeholder = '(___) ___-____',
  required = false,
  disabled = false,
}: PhoneInputProps) {
  const [displayValue, setDisplayValue] = useState('');

  // Formatea el número para mostrar
  const formatPhoneDisplay = (input: string): string => {
    // Elimina todo excepto números
    const numbers = input.replace(/\D/g, '');
    
    // Limita a 10 dígitos
    const limited = numbers.slice(0, 10);
    
    // Aplica formato (xxx) xxx-xxxx
    if (limited.length === 0) return '';
    if (limited.length <= 3) return `(${limited}`;
    if (limited.length <= 6) return `(${limited.slice(0, 3)}) ${limited.slice(3)}`;
    return `(${limited.slice(0, 3)}) ${limited.slice(3, 6)}-${limited.slice(6)}`;
  };

  // Convierte a formato backend +1xxxxxxxxxx
  const formatPhoneBackend = (input: string): string => {
    const numbers = input.replace(/\D/g, '').slice(0, 10);
    return numbers.length === 10 ? `+1${numbers}` : '';
  };

  // Maneja cambios en el input
  const handleChange = (e: ChangeEvent<HTMLInputElement>) => {
    const inputValue = e.target.value;
    
    // Formatea para display
    const formatted = formatPhoneDisplay(inputValue);
    setDisplayValue(formatted);
    
    // Formatea para backend y notifica al componente padre
    const backendValue = formatPhoneBackend(inputValue);
    if (onChange) {
      onChange(formatted, backendValue);
    }
  };

  // Sincroniza con el valor prop externo si cambia
  useEffect(() => {
    if (value) {
      setDisplayValue(formatPhoneDisplay(value));
    }
  }, [value]);

  return (
    <input
      type="tel"
      name={name}
      id={id || name}
      value={displayValue}
      onChange={handleChange}
      className={`w-full px-4 py-3 rounded-lg border border-gray-300 focus:border-aqua focus:ring-2 focus:ring-aqua/20 transition-colors ${className}`}
      placeholder={placeholder}
      required={required}
      disabled={disabled}
      autoComplete="tel"
    />
  );
}
