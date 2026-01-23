// Manejo de eventos del DOM - Implementación completa
import { debounce } from "../utils/CrudUtils.js";

export class CrudEventHandler {
    constructor(crudManager) {
        this.crudManager = crudManager;
    }

    /**
     * Adjuntar todos los event listeners
     */
    attachEventListeners() {
        // Búsqueda con debounce
        $(this.crudManager.searchSelector).on(
            "input",
            debounce(() => {
                this.crudManager.searchTerm = $(
                    this.crudManager.searchSelector
                ).val();
                this.crudManager.currentPage = 1;
                this.crudManager.loadEntities();
            }, 300)
        );

        // Cambio de registros por página
        $(this.crudManager.perPageSelector).on("change", () => {
            this.crudManager.perPage = $(
                this.crudManager.perPageSelector
            ).val();
            this.crudManager.currentPage = 1;
            this.crudManager.loadEntities();
        });

        // Toggle para mostrar eliminados
        $(this.crudManager.showDeletedSelector).on("change", () => {
            this.crudManager.showDeleted = $(
                this.crudManager.showDeletedSelector
            ).is(":checked");
            localStorage.setItem("showDeleted", this.crudManager.showDeleted);
            this.crudManager.currentPage = 1;
            this.crudManager.loadEntities();
        });

        // Botones de acción en la tabla
        $(document).on("click", ".create-btn", () =>
            this.crudManager.showCreateModal()
        );

        $(document).on("click", ".edit-btn", (e) => {
            const id = $(e.currentTarget).data("id");
            // Solo ejecutar si hay un ID válido
            if (id && id !== "undefined" && id !== "null") {
                this.crudManager.showEditModal(id);
            } else {
                console.warn(
                    "Edit button clicked but no valid data-id found:",
                    id
                );
            }
        });

        $(document).on("click", ".delete-btn", (e) => {
            const id = $(e.currentTarget).data("id");
            this.crudManager.deleteEntity(id);
        });

        $(document).on("click", ".restore-btn", (e) => {
            const id = $(e.currentTarget).data("id");
            this.crudManager.restoreEntity(id);
        });

        // Ordenamiento de columnas
        $(document).on("click", ".sort-header", (e) => {
            const field = $(e.currentTarget).data("field");
            if (this.crudManager.sortField === field) {
                this.crudManager.sortDirection =
                    this.crudManager.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.crudManager.sortField = field;
                this.crudManager.sortDirection = "asc";
            }

            // Actualizar clases de ordenamiento
            $(".sort-header").removeClass("sort-asc sort-desc");
            $(e.currentTarget).addClass(
                this.crudManager.sortDirection === "asc"
                    ? "sort-asc"
                    : "sort-desc"
            );

            this.crudManager.loadEntities();
        });

        // Event listeners para la paginación (se agregan dinámicamente en renderPagination)
        this.attachPaginationEventListeners();

        // Event listeners para formularios modales (se configuran cuando se abre el modal)
        this.attachFormEventListeners();
    }

    /**
     * Event listeners para paginación (llamado dinámicamente)
     */
    attachPaginationEventListeners() {
        $(document).on("click", ".pagination-btn", (e) => {
            const page = $(e.target).data("page");
            if (page) {
                this.crudManager.loadEntities(page);
            }
        });
    }

    /**
     * Event listeners para formularios modales
     */
    attachFormEventListeners() {
        // Estos se configuran dinámicamente cuando se abren los modales
        // ya que los elementos no existen en el DOM hasta que se abren
        // Los event listeners específicos de validación, formato de teléfono, etc.
        // se configuran en el método initializeFormElements() de CrudManager
        // que a su vez llama a los métodos correspondientes del CrudValidator
    }

    /**
     * Configurar event listeners específicos para un campo
     */
    attachFieldEventListeners(fieldName, fieldType) {
        const field = document.getElementById(fieldName);
        if (!field) return;

        switch (fieldType) {
            case "email":
                this.attachEmailValidation(field);
                break;
            case "tel":
                this.attachPhoneValidation(field);
                break;
            case "text":
                this.attachTextValidation(field);
                break;
            default:
                this.attachBasicValidation(field);
                break;
        }
    }

    /**
     * Event listeners para validación de email
     */
    attachEmailValidation(field) {
        let emailTimeout;
        field.addEventListener("input", (e) => {
            clearTimeout(emailTimeout);
            emailTimeout = setTimeout(() => {
                this.crudManager.validator.validateEmailField(e.target.value);
            }, 500);
        });
    }

    /**
     * Event listeners para validación de teléfono
     */
    attachPhoneValidation(field) {
        // Formato en tiempo real
        field.addEventListener("input", (e) => {
            this.crudManager.validator.formatPhoneInput(e);
        });

        // Validación con debounce
        let phoneTimeout;
        field.addEventListener("input", (e) => {
            clearTimeout(phoneTimeout);
            phoneTimeout = setTimeout(() => {
                this.crudManager.validator.validatePhoneField(e.target.value);
            }, 500);
        });
    }

    /**
     * Event listeners para validación de texto
     */
    attachTextValidation(field) {
        // Capitalización automática
        field.addEventListener("input", (e) => {
            this.crudManager.validator.capitalizeInput(e);
        });

        // Validación básica
        let textTimeout;
        field.addEventListener("input", (e) => {
            clearTimeout(textTimeout);
            textTimeout = setTimeout(() => {
                const fieldConfig = this.crudManager.formFields.find(
                    (f) => f.name === field.name
                );
                if (fieldConfig) {
                    this.crudManager.validator.validateBasicField(
                        fieldConfig,
                        e.target.value
                    );
                }
            }, 300);
        });
    }

    /**
     * Event listeners para validación básica
     */
    attachBasicValidation(field) {
        let validationTimeout;
        field.addEventListener("input", (e) => {
            clearTimeout(validationTimeout);
            validationTimeout = setTimeout(() => {
                const fieldConfig = this.crudManager.formFields.find(
                    (f) => f.name === field.name
                );
                if (fieldConfig) {
                    this.crudManager.validator.validateBasicField(
                        fieldConfig,
                        e.target.value
                    );
                }
            }, 300);
        });

        field.addEventListener("blur", (e) => {
            const fieldConfig = this.crudManager.formFields.find(
                (f) => f.name === field.name
            );
            if (fieldConfig) {
                this.crudManager.validator.validateBasicField(
                    fieldConfig,
                    e.target.value
                );
            }
        });
    }

    /**
     * Limpiar event listeners específicos
     */
    removeEventListeners() {
        // Remover event listeners específicos si es necesario
        // Esto es útil para cleanup cuando se destruye el componente
        $(this.crudManager.searchSelector).off("input");
        $(this.crudManager.perPageSelector).off("change");
        $(this.crudManager.showDeletedSelector).off("change");
        $(document).off("click", ".create-btn");
        $(document).off("click", ".edit-btn");
        $(document).off("click", ".delete-btn");
        $(document).off("click", ".restore-btn");
        $(document).off("click", ".sort-header");
        $(document).off("click", ".pagination-btn");
    }

    /**
     * Refrescar event listeners
     */
    refreshEventListeners() {
        this.removeEventListeners();
        this.attachEventListeners();
    }
}
