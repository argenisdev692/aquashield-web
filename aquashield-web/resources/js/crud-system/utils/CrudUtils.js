// Funciones utilitarias generales para el sistema CRUD
export const CrudUtils = {
    debounce(func, wait) {
        let timeout;
        return function (...args) {
            clearTimeout(timeout);
            timeout = setTimeout(() => func.apply(this, args), wait);
        };
    },
    // Puedes agregar más utilidades aquí (formateo, helpers, etc.)
};

// Utilidades generales para el sistema CRUD
export function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

export function formatPhoneForDisplay(phone) {
    if (!phone) return "";

    // Extraer solo los dígitos
    const cleaned = phone.replace(/\D/g, "");

    // Si tiene 11 dígitos y empieza con 1 (formato +1XXXXXXXXXX)
    if (cleaned.length === 11 && cleaned.startsWith("1")) {
        const phoneDigits = cleaned.substring(1); // Remover el 1
        return `(${phoneDigits.substring(0, 3)}) ${phoneDigits.substring(
            3,
            6
        )}-${phoneDigits.substring(6, 10)}`;
    }
    // Si tiene 10 dígitos (formato XXXXXXXXXX)
    else if (cleaned.length === 10) {
        return `(${cleaned.substring(0, 3)}) ${cleaned.substring(
            3,
            6
        )}-${cleaned.substring(6, 10)}`;
    }

    // Para otros formatos, devolver tal como está
    return phone;
}

export function formatPhoneForStorage(phone) {
    if (!phone) return "";

    // Extraer solo los dígitos para enviar al backend
    const cleaned = phone.replace(/\D/g, "");

    // El backend espera solo dígitos y él se encarga del formato +1XXXXXXXXXX
    if (cleaned.length === 10) {
        return cleaned; // Enviar solo los 10 dígitos
    }

    // Si ya tiene 11 dígitos y empieza con 1, enviar tal como está
    if (cleaned.length === 11 && cleaned.startsWith("1")) {
        return cleaned;
    }

    return cleaned;
}

export function capitalizeText(text) {
    return text.replace(/\b\w/g, (match) => match.toUpperCase());
}

export function __(key, fallback = "") {
    if (
        typeof window.translations !== "undefined" &&
        window.translations[key]
    ) {
        return window.translations[key];
    }
    return fallback || key;
}
