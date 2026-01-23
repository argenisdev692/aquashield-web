@props([
    'id' => 'showDeleted',
    'label' => '',
    'hasLabel' => true,
    'managerName' => 'appointmentManager',
])

<div class="flex items-center justify-between md:justify-start space-x-2">
    @if ($hasLabel)
        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $label ?: __('show_inactive_items') }}</span>
    @endif

    <label for="{{ $id }}" class="relative inline-flex items-center cursor-pointer">
        <input type="checkbox" id="{{ $id }}" class="sr-only peer" {{ $attributes }}>
        <div
            class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600">
        </div>
    </label>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleElement = document.getElementById('{{ $id }}');

        if (toggleElement) {
            // Check if there's a value stored in localStorage
            const storedValue = localStorage.getItem('{{ $id }}');

            // Inicializar el toggle basado en localStorage
            if (storedValue !== null) {
                toggleElement.checked = storedValue === 'true';
                console.log('Toggle inicializado desde localStorage:', {
                    checked: toggleElement.checked
                });
            }

            // Función para aplicar el estado del toggle al manager
            function applyToggleState() {
                if (!window.{{ $managerName }}) {
                    console.log('{{ $managerName }} no disponible todavía');
                    return false;
                }

                window.{{ $managerName }}.showDeleted = toggleElement.checked;
                console.log('Estado aplicado a {{ $managerName }}:', {
                    showDeleted: window.{{ $managerName }}.showDeleted
                });
                return true;
            }

            // Esperar a que el manager esté disponible y aplicar el estado
            let attempts = 0;
            const maxAttempts = 10;
            const waitForManager = setInterval(function() {
                attempts++;

                if (applyToggleState()) {
                    clearInterval(waitForManager);
                    if (toggleElement.checked) {
                        // Forzar recarga con los elementos inactivos si el toggle está activado
                        window.{{ $managerName }}.loadEntities();
                        console.log('Cargando entidades inactivas al iniciar');
                    }
                } else if (attempts >= maxAttempts) {
                    clearInterval(waitForManager);
                    console.log('No se pudo sincronizar con {{ $managerName }} después de', attempts,
                        'intentos');
                }
            }, 200);

            // Handle change events
            toggleElement.addEventListener('change', function() {
                // Store the current state in localStorage
                localStorage.setItem('{{ $id }}', this.checked);
                console.log('Toggle cambiado por usuario, guardado en localStorage:', {
                    checked: this.checked
                });

                // If we have access to the CrudManager instance, update and refresh
                if (window.{{ $managerName }}) {
                    window.{{ $managerName }}.showDeleted = this.checked;
                    window.{{ $managerName }}.currentPage = 1; // Reset to first page 
                    window.{{ $managerName }}.loadEntities();
                    console.log('Estado de toggle actualizado y datos recargados:', {
                        showDeleted: this.checked
                    });
                }
            });
        }
    });
</script>
