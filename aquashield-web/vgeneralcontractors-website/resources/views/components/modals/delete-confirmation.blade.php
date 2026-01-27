@props(['itemType' => 'item'])

<div x-data="{
    showDeleteModal: false,
    itemToDelete: null,
    isDeleting: false,
    init() {
        // Escuchar evento genérico
        window.addEventListener('delete-confirmation', (event) => {
            this.itemToDelete = event.detail;
            this.showDeleteModal = true;
        });

        // Escuchar evento específico por tipo
        if ('{{ $itemType }}' === 'user') {
            window.addEventListener('user-delete-confirmation', (event) => {
                this.itemToDelete = event.detail;
                this.showDeleteModal = true;
            });

            // Eventos de finalización específicos
            $wire.on('userDeleted', () => {
                this.showDeleteModal = false;
                this.isDeleting = false;
                this.itemToDelete = null;
            });

            $wire.on('userDeleteError', () => {
                this.isDeleting = false;
            });
        }

        // Escuchar evento genérico de finalización
        window.addEventListener('itemDeleted', () => {
            this.showDeleteModal = false;
            this.isDeleting = false;
            this.itemToDelete = null;
        });
    }
}" x-show="showDeleteModal" x-cloak class="fixed inset-0 overflow-y-auto z-50">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" x-show="showDeleteModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full md:w-3/4 sm:w-full"
            x-show="showDeleteModal">
            <div class="bg-red-600 px-4 py-3">
                <div class="flex items-center justify-center">
                    <h3 class="text-lg font-medium text-white text-center" id="modal-headline">
                        {{ __('confirm_deletion') }}
                    </h3>
                    <button @click="showDeleteModal = false; itemToDelete = null;"
                        class="absolute right-0 mr-4 text-white hover:text-gray-200 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-headline">
                            {{ __('delete') }} {{ ucfirst($itemType) }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-show="itemToDelete">
                                {{ __('are_you_sure_delete') }} {{ $itemType }} <span class="font-bold"
                                    x-text="itemToDelete?.name"></span>{{ __('this_action_cannot_be_undone') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    x-on:click="
                    isDeleting = true;
                    @this.delete(itemToDelete.uuid).then(() => {
                        showDeleteModal = false;
                        isDeleting = false;
                        itemToDelete = null;
                    }).catch(error => {
                        console.error('Error deleting {{ $itemType }}:', error);
                        isDeleting = false;
                    });"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm"
                    :disabled="isDeleting" :class="{ 'opacity-75 cursor-not-allowed': isDeleting }">
                    <span x-show="!isDeleting">{{ __('delete') }}</span>
                    <span x-show="isDeleting" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('deleting') }}
                    </span>
                </button>
                <button type="button" @click="showDeleteModal = false; itemToDelete = null;"
                    class="hidden lg:inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    :disabled="isDeleting" :class="{ 'opacity-75 cursor-not-allowed': isDeleting }">
                    {{ __('cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
