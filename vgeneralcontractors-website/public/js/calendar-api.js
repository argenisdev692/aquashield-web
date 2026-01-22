/**
 * Calendar API Module
 * Manejo de llamadas AJAX del calendario
 */

class CalendarAPI {
    constructor(calendar, translations, routes) {
        this.calendar = calendar;
        this.translations = translations;
        this.routes = routes;
    }

    /**
     * Cargar clientes para el selector
     */
    loadClients() {
        if (!this.routes.clients) {
            console.error("Clients route not configured");
            return;
        }

        $.ajax({
            url: this.routes.clients,
            type: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                this.populateClientSelector(response.data || response);
            },
            error: (xhr) => {
                console.error("Error loading clients:", xhr.responseText);
                Swal.fire(
                    this.translations.error,
                    this.translations.client_load_error || "Could not load clients. Please try again.",
                    'error'
                );
            }
        });
    }

    /**
     * Poblar selector de clientes
     */
    populateClientSelector(clients) {
        const clientSelector = document.getElementById('clientSelector');
        if (!clientSelector) return;

        // Limpiar opciones existentes excepto la primera
        while (clientSelector.options.length > 1) {
            clientSelector.removeChild(clientSelector.lastChild);
        }

        // Agregar clientes
        clients.forEach(client => {
            const option = document.createElement('option');
            option.value = client.uuid;
            option.textContent = `${client.first_name} ${client.last_name} - ${client.email}`;
            clientSelector.appendChild(option);
        });

        // Si no hay clientes, mostrar mensaje
        if (clients.length === 0) {
            const option = document.createElement('option');
            option.value = '';
            option.textContent = this.translations.no_clients_available || 'No clients available';
            option.disabled = true;
            clientSelector.appendChild(option);
        }
    }

    /**
     * Crear nueva cita
     */
    createAppointment() {
        const form = document.getElementById('newAppointmentForm');
        if (!form) return;

        const formData = new FormData(form);
        const createBtn = document.getElementById('createAppointmentBtn');

        // Mostrar estado de carga
        this.toggleCreateButtonLoading(createBtn, true);

        $.ajax({
            url: this.routes.create,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: (response) => {
                Swal.fire(
                    this.translations.success,
                    response.message || this.translations.appointment_created_successfully,
                    'success'
                );

                // Cerrar modal
                if (window.CalendarModals) {
                    window.CalendarModals.closeNewAppointmentModal();
                }

                // Refrescar eventos del calendario
                this.calendar.refetchEvents();

                // Resetear formulario
                form.reset();
            },
            error: (xhr) => {
                console.error("Error creating appointment:", xhr.responseText);
                
                let errorMessage = this.translations.unexpected_error;
                
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    // Errores de validación
                    const errors = xhr.responseJSON.errors;
                    const errorMessages = Object.values(errors).flat();
                    errorMessage = errorMessages.join('\\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }

                Swal.fire(this.translations.error, errorMessage, 'error');
            },
            complete: () => {
                this.toggleCreateButtonLoading(createBtn, false);
            }
        });
    }

    /**
     * Alternar estado de carga del botón crear
     */
    toggleCreateButtonLoading(button, isLoading) {
        if (!button) return;

        const normalText = button.querySelector('.normal-btn-text');
        const loadingText = button.querySelector('.loading-btn-text');

        if (isLoading) {
            if (normalText) normalText.classList.add('hidden');
            if (loadingText) loadingText.classList.remove('hidden');
            button.disabled = true;
        } else {
            if (normalText) normalText.classList.remove('hidden');
            if (loadingText) loadingText.classList.add('hidden');
            button.disabled = false;
        }
    }

    /**
     * Validar disponibilidad de horario
     */
    checkTimeSlotAvailability(date, time, excludeId = null) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.routes.events}?check_availability=1`,
                type: 'GET',
                data: {
                    date: date,
                    time: time,
                    exclude_id: excludeId
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    resolve(response.available || false);
                },
                error: (xhr) => {
                    console.error("Error checking availability:", xhr.responseText);
                    reject(xhr);
                }
            });
        });
    }

    /**
     * Obtener detalles de evento
     */
    getEventDetails(eventId) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: `${this.routes.events}/${eventId}`,
                type: 'GET',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    resolve(response.data || response);
                },
                error: (xhr) => {
                    console.error("Error fetching event details:", xhr.responseText);
                    reject(xhr);
                }
            });
        });
    }

    /**
     * Eliminar evento/cita
     */
    deleteEvent(eventId) {
        return new Promise((resolve, reject) => {
            Swal.fire({
                title: this.translations.confirm_delete_title || 'Are you sure?',
                text: this.translations.confirm_delete_text || "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: this.translations.yes_delete || 'Yes, delete it!',
                cancelButtonText: this.translations.cancel || 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `${this.routes.events}/${eventId}`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: (response) => {
                            const deletedTitle = this.translations?.deleted || 'Deleted!';
                            Swal.fire(deletedTitle, response.message || this.translations.appointment_deleted_successfully || 'Event has been deleted.', 'success');
                            this.calendar.refetchEvents();
                            resolve(response);
                        },
                        error: (xhr) => {
                            console.error("Error deleting event:", xhr.responseText);
                            const errorTitle = this.translations?.error || 'Error!';
                            const errorMessage = this.translations?.could_not_delete_event || 'Could not delete the event.';
                            Swal.fire(errorTitle, errorMessage, 'error');
                            reject(xhr);
                        }
                    });
                } else {
                    reject(new Error('Deletion cancelled'));
                }
            });
        });
    }

    /**
     * Actualizar evento
     */
    updateEvent(eventId, data) {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: this.routes.update.replace(':id', eventId),
                type: 'PATCH',
                data: data,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: (response) => {
                    this.calendar.refetchEvents();
                    resolve(response);
                },
                error: (xhr) => {
                    console.error("Error updating event:", xhr.responseText);
                    reject(xhr);
                }
            });
        });
    }

    /**
     * Validación de formulario en tiempo real
     */
    setupRealTimeValidation() {
        const clientSelector = document.getElementById('clientSelector');
        const appointmentDate = document.getElementById('appointmentDate');
        const appointmentTime = document.getElementById('appointmentTime');

        if (clientSelector) {
            clientSelector.addEventListener('change', () => {
                this.validateForm();
            });
        }

        if (appointmentDate && appointmentTime) {
            [appointmentDate, appointmentTime].forEach(field => {
                field.addEventListener('change', () => {
                    this.validateTimeSlot();
                });
            });
        }
    }

    /**
     * Validar formulario
     */
    validateForm() {
        const clientSelector = document.getElementById('clientSelector');
        const createBtn = document.getElementById('createAppointmentBtn');

        if (!clientSelector || !createBtn) return;

        const isValid = clientSelector.value !== '';
        createBtn.disabled = !isValid;

        if (isValid) {
            createBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            createBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }

    /**
     * Validar horario seleccionado
     */
    async validateTimeSlot() {
        const appointmentDate = document.getElementById('appointmentDate');
        const appointmentTime = document.getElementById('appointmentTime');

        if (!appointmentDate || !appointmentTime || !appointmentDate.value || !appointmentTime.value) {
            return;
        }

        try {
            const isAvailable = await this.checkTimeSlotAvailability(appointmentDate.value, appointmentTime.value);
            
            const timeContainer = appointmentTime.closest('.form-group');
            const existingError = timeContainer?.querySelector('.error-message');

            if (!isAvailable) {
                if (!existingError) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'error-message text-red-500 text-sm mt-1';
                    errorDiv.textContent = this.translations.time_slot_unavailable || 'This time slot is already booked. Please select a different time.';
                    timeContainer?.appendChild(errorDiv);
                }
                appointmentTime.classList.add('border-red-500');
            } else {
                if (existingError) {
                    existingError.remove();
                }
                appointmentTime.classList.remove('border-red-500');
            }
        } catch (error) {
            console.error('Error validating time slot:', error);
        }
    }
}

// Hacer disponible globalmente
window.CalendarAPI = CalendarAPI;