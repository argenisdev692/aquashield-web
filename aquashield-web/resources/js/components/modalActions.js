/**
 * Modal Actions
 * Handles generic modal functionality
 */
export default {
    /**
     * Initialize modal functionality
     * @param {Object} options - Configuration options
     */
    init(options = {}) {
        this.options = {
            openModalEvent: "user-edit",
            closeModalEvent: "closeModal",
            createdSuccessEvent: "user-created-success",
            updatedSuccessEvent: "user-updated-success",
            errorEvent: "user-edit-error",
            ...options,
        };

        this.setupListeners();
    },

    /**
     * Setup event listeners for modal actions
     */
    setupListeners() {
        // Listen for modal open event with data
        document.addEventListener(this.options.openModalEvent, (event) => {
            if (event.detail) {
                this.populateModalFields(event.detail);
            }
            this.openModal();
        });

        // Success events
        document.addEventListener(this.options.createdSuccessEvent, () => {
            this.showSuccessMessage("User created successfully");
        });

        document.addEventListener(this.options.updatedSuccessEvent, () => {
            this.showSuccessMessage("User updated successfully");
        });

        // Error event
        document.addEventListener(this.options.errorEvent, () => {
            this.showErrorMessage("An error occurred");
        });

        // Setup close button listeners
        this.setupCloseButtons();
    },

    /**
     * Setup close button event handlers
     */
    setupCloseButtons() {
        document.querySelectorAll("[data-modal-close]").forEach((button) => {
            button.addEventListener("click", () => {
                this.closeModal();
            });
        });
    },

    /**
     * Open the modal
     */
    openModal() {
        const modal = document.querySelector(".modal");
        if (modal) {
            modal.classList.remove("hidden");
            document.body.classList.add("overflow-hidden");
        }
    },

    /**
     * Close the modal
     */
    closeModal() {
        const modal = document.querySelector(".modal");
        if (modal) {
            modal.classList.add("hidden");
            document.body.classList.remove("overflow-hidden");

            // Notify Livewire component to close modal
            if (window.Livewire) {
                window.Livewire.dispatch(this.options.closeModalEvent);
            }
        }
    },

    /**
     * Populate modal fields with provided data
     * @param {Object} data - Data to populate fields with
     */
    populateModalFields(data) {
        // Skip if no data provided
        if (!data || Object.keys(data).length === 0) {
            return;
        }

        // Populate each field that exists in the form
        Object.keys(data).forEach((key) => {
            const input = document.querySelector(`[name="${key}"]`);
            if (input) {
                // Handle different input types
                if (input.type === "checkbox") {
                    input.checked = !!data[key];
                } else {
                    input.value = data[key] || "";
                }
            }
        });
    },

    /**
     * Show success message
     * @param {string} message - Success message to display
     */
    showSuccessMessage(message) {
        const alertContainer = document.getElementById("alert-container");
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert alert-success">
                    ${message}
                </div>
            `;

            // Remove after 3 seconds
            setTimeout(() => {
                alertContainer.innerHTML = "";
            }, 3000);
        }
    },

    /**
     * Show error message
     * @param {string} message - Error message to display
     */
    showErrorMessage(message) {
        const alertContainer = document.getElementById("alert-container");
        if (alertContainer) {
            alertContainer.innerHTML = `
                <div class="alert alert-error">
                    ${message}
                </div>
            `;

            // Remove after 5 seconds
            setTimeout(() => {
                alertContainer.innerHTML = "";
            }, 5000);
        }
    },

    /**
     * Create configuration for a delete confirmation modal
     * @param {Object} config - Configuration options
     * @returns {Object} - Alpine.js data object for delete modal
     */
    deleteConfirmation(config = {}) {
        return {
            showModal: false,
            itemToDelete: null,
            isProcessing: false,
            eventName: config.eventName || "delete-confirmation",
            successEvent: config.successEvent || "itemDeleted",

            init() {
                // Listen for confirmation request
                window.addEventListener(this.eventName, (event) => {
                    this.itemToDelete = event.detail;
                    this.showModal = true;
                });

                // Listen for success event
                window.addEventListener(this.successEvent, () => {
                    this.showModal = false;
                    this.isProcessing = false;
                    this.itemToDelete = null;
                });
            },

            confirmDelete(deleteFunction) {
                if (!this.itemToDelete || this.isProcessing) return;

                this.isProcessing = true;

                // Execute the provided delete function
                deleteFunction(this.itemToDelete)
                    .then(() => {
                        this.showModal = false;
                        this.isProcessing = false;
                    })
                    .catch((error) => {
                        console.error("Error processing delete action:", error);
                        this.isProcessing = false;
                    });
            },

            cancel() {
                this.showModal = false;
                this.itemToDelete = null;
            },
        };
    },

    /**
     * Create configuration for a restore confirmation modal
     * @param {Object} config - Configuration options
     * @returns {Object} - Alpine.js data object for restore modal
     */
    restoreConfirmation(config = {}) {
        return {
            showModal: false,
            itemToRestore: null,
            isProcessing: false,
            eventName: config.eventName || "restore-confirmation",
            successEvent: config.successEvent || "itemRestored",

            init() {
                // Listen for confirmation request
                window.addEventListener(this.eventName, (event) => {
                    this.itemToRestore = event.detail;
                    this.showModal = true;
                });

                // Listen for success event
                window.addEventListener(this.successEvent, () => {
                    this.showModal = false;
                    this.isProcessing = false;
                    this.itemToRestore = null;
                });
            },

            confirmRestore(restoreFunction) {
                if (!this.itemToRestore || this.isProcessing) return;

                this.isProcessing = true;

                // Execute the provided restore function
                restoreFunction(this.itemToRestore)
                    .then(() => {
                        this.showModal = false;
                        this.isProcessing = false;
                    })
                    .catch((error) => {
                        console.error(
                            "Error processing restore action:",
                            error
                        );
                        this.isProcessing = false;
                    });
            },

            cancel() {
                this.showModal = false;
                this.itemToRestore = null;
            },
        };
    },
};

/**
 * Initialize keyboard shortcuts
 */
export function initKeyboardShortcuts() {
    document.addEventListener("keydown", (e) => {
        // Only process if no modals are open and not typing in an input
        const isTyping = ["INPUT", "TEXTAREA", "SELECT"].includes(
            document.activeElement.tagName
        );
        const modalOpen = document.querySelector(
            ".fixed.inset-0.z-50:not(.hidden)"
        );

        if (isTyping || modalOpen) return;

        switch (e.key) {
            case "n": // Create new user
                Livewire.dispatch("openModal");
                break;
            case "f": // Focus search box
                document.getElementById("search")?.focus();
                break;
            case "r": // Toggle show deleted users
                Livewire.dispatch("toggleShowDeleted");
                break;
            case "?": // Show keyboard shortcuts
                const keyboardShortcuts =
                    document.getElementById("keyboard-shortcuts");
                if (keyboardShortcuts) {
                    keyboardShortcuts.classList.toggle("hidden");
                    keyboardShortcuts.classList.toggle("flex");
                }
                break;
        }
    });
}

export function confirmDelete(uuid, name) {
    window.dispatchEvent(
        new CustomEvent("delete-confirmation", {
            detail: { uuid, name },
        })
    );
}

export function confirmRestore(uuid, name) {
    window.dispatchEvent(
        new CustomEvent("restore-confirmation", {
            detail: { uuid, name },
        })
    );
}
