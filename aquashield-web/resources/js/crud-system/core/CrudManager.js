import { CrudApiClient } from "./CrudApiClient.js";
import { CrudFormBuilder } from "./CrudFormBuilder.js";
import { CrudValidator } from "./CrudValidator.js";
import { CrudTableRenderer } from "./CrudTableRenderer.js";
import { CrudModalManager } from "./CrudModalManager.js";
import { CrudEventHandler } from "./CrudEventHandler.js";
import { debounce } from "../utils/CrudUtils.js";
import { crudTranslations } from "../utils/CrudTranslations.js";

export class CrudManager {
    constructor(options) {
        // Configuración básica
        this.entityName = options.entityName || "Entity";
        this.entityNamePlural = options.entityNamePlural || "Entities";
        this.routes = options.routes || {};
        this.idField = options.idField || "id";

        // Selectores de elementos DOM
        this.tableSelector = options.tableSelector || "#dataTable";
        this.searchSelector = options.searchSelector || "#searchInput";
        this.perPageSelector = options.perPageSelector || "#perPage";
        this.showDeletedSelector =
            options.showDeletedSelector || "#showDeleted";
        this.paginationSelector = options.paginationSelector || "#pagination";
        this.alertSelector = options.alertSelector || "#alertContainer";

        // Configuración de tabla
        this.tableHeaders = options.tableHeaders || [];
        this.validationFields = options.validationFields || [];
        this.formFields = options.formFields || [];

        // Configuración de numeración secuencial
        this.showSequentialNumbers = options.showSequentialNumbers !== false; // default true
        this.sequentialNumberLabel = options.sequentialNumberLabel || "Nro";

        // Estado interno
        this.currentPage = 1;
        this.perPage = 10;
        this.sortField = options.defaultSortField || "created_at";
        this.sortDirection = options.defaultSortDirection || "desc";
        this.searchTerm = "";
        this.showDeleted = options.showDeleted || false;
        this.currentEntity = null;
        this.currentData = null;
        this.isEditing = false;
        this.alertTimeout = null;

        // Filtros de fecha
        this.dateFilters = {
            startDate: "",
            endDate: "",
            dateField: options.dateField || "created_at", // Campo de fecha por defecto
        };

        // Modo de registro único
        this.singleRecordMode = options.singleRecordMode || false;

        // Configuración de modales
        this.modalConfig = {
            width: options.modalWidth || "800px",
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: "Cancel",
        };

        // Configuración de colores para diferentes modos
        this.colorConfig = {
            create: { confirmButtonColor: "#10B981" },
            edit: { confirmButtonColor: "#3B82F6" },
        };

        // Configuración de traducciones
        this.translations =
            options.translations || this.getDefaultTranslations();

        // Configuración de entidad
        this.entityConfig = options.entityConfig || {
            identifierField: "name",
            displayName: "element",
            fallbackFields: ["title", "description", "email"],
        };

        // Inicializar módulos
        this.apiClient = new CrudApiClient(this.routes);
        this.formBuilder = new CrudFormBuilder(this.formFields);
        this.modalManager = new CrudModalManager(
            this.modalConfig,
            this.colorConfig
        );
        this.validator = new CrudValidator(this.formFields, this.routes);
        this.eventHandler = new CrudEventHandler(this);

        this.init();
    }

    /**
     * Obtener traducciones por defecto usando el sistema centralizado
     */
    getDefaultTranslations() {
        // Usar el sistema de traducciones centralizado
        try {
            return crudTranslations.getCrudTranslations();
        } catch (error) {
            console.warn(
                "CrudTranslations not available, using fallback:",
                error
            );
            // Fallback a traducciones hardcodeadas si no está disponible el sistema
            return {
                confirmDelete: "Are you sure?",
                deleteMessage: "Do you want to delete this element?",
                confirmRestore: "Restore record?",
                restoreMessage: "Do you want to restore this element?",
                yesDelete: "Yes, delete",
                yesRestore: "Yes, restore",
                cancel: "Cancel",
                deletedSuccessfully: "deleted successfully",
                restoredSuccessfully: "restored successfully",
                errorDeleting: "Error deleting record",
                errorRestoring: "Error restoring record",
                validationErrors: "Validation errors",
                pleaseCorrectErrors:
                    "Please correct the errors before continuing",
                createdSuccessfully: "created successfully",
                updatedSuccessfully: "updated successfully",
                errorCreatingRecord: "Error creating record",
                errorUpdatingRecord: "Error updating record",
                invalidIdEditing: "Invalid ID provided for editing",
                noEditInfoFound:
                    "No information found for editing. Contact administrator.",
                errorLoadingEditData: "Error loading data for editing",
                // Pagination translations
                showing: "Showing",
                to: "to",
                of: "of",
                results: "results",
                total_records: "Total Records",
                records: "records",
                // Additional translations
                loading: "Loading...",
                create: "Create",
                edit: "Edit",
                endDateBeforeStart:
                    "End date cannot be earlier than start date",
                confirmAction: "Do you want to",
                deleteAction: "delete",
                restoreAction: "restore",
            };
        }
    }

    /**
     * Inicializar el manager
     */
    init() {
        // Establecer el estado inicial del toggle
        $(this.showDeletedSelector).prop("checked", this.showDeleted);

        this.eventHandler.attachEventListeners();
        this.loadEntities();
    }

    /**
     * Cargar entidades
     */
    async loadEntities(page = 1) {
        console.log("loadEntities called with page:", page);
        this.currentPage = page;

        // Mostrar loading
        this.showTableLoading();

        const requestData = {
            page: this.currentPage,
            per_page: this.perPage,
            sort_field: this.sortField,
            sort_direction: this.sortDirection,
            search: this.searchTerm,
            show_deleted: this.showDeleted ? "true" : "false",
            // Agregar filtros de fecha
            date_start: this.dateFilters.startDate,
            date_end: this.dateFilters.endDate,
            date_field: this.dateFilters.dateField,
        };

        try {
            const response = await this.apiClient.fetchEntities(requestData);
            console.log("AJAX success response:", response);
            this.currentData = response;
            this.renderTable(response);
            this.renderPagination(response);
        } catch (error) {
            console.error("Error loading entities:", error);
            this.modalManager.showAlert(
                "error",
                `Error loading ${this.entityNamePlural}: ${error.message}`
            );
            this.hideTableLoading();
        }
    }

    /**
     * Mostrar modal de creación
     */
    async showCreateModal() {
        // Limpiar alertas previas
        this.modalManager.clearAlerts(this.alertSelector);

        // En modo de registro único, redirigir a editar el registro existente
        if (this.singleRecordMode) {
            await this.loadEntities();
            if (
                this.currentData &&
                this.currentData.data &&
                this.currentData.data.length > 0
            ) {
                const entity = this.currentData.data[0];
                await this.showEditModal(entity[this.idField]);
                return;
            }
            this.modalManager.showAlert(
                "info",
                this.translations.noEditInfoFound ||
                    "No se encontró información para editar. Contacte al administrador."
            );
            return;
        }

        this.isEditing = false;
        this.currentEntity = null;
        this.validator.setEditingContext(false, null, this.translations);

        const formHtml = this.formBuilder.generateFormHtml();

        await this.modalManager.showCreateModal(
            `${this.translations.create || "Crear"} ${this.entityName}`,
            formHtml,
            () => this.validator.validateAndGetFormData(false),
            () => this.initializeFormElements(),
            async (result) => await this.createEntity(result.value)
        );
    }

    /**
     * Mostrar modal de edición
     */
    async showEditModal(id) {
        console.log("showEditModal called with id:", id);

        // Limpiar alertas previas
        this.modalManager.clearAlerts(this.alertSelector);

        this.isEditing = true;

        // Validar que tenemos un ID válido
        if (!id || id === "undefined" || id === "null") {
            console.error("Invalid ID provided to showEditModal:", id);
            this.modalManager.showAlert(
                "error",
                this.translations.invalidIdEditing ||
                    "ID inválido proporcionado para edición"
            );
            return;
        }

        try {
            const editUrl = this.routes.edit.replace(":id", id);
            console.log("Edit URL:", editUrl);

            const response = await $.ajax({
                url: editUrl,
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
            });

            console.log("Edit AJAX response received successfully");
            this.currentEntity = response.data || response;
            this.validator.setEditingContext(
                true,
                this.currentEntity,
                this.translations
            );

            const formHtml = this.formBuilder.generateFormHtml(
                this.currentEntity
            );

            await this.modalManager.showEditModal(
                `${this.translations.edit || "Editar"} ${this.entityName}`,
                formHtml,
                () => this.validator.validateAndGetFormData(true),
                () => {
                    this.initializeFormElements();
                    this.formBuilder.populateForm(this.currentEntity);

                    // Verificar y corregir valores de select después de un breve delay
                    setTimeout(() => {
                        this.formBuilder.verifyAndFixSelectValues(
                            this.currentEntity
                        );
                        this.validator.updateSubmitButtonState();
                    }, 200);
                },
                async (result) => await this.updateEntity(id, result.value)
            );
        } catch (error) {
            console.error("Error loading entity for edit:", error);
            this.modalManager.showAlert(
                "error",
                this.translations.errorLoadingEditData ||
                    "Error al cargar datos para edición"
            );
        }
    }

    /**
     * Crear entidad
     */
    async createEntity(data) {
        try {
            Swal.showLoading();

            const response = await this.apiClient.createEntity(data);

            Swal.close();
            this.modalManager.showAlert(
                "success",
                this.translations.createdSuccessfully ||
                    `${this.entityName} created successfully`,
                this.alertSelector
            );
            this.loadEntities();
        } catch (error) {
            Swal.close();
            console.error("Error creating entity:", error);

            if (error.message.includes("422") && error.responseJSON?.errors) {
                this.modalManager.showValidationErrors(
                    error.responseJSON.errors
                );
            } else {
                this.modalManager.showAlert(
                    "error",
                    error.message ||
                        this.translations.errorCreatingRecord ||
                        "Error creating record"
                );
            }
        }
    }

    /**
     * Actualizar entidad
     */
    async updateEntity(id, data) {
        try {
            Swal.showLoading();

            const response = await this.apiClient.updateEntity(id, data);

            Swal.close();
            this.modalManager.showAlert(
                "success",
                this.translations.updatedSuccessfully ||
                    `${this.entityName} updated successfully`,
                this.alertSelector
            );
            this.loadEntities();
        } catch (error) {
            Swal.close();
            console.error("Error updating entity:", error);

            if (error.message.includes("422") && error.responseJSON?.errors) {
                this.modalManager.showValidationErrors(
                    error.responseJSON.errors
                );
            } else {
                this.modalManager.showAlert(
                    "error",
                    error.message ||
                        this.translations.errorUpdatingRecord ||
                        "Error updating record"
                );
            }
        }
    }

    /**
     * Eliminar entidad
     */
    async deleteEntity(id) {
        let entityDisplayName = "";
        let entityIdentifier = "";
        let entity = null;

        // Intentar obtener datos desde la fila de la tabla
        const buttonElement = $(`.delete-btn[data-id="${id}"]`)[0];
        if (buttonElement) {
            entity = this.getEntityDataFromRow(buttonElement);
        }

        // Si no se pudo obtener desde la tabla, hacer llamada AJAX como fallback
        if (!entity) {
            try {
                const response = await $.ajax({
                    url: this.routes.edit.replace(":id", id),
                    type: "GET",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                });

                entity = response.data || response;
            } catch (error) {
                console.error(
                    "Error getting entity data for delete confirmation:",
                    error
                );
                entityIdentifier = "this element";
                entityDisplayName = "element";
            }
        }

        // Obtener identificador usando la configuración
        if (entity) {
            const entityInfo = this.getEntityIdentifier(entity);
            entityIdentifier = entityInfo.identifier;
            entityDisplayName = entityInfo.displayName;
        }

        // Crear mensaje personalizado
        let customMessage = this.translations.deleteMessage;
        if (entityIdentifier && entityIdentifier !== "this element") {
            // Usar función de formato desde crudTranslations directamente
            customMessage = crudTranslations.formatConfirmMessage(
                "delete",
                entityDisplayName,
                entityIdentifier
            );
        }

        const result = await Swal.fire({
            title: this.translations.confirmDelete,
            html: customMessage,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: this.translations.yesDelete,
            cancelButtonText: this.translations.cancel,
        });

        if (result.isConfirmed) {
            try {
                await this.apiClient.deleteEntity(id);
                this.modalManager.showAlert(
                    "success",
                    `${this.entityName} ${this.translations.deletedSuccessfully}`,
                    this.alertSelector
                );
                this.loadEntities();
            } catch (error) {
                console.error("Error deleting entity:", error);
                this.modalManager.showAlert(
                    "error",
                    this.translations.errorDeleting
                );
            }
        }
    }

    /**
     * Restaurar entidad
     */
    async restoreEntity(id) {
        let entityDisplayName = "";
        let entityIdentifier = "";
        let entity = null;

        // Intentar obtener datos desde la fila de la tabla
        const buttonElement = $(`.restore-btn[data-id="${id}"]`)[0];
        if (buttonElement) {
            entity = this.getEntityDataFromRow(buttonElement);
        }

        // Si no se pudo obtener desde la tabla, hacer llamada AJAX como fallback
        if (!entity) {
            try {
                const response = await $.ajax({
                    url: this.routes.edit.replace(":id", id),
                    type: "GET",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                });

                entity = response.data || response;
            } catch (error) {
                console.error(
                    "Error getting entity data for restore confirmation:",
                    error
                );
                entityIdentifier = "this element";
                entityDisplayName = "element";
            }
        }

        // Obtener identificador usando la configuración
        if (entity) {
            const entityInfo = this.getEntityIdentifier(entity);
            entityIdentifier = entityInfo.identifier;
            entityDisplayName = entityInfo.displayName;
        }

        // Crear mensaje personalizado
        let customMessage = this.translations.restoreMessage;
        if (entityIdentifier && entityIdentifier !== "this element") {
            // Usar función de formato desde crudTranslations directamente
            customMessage = crudTranslations.formatConfirmMessage(
                "restore",
                entityDisplayName,
                entityIdentifier
            );
        }

        const result = await Swal.fire({
            title: this.translations.confirmRestore,
            html: customMessage,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: this.translations.yesRestore,
            cancelButtonText: this.translations.cancel,
        });

        if (result.isConfirmed) {
            try {
                await this.apiClient.restoreEntity(id);
                this.modalManager.showAlert(
                    "success",
                    `${this.entityName} ${this.translations.restoredSuccessfully}`,
                    this.alertSelector
                );
                this.loadEntities();
            } catch (error) {
                console.error("Error restoring entity:", error);
                this.modalManager.showAlert(
                    "error",
                    this.translations.errorRestoring
                );
            }
        }
    }

    /**
     * Inicializar elementos del formulario
     */
    initializeFormElements() {
        this.validator.setupPhoneFormatting();
        this.validator.setupRealTimeValidation();
        this.validator.setupAutoCapitalization();
    }

    /**
     * Obtener datos de entidad desde la fila de la tabla
     */
    getEntityDataFromRow(button) {
        try {
            const row = $(button).closest("tr");
            const entityDataString = row.attr("data-entity");

            if (entityDataString) {
                const entityData = JSON.parse(
                    entityDataString.replace(/&quot;/g, '"')
                );
                return entityData;
            }

            return null;
        } catch (error) {
            console.error("Error parsing entity data from row:", error);
            return null;
        }
    }

    /**
     * Obtener identificador de entidad usando configuración
     */
    getEntityIdentifier(entity) {
        // Si hay una función personalizada de formato, usarla
        if (
            this.entityConfig.detailFormat &&
            typeof this.entityConfig.detailFormat === "function"
        ) {
            try {
                const customIdentifier = this.entityConfig.detailFormat(entity);
                return {
                    identifier: customIdentifier,
                    displayName: this.entityConfig.displayName,
                    field: "custom",
                };
            } catch (error) {
                console.error("Error using custom detail format:", error);
            }
        }

        // Intentar con el campo principal configurado
        if (entity && entity[this.entityConfig.identifierField]) {
            const identifier = entity[this.entityConfig.identifierField];
            return {
                identifier: identifier,
                displayName: this.entityConfig.displayName,
                field: this.entityConfig.identifierField,
            };
        }

        // Intentar con campos alternativos
        for (const field of this.entityConfig.fallbackFields) {
            if (entity && entity[field]) {
                const identifier = entity[field];
                return {
                    identifier: identifier,
                    displayName: this.entityConfig.displayName,
                    field: field,
                };
            }
        }

        // Fallback final
        return {
            identifier: "this element",
            displayName: "element",
            field: null,
        };
    }

    /**
     * Calcular número secuencial para la fila
     */
    getSequentialNumber(index) {
        if (!this.currentData) return index + 1;

        // Calcular el número basado en la página actual y el índice
        const baseNumber = (this.currentPage - 1) * this.perPage;
        return baseNumber + index + 1;
    }

    /**
     * Renderizar tabla
     */
    renderTable(data) {
        // En modo de registro único, no renderizamos tabla
        if (this.singleRecordMode) {
            console.log("Single record mode - skipping table rendering");
            return;
        }

        const entities = data.data;
        let html = "";

        if (entities.length === 0) {
            const noRecordsText =
                this.translations.noRecordsFound ||
                "No se encontraron registros";

            // Calcular el número total de columnas (incluyendo la columna de número si está habilitada)
            const totalColumns =
                this.tableHeaders.length + (this.showSequentialNumbers ? 1 : 0);

            html = `<tr><td colspan="${totalColumns}" class="px-6 py-4 text-center text-sm text-gray-500">${noRecordsText}</td></tr>`;
        } else {
            entities.forEach((entity, index) => {
                const isDeleted = entity.deleted_at !== null;
                const rowClass = isDeleted ? "deleted-row" : "";

                // Almacenar datos de la entidad como JSON en atributo data
                const entityData = JSON.stringify(entity).replace(
                    /"/g,
                    "&quot;"
                );

                html += `<tr class="${rowClass}" data-entity="${entityData}">`;

                // Agregar columna de número secuencial si está habilitada
                if (this.showSequentialNumbers) {
                    const sequentialNumber = this.getSequentialNumber(index);
                    html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">${sequentialNumber}</td>`;
                }

                this.tableHeaders.forEach((header) => {
                    let value = header.getter
                        ? header.getter(entity)
                        : entity[header.field];
                    html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">${value}</td>`;
                });

                html += `</tr>`;
            });
        }

        $(this.tableSelector).html(html);
    }

    /**
     * Renderizar paginación con estilos glassmorphism
     */
    renderPagination(data) {
        // En modo de registro único, no renderizamos paginación
        if (this.singleRecordMode) {
            console.log("Single record mode - skipping pagination rendering");
            return;
        }

        let paginationHtml = "";

        // Always show record count information, even for single page
        const recordInfo = this.generateRecordInfo(data);

        if (data.last_page > 1) {
            // Contenedor principal con clases glassmorphism
            paginationHtml += '<div class="pagination-wrapper">';

            paginationHtml +=
                '<nav class="pagination" aria-label="Pagination">';

            // Botón anterior
            if (data.current_page > 1) {
                paginationHtml += `
                <div class="page-item">
                    <button class="page-link" data-page="${
                        data.current_page - 1
                    }">
                        <span>&laquo;</span>
                    </button>
                </div>`;
            } else {
                paginationHtml += `
                <div class="page-item disabled">
                    <button class="page-link" disabled>
                        <span>&laquo;</span>
                    </button>
                </div>`;
            }

            // Números de página (máximo 5 visibles)
            let start = Math.max(1, data.current_page - 2);
            let end = Math.min(data.last_page, data.current_page + 2);
            if (data.current_page <= 2) end = Math.min(5, data.last_page);
            if (data.current_page >= data.last_page - 1)
                start = Math.max(1, data.last_page - 4);

            for (let i = start; i <= end; i++) {
                const isActive = i === data.current_page;
                const activeClass = isActive ? " active" : "";

                paginationHtml += `
                <div class="page-item${activeClass}">
                    <button class="page-link" data-page="${i}">
                        <span>${i}</span>
                    </button>
                </div>`;
            }

            // Botón siguiente
            if (data.current_page < data.last_page) {
                paginationHtml += `
                <div class="page-item">
                    <button class="page-link" data-page="${
                        data.current_page + 1
                    }">
                        <span>&raquo;</span>
                    </button>
                </div>`;
            } else {
                paginationHtml += `
                <div class="page-item disabled">
                    <button class="page-link" disabled>
                        <span>&raquo;</span>
                    </button>
                </div>`;
            }

            paginationHtml += "</nav>";

            // Add record information at the bottom
            paginationHtml += `<div class="record-info">${recordInfo}</div>`;

            paginationHtml += "</div>";
        } else {
            // Single page case - show only record information with wrapper
            paginationHtml += '<div class="pagination-wrapper single-page">';
            paginationHtml += `<div class="record-info-single">${recordInfo}</div>`;
            paginationHtml += "</div>";
        }

        $(this.paginationSelector).html(paginationHtml);

        // Event listener para paginación
        $(".pagination .page-link").on("click", (e) => {
            e.preventDefault();
            const $button = $(e.currentTarget);
            const page = $button.data("page");

            // Verificar que no esté deshabilitado y que tenga un número de página válido
            if (
                !$button.closest(".page-item").hasClass("disabled") &&
                page &&
                page !== data.current_page
            ) {
                this.loadEntities(page);
            }
        });
    }

    /**
     * Generate record information text
     */
    generateRecordInfo(data) {
        const from = data.from || 0;
        const to = data.to || 0;
        const total = data.total || 0;

        if (total === 0) {
            return `${this.translations.showing || "Showing"} 0 ${
                this.translations.results || "results"
            }`;
        }

        const showingText = this.translations.showing || "Showing";
        const toText = this.translations.to || "to";
        const ofText = this.translations.of || "of";
        const recordsText = this.translations.total_records || "Total Records";

        return `${showingText} ${from}-${to} ${ofText} ${total} ${recordsText}`;
    }

    /**
     * Mostrar loading en tabla
     */
    showTableLoading() {
        // Calcular el número total de columnas (incluyendo la columna de número si está habilitada)
        const totalColumns =
            this.tableHeaders.length + (this.showSequentialNumbers ? 1 : 0);

        const loadingHtml = `
            <tr id="loadingRow">
                <td colspan="${totalColumns}" class="px-6 py-4 text-center">
                    <svg class="animate-spin h-5 w-5 mr-3 text-blue-500 inline-block" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    ${this.translations.loading || "Loading..."}
                </td>
            </tr>
        `;
        $(this.tableSelector).html(loadingHtml);
    }

    /**
     * Ocultar loading en tabla
     */
    hideTableLoading() {
        $("#loadingRow").remove();
    }

    /**
     * Aplicar filtros de fecha
     */
    applyDateFilters(startDate, endDate, dateField = null) {
        console.log("Applying date filters:", {
            startDate,
            endDate,
            dateField,
        });

        // Validar que end date no sea menor que start date
        if (startDate && endDate && new Date(endDate) < new Date(startDate)) {
            this.modalManager.showAlert(
                "error",
                this.translations.endDateBeforeStart ||
                    "End date cannot be earlier than start date",
                this.alertSelector
            );
            return false;
        }

        // Actualizar filtros
        this.dateFilters.startDate = startDate || "";
        this.dateFilters.endDate = endDate || "";
        if (dateField) {
            this.dateFilters.dateField = dateField;
        }

        // Resetear a página 1 y recargar datos
        this.currentPage = 1;
        this.loadEntities();
        return true;
    }

    /**
     * Limpiar filtros de fecha
     */
    clearDateFilters() {
        console.log("Clearing date filters");
        this.dateFilters.startDate = "";
        this.dateFilters.endDate = "";
        this.currentPage = 1;
        this.loadEntities();
    }

    /**
     * Obtener estado actual de filtros de fecha
     */
    getDateFilters() {
        return { ...this.dateFilters };
    }

    /**
     * Validar rango de fechas
     */
    validateDateRange(startDate, endDate) {
        if (!startDate || !endDate) {
            return { valid: true };
        }

        const start = new Date(startDate);
        const end = new Date(endDate);

        if (end < start) {
            return {
                valid: false,
                message:
                    this.translations.endDateBeforeStart ||
                    "End date cannot be earlier than start date",
            };
        }

        return { valid: true };
    }

    // Los módulos están disponibles directamente como propiedades de la instancia
    // No necesitamos getters que causen conflictos
}
