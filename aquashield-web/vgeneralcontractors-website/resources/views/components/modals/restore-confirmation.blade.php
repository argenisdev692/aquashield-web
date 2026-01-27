@props(['itemType' => 'item'])

<div x-data="{
    showRestoreModal: false,
    itemToRestore: null,
    isRestoring: false,
    init() {
        // Escuchar evento genérico
        window.addEventListener('restore-confirmation', (event) => {
            this.itemToRestore = event.detail;
            this.showRestoreModal = true;
        });

        // Escuchar evento específico por tipo
        if ('{{ $itemType }}' === 'user') {
            window.addEventListener('user-restore-confirmation', (event) => {
                this.itemToRestore = event.detail;
                this.showRestoreModal = true;
            });

            // Eventos de finalización específicos
            $wire.on('userRestored', () => {
                this.showRestoreModal = false;
                this.isRestoring = false;
                this.itemToRestore = null;
            });

            $wire.on('userRestoreError', () => {
                this.isRestoring = false;
            });
        }

        // Escuchar evento genérico de finalización
        window.addEventListener('itemRestored', () => {
            this.showRestoreModal = false;
            this.isRestoring = false;
            this.itemToRestore = null;
        });
    }
}" x-show="showRestoreModal" x-cloak class="fixed inset-0 overflow-y-auto z-50">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Background overlay -->
        <div class="fixed inset-0 transition-opacity" aria-hidden="true" x-show="showRestoreModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full md:w-3/4 sm:w-full"
            x-show="showRestoreModal">
            <div class="bg-green-600 px-4 py-3">
                <div class="flex items-center justify-center">
                    <h3 class="text-lg font-medium text-white text-center" id="modal-headline">
                        {{ __('confirm_restoration') }}
                    </h3>
                    <button @click="showRestoreModal = false; itemToRestore = null;"
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
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z">
                            </path>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" id="modal-headline">
                            {{ __('restore') }} {{ ucfirst($itemType) }}
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 dark:text-gray-400" x-show="itemToRestore">
                                {{ __('are_you_sure_restore') }} {{ $itemType }} <span class="font-bold"
                                    x-text="itemToRestore?.name"></span>?
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button"
                    x-on:click="
                    isRestoring = true;
                    @this.restore(itemToRestore.uuid).then(() => {
                        showRestoreModal = false;
                        isRestoring = false;
                        itemToRestore = null;
                    }).catch(error => {
                        console.error('Error restoring {{ $itemType }}:', error);
                        isRestoring = false;
                    });"
                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm"
                    :disabled="isRestoring" :class="{ 'opacity-75 cursor-not-allowed': isRestoring }">
                    <span x-show="!isRestoring">{{ __('restore') }}</span>
                    <span x-show="isRestoring" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg"
                            fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                        {{ __('restoring') }}
                    </span>
                </button>
                <button type="button" @click="showRestoreModal = false; itemToRestore = null;"
                    class="hidden lg:inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-600 text-base font-medium text-gray-700 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                    :disabled="isRestoring" :class="{ 'opacity-75 cursor-not-allowed': isRestoring }">
                    {{ __('cancel') }}
                </button>
            </div>
        </div>
    </div>
</div>
