// Fade In Observer
export function initFadeInObserver() {
    const observerOptions = {
        root: null,
        rootMargin: "0px",
        threshold: 0.1,
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add("is-visible");
            }
        });
    }, observerOptions);

    document.querySelectorAll(".fade-in-section").forEach((section) => {
        observer.observe(section);
    });
}

// Initialize all common functions
document.addEventListener("DOMContentLoaded", function () {
    initFadeInObserver();
});

/**
 * Capitalización automática para inputs
 */
function setupGlobalCapitalization() {
    // Función para capitalizar texto
    function capitalizeText(input) {
        const cursorPosition = input.selectionStart;
        const value = input.value;

        // Capitalizar la primera letra de cada palabra
        const capitalizedValue = value.replace(/\b\w/g, function (match) {
            return match.toUpperCase();
        });

        // Solo actualizar si hay cambios para evitar loops
        if (capitalizedValue !== value) {
            input.value = capitalizedValue;
            // Restaurar la posición del cursor
            input.setSelectionRange(cursorPosition, cursorPosition);
        }
    }

    // Aplicar a inputs existentes
    document.querySelectorAll(".auto-capitalize").forEach((input) => {
        if (!input.hasAttribute("data-capitalize-listener")) {
            input.addEventListener("input", function (event) {
                capitalizeText(this);
            });
            input.setAttribute("data-capitalize-listener", "true");
        }
    });

    // Observer para inputs que se agreguen dinámicamente
    const observer = new MutationObserver(function (mutations) {
        mutations.forEach(function (mutation) {
            mutation.addedNodes.forEach(function (node) {
                if (node.nodeType === 1) {
                    // Element node
                    // Buscar inputs con la clase auto-capitalize en el nodo agregado
                    const inputs = node.querySelectorAll
                        ? node.querySelectorAll(".auto-capitalize")
                        : [];
                    inputs.forEach((input) => {
                        if (!input.hasAttribute("data-capitalize-listener")) {
                            input.addEventListener("input", function (event) {
                                capitalizeText(this);
                            });
                            input.setAttribute(
                                "data-capitalize-listener",
                                "true"
                            );
                        }
                    });

                    // Si el nodo mismo tiene la clase
                    if (
                        node.classList &&
                        node.classList.contains("auto-capitalize") &&
                        !node.hasAttribute("data-capitalize-listener")
                    ) {
                        node.addEventListener("input", function (event) {
                            capitalizeText(this);
                        });
                        node.setAttribute("data-capitalize-listener", "true");
                    }
                }
            });
        });
    });

    // Iniciar el observer
    observer.observe(document.body, {
        childList: true,
        subtree: true,
    });
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", setupGlobalCapitalization);

// Exportar función para uso global
window.setupGlobalCapitalization = setupGlobalCapitalization;
