/**
 * Calendar Events Module
 * Manejo de eventos del calendario (click, drag, drop, etc.)
 */

class CalendarEvents {
    constructor(calendar, translations, routes) {
        this.calendar = calendar;
        this.translations = translations;
        this.routes = routes;
        this.currentAppointmentId = null;
    }

    /**
     * Configurar todos los event handlers del calendario
     */
    setupEventHandlers() {
        return {
            // Handle date selection for new appointments
            select: (info) => {
                if (window.CalendarModals) {
                    // DEBUG: Log selection info
                    console.log("DEBUG - Selection Info:", {
                        originalStart: info.start,
                        originalEnd: info.end,
                        originalDuration: info.end
                            ? (info.end.getTime() - info.start.getTime()) /
                                  1000 /
                                  60 +
                              " minutes"
                            : "N/A",
                    });

                    // Force 3-hour duration regardless of selection
                    const endTime = new Date(
                        info.start.getTime() + 3 * 60 * 60 * 1000
                    );

                    // DEBUG: Log forced duration
                    console.log("DEBUG - Forced Duration:", {
                        forcedStart: info.start,
                        forcedEnd: endTime,
                        forcedDuration:
                            (endTime.getTime() - info.start.getTime()) /
                                1000 /
                                60 +
                            " minutes",
                    });

                    // Create temporary visual event to show 3-hour duration
                    this.createTemporarySelectionEvent(info.start, endTime);

                    window.CalendarModals.openNewAppointmentModal(
                        info.start,
                        endTime
                    );
                }
            },

            // Handle event dragging/rescheduling
            eventDrop: (info) => {
                this.handleEventDrop(info);
            },

            // Handle clicking on an event
            eventClick: (info) => {
                this.handleEventClick(info);
            },

            // Handle mouse enter for tooltips
            eventMouseEnter: (info) => {
                this.setupEventTooltip(info);
            },
        };
    }

    /**
     * Manejar el arrastre de eventos (reagendar)
     */
    handleEventDrop(info) {
        const event = info.event;
        const newStart = event.start.toISOString();
        const newEnd = event.end ? event.end.toISOString() : null;

        Swal.fire({
            title: this.translations.reschedule_appointment,
            html: this.translations.move_appointment_to
                .replace("{title}", event.title)
                .replace(
                    "{newTime}",
                    event.start.toLocaleString("en-US", {
                        dateStyle: "short",
                        timeStyle: "short",
                    })
                ),
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: this.translations.yes_move,
            cancelButtonText: this.translations.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                this.updateEventTime(event.id, newStart, newEnd, info);
            } else {
                info.revert();
            }
        });
    }

    /**
     * Actualizar tiempo del evento en el backend
     */
    updateEventTime(eventId, newStart, newEnd, info) {
        $.ajax({
            url: this.routes.update.replace(":id", eventId),
            type: "PATCH",
            data: {
                start: newStart,
                end: newEnd,
            },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                Swal.fire(this.translations.moved, response.message, "success");
            },
            error: (xhr) => {
                console.error("Error updating event:", xhr.responseText);
                let errorMessage =
                    this.translations.could_not_update_appointment;
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage += ` ${xhr.responseJSON.message}`;
                }
                Swal.fire(this.translations.error, errorMessage, "error");
                info.revert();
            },
        });
    }

    /**
     * Manejar click en evento
     */
    handleEventClick(info) {
        info.jsEvent.preventDefault();
        
        console.log("DEBUG - Event clicked:", {
            eventId: info.event.id,
            eventTitle: info.event.title,
            extendedProps: info.event.extendedProps
        });

        try {
            const props = info.event.extendedProps;
            this.currentAppointmentId = info.event.id;

            // Verificar que CalendarModals existe
            if (!window.CalendarModals) {
                console.error("CalendarModals not found in window object");
                return;
            }

            console.log("DEBUG - Opening event detail modal");
            
            // Abrir modal de detalles
            window.CalendarModals.openEventDetailModal(info.event, props);
            
            console.log("DEBUG - Modal should be open now");
        } catch (error) {
            console.error("Error in eventClick handler:", error);
        }
    }

    /**
     * Configurar tooltip para evento
     */
    setupEventTooltip(info) {
        if (typeof tippy !== "undefined") {
            tippy(info.el, {
                content: `<strong>${info.event.title}</strong><br>Status: ${
                    info.event.extendedProps.status || "Pending"
                }`,
                allowHTML: true,
                theme: "light-border",
                placement: "top",
                arrow: true,
            });
        }
    }

    /**
     * Confirmar cita
     */
    confirmAppointment(appointmentId) {
        if (!appointmentId) return;

        Swal.fire({
            title: this.translations.confirm_appointment_title,
            text: this.translations.confirm_appointment_text,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#10b981",
            cancelButtonColor: "#6b7280",
            confirmButtonText: this.translations.yes_confirm,
            cancelButtonText: this.translations.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                this.updateAppointmentStatus(appointmentId, "Confirmed");
            }
        });
    }

    /**
     * Declinar cita
     */
    declineAppointment(appointmentId) {
        if (!appointmentId) return;

        Swal.fire({
            title: this.translations.decline_appointment_title,
            text: this.translations.decline_appointment_text,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#6b7280",
            confirmButtonText: this.translations.yes_decline,
            cancelButtonText: this.translations.cancel,
        }).then((result) => {
            if (result.isConfirmed) {
                this.updateAppointmentStatus(appointmentId, "Declined");
            }
        });
    }

    /**
     * Actualizar estado de cita
     */
    updateAppointmentStatus(appointmentId, status) {
        const button =
            status === "Confirmed"
                ? document.getElementById("confirmAppointmentBtn")
                : document.getElementById("declineAppointmentBtn");

        if (button) {
            const btnText = button.querySelector(".normal-btn-text");
            const processingText = button.querySelector(".processing-btn-text");

            btnText.classList.add("hidden");
            processingText.classList.remove("hidden");
            button.disabled = true;
        }

        $.ajax({
            url: this.routes.status.replace(":id", appointmentId),
            type: "PATCH",
            data: { status: status },
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
            success: (response) => {
                const message =
                    status === "Confirmed"
                        ? this.translations.confirmed
                        : this.translations.declined;

                Swal.fire(message, response.message, "success");
                this.calendar.refetchEvents();

                // Cerrar modal
                const modal = document.getElementById("eventDetailModal");
                if (modal) {
                    modal.classList.add("hidden");
                    modal.style.display = "none";
                }
            },
            error: (xhr) => {
                const errorMessage =
                    status === "Confirmed"
                        ? this.translations.could_not_confirm_appointment
                        : this.translations.could_not_decline_appointment;

                console.error(
                    "Error updating appointment status:",
                    xhr.responseText
                );
                Swal.fire(this.translations.error, errorMessage, "error");
            },
            complete: () => {
                // Restaurar estado del botón
                if (button) {
                    const btnText = button.querySelector(".normal-btn-text");
                    const processingText = button.querySelector(
                        ".processing-btn-text"
                    );

                    btnText.classList.remove("hidden");
                    processingText.classList.add("hidden");
                    button.disabled = false;
                }
            },
        });
    }

    /**
     * Obtener ID de cita actual
     */
    getCurrentAppointmentId() {
        return this.currentAppointmentId;
    }

    /**
     * Create temporary visual event during selection
     */
    createTemporarySelectionEvent(start, end) {
        // Remove any existing temporary event
        this.removeTemporarySelectionEvent();

        // Create temporary event
        const tempEvent = {
            id: "temp-selection",
            title:
                "🆕 " +
                (this.translations.new_appointment || "New Appointment"),
            start: start.toISOString(),
            end: end.toISOString(),
            backgroundColor: "#3b82f6",
            borderColor: "#1d4ed8",
            className: "temp-selection-event",
            display: "block",
            editable: false,
            extendedProps: {
                status: "New",
                clientName:
                    this.translations.new_appointment || "New Appointment",
                isTemporary: true,
            },
        };

        // Add to calendar
        this.calendar.addEvent(tempEvent);

        console.log("DEBUG - Temporary event created:", tempEvent);
    }

    /**
     * Remove temporary selection event
     */
    removeTemporarySelectionEvent() {
        const tempEvent = this.calendar.getEventById("temp-selection");
        if (tempEvent) {
            tempEvent.remove();
            console.log("DEBUG - Temporary event removed");
        }
    }
}

// Hacer disponible globalmente
window.CalendarEvents = CalendarEvents;
