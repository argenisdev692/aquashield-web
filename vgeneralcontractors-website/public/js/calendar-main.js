/**
 * Calendar Main Module
 * Coordina la inicialización y funcionamiento del calendario
 */

class CalendarMain {
    constructor() {
        this.calendar = null;
        this.config = null;
        this.events = null;
        this.modals = null;
        this.api = null;
        this.translations = {};
        this.routes = {};
    }

    /**
     * Inicializar el calendario
     */
    async init() {
        try {
            console.log("Initializing Calendar...");

            // Verificar dependencias
            if (!this.checkDependencies()) {
                console.error("Missing required dependencies");
                return;
            }

            // Inicializar configuración
            this.config = new CalendarConfig();
            this.translations = this.config.getTranslations();
            this.routes = this.config.getRoutes();

            // Crear instancia del calendario
            await this.createCalendarInstance();

            // Inicializar módulos
            this.initializeModules();

            // Configurar event listeners adicionales
            this.setupAdditionalEventListeners();

            // Configurar validaciones
            this.setupValidations();

            console.log("Calendar initialized successfully");
        } catch (error) {
            console.error("Error initializing calendar:", error);
            this.showError(
                "Failed to initialize calendar. Please refresh the page."
            );
        }
    }

    /**
     * Verificar dependencias necesarias
     */
    checkDependencies() {
        const coreDependencies = [
            {
                name: "FullCalendar",
                check: () => typeof FullCalendar !== "undefined",
            },
            { name: "jQuery", check: () => typeof $ !== "undefined" },
            { name: "SweetAlert2", check: () => typeof Swal !== "undefined" },
        ];

        const moduleDependencies = [
            {
                name: "CalendarConfig",
                check: () => typeof CalendarConfig !== "undefined",
            },
            {
                name: "CalendarEvents",
                check: () => typeof CalendarEvents !== "undefined",
            },
            {
                name: "CalendarModals",
                check: () => typeof CalendarModals !== "undefined",
            },
            {
                name: "CalendarAPI",
                check: () => typeof CalendarAPI !== "undefined",
            },
            {
                name: "CalendarUtils",
                check: () => typeof CalendarUtils !== "undefined",
            },
        ];

        let missingCore = [];
        let missingModules = [];

        coreDependencies.forEach((dep) => {
            if (!dep.check()) {
                missingCore.push(dep.name);
            }
        });

        moduleDependencies.forEach((dep) => {
            if (!dep.check()) {
                missingModules.push(dep.name);
            }
        });

        if (missingCore.length > 0) {
            console.error("Missing CORE dependencies:", missingCore);
            return false;
        }

        if (missingModules.length > 0) {
            console.error("Missing MODULE dependencies:", missingModules);
            return false;
        }

        return true;
    }

    /**
     * Crear instancia del calendario
     */
    async createCalendarInstance() {
        const calendarEl = document.getElementById("calendar");
        if (!calendarEl) {
            throw new Error("Calendar element not found");
        }

        // Obtener configuración base
        const baseConfig = this.config.getCalendarConfig();
        const eventConfig = this.config.getEventContentConfig();

        // Crear configuración completa
        const fullConfig = {
            ...baseConfig,
            ...eventConfig,
            // Los event handlers se agregarán después de crear los módulos
        };

        // Crear calendario
        this.calendar = new FullCalendar.Calendar(calendarEl, fullConfig);

        // Renderizar calendario
        await this.calendar.render();

        // Hacer el calendario disponible globalmente para debugging
        window.calendar = this.calendar;
    }

    /**
     * Inicializar módulos
     */
    initializeModules() {
        // Inicializar módulo de eventos
        this.events = new CalendarEvents(
            this.calendar,
            this.translations,
            this.routes
        );
        window.CalendarEvents = this.events;

        // Inicializar módulo de modales
        this.modals = new CalendarModals(
            this.calendar,
            this.translations,
            this.routes
        );
        window.CalendarModals = this.modals;

        // Inicializar módulo de API
        this.api = new CalendarAPI(
            this.calendar,
            this.translations,
            this.routes
        );
        window.CalendarAPI = this.api;

        // Configurar event handlers del calendario
        this.setupCalendarEventHandlers();

        console.log("Calendar modules initialized");
    }

    /**
     * Configurar event handlers del calendario
     */
    setupCalendarEventHandlers() {
        const eventHandlers = this.events.setupEventHandlers();

        // Agregar event handlers al calendario
        Object.keys(eventHandlers).forEach((eventName) => {
            this.calendar.on(eventName, eventHandlers[eventName]);
        });
    }

    /**
     * Configurar event listeners adicionales
     */
    setupAdditionalEventListeners() {
        // Manejar cambios de tamaño de ventana
        window.addEventListener(
            "resize",
            CalendarUtils.debounce(() => {
                if (this.calendar) {
                    this.calendar.updateSize();
                }
            }, 250)
        );

        // Manejar teclas de teclado
        document.addEventListener("keydown", (e) => {
            this.handleKeyboardShortcuts(e);
        });

        // Prevenir pérdida de datos al salir
        window.addEventListener("beforeunload", (e) => {
            // Si hay modales abiertos o formularios sin guardar, preguntar antes de salir
            if (this.hasUnsavedChanges()) {
                e.preventDefault();
                e.returnValue = "";
            }
        });
    }

    /**
     * Manejar atajos de teclado
     */
    handleKeyboardShortcuts(e) {
        // ESC para cerrar modales
        if (e.key === "Escape") {
            this.closeAllModals();
        }

        // F5 o Ctrl+R para refrescar eventos
        if (e.key === "F5" || (e.ctrlKey && e.key === "r")) {
            e.preventDefault();
            this.refreshCalendar();
        }

        // Ctrl+N para nueva cita
        if (e.ctrlKey && e.key === "n") {
            e.preventDefault();
            this.createNewAppointment();
        }
    }

    /**
     * Configurar validaciones
     */
    setupValidations() {
        if (
            this.api &&
            typeof this.api.setupRealTimeValidation === "function"
        ) {
            this.api.setupRealTimeValidation();
        }
    }

    /**
     * Verificar si hay cambios sin guardar
     */
    hasUnsavedChanges() {
        // Verificar si hay modales abiertos con formularios
        const modals = ["newAppointmentModal", "eventDetailModal"];

        for (let modalId of modals) {
            const modal = document.getElementById(modalId);
            if (modal && !modal.classList.contains("hidden")) {
                const forms = modal.querySelectorAll("form");
                for (let form of forms) {
                    if (this.formHasChanges(form)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Verificar si un formulario tiene cambios
     */
    formHasChanges(form) {
        const inputs = form.querySelectorAll("input, select, textarea");

        for (let input of inputs) {
            if (
                input.type === "text" ||
                input.type === "email" ||
                input.type === "tel"
            ) {
                if (input.value.trim() !== "") return true;
            } else if (input.type === "select-one") {
                if (input.selectedIndex > 0) return true;
            }
        }

        return false;
    }

    /**
     * Cerrar todos los modales
     */
    closeAllModals() {
        if (this.modals) {
            this.modals.closeEventDetailModal();
            this.modals.closeNewAppointmentModal();
        }
    }

    /**
     * Refrescar calendario
     */
    refreshCalendar() {
        if (this.calendar) {
            CalendarUtils.showToast("Refreshing calendar...", "info", 1000);

            // Clear cache and refetch events
            fetch("/appointment-calendar/clear-cache", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document
                        .querySelector('meta[name="csrf-token"]')
                        .getAttribute("content"),
                    "Content-Type": "application/json",
                },
            })
                .then(() => {
                    this.calendar.refetchEvents();
                })
                .catch((error) => {
                    console.log(
                        "Cache clear failed, but refetching events anyway:",
                        error
                    );
                    this.calendar.refetchEvents();
                });
        }
    }

    /**
     * Crear nueva cita
     */
    createNewAppointment() {
        if (this.modals) {
            const now = new Date();
            const end = CalendarUtils.addHours(now, 3); // Fixed: 3 hours instead of 2
            this.modals.openNewAppointmentModal(now, end);
        }
    }

    /**
     * Mostrar error
     */
    showError(message) {
        if (typeof Swal !== "undefined") {
            const errorTitle = window.translations?.error || "Error";
            Swal.fire(errorTitle, message, "error");
        } else {
            alert(message);
        }
    }

    /**
     * Destruir calendario
     */
    destroy() {
        if (this.calendar) {
            this.calendar.destroy();
            this.calendar = null;
        }

        // Limpiar referencias globales
        window.calendar = null;
        window.CalendarEvents = null;
        window.CalendarModals = null;
        window.CalendarAPI = null;

        console.log("Calendar destroyed");
    }

    /**
     * Reinicializar calendario
     */
    async reinitialize() {
        this.destroy();
        await this.init();
    }

    /**
     * Obtener información del estado del calendario
     */
    getStatus() {
        return {
            initialized: !!this.calendar,
            currentView: this.calendar?.view?.type,
            eventsCount: this.calendar?.getEvents()?.length || 0,
            modules: {
                config: !!this.config,
                events: !!this.events,
                modals: !!this.modals,
                api: !!this.api,
            },
        };
    }
}

// Inicialización automática cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", async () => {
    try {
        // Crear instancia principal del calendario
        window.calendarMain = new CalendarMain();

        // Inicializar
        await window.calendarMain.init();

        // Log de éxito
        console.log("Calendar system ready:", window.calendarMain.getStatus());
    } catch (error) {
        console.error("Failed to initialize calendar system:", error);
    }
});

// Hacer disponible globalmente
window.CalendarMain = CalendarMain;
