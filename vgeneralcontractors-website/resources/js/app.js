import "./bootstrap";
import "../css/app.css";
import "../css/common.css"; // Import common styles
import "./common"; // Import common scripts

// Importa el paquete livewire-sortable
import "livewire-sortable";

// Import Alpine.js
import Alpine from "alpinejs";

// Import SweetAlert2
import Swal from "sweetalert2"; // Import
import "sweetalert2/dist/sweetalert2.min.css"; // Import the CSS

// Make Alpine available globally
window.Alpine = Alpine;

// Make Swal globally accessible (optional, but needed for your current form JS)
window.Swal = Swal;

// Start Alpine
Alpine.start();

// Importar los componentes
import formValidation from "./components/formValidation.js";
import phoneFormat from "./components/phoneFormat.js";
import modalActions from "./components/modalActions.js";

// Exponer componentes a la ventana global para uso con Alpine.js
window.formValidation = formValidation;
window.phoneFormat = phoneFormat;
window.modalActions = modalActions;

// Configurar eventos de Livewire
document.addEventListener("livewire:initialized", () => {
    Livewire.on("refreshComponent", () => {
        Livewire.dispatch("$refresh");
    });

    Livewire.on("closeModal", () => {
        document.getElementById("closeModalButton")?.click();
    });

    // Eventos de validación y formularios
    Livewire.on("validationErrors", (errors) => {
        window.dispatchEvent(
            new CustomEvent("validation-errors", {
                detail: errors,
            })
        );
    });

    Livewire.on("formData", (data) => {
        window.dispatchEvent(
            new CustomEvent("form-data", {
                detail: data,
            })
        );
    });

    // Evento para confirmación de eliminación
    Livewire.on("confirmDelete", (userData) => {
        window.dispatchEvent(
            new CustomEvent("delete-confirmation", {
                detail: userData,
            })
        );
    });

    // Evento para confirmación de restauración
    Livewire.on("confirmRestore", (userData) => {
        window.dispatchEvent(
            new CustomEvent("restore-confirmation", {
                detail: userData,
            })
        );
    });
});

// Para registrar navegaciones en Livewire con Facebook Pixel
document.addEventListener("livewire:navigated", () => {
    if (typeof fbq === "function") {
        fbq("track", "PageView");
    }
});

// Puedes agregar aquí cualquier otra inicialización que necesites
console.log("Application JavaScript initialized");
