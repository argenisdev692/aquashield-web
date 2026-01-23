// Comunicación con APIs (fetch, create, update, delete, restore) - Mejorado
export class CrudApiClient {
    constructor(routes) {
        this.routes = routes;
    }

    async fetchEntities(params = {}) {
        const url = new URL(this.routes.index, window.location.origin);
        Object.keys(params).forEach((key) =>
            url.searchParams.append(key, params[key])
        );

        try {
            const response = await fetch(url, {
                method: "GET",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                credentials: "same-origin",
            });
            return this._handleResponse(response);
        } catch (error) {
            console.error("Network error in fetchEntities:", error);
            throw new Error(`Network error: ${error.message}`);
        }
    }

    async createEntity(data) {
        try {
            const response = await fetch(this.routes.store, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: JSON.stringify(data),
                credentials: "same-origin",
            });
            return this._handleResponse(response);
        } catch (error) {
            console.error("Network error in createEntity:", error);
            throw new Error(`Network error: ${error.message}`);
        }
    }

    async updateEntity(id, data) {
        try {
            const url = this.routes.update.replace(":id", id);
            const response = await fetch(url, {
                method: "PUT",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                body: JSON.stringify(data),
                credentials: "same-origin",
            });
            return this._handleResponse(response);
        } catch (error) {
            console.error("Network error in updateEntity:", error);
            throw new Error(`Network error: ${error.message}`);
        }
    }

    async deleteEntity(id) {
        try {
            const url = this.routes.destroy.replace(":id", id);
            const response = await fetch(url, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                credentials: "same-origin",
            });
            return this._handleResponse(response);
        } catch (error) {
            console.error("Network error in deleteEntity:", error);
            throw new Error(`Network error: ${error.message}`);
        }
    }

    async restoreEntity(id) {
        try {
            const url = this.routes.restore.replace(":id", id);
            const response = await fetch(url, {
                method: "PATCH", // Cambiado de POST a PATCH para ser más semánticamente correcto
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                credentials: "same-origin",
            });
            return this._handleResponse(response);
        } catch (error) {
            console.error("Network error in restoreEntity:", error);
            throw new Error(`Network error: ${error.message}`);
        }
    }

    async _handleResponse(response) {
        // Si la respuesta no es JSON, lanza error con el texto recibido (probablemente HTML)
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error(
                "Non-JSON response received:",
                text.substring(0, 200)
            );
            throw new Error(
                `Respuesta inesperada del servidor: ${response.status} ${response.statusText}`
            );
        }

        const data = await response.json();

        if (!response.ok) {
            // Crear un error más detallado para diferentes códigos de estado
            const errorMessage =
                data.message ||
                `Error ${response.status}: ${response.statusText}`;
            const error = new Error(errorMessage);

            // Adjuntar información adicional al error para que CrudManager pueda usarla
            error.status = response.status;
            error.statusText = response.statusText;
            error.responseJSON = data;

            // Para errores de validación (422), incluir los errores específicos
            if (response.status === 422 && data.errors) {
                error.validationErrors = data.errors;
            }

            console.error("API Error:", {
                status: response.status,
                statusText: response.statusText,
                message: errorMessage,
                data: data,
            });

            throw error;
        }

        return data;
    }
}
