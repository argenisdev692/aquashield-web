// Construcción dinámica de formularios - Implementación completa
export class CrudFormBuilder {
    constructor(formFields) {
        this.formFields = formFields;
    }

    /**
     * Generar HTML del formulario completo
     */
    generateFormHtml(entity = null) {
        let html = '<div class="crud-modal-form">';
        const isEditMode = entity !== null;

        this.formFields.forEach((field) => {
            // Verificar si el campo debe mostrarse en el modo actual
            if (field.showInCreate === false && !isEditMode) {
                return; // No mostrar el campo si es creación y showInCreate es false
            }
            if (field.showInEdit === false && isEditMode) {
                return; // No mostrar el campo si es edición y showInEdit es false
            }

            html += this.generateFieldHtml(field, entity);
        });

        html += "</div>";
        return html;
    }

    /**
     * Generar HTML de un campo específico
     */
    generateFieldHtml(field, entity = null) {
        const value = entity ? entity[field.name] || "" : "";
        const required = field.required ? "required" : "";
        const disabled = field.disabled ? "disabled" : "";

        // Para campos hidden, no necesitamos wrapper ni label
        if (field.type === "hidden") {
            const hiddenValue = field.value || value || "";
            return `<input type="hidden" id="${field.name}" name="${field.name}" value="${hiddenValue}">`;
        }

        let html = `<div class="form-group mb-4">`;
        const labelClass = field.required
            ? "block text-sm font-medium text-gray-700 mb-2 required"
            : "block text-sm font-medium text-gray-700 mb-2";
        html += `<label for="${field.name}" class="${labelClass}">${field.label}</label>`;

        switch (field.type) {
            case "text":
            case "email":
            case "number":
            case "tel":
            case "url":
                const capitalizationClass =
                    field.type === "text" && field.capitalize
                        ? " auto-capitalize"
                        : "";
                html += `<input type="${field.type}" id="${field.name}" name="${
                    field.name
                }" value="${value}" ${required} ${disabled} class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent${capitalizationClass}" placeholder="${
                    field.placeholder || ""
                }">`;
                break;

            case "textarea":
                const textareaCapitalizationClass = field.capitalize
                    ? " auto-capitalize"
                    : "";
                html += `<textarea id="${field.name}" name="${
                    field.name
                }" ${required} ${disabled} rows="${
                    field.rows || 3
                }" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent${textareaCapitalizationClass}" placeholder="${
                    field.placeholder || ""
                }">${value}</textarea>`;
                break;

            case "select":
                html += `<select id="${field.name}" name="${field.name}" ${required} ${disabled} class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">`;
                if (field.placeholder) {
                    html += `<option value="">${field.placeholder}</option>`;
                }
                field.options.forEach((option) => {
                    const selected = value == option.value ? "selected" : "";
                    html += `<option value="${option.value}" ${selected}>${option.text}</option>`;
                });
                html += `</select>`;
                break;

            case "checkbox":
                const checked = value ? "checked" : "";
                html += `<div class="flex items-center">`;
                html += `<input type="checkbox" id="${field.name}" name="${field.name}" ${checked} ${disabled} class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">`;
                html += `<label for="${
                    field.name
                }" class="ml-2 block text-sm text-gray-700">${
                    field.checkboxLabel || field.label
                }</label>`;
                html += `</div>`;
                break;

            case "date":
                html += `<input type="date" id="${field.name}" name="${field.name}" value="${value}" ${required} ${disabled} class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">`;
                break;

            case "time":
                html += `<input type="time" id="${field.name}" name="${field.name}" value="${value}" ${required} ${disabled} class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">`;
                break;
        }

        if (field.help) {
            html += `<p class="mt-1 text-sm text-gray-500">${field.help}</p>`;
        }

        html += `<div class="error-message text-red-500 text-sm mt-1 hidden" id="error-${field.name}"></div>`;
        html += `</div>`;

        return html;
    }

    /**
     * Poblar formulario con datos de entidad
     */
    populateForm(entity) {
        this.formFields.forEach((field) => {
            const element = $(`#${field.name}`);
            let value = entity[field.name];

            if (field.type === "checkbox") {
                element.prop("checked", !!value);
            } else {
                // Formatear teléfono para mostrar en el formulario
                if (field.name === "phone" && value) {
                    value = this.formatPhoneForDisplay(value);
                }

                // Para selects, asegurar que el valor se establezca correctamente
                if (field.type === "select") {
                    element.val(value || "");

                    // Verificar si el valor se estableció correctamente
                    setTimeout(() => {
                        const actualValue = element.val();
                        if (actualValue !== value && value) {
                            element.val(value);
                        }
                    }, 100);
                } else {
                    element.val(value || "");
                }
            }
        });
    }

    /**
     * Verificar y corregir valores de selects
     */
    verifyAndFixSelectValues(entity) {
        this.formFields.forEach((field) => {
            if (field.type === "select") {
                const element = $(`#${field.name}`);
                const expectedValue = entity[field.name];
                const actualValue = element.val();

                if (expectedValue && actualValue !== expectedValue) {
                    element.val(expectedValue);

                    // Verificar una vez más
                    setTimeout(() => {
                        const finalValue = element.val();
                        if (finalValue !== expectedValue) {
                            element.val(expectedValue);
                        }
                    }, 50);
                }
            }
        });
    }

    /**
     * Formatear teléfono para mostrar en formulario
     */
    formatPhoneForDisplay(phone) {
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
}
