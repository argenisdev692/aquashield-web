/**
 * Calendar Modals Module
 * Manejo de modales del calendario (detalles de evento, nuevo appointment)
 */

class CalendarModals {
    constructor(calendar, translations, routes) {
        this.calendar = calendar;
        this.translations = translations;
        this.routes = routes;
        this.selectedStart = null;
        this.selectedEnd = null;
        this.setupModalEventListeners();
    }

    /**
     * Configurar event listeners para modales
     */
    setupModalEventListeners() {
        // Event detail modal
        const closeEventModalBtn =
            document.getElementById("closeEventModalBtn");
        const eventDetailModal = document.getElementById("eventDetailModal");

        if (closeEventModalBtn) {
            closeEventModalBtn.addEventListener("click", () => {
                this.closeEventDetailModal();
            });
        }

        if (eventDetailModal) {
            eventDetailModal.addEventListener("click", (event) => {
                if (event.target === eventDetailModal) {
                    this.closeEventDetailModal();
                }
            });
        }

        // New appointment modal
        const closeNewAppointmentModalBtn = document.getElementById(
            "closeNewAppointmentModalBtn"
        );
        const newAppointmentModal = document.getElementById(
            "newAppointmentModal"
        );

        if (closeNewAppointmentModalBtn) {
            closeNewAppointmentModalBtn.addEventListener("click", () => {
                this.closeNewAppointmentModal();
            });
        }

        if (newAppointmentModal) {
            newAppointmentModal.addEventListener("click", (event) => {
                if (event.target === newAppointmentModal) {
                    this.closeNewAppointmentModal();
                }
            });
        }

        // Create appointment button
        const createAppointmentBtn = document.getElementById(
            "createAppointmentBtn"
        );
        if (createAppointmentBtn) {
            createAppointmentBtn.addEventListener("click", () => {
                this.handleCreateAppointment();
            });
        }

        // Setup styled radio buttons and checkbox event listeners
        this.setupStyledFormElements();

        // Setup client toggle functionality
        this.setupClientToggle();

        // Action buttons
        const confirmAppointmentBtn = document.getElementById(
            "confirmAppointmentBtn"
        );
        const declineAppointmentBtn = document.getElementById(
            "declineAppointmentBtn"
        );

        if (confirmAppointmentBtn && window.CalendarEvents) {
            confirmAppointmentBtn.addEventListener("click", () => {
                const appointmentId =
                    window.CalendarEvents.getCurrentAppointmentId();
                window.CalendarEvents.confirmAppointment(appointmentId);
            });
        }

        if (declineAppointmentBtn && window.CalendarEvents) {
            declineAppointmentBtn.addEventListener("click", () => {
                const appointmentId =
                    window.CalendarEvents.getCurrentAppointmentId();
                window.CalendarEvents.declineAppointment(appointmentId);
            });
        }
    }

    /**
     * Setup styled form elements (radio buttons and checkboxes)
     */
    setupStyledFormElements() {
        // Handle insurance property radio buttons
        const insuranceLabels = document.querySelectorAll(".insurance-label");
        insuranceLabels.forEach((label) => {
            label.addEventListener("click", (e) => {
                // Find the associated radio button
                const radioId = label.getAttribute("for");
                const radio = document.getElementById(radioId);

                if (radio) {
                    // Check the radio button
                    radio.checked = true;

                    // Remove selected class from labels in the SAME GROUP only
                    const groupName = radio.name;
                    const groupRadios = document.querySelectorAll(
                        `input[name="${groupName}"]`
                    );
                    groupRadios.forEach((groupRadio) => {
                        const groupLabel = document.querySelector(
                            `label[for="${groupRadio.id}"]`
                        );
                        if (
                            groupLabel &&
                            groupLabel.classList.contains("insurance-label")
                        ) {
                            groupLabel.classList.remove("selected");
                        }
                    });

                    // Add selected class to clicked label
                    label.classList.add("selected");

                    // Trigger change event for form validation
                    radio.dispatchEvent(new Event("change"));
                }
            });
        });

        // Handle radio button change events for form validation
        const radioButtons = document.querySelectorAll('input[type="radio"]');
        radioButtons.forEach((radio) => {
            radio.addEventListener("change", () => {
                // Update insurance label styling if this is an insurance radio
                if (
                    radio.name === "insurance_property" ||
                    radio.name === "intent_to_claim"
                ) {
                    const label = document.querySelector(
                        `label[for="${radio.id}"]`
                    );
                    if (label && label.classList.contains("insurance-label")) {
                        // Remove selected from all labels in this group
                        const groupLabels = document.querySelectorAll(
                            `input[name="${radio.name}"]`
                        );
                        groupLabels.forEach((groupRadio) => {
                            const groupLabel = document.querySelector(
                                `label[for="${groupRadio.id}"]`
                            );
                            if (groupLabel) {
                                groupLabel.classList.remove("selected");
                            }
                        });

                        // Add selected to current label
                        label.classList.add("selected");
                    }
                }
            });
        });
    }

    /**
     * Setup client toggle functionality
     */
    setupClientToggle() {
        const toggle = document.getElementById("createNewClientToggle");
        const existingClientSection = document.getElementById(
            "existingClientSection"
        );
        const newClientSection = document.getElementById("newClientSection");
        const createBtnText = document.getElementById("createBtnText");
        const clientSelector = document.getElementById("clientSelector");

        if (!toggle || !existingClientSection || !newClientSection) {
            console.warn("Client toggle elements not found");
            return;
        }

        // Initialize toggle state as instance property
        this.isCreateNewMode = false;

        // Handle toggle click
        toggle.addEventListener("click", () => {
            this.isCreateNewMode = !this.isCreateNewMode;

            if (this.isCreateNewMode) {
                // Create new client mode
                existingClientSection.classList.add("hidden");
                newClientSection.classList.remove("hidden");
                toggle.classList.remove(
                    "bg-gray-100",
                    "hover:bg-gray-200",
                    "text-gray-700"
                );
                toggle.classList.add(
                    "bg-indigo-100",
                    "hover:bg-indigo-200",
                    "text-indigo-700",
                    "border-indigo-300"
                );
                toggle.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver a Seleccionar Cliente
                `;
                if (createBtnText) {
                    createBtnText.textContent =
                        this.translations.create_confirmed_lead ||
                        "Crear y Confirmar Lead";
                }
                // Clear client selector
                if (clientSelector) {
                    clientSelector.value = "";
                }
            } else {
                // Select existing client mode
                existingClientSection.classList.remove("hidden");
                newClientSection.classList.add("hidden");
                toggle.classList.remove(
                    "bg-indigo-100",
                    "hover:bg-indigo-200",
                    "text-indigo-700",
                    "border-indigo-300"
                );
                toggle.classList.add(
                    "bg-gray-100",
                    "hover:bg-gray-200",
                    "text-gray-700"
                );
                toggle.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    ${
                        this.translations.create_new_client ||
                        "Crear Nuevo Cliente"
                    }
                `;
                if (createBtnText) {
                    createBtnText.textContent =
                        this.translations.create_lead || "Create Lead";
                }
                // Load clients if not already loaded
                this.loadClients();
            }
        });

        // Add event listener for hide button
        const hideNewClientBtn = document.getElementById("hideNewClientBtn");
        if (hideNewClientBtn) {
            hideNewClientBtn.addEventListener("click", () => {
                // Switch back to existing client mode
                this.isCreateNewMode = false;
                existingClientSection.classList.remove("hidden");
                newClientSection.classList.add("hidden");
                toggle.classList.remove(
                    "bg-indigo-100",
                    "hover:bg-indigo-200",
                    "text-indigo-700",
                    "border-indigo-300"
                );
                toggle.classList.add(
                    "bg-gray-100",
                    "hover:bg-gray-200",
                    "text-gray-700"
                );
                toggle.innerHTML = `
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    ${
                        this.translations.create_new_client ||
                        "Crear Nuevo Cliente"
                    }
                `;
                if (createBtnText) {
                    createBtnText.textContent =
                        this.translations.create_lead || "Create Lead";
                }
                // Clear all error messages when hiding the new client form
                const form = document.getElementById("newAppointmentForm");
                if (form) {
                    const errorMessages =
                        form.querySelectorAll(".error-message");
                    errorMessages.forEach((error) => {
                        error.textContent = "";
                        error.style.display = "none";
                    });
                }

                // Load clients if not already loaded
                this.loadClients();
            });
        }

        // Initialize with default state (select existing client)
        this.isCreateNewMode = false;
        existingClientSection.classList.remove("hidden");
        newClientSection.classList.add("hidden");
        if (createBtnText) {
            createBtnText.textContent =
                this.translations.create_lead || "Create Lead";
        }
        // Load clients on initialization
        this.loadClients();
    }

    /**
     * Load clients for the selector
     */
    async loadClients() {
        const clientSelector = document.getElementById("clientSelector");
        if (!clientSelector) return;

        try {
            const response = await fetch(this.routes.getClients);
            if (!response.ok) {
                throw new Error("Failed to fetch clients");
            }

            const data = await response.json();
            const clients = data.data || data;

            // Clear existing options except the first one
            clientSelector.innerHTML = `<option value="">${
                this.translations.please_select_client ||
                "Please select a client"
            }</option>`;

            // Add client options
            clients.forEach((client) => {
                const option = document.createElement("option");
                option.value = client.uuid;
                option.textContent = `${client.first_name} ${client.last_name} - ${client.phone}`;
                clientSelector.appendChild(option);
            });
        } catch (error) {
            console.error("Error loading clients:", error);
            // Show user-friendly error
            clientSelector.innerHTML = `<option value="">Error loading clients</option>`;
        }
    }

    /**
     * Abrir modal de detalles de evento
     */
    openEventDetailModal(event, props) {
        console.log("DEBUG - openEventDetailModal called with:", {
            event: event,
            props: props,
        });

        const modal = document.getElementById("eventDetailModal");
        if (!modal) {
            console.error("DEBUG - eventDetailModal element not found in DOM");
            return;
        }

        console.log("DEBUG - Modal element found:", modal);

        // Poblar datos del modal
        console.log("DEBUG - Populating modal data");
        this.populateEventDetailModal(event, props);

        // Mostrar modal
        console.log("DEBUG - Showing modal");
        modal.classList.remove("hidden");
        modal.style.display = "block";

        console.log(
            "DEBUG - Modal classes after show:",
            modal.classList.toString()
        );
        console.log("DEBUG - Modal display style:", modal.style.display);
    }

    /**
     * Poblar modal de detalles con datos del evento
     */
    populateEventDetailModal(event, props) {
        // Título
        const titleElement = document.getElementById("modalEventTitle");
        if (titleElement) {
            titleElement.textContent = props.clientName || event.title;
        }

        // Email
        const emailElement = document.getElementById("modalEventEmail");
        if (emailElement) {
            emailElement.textContent = props.clientEmail || "N/A";
        }

        // Teléfono
        const phoneElement = document.getElementById("modalEventPhone");
        if (phoneElement) {
            phoneElement.textContent = props.clientPhone || "N/A";
        }

        // Fecha y hora
        const dateTimeElement = document.getElementById("modalEventDateTime");
        if (dateTimeElement) {
            const start = event.start;
            const end = event.end;
            let formattedDateTime = new Intl.DateTimeFormat("en-US", {
                weekday: "long",
                year: "numeric",
                month: "long",
                day: "numeric",
                hour: "numeric",
                minute: "numeric",
                hour12: true,
            }).format(start);

            if (end) {
                formattedDateTime +=
                    " - " +
                    new Intl.DateTimeFormat("en-US", {
                        hour: "numeric",
                        minute: "numeric",
                        hour12: true,
                    }).format(end);
            } else {
                // Calculate end time (3 hours later) if no end time provided
                const endTime = new Date(start.getTime() + 3 * 60 * 60 * 1000);
                const endTimeStr = endTime.toLocaleTimeString([], {
                    hour: "2-digit",
                    minute: "2-digit",
                    hour12: true,
                });
                formattedDateTime += ` - ${endTimeStr} (3 hours)`;
            }

            dateTimeElement.textContent = formattedDateTime;
        }

        // Estado de la cita
        this.setStatusBadge("modalEventStatus", props.status);

        // Estado del lead
        this.setStatusBadge("modalEventLeadStatus", props.leadStatus);

        // Dirección
        const addressElement = document.getElementById("modalEventAddress");
        if (addressElement) {
            addressElement.textContent = props.address || "N/A";
        }

        // Configurar compartir ubicación
        this.setupMapSharing(props);

        // Notas
        const notesElement = document.getElementById("modalEventNotes");
        if (notesElement) {
            notesElement.textContent = props.notes || "N/A";
        }

        // Daños
        const damageElement = document.getElementById("modalEventDamage");
        if (damageElement) {
            damageElement.textContent = props.damage || "N/A";
        }

        // Seguro
        this.setInsuranceBadge("modalEventInsurance", props.hasInsurance);

        // Configurar botones de acción
        this.setupActionButtons(props.status);
    }

    /**
     * Configurar badge de estado
     */
    setStatusBadge(elementId, status) {
        const element = document.getElementById(elementId);
        if (!element) return;

        element.textContent = status || "N/A";
        element.className =
            "px-2 py-1 text-xs font-semibold rounded-full text-white";

        switch (status) {
            case "Confirmed":
                element.classList.add("bg-purple-600");
                break;
            case "Completed":
                element.classList.add("bg-green-600");
                break;
            case "Pending":
                element.classList.add("bg-orange-600");
                break;
            case "Declined":
                element.classList.add("bg-red-600");
                break;
            case "New":
                element.classList.add("bg-blue-600");
                break;
            case "Called":
                element.classList.add("bg-green-600");
                break;
            default:
                element.classList.add("bg-gray-600");
                break;
        }
    }

    /**
     * Configurar badge de seguro
     */
    setInsuranceBadge(elementId, hasInsurance) {
        const element = document.getElementById(elementId);
        if (!element) return;

        element.className =
            "px-2 py-1 text-xs font-semibold rounded-full text-white";

        if (hasInsurance === "Yes") {
            element.textContent = "Yes";
            element.classList.add("bg-green-600");
        } else if (hasInsurance === "No") {
            element.textContent = "No";
            element.classList.add("bg-red-600");
        } else {
            element.textContent = "N/A";
            element.classList.add("bg-gray-600");
        }
    }

    /**
     * Configurar botones de acción según estado
     */
    setupActionButtons(status) {
        const statusActionButtons = document.getElementById(
            "statusActionButtons"
        );
        const confirmBtn = document.getElementById("confirmAppointmentBtn");

        if (!statusActionButtons) return;

        if (status === "Completed" || status === "Declined") {
            statusActionButtons.classList.add("hidden");
        } else {
            statusActionButtons.classList.remove("hidden");

            // Deshabilitar botón confirmar si ya está confirmado
            if (confirmBtn) {
                if (status === "Confirmed") {
                    confirmBtn.classList.add(
                        "opacity-50",
                        "cursor-not-allowed"
                    );
                    confirmBtn.disabled = true;
                } else {
                    confirmBtn.classList.remove(
                        "opacity-50",
                        "cursor-not-allowed"
                    );
                    confirmBtn.disabled = false;
                }
            }
        }
    }

    /**
     * Configurar funcionalidad de compartir ubicación
     */
    setupMapSharing(props) {
        const address = props.address || "";
        const lat = props.latitude || "";
        const lng = props.longitude || "";

        // Almacenar coordenadas en campos ocultos
        const latField = document.getElementById("event-latitude");
        const lngField = document.getElementById("event-longitude");

        if (latField) latField.value = lat;
        if (lngField) lngField.value = lng;

        // Crear URL de Google Maps
        const mapsUrl =
            lat && lng
                ? `https://www.google.com/maps?q=${lat},${lng}`
                : `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(
                      address
                  )}`;

        // Configurar enlaces de compartir
        this.setupSharingLinks(address, mapsUrl);
    }

    /**
     * Configurar enlaces de compartir
     */
    setupSharingLinks(address, mapsUrl) {
        // WhatsApp
        const whatsappLink = document.getElementById("share-whatsapp");
        if (whatsappLink) {
            whatsappLink.href = `https://wa.me/?text=Location for inspection: ${encodeURIComponent(
                address
            )} - ${encodeURIComponent(mapsUrl)}`;
            whatsappLink.target = "_blank";
        }

        // Email
        const emailLink = document.getElementById("share-email");
        if (emailLink) {
            const subject = encodeURIComponent("Location for inspection");
            const body = encodeURIComponent(
                `The location for the inspection is: ${address}\n\nView in Google Maps: ${mapsUrl}`
            );
            emailLink.href = `mailto:?subject=${subject}&body=${body}`;
        }

        // Google Maps
        const mapsLink = document.getElementById("share-maps");
        if (mapsLink) {
            mapsLink.href = mapsUrl;
            mapsLink.target = "_blank";
        }

        // Botón copiar
        const copyButton = document.getElementById("copy-address");
        if (copyButton) {
            copyButton.onclick = (e) => {
                e.preventDefault();
                navigator.clipboard.writeText(mapsUrl).then(() => {
                    const originalHTML = copyButton.innerHTML;
                    copyButton.innerHTML =
                        '<svg class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> Copied!';
                    setTimeout(() => {
                        copyButton.innerHTML = originalHTML;
                    }, 2000);
                });
            };
        }

        // Habilitar/deshabilitar botones según disponibilidad de dirección
        const buttons = [whatsappLink, emailLink, mapsLink, copyButton];
        buttons.forEach((button) => {
            if (button) {
                if (!address) {
                    button.classList.add("opacity-50", "cursor-not-allowed");
                } else {
                    button.classList.remove("opacity-50", "cursor-not-allowed");
                }
            }
        });
    }

    /**
     * Cerrar modal de detalles de evento
     */
    closeEventDetailModal() {
        const modal = document.getElementById("eventDetailModal");
        if (modal) {
            modal.classList.add("hidden");
            modal.style.display = "none";
        }
    }

    /**
     * Abrir modal de nueva cita (ahora para crear leads)
     */
    openNewAppointmentModal(start, end) {
        this.selectedStart = start;
        this.selectedEnd = end;

        const modal = document.getElementById("newAppointmentModal");
        if (!modal) return;

        // Limpiar formulario
        this.clearLeadForm();

        // Configurar fecha y hora de inspección si se seleccionó desde calendario
        if (start) {
            this.setInspectionDateTime(start);
            // Formatear y mostrar la fecha/hora seleccionada
            this.formatSelectedDateTime(start, end);
        }

        // Inicializar Google Maps si está disponible
        this.initializeGoogleMaps();

        // Configurar validación en tiempo real
        this.setupFormValidation();

        // Mostrar modal
        modal.classList.remove("hidden");
        modal.style.display = "block";
    }

    /**
     * Formatear fecha y hora seleccionada
     */
    formatSelectedDateTime(start, end) {
        const selectedDateTime = document.getElementById("selectedDateTime");
        const appointmentDate = document.getElementById("appointmentDate");
        const appointmentTime = document.getElementById("appointmentTime");

        if (!selectedDateTime || !appointmentDate || !appointmentTime) return;

        // Formatear fecha para mostrar (formato más compacto)
        const formattedDate = start.toLocaleDateString("en-US", {
            month: "short",
            day: "numeric",
            year: "numeric",
        });

        // Asegurar que la cita dure 3 horas (estandarizado)
        const actualEnd = new Date(start.getTime() + 3 * 60 * 60 * 1000);

        const formattedTime =
            start.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            }) +
            " - " +
            actualEnd.toLocaleTimeString("en-US", {
                hour: "2-digit",
                minute: "2-digit",
                hour12: false,
            });

        // Mostrar fecha y hora formateada (formato compacto)
        selectedDateTime.value = `${formattedDate} • ${formattedTime} (3h)`;

        // Configurar campos ocultos para envío
        const dateStr = start.toISOString().split("T")[0]; // YYYY-MM-DD
        const timeStr = start.toTimeString().substring(0, 5); // HH:MM

        appointmentDate.value = dateStr;
        appointmentTime.value = timeStr;
    }

    /**
     * Cerrar modal de nueva cita
     */
    closeNewAppointmentModal() {
        const modal = document.getElementById("newAppointmentModal");
        if (modal) {
            modal.classList.add("hidden");
            modal.style.display = "none";
        }

        // Clear the form when closing
        this.clearLeadForm();

        // Reset button text to correct default state (existing client mode)
        const createBtnText = document.getElementById("createBtnText");
        if (createBtnText) {
            createBtnText.textContent =
                this.translations.create_lead || "Create Lead";
        }

        // Ensure we're in existing client mode after reset
        const existingClientSection = document.getElementById(
            "existingClientSection"
        );
        const newClientSection = document.getElementById("newClientSection");
        if (existingClientSection && newClientSection) {
            existingClientSection.classList.remove("hidden");
            newClientSection.classList.add("hidden");
        }

        // Limpiar selección en calendario
        if (this.calendar) {
            this.calendar.unselect();
        }

        // Remove temporary selection event
        if (window.CalendarEvents) {
            window.CalendarEvents.removeTemporarySelectionEvent();
        }
    }

    /**
     * Manejar creación de cita
     */
    handleCreateAppointment() {
        const button = document.getElementById("createAppointmentBtn");

        // Prevenir envío si el botón está deshabilitado
        if (button.disabled) {
            return;
        }

        const toggle = document.getElementById("createNewClientToggle");
        const newClientSection = document.getElementById("newClientSection");
        const isNewClient = this.isCreateNewMode;

        // Validar formulario según el modo
        if (!this.validateForm(isNewClient)) {
            return;
        }

        // Mostrar estado de carga
        const normalText = button.querySelector(".normal-btn-text");
        const loadingText = button.querySelector(".loading-btn-text");

        normalText.classList.add("hidden");
        loadingText.classList.remove("hidden");
        button.disabled = true;

        // Preparar datos según el modo
        const formData = this.prepareFormData(isNewClient);

        // Determinar endpoint
        const endpoint = isNewClient
            ? this.routes.store
            : this.routes.createAppointment;

        fetch(endpoint, {
            method: "POST",
            body: formData,
            headers: {
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    .getAttribute("content"),
            },
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.success) {
                    Swal.fire({
                        title: this.translations.success,
                        text: data.message,
                        icon: "success",
                        confirmButtonText: this.translations.ok,
                    }).then(() => {
                        this.closeNewAppointmentModal();
                        // Remove temporary selection event before refreshing
                        if (window.CalendarEvents) {
                            window.CalendarEvents.removeTemporarySelectionEvent();
                        }
                        // Refrescar calendario
                        if (this.calendar) {
                            this.calendar.refetchEvents();
                        }
                    });
                } else {
                    this.handleFormErrors(data.errors || {});
                    Swal.fire(
                        this.translations.error,
                        data.message || this.translations.unexpected_error,
                        "error"
                    );
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                Swal.fire(
                    this.translations.error,
                    this.translations.unexpected_error,
                    "error"
                );
            })
            .finally(() => {
                // Restaurar estado del botón
                normalText.classList.remove("hidden");
                loadingText.classList.add("hidden");
                button.disabled = false;
            });
    }

    /**
     * Prepare form data based on mode
     */
    prepareFormData(isNewClient) {
        const formData = new FormData();

        if (isNewClient) {
            // New client mode - get all form data
            const form = document.getElementById("newAppointmentForm");
            const formDataFromForm = new FormData(form);

            // Copy all form data
            for (let [key, value] of formDataFromForm.entries()) {
                formData.append(key, value);
            }
        } else {
            // Existing client mode - only need client_uuid and appointment details
            const clientSelector = document.getElementById("clientSelector");
            if (clientSelector && clientSelector.value) {
                formData.append("client_uuid", clientSelector.value);
            }
        }

        // Add inspection date/time (common for both modes)
        if (this.selectedStart) {
            formData.append(
                "inspection_date",
                this.selectedStart.toISOString().split("T")[0]
            );
            formData.append(
                "inspection_time",
                this.selectedStart.toTimeString().substring(0, 5)
            );

            // Add inspection_status for existing client appointments
            if (!isNewClient) {
                formData.append("inspection_status", "Confirmed");
            }
        }

        return formData;
    }

    /**
     * Validate form based on mode
     */
    validateForm(isNewClient) {
        if (isNewClient) {
            // Validate new client form
            return this.validateLeadForm();
        } else {
            // Validate existing client selection
            const clientSelector = document.getElementById("clientSelector");
            if (!clientSelector || !clientSelector.value) {
                const errorElement = document.querySelector(
                    '.error-message[data-field="client_uuid"]'
                );
                if (errorElement) {
                    this.showFieldError(
                        errorElement,
                        this.translations.please_select_client ||
                            "Please select a client"
                    );
                }
                return false;
            }

            // Clear any previous error
            const errorElement = document.querySelector(
                '.error-message[data-field="client_uuid"]'
            );
            if (errorElement) {
                this.clearFieldError(errorElement);
            }

            return true;
        }
    }

    /**
     * Limpiar formulario de lead
     */
    clearLeadForm() {
        const form = document.getElementById("newAppointmentForm");
        if (!form) return;

        // Limpiar todos los campos de texto
        const textInputs = form.querySelectorAll(
            'input[type="text"], input[type="email"], input[type="tel"], input[type="date"], input[type="time"], textarea'
        );
        textInputs.forEach((input) => (input.value = ""));

        // Limpiar campo de fecha/hora seleccionada
        const selectedDateTime = document.getElementById("selectedDateTime");
        if (selectedDateTime) {
            selectedDateTime.textContent =
                this.translations.select_time_from_calendar ||
                "Selecciona un horario del calendario";
        }

        // Limpiar radio buttons y sus labels
        const radioInputs = form.querySelectorAll('input[type="radio"]');
        radioInputs.forEach((input) => {
            input.checked = false;
            // Remover clase selected de labels de insurance
            const label = form.querySelector(`label[for="${input.id}"]`);
            if (label && label.classList.contains("insurance-label")) {
                label.classList.remove("selected");
            }
        });

        // Limpiar checkboxes (except the toggle)
        const checkboxInputs = form.querySelectorAll(
            'input[type="checkbox"]:not(#createNewClientToggle)'
        );
        checkboxInputs.forEach((input) => (input.checked = false));

        // Reset toggle to default state (select existing client)
        const toggle = document.getElementById("createNewClientToggle");
        const existingClientSection = document.getElementById(
            "existingClientSection"
        );
        const newClientSection = document.getElementById("newClientSection");
        const createBtnText = document.getElementById("createBtnText");

        if (toggle && existingClientSection && newClientSection) {
            // Reset toggle state
            this.isCreateNewMode = false;

            // Reset to existing client mode
            existingClientSection.classList.remove("hidden");
            newClientSection.classList.add("hidden");
            toggle.classList.remove(
                "bg-indigo-100",
                "hover:bg-indigo-200",
                "text-indigo-700",
                "border-indigo-300"
            );
            toggle.classList.add(
                "bg-gray-100",
                "hover:bg-gray-200",
                "text-gray-700"
            );
            toggle.innerHTML = `
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                ${this.translations.create_new_client || "Crear Nuevo Cliente"}
            `;

            // Reset button text to match existing client mode
            if (createBtnText) {
                createBtnText.textContent =
                    this.translations.create_lead || "Create Lead";
            }
        }

        // Clear client selector
        const clientSelector = document.getElementById("clientSelector");
        if (clientSelector) {
            clientSelector.value = "";
        }

        // Reset all select elements to their first option
        const selectElements = form.querySelectorAll("select");
        selectElements.forEach((select) => {
            if (select.options.length > 0) {
                select.selectedIndex = 0;
                // Dispatch change event to ensure dependent elements are updated
                select.dispatchEvent(new Event("change", { bubbles: true }));
            }
        });

        // Limpiar mensajes de error
        const errorMessages = form.querySelectorAll(".error-message");
        errorMessages.forEach((error) => (error.textContent = ""));

        // Limpiar mapa si existe
        if (this.leadMap) {
            this.leadMap.setCenter({ lat: 39.8283, lng: -98.5795 }); // Centro de EE.UU.
            this.leadMap.setZoom(4);
            if (this.leadMapMarker) {
                this.leadMapMarker.setMap(null);
            }
        }
    }

    /**
     * Configurar fecha y hora de inspección
     */
    setInspectionDateTime(start) {
        const dateInput = document.getElementById("inspection_date");
        const timeInput = document.getElementById("inspection_time");

        if (dateInput && timeInput) {
            const dateStr = start.toISOString().split("T")[0]; // YYYY-MM-DD
            const timeStr = start.toTimeString().substring(0, 5); // HH:MM

            dateInput.value = dateStr;
            timeInput.value = timeStr;
        }
    }

    /**
     * Inicializar Google Maps
     */
    initializeGoogleMaps() {
        if (typeof google === "undefined" || !google.maps) {
            console.warn("Google Maps API not loaded");
            return;
        }

        const mapContainer = document.getElementById("location-map");
        const addressInput = document.getElementById("address_map_input");

        if (!mapContainer || !addressInput) return;

        // Inicializar mapa
        this.leadMap = new google.maps.Map(mapContainer, {
            center: { lat: 39.8283, lng: -98.5795 }, // Centro de EE.UU.
            zoom: 4,
        });

        // Configurar autocompletado
        this.leadAutocomplete = new google.maps.places.Autocomplete(
            addressInput,
            {
                types: ["address"],
                componentRestrictions: { country: "us" },
            }
        );

        // Configurar listener del autocompletado
        this.leadAutocomplete.addListener("place_changed", () => {
            const place = this.leadAutocomplete.getPlace();
            this.handlePlaceSelection(place);
        });
    }

    /**
     * Manejar selección de lugar
     */
    handlePlaceSelection(place) {
        if (!place.geometry) return;

        // Actualizar mapa
        this.leadMap.setCenter(place.geometry.location);
        this.leadMap.setZoom(15);

        // Limpiar marcador anterior
        if (this.leadMapMarker) {
            this.leadMapMarker.setMap(null);
        }

        // Agregar nuevo marcador
        this.leadMapMarker = new google.maps.Marker({
            position: place.geometry.location,
            map: this.leadMap,
            title: place.formatted_address,
        });

        // Extraer componentes de dirección
        const addressComponents = place.address_components;
        let streetNumber = "";
        let route = "";
        let city = "";
        let state = "";
        let zipcode = "";
        let country = "";

        addressComponents.forEach((component) => {
            const types = component.types;
            if (types.includes("street_number")) {
                streetNumber = component.long_name;
            } else if (types.includes("route")) {
                route = component.long_name;
            } else if (types.includes("locality")) {
                city = component.long_name;
            } else if (types.includes("administrative_area_level_1")) {
                state = component.short_name;
            } else if (types.includes("postal_code")) {
                zipcode = component.long_name;
            } else if (types.includes("country")) {
                country = "USA"; // Always set to USA for US addresses
            }
        });

        // Actualizar campos ocultos
        document.getElementById("address").value =
            `${streetNumber} ${route}`.trim();
        document.getElementById("city").value = city;
        document.getElementById("state").value = state;
        document.getElementById("zipcode").value = zipcode;
        document.getElementById("country").value = country;

        // Actualizar coordenadas
        const lat = place.geometry.location.lat();
        const lng = place.geometry.location.lng();
        document.getElementById("latitude").value = lat;
        document.getElementById("longitude").value = lng;

        // Update the visible address field with complete address including zipcode
        const addressMapInput = document.getElementById("address_map_input");
        if (addressMapInput && place.formatted_address) {
            let completeAddress = place.formatted_address;

            // Check if zipcode is not already in the formatted address and add it if missing
            if (zipcode && !completeAddress.includes(zipcode)) {
                completeAddress += `, ${zipcode}`;
            }

            addressMapInput.value = completeAddress;
        }
    }

    /**
     * Configurar validación en tiempo real
     */
    setupFormValidation() {
        const form = document.getElementById("newAppointmentForm");
        if (!form) return;

        const submitButton = document.getElementById("createAppointmentBtn");
        const firstNameInput = document.getElementById("first_name");
        const lastNameInput = document.getElementById("last_name");
        const emailInput = document.getElementById("email");
        const phoneInput = document.getElementById("phone");
        const requiredInputs = form.querySelectorAll("[required]");

        // Debounce function for AJAX calls
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };

        // Check form validity and enable/disable submit button
        const checkFormValidity = () => {
            let allRequiredFilled = true;
            requiredInputs.forEach((input) => {
                let value = input.value.trim();
                if (input.type === "radio") {
                    const groupName = input.name;
                    if (
                        !form.querySelector(
                            `input[name="${groupName}"]:checked`
                        )
                    ) {
                        allRequiredFilled = false;
                    }
                } else if (!value) {
                    allRequiredFilled = false;
                }
            });

            // Check if any error messages are currently displayed
            const hasVisibleErrors = Array.from(
                form.querySelectorAll(".error-message")
            ).some((span) => span.textContent.trim() !== "");

            // Enable button only if all required fields are filled AND there are no errors
            submitButton.disabled = !allRequiredFilled || hasVisibleErrors;
        };

        // Format phone input (improved version from Facebook modal)
        const formatPhoneInput = (inputElement, event) => {
            const isBackspace = event?.inputType === "deleteContentBackward";
            let value = inputElement.value.replace(/\D/g, "");

            if (isBackspace) {
                // Allow backspace to work naturally
            } else {
                value = value.substring(0, 10);
            }

            let formattedValue = "";
            if (value.length === 0) {
                formattedValue = "";
            } else if (value.length <= 3) {
                formattedValue = `(${value}`;
            } else if (value.length <= 6) {
                formattedValue = `(${value.substring(0, 3)}) ${value.substring(
                    3
                )}`;
            } else {
                formattedValue = `(${value.substring(0, 3)}) ${value.substring(
                    3,
                    6
                )}-${value.substring(6, 10)}`;
            }
            inputElement.value = formattedValue;
        };

        // Format name inputs
        const formatName = (inputElement) => {
            const cursorPosition = inputElement.selectionStart;
            let value = inputElement.value;

            if (typeof value === "string" && value.length > 0) {
                // Limit to 50 characters
                if (value.length > 50) {
                    value = value.substring(0, 50);
                }

                // Check if value ends with space to preserve it
                const endsWithSpace = value.endsWith(" ");

                // Replace multiple spaces with single space
                value = value.replace(/\s+/g, " ");

                // Split by spaces and filter out empty parts
                let parts = value
                    .trim()
                    .split(" ")
                    .filter((part) => part.length > 0);

                // Capitalize each word
                parts = parts.map((part) => {
                    return (
                        part.charAt(0).toUpperCase() +
                        part.slice(1).toLowerCase()
                    );
                });

                // Join parts with single space
                let formattedValue = parts.join(" ");

                // Preserve trailing space if original had it and we're under 50 chars
                if (endsWithSpace && formattedValue.length < 50) {
                    formattedValue += " ";
                }

                inputElement.value = formattedValue;

                // Restore cursor position
                const newCursorPosition = Math.min(
                    cursorPosition,
                    formattedValue.length
                );
                inputElement.setSelectionRange(
                    newCursorPosition,
                    newCursorPosition
                );
            }
        };

        // AJAX validation functions
        const validateEmailAjax = async (email) => {
            if (!email) return { valid: true, exists: false };

            try {
                const response = await fetch(
                    "/appointment-calendar/check-email",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify({ email: email }),
                    }
                );

                return await response.json();
            } catch (error) {
                console.error("Email validation error:", error);
                return { valid: true, exists: false };
            }
        };

        const validatePhoneAjax = async (phone) => {
            if (!phone) return { valid: true, exists: false };

            try {
                const response = await fetch(
                    "/appointment-calendar/check-phone",
                    {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute("content"),
                        },
                        body: JSON.stringify({ phone: phone }),
                    }
                );

                return await response.json();
            } catch (error) {
                console.error("Phone validation error:", error);
                return { valid: true, exists: false };
            }
        };

        // Debounced validation functions
        const debouncedEmailValidation = debounce(async (inputElement) => {
            const email = inputElement.value.trim();
            const errorElement = document.querySelector(
                '.error-message[data-field="email"]'
            );

            if (!email) {
                this.showFieldError(
                    errorElement,
                    this.translations.email_required || "Email is required"
                );
                checkFormValidity();
                return;
            }

            const result = await validateEmailAjax(email);

            if (!result.valid) {
                this.showFieldError(
                    errorElement,
                    result.message ||
                        this.translations.invalid_email_format ||
                        "Invalid email format"
                );
            } else if (result.exists) {
                this.showFieldError(
                    errorElement,
                    result.message ||
                        this.translations.email_already_exists ||
                        "This email is already registered"
                );
            } else {
                this.clearFieldError(errorElement);
            }

            checkFormValidity();
        }, 500);

        const debouncedPhoneValidation = debounce(async (inputElement) => {
            const phone = inputElement.value.trim();
            const errorElement = document.querySelector(
                '.error-message[data-field="phone"]'
            );

            if (!phone) {
                this.showFieldError(
                    errorElement,
                    this.translations.phone_required || "Phone is required"
                );
                checkFormValidity();
                return;
            }

            const result = await validatePhoneAjax(phone);

            if (!result.valid) {
                this.showFieldError(
                    errorElement,
                    result.message ||
                        this.translations.invalid_phone_format ||
                        "Invalid phone format"
                );
            } else if (result.exists) {
                this.showFieldError(
                    errorElement,
                    result.message ||
                        this.translations.phone_already_exists ||
                        "This phone number is already registered"
                );
            } else {
                this.clearFieldError(errorElement);
            }

            checkFormValidity();
        }, 500);

        // Setup event listeners
        if (firstNameInput) {
            firstNameInput.addEventListener("input", (event) => {
                formatName(event.target);
                checkFormValidity();
            });
            firstNameInput.addEventListener("blur", (event) => {
                formatName(event.target);
                this.validateName(event);
                checkFormValidity();
            });
        }

        if (lastNameInput) {
            lastNameInput.addEventListener("input", (event) => {
                formatName(event.target);
                checkFormValidity();
            });
            lastNameInput.addEventListener("blur", (event) => {
                formatName(event.target);
                this.validateName(event);
                checkFormValidity();
            });
        }

        if (emailInput) {
            emailInput.addEventListener("input", (event) => {
                // Basic format validation on input for immediate feedback
                const email = event.target.value.trim();
                const errorElement = document.querySelector(
                    '.error-message[data-field="email"]'
                );

                if (!email) {
                    this.showFieldError(
                        errorElement,
                        this.translations.email_required || "Email is required"
                    );
                } else {
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (emailRegex.test(email)) {
                        // Clear error if format is valid (AJAX validation will run on blur)
                        this.clearFieldError(errorElement);
                    } else {
                        this.showFieldError(
                            errorElement,
                            this.translations.invalid_email_format ||
                                "Please enter a valid email address"
                        );
                    }
                }

                checkFormValidity();
            });
            emailInput.addEventListener("blur", (event) => {
                debouncedEmailValidation(event.target);
            });
        }

        if (phoneInput) {
            phoneInput.addEventListener("input", (event) => {
                formatPhoneInput(phoneInput, event);

                // Basic format validation on input for immediate feedback
                const phone = event.target.value.trim();
                const errorElement = document.querySelector(
                    '.error-message[data-field="phone"]'
                );

                if (!phone) {
                    this.showFieldError(
                        errorElement,
                        this.translations.phone_required || "Phone is required"
                    );
                } else {
                    // Basic phone validation - check if it has at least 10 digits
                    const phoneDigits = phone.replace(/\D/g, "");
                    if (phoneDigits.length >= 10) {
                        // Clear error if basic format is valid (AJAX validation will run on blur)
                        this.clearFieldError(errorElement);
                    } else {
                        this.showFieldError(
                            errorElement,
                            this.translations.invalid_phone_format ||
                                "Please enter a valid phone number"
                        );
                    }
                }

                checkFormValidity();
            });
            phoneInput.addEventListener("blur", (event) => {
                debouncedPhoneValidation(event.target);
            });
        }

        // Setup other inputs
        const allInputs = form.querySelectorAll(
            ".input-field, .radio-field, .checkbox-field"
        );
        allInputs.forEach((input) => {
            if (
                input.name === "first_name" ||
                input.name === "last_name" ||
                input.name === "phone" ||
                input.name === "email"
            )
                return;

            if (input.type === "checkbox" || input.type === "radio") {
                input.addEventListener("change", () => {
                    checkFormValidity();
                });
            } else {
                input.addEventListener("input", () => {
                    checkFormValidity();
                });
            }
        });

        // Initial check
        checkFormValidity();
    }

    /**
     * Validar email
     */
    validateEmail(e) {
        const email = e.target.value;
        const errorElement = document.querySelector(
            '.error-message[data-field="email"]'
        );

        if (!email) {
            this.showFieldError(
                errorElement,
                this.translations.email_required || "Email is required"
            );
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showFieldError(
                errorElement,
                this.translations.invalid_email_format ||
                    "Please enter a valid email address"
            );
            return false;
        }

        this.clearFieldError(errorElement);
        return true;
    }

    /**
     * Validar nombre
     */
    validateName(e) {
        const name = e.target.value;
        const fieldName = e.target.name;
        const errorElement = document.querySelector(
            `.error-message[data-field="${fieldName}"]`
        );

        if (!name) {
            const requiredMessage =
                fieldName === "first_name"
                    ? this.translations.first_name_required ||
                      "First name is required"
                    : fieldName === "last_name"
                    ? this.translations.last_name_required ||
                      "Last name is required"
                    : `${fieldName.replace("_", " ")} is required`;
            this.showFieldError(errorElement, requiredMessage);
            return false;
        }

        const nameRegex = /^[a-zA-Z\s'-]+$/;
        if (!nameRegex.test(name)) {
            this.showFieldError(
                errorElement,
                this.translations.invalid_name || "Please enter a valid name"
            );
            return false;
        }

        this.clearFieldError(errorElement);
        return true;
    }

    /**
     * Mostrar error en campo
     */
    showFieldError(errorElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = "block";
        }
    }

    /**
     * Limpiar error en campo
     */
    clearFieldError(errorElement) {
        if (errorElement) {
            errorElement.textContent = "";
            errorElement.style.display = "none";
        }
    }

    /**
     * Validar formulario de lead
     */
    validateLeadForm() {
        const form = document.getElementById("newAppointmentForm");
        if (!form) return false;

        let isValid = true;

        // Validar campos requeridos
        const requiredFields = [
            "first_name",
            "last_name",
            "email",
            "phone",
            "address_map_input",
            "lead_source",
        ];
        requiredFields.forEach((fieldName) => {
            const field = document.getElementById(fieldName);
            const errorElement = document.querySelector(
                `.error-message[data-field="${fieldName}"]`
            );

            if (!field || !field.value.trim()) {
                const requiredMessage =
                    fieldName === "first_name"
                        ? this.translations.first_name_required ||
                          "First name is required"
                        : fieldName === "last_name"
                        ? this.translations.last_name_required ||
                          "Last name is required"
                        : fieldName === "email"
                        ? this.translations.email_required ||
                          "Email is required"
                        : fieldName === "phone"
                        ? this.translations.phone_required ||
                          "Phone is required"
                        : `${fieldName.replace("_", " ")} is required`;
                this.showFieldError(errorElement, requiredMessage);
                isValid = false;
            } else {
                this.clearFieldError(errorElement);
            }
        });

        // Validar insurance_property
        const insuranceRadios = document.querySelectorAll(
            'input[name="insurance_property"]'
        );
        const insuranceChecked = Array.from(insuranceRadios).some(
            (radio) => radio.checked
        );
        const insuranceError = document.querySelector(
            '.error-message[data-field="insurance_property"]'
        );

        if (!insuranceChecked) {
            this.showFieldError(
                insuranceError,
                this.translations.please_select_insurance_option ||
                    "Please select an insurance option"
            );
            isValid = false;
        } else {
            this.clearFieldError(insuranceError);
        }

        return isValid;
    }

    /**
     * Manejar errores del formulario
     */
    handleFormErrors(errors) {
        Object.keys(errors).forEach((fieldName) => {
            const errorElement = document.querySelector(
                `.error-message[data-field="${fieldName}"]`
            );
            if (errorElement && errors[fieldName][0]) {
                this.showFieldError(errorElement, errors[fieldName][0]);
            }
        });
    }
}

// Hacer disponible globalmente
window.CalendarModals = CalendarModals;
