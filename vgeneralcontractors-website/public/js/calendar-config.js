/**
 * Calendar Configuration Module
 * Configuración principal para FullCalendar
 */

class CalendarConfig {
    constructor() {
        this.routes = this.getRoutes();
        this.translations = this.getTranslations();
    }

    /**
     * Obtener rutas desde las meta tags
     */
    getRoutes() {
        return {
            events:
                document.querySelector('meta[name="calendar-events-url"]')
                    ?.content || "",
            store:
                document.querySelector('meta[name="calendar-create-url"]')
                    ?.content || "",
            update:
                document.querySelector('meta[name="calendar-update-url"]')
                    ?.content || "",
            status:
                document.querySelector('meta[name="calendar-status-url"]')
                    ?.content || "",
            getClients:
                document.querySelector('meta[name="calendar-clients-url"]')
                    ?.content || "",
            createAppointment:
                document.querySelector(
                    'meta[name="calendar-create-appointment-url"]'
                )?.content || "",
        };
    }

    /**
     * Obtener traducciones desde las meta tags o variables globales
     */
    getTranslations() {
        // Las traducciones pueden estar definidas globalmente o en meta tags
        return (
            window.translations || {
                please_select_client: window.translations?.please_select_client || "Please select a client",
                appointment_created_successfully: window.translations?.appointment_created_successfully ||
                    "Appointment created successfully",
                success: window.translations?.success || "Success",
                error: window.translations?.error || "Error",
                unexpected_error: window.translations?.unexpected_error || "Unexpected error",
                reschedule_appointment: window.translations?.reschedule_appointment || "Reschedule Appointment",
                move_appointment_to: window.translations?.move_appointment_to || "Move appointment to",
                yes_move: window.translations?.yes_move || "Yes, move",
                cancel: window.translations?.cancel || "Cancel",
                moved: window.translations?.moved || "Moved",
                could_not_update_appointment: window.translations?.could_not_update_appointment || "Could not update appointment",
                confirm_appointment_title: window.translations?.confirm_appointment_title || "Confirm Appointment",
                confirm_appointment_text: window.translations?.confirm_appointment_text ||
                    "Are you sure you want to confirm this appointment?",
                yes_confirm: window.translations?.yes_confirm || "Yes, confirm",
                confirmed: window.translations?.confirmed || "Confirmed",
                could_not_confirm_appointment: window.translations?.could_not_confirm_appointment || "Could not confirm appointment",
                decline_appointment_title: window.translations?.decline_appointment_title || "Decline Appointment",
                decline_appointment_text: window.translations?.decline_appointment_text ||
                    "Are you sure you want to decline this appointment?",
                yes_decline: window.translations?.yes_decline || "Yes, decline",
                declined: window.translations?.declined || "Declined",
                could_not_decline_appointment: window.translations?.could_not_decline_appointment || "Could not decline appointment",
                create_new_client: window.translations?.create_new_client || "Create New Client",
                create_confirmed_appointment: window.translations?.create_confirmed_appointment || "Create Confirmed Appointment",
                create_lead: window.translations?.create_lead || "Create Lead",
                new_appointment: window.translations?.new_appointment || "New Appointment",
                
                // Client management
                select_client_3_hours: window.translations?.select_client_3_hours || "Select a client to create a 3-hour appointment",
                loading_clients: window.translations?.loading_clients || "Loading clients...",
                no_clients_available: window.translations?.no_clients_available || "No clients available",
                client_load_error: window.translations?.client_load_error || "Error loading clients",
                
                // Validation and errors
                time_slot_unavailable: window.translations?.time_slot_unavailable || "This time slot is unavailable",
                past_date_error: window.translations?.past_date_error || "Cannot schedule appointments in the past",
                invalid_email: window.translations?.invalid_email || "Invalid email address",
                
                // Form placeholders
                first_name_placeholder: window.translations?.first_name_placeholder || "First Name",
                last_name_placeholder: window.translations?.last_name_placeholder || "Last Name",
                email_placeholder: window.translations?.email_placeholder || "Email Address",
                phone_placeholder: window.translations?.phone_placeholder || "Phone Number",
                address_placeholder: window.translations?.address_placeholder || "Address",
                notes_placeholder: window.translations?.notes_placeholder || "Additional notes",
                
                // Process states
                loading: window.translations?.loading || "Loading...",
                saving: window.translations?.saving || "Saving...",
                processing: window.translations?.processing || "Processing...",
                creating: window.translations?.creating || "Creating...",
                updating: window.translations?.updating || "Updating...",
                deleting: window.translations?.deleting || "Deleting...",
                
                // UI states
                hide: window.translations?.hide || "Hide",
                show: window.translations?.show || "Show",
                calendar_refresh: window.translations?.calendar_refresh || "Calendar refreshed",
                keyboard_shortcuts_help: window.translations?.keyboard_shortcuts_help || "Press ESC to close modals, F5 to refresh, Ctrl+N for new appointment"
            }
        );
    }

    /**
     * Configuración principal de FullCalendar
     */
    getCalendarConfig() {
        return {
            // Core options
            headerToolbar: {
                left: "prev,next today",
                center: "title",
                right: "dayGridMonth,timeGridWeek,timeGridDay,listWeek",
            },
            initialView: "timeGridWeek",
            locale: "en",
            timeZone: "local",
            navLinks: true,
            editable: true,
            eventDurationEditable: true,
            eventStartEditable: true,
            selectable: true,
            selectMirror: false, // Disable mirror to control selection visualization
            selectAllow: (selectInfo) => {
                // Always allow selection but control the visual feedback
                return true;
            },
            dayMaxEvents: true,
            nowIndicator: true,

            // Time grid options
            slotDuration: "00:30:00",
            slotMinTime: "09:00:00",
            slotMaxTime: "20:00:00",
            defaultTimedEventDuration: "03:00:00", // 3 hours default duration
            selectConstraint: "businessHours",
            selectOverlap: false,
            selectMinDistance: 0, // Allow selections to start at any time
            businessHours: {
                daysOfWeek: [1, 2, 3, 4, 5], // Monday - Friday
                startTime: "09:00",
                endTime: "18:00",
            },

            // Event Data Source
            events: {
                url: this.routes.events,
                failure: (err) => {
                    console.error("Failed to load events:", err);
                },
                success: (events) => {
                    console.log("Events loaded successfully:", events);
                },
            },

            // Event formatting
            eventTimeFormat: {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            },
        };
    }

    /**
     * Configuración para el renderizado personalizado de eventos
     */
    getEventContentConfig() {
        return {
            eventContent: (arg) => {
                let content = document.createElement("div");
                content.classList.add("fc-event-content-custom");
                content.style.cursor = "pointer";
                content.style.width = "100%";
                content.style.height = "100%";
                content.style.padding = "6px 8px";
                content.style.overflow = "hidden";

                // 1. Nombre del cliente (primera línea) con icono
                let clientTitle = document.createElement("div");
                clientTitle.classList.add("client-title");
                clientTitle.style.display = "flex";
                clientTitle.style.alignItems = "center";
                clientTitle.style.fontWeight = "600";
                clientTitle.style.fontSize = "13px";
                clientTitle.style.color = "#ffffff";
                clientTitle.style.marginBottom = "3px";
                clientTitle.style.textShadow = "0 1px 2px rgba(0,0,0,0.3)";

                // Cliente icon
                let clientIcon = document.createElement("span");
                clientIcon.innerHTML = "👤";
                clientIcon.style.marginRight = "4px";
                clientIcon.style.fontSize = "11px";

                let clientName = document.createElement("span");
                clientName.textContent = arg.event.title;
                clientName.style.whiteSpace = "nowrap";
                clientName.style.overflow = "hidden";
                clientName.style.textOverflow = "ellipsis";

                clientTitle.appendChild(clientIcon);
                clientTitle.appendChild(clientName);

                // 2. Horario (segunda línea) con icono de reloj
                let timeText = document.createElement("div");
                timeText.classList.add("event-time");
                timeText.style.display = "flex";
                timeText.style.alignItems = "center";
                timeText.style.fontSize = "11px";
                timeText.style.color = "rgba(255,255,255,0.95)";
                timeText.style.marginBottom = "4px";
                timeText.style.fontWeight = "500";

                const start = arg.event.start;
                const startTime = start.toLocaleTimeString("en-US", {
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: true,
                });

                // Force display of 3-hour duration
                const calculatedEnd = new Date(
                    start.getTime() + 3 * 60 * 60 * 1000
                );
                const endTime = calculatedEnd.toLocaleTimeString("en-US", {
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: true,
                });

                // Clock icon
                let timeIcon = document.createElement("span");
                timeIcon.innerHTML = "🕐";
                timeIcon.style.marginRight = "4px";
                timeIcon.style.fontSize = "10px";

                let timeSpan = document.createElement("span");
                timeSpan.textContent = `${startTime} - ${endTime}`;
                timeSpan.style.whiteSpace = "nowrap";

                let durationBadge = document.createElement("span");
                durationBadge.textContent = "3h";
                durationBadge.style.backgroundColor = "rgba(255,255,255,0.2)";
                durationBadge.style.padding = "1px 4px";
                durationBadge.style.borderRadius = "8px";
                durationBadge.style.fontSize = "9px";
                durationBadge.style.fontWeight = "600";
                durationBadge.style.marginLeft = "4px";

                timeText.appendChild(timeIcon);
                timeText.appendChild(timeSpan);
                timeText.appendChild(durationBadge);

                // 3. Estado (última línea) con badge estilizado
                let statusContainer = document.createElement("div");
                statusContainer.classList.add("appointment-status-container");
                statusContainer.style.display = "flex";
                statusContainer.style.alignItems = "center";
                statusContainer.style.justifyContent = "space-between";

                let statusBadge = document.createElement("span");
                statusBadge.classList.add("status-badge");
                const status = arg.event.extendedProps.status || "Pending";
                statusBadge.textContent = status;

                // Status styling
                statusBadge.style.fontSize = "10px";
                statusBadge.style.fontWeight = "600";
                statusBadge.style.padding = "3px 8px";
                statusBadge.style.borderRadius = "12px";
                statusBadge.style.textTransform = "uppercase";
                statusBadge.style.letterSpacing = "0.5px";
                statusBadge.style.border = "1px solid rgba(255,255,255,0.3)";
                statusBadge.style.textShadow = "none";

                // Status-specific colors
                switch (status.toLowerCase()) {
                    case "confirmed":
                        statusBadge.style.backgroundColor = "#ffffff";
                        statusBadge.style.color = "#10b981";
                        statusBadge.style.border = "1px solid #10b981";
                        statusBadge.style.boxShadow =
                            "0 1px 3px rgba(16, 185, 129, 0.2)";
                        statusBadge.innerHTML =
                            "<span style='color: #10b981;'>✓</span> CONFIRMED";
                        break;
                    case "completed":
                        statusBadge.style.backgroundColor =
                            "rgba(34, 197, 94, 0.9)";
                        statusBadge.style.color = "#ffffff";
                        statusBadge.innerHTML = "✅ COMPLETED";
                        break;
                    case "pending":
                        statusBadge.style.backgroundColor =
                            "rgba(245, 158, 11, 0.9)";
                        statusBadge.style.color = "#ffffff";
                        statusBadge.innerHTML = "⏳ PENDING";
                        break;
                    case "declined":
                        statusBadge.style.backgroundColor =
                            "rgba(239, 68, 68, 0.9)";
                        statusBadge.style.color = "#ffffff";
                        statusBadge.innerHTML = "❌ DECLINED";
                        break;
                    default:
                        statusBadge.style.backgroundColor =
                            "rgba(107, 114, 128, 0.9)";
                        statusBadge.style.color = "#ffffff";
                        statusBadge.innerHTML = "📋 " + status.toUpperCase();
                        break;
                }

                statusContainer.appendChild(statusBadge);

                // Agregar todo al contenedor
                content.appendChild(clientTitle);
                content.appendChild(timeText);
                content.appendChild(statusContainer);

                return { domNodes: [content] };
            },
        };
    }
}

// Hacer disponible globalmente
window.CalendarConfig = CalendarConfig;
