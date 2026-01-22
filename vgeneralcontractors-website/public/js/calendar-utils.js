/**
 * Calendar Utilities Module
 * Funciones de utilidad para el calendario
 */

class CalendarUtils {
    /**
     * Formatear número de teléfono
     */
    static formatPhoneNumber(value) {
        if (!value) return value;

        // Remover todo excepto números
        const phoneNumber = value.replace(/[^\d]/g, "");

        // Verificar que tenga 10 dígitos
        if (phoneNumber.length !== 10) return value;

        // Formatear como (XXX) XXX-XXXX
        return `(${phoneNumber.slice(0, 3)}) ${phoneNumber.slice(
            3,
            6
        )}-${phoneNumber.slice(6, 10)}`;
    }

    /**
     * Formatear fecha para mostrar
     */
    static formatDisplayDate(date) {
        if (!date) return "";

        const d = new Date(date);
        return d.toLocaleDateString("en-US", {
            weekday: "long",
            year: "numeric",
            month: "long",
            day: "numeric",
        });
    }

    /**
     * Formatear hora para mostrar
     */
    static formatDisplayTime(time) {
        if (!time) return "";

        const [hours, minutes] = time.split(":");
        const d = new Date();
        d.setHours(parseInt(hours), parseInt(minutes));

        return d.toLocaleTimeString("en-US", {
            hour: "numeric",
            minute: "2-digit",
            hour12: true,
        });
    }

    /**
     * Formatear fecha y hora completa
     */
    static formatDateTime(date, time) {
        if (!date || !time) return "";

        const displayDate = this.formatDisplayDate(date);
        const displayTime = this.formatDisplayTime(time);

        return `${displayDate} at ${displayTime}`;
    }

    /**
     * Validar email
     */
    static isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    /**
     * Validar teléfono
     */
    static isValidPhone(phone) {
        const phoneRegex = /^\(?([0-9]{3})\)?[-. ]?([0-9]{3})[-. ]?([0-9]{4})$/;
        return phoneRegex.test(phone);
    }

    /**
     * Obtener color del evento según el estado
     */
    static getEventColor(status) {
        const colorMap = {
            New: "#3b82f6", // blue-500
            Called: "#10b981", // emerald-500
            Pending: "#f59e0b", // amber-500
            Confirmed: "#8b5cf6", // violet-500
            Completed: "#059669", // emerald-600
            Declined: "#ef4444", // red-500
            Cancelled: "#6b7280", // gray-500
        };

        return colorMap[status] || "#6b7280";
    }

    /**
     * Crear tooltip de evento
     */
    static createEventTooltip(event) {
        const props = event.extendedProps;

        let tooltip = `<div class="text-left">`;
        tooltip += `<div class="font-bold">${event.title}</div>`;

        if (props.clientEmail) {
            tooltip += `<div class="text-sm">📧 ${props.clientEmail}</div>`;
        }

        if (props.clientPhone) {
            tooltip += `<div class="text-sm">📞 ${props.clientPhone}</div>`;
        }

        if (props.address) {
            tooltip += `<div class="text-sm">📍 ${props.address}</div>`;
        }

        tooltip += `<div class="text-sm mt-1">`;
        tooltip += `<span class="inline-block px-2 py-1 text-xs rounded" style="background-color: ${this.getEventColor(
            props.status
        )}; color: white;">`;
        tooltip += `${props.status || "Pending"}`;
        tooltip += `</span>`;
        tooltip += `</div>`;

        tooltip += `</div>`;

        return tooltip;
    }

    /**
     * Debounce función
     */
    static debounce(func, wait) {
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

    /**
     * Throttle función
     */
    static throttle(func, limit) {
        let inThrottle;
        return function () {
            const args = arguments;
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => (inThrottle = false), limit);
            }
        };
    }

    /**
     * Escapar HTML
     */
    static escapeHtml(text) {
        const map = {
            "&": "&amp;",
            "<": "&lt;",
            ">": "&gt;",
            '"': "&quot;",
            "'": "&#039;",
        };

        return text.replace(/[&<>"']/g, (m) => map[m]);
    }

    /**
     * Capitalizar primera letra
     */
    static capitalize(str) {
        if (!str) return str;
        return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
    }

    /**
     * Generar ID único
     */
    static generateUniqueId() {
        return Date.now().toString(36) + Math.random().toString(36).substr(2);
    }

    /**
     * Verificar si es móvil
     */
    static isMobile() {
        return window.innerWidth <= 768;
    }

    /**
     * Obtener fecha actual en formato ISO
     */
    static getCurrentDateISO() {
        return new Date().toISOString().split("T")[0];
    }

    /**
     * Obtener hora actual en formato HH:MM
     */
    static getCurrentTime() {
        const now = new Date();
        return now.toTimeString().substring(0, 5);
    }

    /**
     * Agregar horas a una fecha
     */
    static addHours(date, hours) {
        const result = new Date(date);
        result.setHours(result.getHours() + hours);
        return result;
    }

    /**
     * Verificar si dos fechas son el mismo día
     */
    static isSameDay(date1, date2) {
        const d1 = new Date(date1);
        const d2 = new Date(date2);

        return (
            d1.getFullYear() === d2.getFullYear() &&
            d1.getMonth() === d2.getMonth() &&
            d1.getDate() === d2.getDate()
        );
    }

    /**
     * Obtener rango de fechas para la semana actual
     */
    static getCurrentWeekRange() {
        const now = new Date();
        const startOfWeek = new Date(now.setDate(now.getDate() - now.getDay()));
        const endOfWeek = new Date(
            now.setDate(now.getDate() - now.getDay() + 6)
        );

        return {
            start: startOfWeek,
            end: endOfWeek,
        };
    }

    /**
     * Formatear duración en minutos a texto legible
     */
    static formatDuration(minutes) {
        if (minutes < 60) {
            return `${minutes} min`;
        }

        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;

        if (remainingMinutes === 0) {
            return `${hours}h`;
        }

        return `${hours}h ${remainingMinutes}min`;
    }

    /**
     * Copiar texto al portapapeles
     */
    static async copyToClipboard(text) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (err) {
            console.error("Failed to copy text: ", err);

            // Fallback para navegadores que no soportan clipboard API
            const textArea = document.createElement("textarea");
            textArea.value = text;
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            try {
                document.execCommand("copy");
                document.body.removeChild(textArea);
                return true;
            } catch (fallbackErr) {
                console.error("Fallback copy failed: ", fallbackErr);
                document.body.removeChild(textArea);
                return false;
            }
        }
    }

    /**
     * Mostrar notificación toast
     */
    static showToast(message, type = "success", duration = 3000) {
        // Si existe Swal, usar toast de SweetAlert2
        if (typeof Swal !== "undefined") {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: duration,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener("mouseenter", Swal.stopTimer);
                    toast.addEventListener("mouseleave", Swal.resumeTimer);
                },
            });

            Toast.fire({
                icon: type,
                title: message,
            });
        } else {
            // Fallback simple
            console.log(`${type.toUpperCase()}: ${message}`);
        }
    }

    /**
     * Validar si es horario de negocio
     */
    static isBusinessHours(date) {
        const day = date.getDay(); // 0 = Sunday, 6 = Saturday
        const hour = date.getHours();

        // Lunes a Viernes, 8 AM a 6 PM
        return day >= 1 && day <= 5 && hour >= 8 && hour < 18;
    }
}

// Hacer disponible globalmente
window.CalendarUtils = CalendarUtils;

// Debug: Verificar que se exportó correctamente
console.log("CalendarUtils loaded and exported:", typeof window.CalendarUtils);
