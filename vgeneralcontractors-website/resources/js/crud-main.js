// Sistema CRUD modular completo
console.log("Loading CRUD modular system...");

// Importar módulos del sistema CRUD
import { CrudManager } from "./crud-system/core/CrudManager.js";

// Exponer CrudManager globalmente
window.CrudManager = CrudManager;

// Función helper para obtener traducciones
function __(key, fallback = "") {
    if (
        typeof window.translations !== "undefined" &&
        window.translations[key]
    ) {
        return window.translations[key];
    }
    return fallback || key;
}

// Implementación de CrudManagerModal usando el sistema modular
class CrudManagerModal {
    constructor(options) {
        console.log(
            "Initializing CrudManagerModal with modular system:",
            options
        );

        // Crear instancia del CrudManager modular
        this.crudManager = new CrudManager(options);

        // Exponer métodos principales para compatibilidad
        this.entityName = options.entityName || "Entity";
        this.entityNamePlural = options.entityNamePlural || "Entities";
        this.routes = options.routes || {};

        console.log(
            "CrudManagerModal initialized successfully with modular system"
        );
    }

    // Métodos que delegan al CrudManager modular
    async showCreateModal() {
        console.log("showCreateModal called - delegating to modular system");
        return await this.crudManager.showCreateModal();
    }

    async showEditModal(id) {
        console.log(
            "showEditModal called with id:",
            id,
            "- delegating to modular system"
        );
        return await this.crudManager.showEditModal(id);
    }

    async deleteEntity(id) {
        console.log(
            "deleteEntity called with id:",
            id,
            "- delegating to modular system"
        );
        return await this.crudManager.deleteEntity(id);
    }

    async restoreEntity(id) {
        console.log(
            "restoreEntity called with id:",
            id,
            "- delegating to modular system"
        );
        return await this.crudManager.restoreEntity(id);
    }

    // Métodos de acceso a las funcionalidades internas
    loadEntities(page = 1) {
        return this.crudManager.loadEntities(page);
    }

    // Getters para compatibilidad
    get currentPage() {
        return this.crudManager.currentPage;
    }

    get perPage() {
        return this.crudManager.perPage;
    }

    get searchTerm() {
        return this.crudManager.searchTerm;
    }

    get showDeleted() {
        return this.crudManager.showDeleted;
    }

    get currentData() {
        return this.crudManager.currentData;
    }

    // Setters para compatibilidad
    set currentPage(value) {
        this.crudManager.currentPage = value;
    }

    set perPage(value) {
        this.crudManager.perPage = value;
    }

    set searchTerm(value) {
        this.crudManager.searchTerm = value;
    }

    set showDeleted(value) {
        this.crudManager.showDeleted = value;
    }

    set currentData(value) {
        this.crudManager.currentData = value;
    }
}

// Hacer disponible globalmente para compatibilidad
window.CrudManagerModal = CrudManagerModal;
console.log("CrudManagerModal loaded with complete modular system");

export default CrudManagerModal;
