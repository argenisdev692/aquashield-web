<!-- components/modals/form-modal.blade.php -->
<div class="fixed z-50 inset-0 overflow-y-auto ease-out duration-400" x-show="{{ $isOpen }}">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <!-- Fondo oscuro -->
        <div class="fixed inset-0 transition-opacity">
            <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
        </div>

        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl w-full md:w-3/4 sm:w-full"
            {{ $attributes }}>
            <form wire:submit.prevent="{{ $modalAction }}">
                <!-- Encabezado -->
                <div class="bg-gray-900 px-4 py-3 sm:px-6">
                    <div class="flex items-center justify-center relative">
                        <h3 class="text-lg font-medium text-white text-center">{{ $modalTitle }}</h3>
                        <button type="button" wire:click="closeModal"
                            class="absolute right-0 text-white hover:text-gray-200 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Cuerpo -->
                <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    {{ $slot }}
                </div>

                <!-- Pie -->
                <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="sm:w-auto w-full inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-gray-900 dark:bg-gray-800 text-base font-medium text-white hover:bg-gray-700 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 dark:focus:ring-gray-600 disabled:opacity-50 disabled:cursor-not-allowed"
                        wire:loading.attr="disabled" wire:target="{{ $modalAction }}"
                        x-bind:disabled="Object.keys(errors).length > 0">
                        <span wire:loading.remove wire:target="{{ $modalAction }}">
                            {{ $modalAction === 'store' ? __('save') : __('update') }}
                        </span>
                        <span wire:loading wire:target="{{ $modalAction }}" class="inline-flex items-center">
                            <svg class="animate-spin mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ __('saving') }}
                        </span>
                    </button>
                    <button type="button" wire:click="closeModal"
                        class="mr-3 hidden lg:inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white dark:bg-gray-800 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-indigo-600 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        {{ __('cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
