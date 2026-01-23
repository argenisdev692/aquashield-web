@props([
    'id',
    'name' => $id,
    'placeholder' => '',
    'managerName' => 'appointmentManager',
    'class' =>
        'block w-full border border-gray-300 dark:border-gray-600 rounded-md shadow-sm py-2 px-3 leading-5 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm text-gray-900 dark:text-gray-100 dark:bg-gray-700',
])

<div class="relative">
    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
            fill="currentColor">
            <path fill-rule="evenodd"
                d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z"
                clip-rule="evenodd" />
        </svg>
    </div>
    <input type="text" id="{{ $id }}" name="{{ $name }}" class="{{ $class }} pl-10 pr-10"
        placeholder="{{ $placeholder ?: __('search') }}" autocomplete="off" {{ $attributes }}>
    <div class="absolute inset-y-0 right-0 pr-3 top-2.5 items-center cursor-pointer hidden"
        id="{{ $id }}-clear">
        <svg class="h-5 w-5 text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300"
            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                clip-rule="evenodd" />
        </svg>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('{{ $id }}');
        const clearButton = document.getElementById('{{ $id }}-clear');
        let debounceTimer;

        if (searchInput && clearButton) {
            // Show/hide clear button based on input content
            searchInput.addEventListener('input', function() {
                if (this.value.length > 0) {
                    clearButton.classList.remove('hidden');
                } else {
                    clearButton.classList.add('hidden');
                }

                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    // Debug log
                    console.log('Search input changed:', this.value);

                    // Ensure the searchTerm is properly set in CrudManager
                    if (window.{{ $managerName }}) {
                        window.{{ $managerName }}.searchTerm = this.value;
                        console.log('Set {{ $managerName }}.searchTerm to:', this.value);
                    }

                    // This will trigger the CrudManager's search handler through both events
                    $(searchInput).trigger('keyup');
                    $(searchInput).trigger('change');
                }, 300); // 300ms debounce
            });

            // Clear input when X button is clicked
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                clearButton.classList.add('hidden');
                searchInput.focus();

                // Ensure the searchTerm is cleared in CrudManager
                if (window.{{ $managerName }}) {
                    window.{{ $managerName }}.searchTerm = '';
                    console.log('Cleared {{ $managerName }}.searchTerm');
                }

                $(searchInput).trigger('keyup');
                $(searchInput).trigger('change');
            });

            // Check initial state
            if (searchInput.value.length > 0) {
                clearButton.classList.remove('hidden');
            }
        }
    });
</script>
