// Validación de datos en tiempo real - Implementación completa
import { formatPhoneForStorage } from "../utils/CrudUtils.js";

export class CrudValidator {
    constructor(formFields, routes) {
        this.formFields = formFields;
        this.routes = routes;
        this.isEditing = false;
        this.currentEntity = null;
        this.translations = {};
    }

    /**
     * Configurar validaciones en tiempo real
     */
    setupRealTimeValidation() {
        // Validación de email
        const emailField = document.getElementById("email");
        if (emailField) {
            let emailTimeout;
            emailField.addEventListener("input", (e) => {
                clearTimeout(emailTimeout);
                emailTimeout = setTimeout(() => {
                    this.validateEmailField(e.target.value);
                }, 500); // Debounce de 500ms
            });
        }

        // Validación de teléfono
        const phoneField = document.getElementById("phone");
        if (phoneField) {
            let phoneTimeout;
            phoneField.addEventListener("input", (e) => {
                clearTimeout(phoneTimeout);
                phoneTimeout = setTimeout(() => {
                    this.validatePhoneField(e.target.value);
                }, 500); // Debounce de 500ms
            });
        }

        // Validación de username
        const usernameField = document.getElementById("username");
        if (usernameField) {
            let usernameTimeout;
            usernameField.addEventListener("input", (e) => {
                clearTimeout(usernameTimeout);
                usernameTimeout = setTimeout(() => {
                    this.validateUsernameField(e.target.value);
                }, 500); // Debounce de 500ms
            });
        }

        // Validación de name (para duplicados)
        const nameField = document.getElementById("name");
        if (nameField) {
            let nameTimeout;
            nameField.addEventListener("input", (e) => {
                clearTimeout(nameTimeout);
                nameTimeout = setTimeout(() => {
                    this.validateNameField(e.target.value);
                }, 500); // Debounce de 500ms
            });
        }

        // Validación de insurance_company_name (para duplicados)
        const insuranceCompanyNameField = document.getElementById(
            "insurance_company_name"
        );
        if (insuranceCompanyNameField) {
            let insuranceNameTimeout;
            insuranceCompanyNameField.addEventListener("input", (e) => {
                clearTimeout(insuranceNameTimeout);
                insuranceNameTimeout = setTimeout(() => {
                    this.validateInsuranceCompanyNameField(e.target.value);
                }, 500); // Debounce de 500ms
            });
        }

        // Configurar validación básica para todos los campos
        this.setupBasicFieldValidation();

        // Configurar limpieza general de errores para todos los campos
        this.setupGeneralErrorClearance();
    }

    /**
     * Configurar validación básica para todos los campos del formulario
     */
    setupBasicFieldValidation() {
        this.formFields.forEach((field) => {
            const fieldElement = document.getElementById(field.name);
            if (fieldElement) {
                // Skip fields that already have specific validation
                const fieldsWithSpecificValidation = [
                    "email",
                    "phone",
                    "username",
                    "name",
                    "insurance_company_name",
                ];
                if (fieldsWithSpecificValidation.includes(field.name)) {
                    return;
                }

                let validationTimeout;
                fieldElement.addEventListener("input", (e) => {
                    clearTimeout(validationTimeout);
                    validationTimeout = setTimeout(() => {
                        this.validateBasicField(field, e.target.value);
                    }, 300); // Debounce más corto para validación básica
                });

                // También validar en blur para campos requeridos
                fieldElement.addEventListener("blur", (e) => {
                    this.validateBasicField(field, e.target.value);
                });
            }
        });
    }

    /**
     * Validación básica para campos sin validación específica
     */
    validateBasicField(field, value) {
        // Limpiar error previo
        this.clearFieldError(field.name);

        // Obtener configuración de validación (puede estar en field.validation o directamente en field)
        const validation = field.validation || field;
        const isRequired = validation.required || field.required;
        const minLength = validation.minLength || field.minLength;
        const maxLength = validation.maxLength || field.maxLength;
        const pattern = validation.pattern || field.pattern;

        // Validar campo requerido
        if (isRequired && (!value || value.trim() === "")) {
            this.showFieldError(
                field.name,
                this.translations.fieldRequired ||
                    `${field.label || field.name} is required`
            );
            return;
        }

        // Solo validar longitud y patrón si hay valor
        if (value && value.trim() !== "") {
            // Validar longitud mínima si está definida
            if (minLength && value.length < minLength) {
                this.showFieldError(
                    field.name,
                    this.translations.minLength ||
                        `Minimum ${minLength} characters required`
                );
                return;
            }

            // Validar longitud máxima si está definida
            if (maxLength && value.length > maxLength) {
                this.showFieldError(
                    field.name,
                    this.translations.maxLength ||
                        `Maximum ${maxLength} characters allowed`
                );
                return;
            }

            // Validar patrón si está definido
            if (pattern && !new RegExp(pattern).test(value)) {
                this.showFieldError(
                    field.name,
                    this.translations.invalidFormat || "Invalid format"
                );
                return;
            }
        }

        // Si llegamos aquí, el campo es válido
        this.updateSubmitButtonState();
    }

    /**
     * Configurar formato de teléfono en tiempo real
     */
    setupPhoneFormatting() {
        const phoneField = document.getElementById("phone");
        if (phoneField) {
            phoneField.addEventListener("input", (e) => {
                this.formatPhoneInput(e);
            });
        }
    }

    /**
     * Formatear entrada de teléfono
     */
    formatPhoneInput(event) {
        const input = event.target;
        const isBackspace = event.inputType === "deleteContentBackward";
        let value = input.value.replace(/\D/g, "");

        if (isBackspace) {
            // Para backspace, mantener el valor actual sin agregar más caracteres
        } else {
            // Limitar a 10 dígitos
            value = value.substring(0, 10);
        }

        let formattedValue = "";
        if (value.length === 0) {
            formattedValue = "";
        } else if (value.length <= 3) {
            formattedValue = `(${value}`;
        } else if (value.length <= 6) {
            formattedValue = `(${value.substring(0, 3)}) ${value.substring(3)}`;
        } else {
            formattedValue = `(${value.substring(0, 3)}) ${value.substring(
                3,
                6
            )}-${value.substring(6)}`;
        }

        input.value = formattedValue;

        // Trigger validation after formatting
        this.validatePhoneField(formattedValue);
    }

    /**
     * Configurar capitalización automática
     */
    setupAutoCapitalization() {
        // Aplicar capitalización automática a todos los campos de texto relevantes
        const textInputs = document.querySelectorAll(
            '#swal2-content input[type="text"], #swal2-content textarea'
        );

        textInputs.forEach((input) => {
            // Excluir campos que no deben ser capitalizados
            const excludeFields = [
                "email",
                "phone",
                "url",
                "password",
                "username",
                "slug",
            ];
            const fieldName = input.name || input.id;

            // No aplicar capitalización a campos excluidos
            if (
                excludeFields.some((exclude) =>
                    fieldName.toLowerCase().includes(exclude)
                )
            ) {
                return;
            }

            // No aplicar capitalización a campos que ya tienen la clase auto-capitalize
            if (input.classList.contains("auto-capitalize")) {
                return;
            }

            // Agregar evento de capitalización
            input.addEventListener("input", (e) => {
                this.capitalizeInput(e);
            });
        });

        // También aplicar a campos específicos por nombre (compatibilidad con configuración anterior)
        const specificFields = [
            "name",
            "category_name",
            "category",
            "title",
            "description",
        ];
        specificFields.forEach((fieldName) => {
            const field = document.getElementById(fieldName);
            if (field && !field.hasAttribute("data-capitalize-applied")) {
                field.addEventListener("input", (e) => {
                    this.capitalizeInput(e);
                });
                // Marcar como procesado para evitar duplicados
                field.setAttribute("data-capitalize-applied", "true");
            }
        });
    }

    /**
     * Capitalizar la primera letra de cada palabra
     */
    capitalizeInput(event) {
        const input = event.target;
        const cursorPosition = input.selectionStart;
        const value = input.value;

        // Capitalizar la primera letra de cada palabra
        const capitalizedValue = value.replace(/\b\w/g, (match) =>
            match.toUpperCase()
        );

        // Solo actualizar si hay cambios para evitar loops
        if (capitalizedValue !== value) {
            input.value = capitalizedValue;
            // Restaurar la posición del cursor
            input.setSelectionRange(cursorPosition, cursorPosition);
        }
    }

    /**
     * Validar campo de email en tiempo real
     */
    async validateEmailField(email) {
        if (!email) {
            this.clearFieldError("email");
            return;
        }

        // Validación básica de formato de email
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            this.showFieldError(
                "email",
                this.translations.invalidEmail || "Invalid email format"
            );
            this.updateSubmitButtonState();
            return;
        }

        // Si existe el endpoint checkEmail, hacer validación de duplicados
        if (this.routes.checkEmail) {
            try {
                const response = await $.ajax({
                    url: this.routes.checkEmail,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        email: email,
                        uuid:
                            this.isEditing && this.currentEntity
                                ? this.currentEntity.uuid
                                : null,
                    },
                });

                if (response.exists) {
                    this.showFieldError(
                        "email",
                        this.translations.emailAlreadyInUse ||
                            "This email is already in use"
                    );
                    this.updateSubmitButtonState();
                } else {
                    this.clearFieldError("email");
                    this.showFieldSuccess(
                        "email",
                        this.translations.emailAvailable || "Email available"
                    );
                    this.updateSubmitButtonState();
                }
            } catch (error) {
                console.error("Error validating email:", error);
                this.clearFieldError("email");
                this.updateSubmitButtonState();
            }
        } else {
            this.clearFieldError("email");
            this.updateSubmitButtonState();
        }
    }

    /**
     * Validar campo de teléfono en tiempo real
     */
    async validatePhoneField(phone) {
        if (!phone) {
            this.clearFieldError("phone");
            return;
        }

        // Validación de formato completo (xxx) xxx-xxxx
        const phoneRegex = /^\(\d{3}\) \d{3}-\d{4}$/;
        if (!phoneRegex.test(phone)) {
            if (phone.length > 0) {
                this.showFieldError("phone", "Formato: (xxx) xxx-xxxx");
            }
            return;
        }

        if (this.routes.checkPhone) {
            try {
                // Convertir el teléfono al formato de almacenamiento para comparar
                const phoneForStorage = formatPhoneForStorage(phone);

                const response = await $.ajax({
                    url: this.routes.checkPhone,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        phone: phoneForStorage,
                        uuid:
                            this.isEditing && this.currentEntity
                                ? this.currentEntity.uuid
                                : null,
                    },
                });

                if (response.exists) {
                    this.showFieldError(
                        "phone",
                        this.translations.phoneAlreadyInUse ||
                            "This phone is already in use"
                    );
                } else {
                    this.clearFieldError("phone");
                    this.showFieldSuccess(
                        "phone",
                        this.translations.phoneAvailable || "Phone available"
                    );
                }
            } catch (error) {
                console.error("Error validating phone:", error);
            }
        }
    }

    /**
     * Validar campo de name en tiempo real (para duplicados)
     */
    async validateNameField(name) {
        if (!name) {
            this.clearFieldError("name");
            this.updateSubmitButtonState();
            return;
        }

        if (!this.routes.checkName) {
            this.clearFieldError("name");
            this.updateSubmitButtonState();
            return;
        }

        try {
            const response = await $.ajax({
                url: this.routes.checkName,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                data: {
                    name: name,
                    uuid:
                        this.isEditing && this.currentEntity
                            ? this.currentEntity.uuid
                            : null,
                },
            });

            if (response.exists) {
                this.showFieldError(
                    "name",
                    this.translations.nameAlreadyInUse ||
                        "This name is already in use"
                );
                this.updateSubmitButtonState();
            } else {
                this.clearFieldError("name");
                this.showFieldSuccess(
                    "name",
                    this.translations.nameAvailable || "Name available"
                );
                this.updateSubmitButtonState();
            }
        } catch (error) {
            console.error("Error validating name:", error);
            this.clearFieldError("name");
            this.updateSubmitButtonState();
        }
    }

    /**
     * Validar campo de insurance_company_name en tiempo real
     */
    async validateInsuranceCompanyNameField(companyName) {
        if (!companyName) {
            this.clearFieldError("insurance_company_name");
            this.updateSubmitButtonState();
            return;
        }

        if (!this.routes.checkName) {
            this.clearFieldError("insurance_company_name");
            this.updateSubmitButtonState();
            return;
        }

        try {
            const response = await $.ajax({
                url: this.routes.checkName,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                data: {
                    insurance_company_name: companyName,
                    exclude_uuid:
                        this.isEditing && this.currentEntity
                            ? this.currentEntity.uuid
                            : null,
                },
            });

            if (response.exists) {
                this.showFieldError(
                    "insurance_company_name",
                    this.translations.nameAlreadyInUse ||
                        "This company name is already in use"
                );
                this.updateSubmitButtonState();
            } else {
                this.clearFieldError("insurance_company_name");
                this.showFieldSuccess(
                    "insurance_company_name",
                    this.translations.nameAvailable || "Company name available"
                );
                this.updateSubmitButtonState();
            }
        } catch (error) {
            console.error("Error validating insurance company name:", error);
            this.clearFieldError("insurance_company_name");
            this.updateSubmitButtonState();
        }
    }

    /**
     * Validar campo de username en tiempo real
     */
    async validateUsernameField(username) {
        if (!username) {
            this.clearFieldError("username");
            return;
        }

        // Validación básica de longitud mínima
        if (username.length < 7) {
            this.showFieldError(
                "username",
                this.translations.minimumCharacters || "Minimum 7 characters"
            );
            return;
        }

        // Validación de que tenga al menos 2 números
        const numberMatches = username.match(/\d/g);
        if (!numberMatches || numberMatches.length < 2) {
            this.showFieldError(
                "username",
                this.translations.mustContainNumbers ||
                    "Must contain at least 2 numbers"
            );
            return;
        }

        if (this.routes.checkUsername) {
            try {
                const response = await $.ajax({
                    url: this.routes.checkUsername,
                    type: "POST",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    data: {
                        username: username,
                        uuid:
                            this.isEditing && this.currentEntity
                                ? this.currentEntity.uuid
                                : null,
                    },
                });

                if (response.exists) {
                    this.showFieldError(
                        "username",
                        this.translations.usernameAlreadyInUse ||
                            "This username is already in use"
                    );
                } else {
                    this.clearFieldError("username");
                    this.showFieldSuccess(
                        "username",
                        this.translations.usernameAvailable ||
                            "Username available"
                    );
                }
            } catch (error) {
                console.error("Error validating username:", error);
            }
        }
    }

    /**
     * Configurar limpieza general de errores
     */
    setupGeneralErrorClearance() {
        this.formFields.forEach((field) => {
            const fieldElement = document.getElementById(field.name);
            if (fieldElement) {
                // Agregar listener para input
                fieldElement.addEventListener("input", (e) => {
                    this.clearFieldErrorOnInput(field.name);
                });

                // Agregar listener para change (útil para selects)
                fieldElement.addEventListener("change", (e) => {
                    this.clearFieldErrorOnInput(field.name);
                });
            }
        });
    }

    /**
     * Limpiar error de campo cuando el usuario empieza a escribir
     */
    clearFieldErrorOnInput(fieldName) {
        const errorElement = $(`#error-${fieldName}`);
        const inputElement = $(`#${fieldName}`);

        // Solo limpiar si hay un error visible
        if (
            errorElement.length &&
            !errorElement.hasClass("hidden") &&
            errorElement.hasClass("text-red-500")
        ) {
            const field = this.formFields.find((f) => f.name === fieldName);
            const currentValue = inputElement.val();

            // Para campos requeridos, solo limpiar si el usuario ha escrito algo
            // Para campos no requeridos, limpiar inmediatamente
            if (
                !field?.required ||
                (currentValue && currentValue.trim() !== "")
            ) {
                this.clearFieldError(fieldName);

                // Limpiar mensaje de validación general de SweetAlert si no hay más errores
                setTimeout(() => {
                    if (!this.hasValidationErrors()) {
                        // Limpiar el mensaje de validación de SweetAlert
                        const validationMessage = $(
                            ".swal2-validation-message"
                        );
                        if (
                            validationMessage.length &&
                            validationMessage.is(":visible")
                        ) {
                            validationMessage.hide();
                        }
                    }
                }, 100);
            }
        }
    }

    /**
     * Validar y obtener datos del formulario
     */
    validateAndGetFormData(isEditMode) {
        const formData = {};
        let isValid = true;

        this.formFields.forEach((field) => {
            // Verificar si el campo debe estar presente en el modo actual
            if (field.showInCreate === false && !isEditMode) {
                return; // Saltar validación para campos no visibles en creación
            }
            if (field.showInEdit === false && isEditMode) {
                return; // Saltar validación para campos no visibles en edición
            }

            const element = $(`#${field.name}`);

            // Si el elemento no existe en el DOM, saltarlo
            if (!element.length) {
                return;
            }

            let value;

            if (field.type === "checkbox") {
                // Para checkboxes, asegurar que siempre enviemos un boolean
                value = element.is(":checked");
                console.log(
                    `Checkbox ${
                        field.name
                    }: checked=${value}, type=${typeof value}`
                );
            } else {
                value = element.val();
            }

            // Validación requerida
            if (field.required && (!value || value.toString().trim() === "")) {
                const requiredMessage =
                    field.name === "last_name"
                        ? this.translations.lastNameRequired ||
                          "Last name is required"
                        : `${field.label} ${
                              this.translations.isRequired || "is required"
                          }`;
                this.showFieldError(field.name, requiredMessage);
                isValid = false;
            }

            // Validaciones específicas
            if (value && field.validation) {
                const validationResult = this.validateFieldValue(field, value);
                if (!validationResult.valid) {
                    this.showFieldError(field.name, validationResult.message);
                    isValid = false;
                }
            }

            formData[field.name] = value;
        });

        // Asegurar que todos los campos checkbox tengan un valor boolean explícito
        this.formFields.forEach((field) => {
            if (field.type === "checkbox") {
                if (!(field.name in formData)) {
                    const shouldShow =
                        (isEditMode && field.showInEdit !== false) ||
                        (!isEditMode && field.showInCreate !== false);
                    if (shouldShow) {
                        formData[field.name] = false;
                    }
                } else {
                    formData[field.name] = Boolean(formData[field.name]);
                }
            }
        });

        console.log("Final form data:", formData);

        // Verificar si hay errores de validación en tiempo real
        if (this.hasValidationErrors()) {
            this.updateSubmitButtonState();
            Swal.showValidationMessage(
                this.translations.pleaseCorrectErrors ||
                    "Please correct the errors before continuing"
            );
            return false;
        }

        return isValid ? formData : false;
    }

    /**
     * Validar valor de campo
     */
    validateFieldValue(field, value) {
        const validation = field.validation;

        if (validation.minLength && value.length < validation.minLength) {
            return {
                valid: false,
                message: (
                    this.translations.minimumCharacters ||
                    "Minimum {count} characters"
                ).replace("{count}", validation.minLength),
            };
        }

        if (validation.maxLength && value.length > validation.maxLength) {
            return {
                valid: false,
                message: (
                    this.translations.maximumCharacters ||
                    "Maximum {count} characters"
                ).replace("{count}", validation.maxLength),
            };
        }

        if (validation.pattern && !validation.pattern.test(value)) {
            return {
                valid: false,
                message:
                    validation.message ||
                    this.translations.invalidFormat ||
                    "Invalid format",
            };
        }

        if (validation.email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
            return {
                valid: false,
                message: this.translations.invalidEmail || "Invalid email",
            };
        }

        return { valid: true };
    }

    /**
     * Verificar si hay errores de validación visibles o campos requeridos vacíos
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

        // Verificar si hay campos requeridos vacíos
        const isEditMode = $(".swal2-popup").hasClass("swal-edit");
        let hasEmptyRequiredFields = false;

        this.formFields.forEach((field) => {
            // Verificar si el campo debe estar presente en el modo actual
            if (field.showInCreate === false && !isEditMode) {
                return; // Saltar validación para campos no visibles en creación
            }
            if (field.showInEdit === false && isEditMode) {
                return; // Saltar validación para campos no visibles en edición
            }

            if (field.required) {
                const element = $(`#${field.name}`);
                let value = "";

                if (field.type === "checkbox") {
                    // Los checkboxes no se consideran "vacíos" para propósitos de required
                    return;
                } else {
                    value = element.val();
                }

                if (!value || value.toString().trim() === "") {
                    hasEmptyRequiredFields = true;
                }
            }
        });

        return visibleErrors.length > 0 || hasEmptyRequiredFields;
    }

    /**
     * Actualizar estado del botón submit
     */
    updateSubmitButtonState() {
        const submitButton = $(".swal2-confirm");
        if (submitButton.length) {
            if (this.hasValidationErrors()) {
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
     * Mostrar mensaje de éxito en campo
     */
    showFieldSuccess(fieldName, message) {
        const errorElement = $(`#error-${fieldName}`);
        const inputElement = $(`#${fieldName}`);

        if (errorElement.length) {
            errorElement
                .removeClass("hidden text-red-500")
                .addClass("text-green-500")
                .text(message);
        }

        if (inputElement.length) {
            inputElement.removeClass("error").addClass("valid");
        }
    }

    /**
     * Limpiar error de campo
     */
    clearFieldError(fieldName) {
        const errorElement = $(`#error-${fieldName}`);
        const inputElement = $(`#${fieldName}`);

        if (errorElement.length) {
            errorElement
                .addClass("hidden")
                .removeClass("text-red-500 text-green-500")
                .text("");
        }

        if (inputElement.length) {
            inputElement.removeClass("error valid");
        }

        // Actualizar estado del botón después de limpiar error
        setTimeout(() => this.updateSubmitButtonState(), 100);
    }

    /**
     * Mostrar error de campo
     */
    showFieldError(fieldName, message) {
        const errorElement = $(`#error-${fieldName}`);
        const inputElement = $(`#${fieldName}`);

        if (errorElement.length) {
            errorElement
                .removeClass("hidden text-green-500")
                .addClass("text-red-500")
                .text(message);
        }

        if (inputElement.length) {
            inputElement.removeClass("valid").addClass("error");
        }

        // Actualizar estado del botón después de mostrar error
        setTimeout(() => this.updateSubmitButtonState(), 100);
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

    // Métodos de configuración de contexto
    setEditingContext(isEditing, currentEntity, translations) {
        this.isEditing = isEditing;
        this.currentEntity = currentEntity;
        this.translations = translations || {};
    }
}
