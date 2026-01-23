// Gestión de modales y alertas - Implementación completa con SweetAlert2
import { crudTranslations } from "../utils/CrudTranslations.js";

export class CrudModalManager {
    constructor(modalConfig, colorConfig) {
        this.modalConfig = modalConfig || {
            width: "800px",
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: "Save",
            cancelButtonText: "Cancel",
        };

        this.colorConfig = colorConfig || {
            create: { confirmButtonColor: "#10B981" },
            edit: { confirmButtonColor: "#3B82F6" },
        };
    }

    /**
     * Mostrar modal de creación
     */
    async showCreateModal(title, formHtml, preConfirm, didOpen, onConfirm) {
        const result = await Swal.fire({
            title: title,
            html: formHtml,
            width: this.modalConfig.width,
            showCloseButton: this.modalConfig.showCloseButton,
            showCancelButton: this.modalConfig.showCancelButton,
            confirmButtonText: this.modalConfig.confirmButtonText,
            cancelButtonText: this.modalConfig.cancelButtonText,
            confirmButtonColor: this.colorConfig.create.confirmButtonColor,
            customClass: {
                container: "swal-modal-container",
                popup: "swal-modal-popup",
                content: "swal-modal-content",
            },
            preConfirm: preConfirm,
            didOpen: () => {
                if (didOpen) didOpen();
                this.applyHeaderColor("create");
                // Verificar estado inicial del botón
                setTimeout(() => this.updateSubmitButtonState(), 200);
            },
        });

        if (result.isConfirmed && result.value && onConfirm) {
            await onConfirm(result);
        }
    }

    /**
     * Mostrar modal de edición
     */
    async showEditModal(title, formHtml, preConfirm, didOpen, onConfirm) {
        const result = await Swal.fire({
            title: title,
            html: formHtml,
            width: this.modalConfig.width,
            showCloseButton: this.modalConfig.showCloseButton,
            showCancelButton: this.modalConfig.showCancelButton,
            confirmButtonText: "Update",
            cancelButtonText: this.modalConfig.cancelButtonText,
            confirmButtonColor: this.colorConfig.edit.confirmButtonColor,
            customClass: {
                container: "swal-modal-container",
                popup: "swal-modal-popup",
                content: "swal-modal-content",
            },
            preConfirm: preConfirm,
            didOpen: () => {
                if (didOpen) didOpen();
                this.applyHeaderColor("edit");
                // Verificar estado inicial del botón después de cargar datos
                setTimeout(() => this.updateSubmitButtonState(), 200);
            },
        });

        if (result.isConfirmed && result.value && onConfirm) {
            await onConfirm(result);
        }
    }

    /**
     * Mostrar alerta
     */
    showAlert(type, message, alertSelector = null) {
        if (alertSelector) {
            // Mostrar en el contenedor específico si se proporciona
            this.showInlineAlert(type, message, alertSelector);
        } else {
            // Mostrar como modal de SweetAlert2
            const icon = type === "success" ? "success" : "error";
            const title =
                type === "success"
                    ? crudTranslations.get("success")
                    : crudTranslations.get("error");

            Swal.fire({
                icon: icon,
                title: title,
                text: message,
                confirmButtonText: crudTranslations.get("ok"),
            });
        }
    }

    /**
     * Mostrar alerta inline en un contenedor específico
     */
    showInlineAlert(type, message, alertSelector) {
        // Limpiar alertas previas
        $(alertSelector).empty().show();

        const alertClass =
            type === "success"
                ? "bg-green-100 border-green-400 text-green-700"
                : "bg-red-100 border-red-400 text-red-700";

        const iconSvg =
            type === "success"
                ? `<svg class="w-5 h-5 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                 <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
               </svg>`
                : `<svg class="w-5 h-5 mr-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                 <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
               </svg>`;

        const alertHtml = `
            <div class="alert ${alertClass} border px-4 py-3 rounded mb-4 transition-all duration-300 ease-in-out" role="alert">
                <div class="flex items-center">
                    ${iconSvg}
                    <span class="block sm:inline">${message}</span>
                </div>
            </div>
        `;

        $(alertSelector).html(alertHtml);

        // Auto-hide después de 5 segundos
        setTimeout(() => {
            $(alertSelector).fadeOut(300, () => {
                $(alertSelector).empty();
            });
        }, 5000);
    }

    /**
     * Aplicar color al header del modal
     */
    applyHeaderColor(mode) {
        setTimeout(() => {
            const popup = document.querySelector(".swal2-popup");
            if (popup) {
                // Remover clases previas
                popup.classList.remove("swal-create", "swal-edit");
                // Agregar clase según el modo
                popup.classList.add(`swal-${mode}`);
            }
        }, 10);
    }

    /**
     * Actualizar estado del botón submit
     */
    updateSubmitButtonState() {
        const submitButton = $(".swal2-confirm");
        if (submitButton.length) {
            // Verificar si hay errores de validación
            const hasErrors = this.hasValidationErrors();

            if (hasErrors) {
                submitButton.prop("disabled", true);
                submitButton.addClass("opacity-50 cursor-not-allowed");
                submitButton.attr(
                    "title",
                    "Complete todos los campos requeridos y corrija los errores antes de continuar"
                );
            } else {
                submitButton.prop("disabled", false);
                submitButton.removeClass("opacity-50 cursor-not-allowed");
                submitButton.removeAttr("title");

                // Limpiar mensaje de validación de SweetAlert si no hay errores
                const validationMessage = $(".swal2-validation-message");
                if (
                    validationMessage.length &&
                    validationMessage.is(":visible")
                ) {
                    validationMessage.hide();
                }
            }
        }
    }

    /**
     * Verificar si hay errores de validación
     */
    hasValidationErrors() {
        // Verificar errores de validación visibles
        const visibleErrors = $(".error-message:not(.hidden)").filter(
            function () {
                return (
                    $(this).hasClass("text-red-500") &&
                    $(this).text().trim() !== ""
                );
            }
        );

        // Verificar campos requeridos vacíos
        let hasEmptyRequiredFields = false;
        const requiredFields = $(
            "input[required], select[required], textarea[required]"
        );

        requiredFields.each(function () {
            const field = $(this);
            let value = "";

            if (field.attr("type") === "checkbox") {
                // Los checkboxes no se consideran "vacíos" para propósitos de required
                return true; // continue
            } else {
                value = field.val();
            }

            if (!value || value.toString().trim() === "") {
                hasEmptyRequiredFields = true;
                return false; // break
            }
        });

        return visibleErrors.length > 0 || hasEmptyRequiredFields;
    }

    /**
     * Mostrar errores de validación
     */
    showValidationErrors(errors) {
        let errorMessage = "";
        try {
            errorMessage = crudTranslations.get("validation_errors") + ":\n";
        } catch (e) {
            errorMessage = "Errores de validación:\n";
        }

        Object.keys(errors).forEach((field) => {
            errorMessage += `• ${errors[field][0]}\n`;
        });

        let title = "";
        try {
            title = crudTranslations.get("validation_errors");
        } catch (e) {
            title = "Errores de validación";
        }

        Swal.fire({
            icon: "error",
            title: title,
            text: errorMessage,
        });
    }

    /**
     * Limpiar alertas
     */
    clearAlerts(alertSelector) {
        if (alertSelector) {
            $(alertSelector).empty().show();
        }
    }
}
