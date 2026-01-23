// Sistema de traducciones para CRUD - Mapea claves del sistema CRUD a traducciones de Laravel
export class CrudTranslations {
    constructor() {
        // Cargar traducciones desde las variables globales de Laravel
        this.translations = window.translations || {};
    }

    /**
     * Obtener traducción por clave
     */
    get(key, replacements = {}) {
        let translation =
            this.translations[key] || this.getDefaultTranslation(key);

        // Aplicar reemplazos si se proporcionan
        Object.keys(replacements).forEach((placeholder) => {
            const value = replacements[placeholder];
            translation = translation.replace(
                new RegExp(`{${placeholder}}`, "g"),
                value
            );
        });

        return translation;
    }

    /**
     * Obtener traducciones por defecto para claves no encontradas
     */
    getDefaultTranslation(key) {
        const defaults = {
            // CRUD Manager
            confirm_delete: "are_you_sure",
            delete_message: "delete_confirmation",
            confirm_restore: "restore_confirmation",
            restore_message: "restore_confirmation",
            yes_delete: "yes_delete",
            yes_restore: "yes_restore",
            cancel: "cancel",
            deleted_successfully: "deleted_successfully",
            restored_successfully: "restored_successfully",
            error_deleting: "error_deleting",
            error_restoring: "error_restoring",
            showing: "showing",
            to: "to",
            of: "of",
            results: "results",
            total_records: "total_records",
            records: "records",
            no_records_found: "no_records_found",
            loading: "loading",
            previous: "previous",
            next: "next",
            this_element: "this_element",
            element: "element",

            // CRUD Validator
            field_required: "is_required",
            minimum_characters: "minimum_characters",
            maximum_characters: "maximum_characters",
            invalid_format: "invalid_format",
            invalid_email: "invalid_email_format",
            email_already_in_use: "email_already_in_use",
            email_available: "email_available",
            phone_already_in_use: "phone_already_in_use",
            phone_available: "phone_available",
            name_already_in_use: "name_already_in_use",
            name_available: "name_available",
            company_name_already_in_use: "company_name_already_in_use",
            company_name_available: "company_name_available",
            username_already_in_use: "username_already_in_use",
            username_available: "username_available",
            minimum_characters_required: "minimum_characters",
            must_contain_numbers: "must_contain_numbers",
            last_name_required: "last_name_required",
            please_correct_errors: "please_correct_errors",

            // CRUD Modal Manager
            success: "success",
            error: "error",
            save: "save",
            update: "update",
            ok: "ok",
            validation_errors: "validation_errors",

            // CRUD API Client
            network_error: "network_error",
            api_error: "api_error",
            unexpected_server_response: "unexpected_server_response",
            network_error_fetch: "network_error_fetch",

            // Mensajes específicos
            invalid_id_editing: "invalid_id_editing",
            no_edit_info_found: "no_edit_info_found",
            error_loading_edit_data: "error_loading_edit_data",
            delete_confirmation: "delete_confirmation",
            restore_confirmation: "restore_confirmation",
            want_to_delete: "want_to_delete",
            want_to_restore: "want_to_restore",
            created_successfully: "created_successfully",
            error_creating_record: "error_creating_record",
            updated_successfully: "updated_successfully",
            error_updating_record: "error_updating_record",
            end_date_before_start: "end_date_cannot_be_earlier",
        };

        // Si hay un mapeo, usar esa clave
        if (defaults[key]) {
            return this.translations[defaults[key]] || key;
        }

        // Si no hay mapeo, devolver la clave tal como está
        return key;
    }

    /**
     * Obtener objeto de traducciones para usar en configuraciones
     */
    getCrudTranslations() {
        return {
            // Para CrudManager
            confirmDelete: this.get("are_you_sure"),
            deleteMessage: this.get("delete_confirmation"),
            confirmRestore: this.get("restore_confirmation"),
            restoreMessage: this.get("restore_confirmation"),
            yesDelete: this.get("yes_delete"),
            yesRestore: this.get("yes_restore"),
            cancel: this.get("cancel"),
            deletedSuccessfully: this.get("deleted_successfully"),
            restoredSuccessfully: this.get("restored_successfully"),
            errorDeleting: this.get("error_deleting"),
            errorRestoring: this.get("error_restoring"),
            showing: this.get("showing"),
            to: this.get("to"),
            of: this.get("of"),
            results: this.get("results"),
            total_records: this.get("total_records"),
            records: this.get("records"),
            noRecordsFound: this.get("no_records_found"),

            // Para validaciones
            fieldRequired: this.get("is_required"),
            minLength: this.get("minimum_characters"),
            maxLength: this.get("maximum_characters"),
            invalidFormat: this.get("invalid_format"),
            invalidEmail: this.get("invalid_email_format"),
            emailAlreadyInUse: this.get("email_already_in_use"),
            emailAvailable: this.get("email_available"),
            phoneAlreadyInUse: this.get("phone_already_in_use"),
            phoneAvailable: this.get("phone_available"),
            nameAlreadyInUse: this.get("name_already_in_use"),
            nameAvailable: this.get("name_available"),
            usernameAlreadyInUse: this.get("username_already_in_use"),
            usernameAvailable: this.get("username_available"),
            minimumCharacters: this.get("minimum_characters"),
            mustContainNumbers: this.get("must_contain_numbers"),
            lastNameRequired: this.get("last_name_required"),
            pleaseCorrectErrors: this.get("please_correct_errors"),

            // Para modales
            success: this.get("success"),
            error: this.get("error"),
            save: this.get("save"),
            update: this.get("update"),
            ok: this.get("ok"),
            validationErrors: this.get("validation_errors"),

            // Para API y red
            networkError: this.get("network_error"),
            apiError: this.get("api_error"),
            unexpectedServerResponse: this.get("unexpected_server_response"),
            networkErrorFetch: this.get("network_error_fetch"),

            // Mensajes específicos
            invalidIdEditing: this.get("invalid_id_editing"),
            noEditInfoFound: this.get("no_edit_info_found"),
            errorLoadingEditData: this.get("error_loading_edit_data"),
            deleteConfirmation: this.get("delete_confirmation"),
            restoreConfirmation: this.get("restore_confirmation"),
        };
    }

    /**
     * Formatear mensaje dinámico para eliminar/restaurar
     */
    formatConfirmMessage(type, entityDisplayName, entityIdentifier) {
        const baseKey =
            type === "delete" ? "want_to_delete" : "want_to_restore";

        if (entityIdentifier && entityIdentifier !== "this element") {
            const actionText = this.get(baseKey);
            return `${actionText} ${entityDisplayName}: <strong>${entityIdentifier}</strong>?`;
        }

        // Fallback para casos genéricos
        const fallbackKey =
            type === "delete"
                ? "confirm_delete_entity"
                : "confirm_restore_entity";
        return this.get(fallbackKey);
    }

    /**
     * Formatear mensaje de caracteres con placeholder
     */
    formatCharacterMessage(type, count) {
        const key =
            type === "min" ? "minimum_characters" : "maximum_characters";
        return this.get(key, { count: count });
    }

    /**
     * Formatear mensaje de paginación
     */
    formatPaginationMessage(from, to, total) {
        const showing = this.get("showing");
        const toText = this.get("to");
        const ofText = this.get("of");
        const recordsText = this.get("total_records");

        return `${showing} ${from}-${to} ${ofText} ${total} ${recordsText}`;
    }
}

// Instancia global para uso en el sistema CRUD
export const crudTranslations = new CrudTranslations();
