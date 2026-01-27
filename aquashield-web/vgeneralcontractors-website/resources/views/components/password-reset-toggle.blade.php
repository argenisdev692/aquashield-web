@props(['model'])

<div class="mb-4 col-span-2">
    <label for="send_password_reset" class="flex items-center cursor-pointer">
        <div class="relative">
            <input type="checkbox" wire:model.defer="send_password_reset" id="send_password_reset" class="sr-only">
            <div class="block bg-gray-600 dark:bg-gray-700 w-14 h-8 rounded-full"></div>
            <div class="dot absolute left-1 top-1 bg-white w-6 h-6 rounded-full transition transform"
                :class="{ 'translate-x-6': $wire.send_password_reset }"></div>
        </div>
        <span class="ml-3 text-gray-700 dark:text-gray-300">{{ __('send_new_password_email') }}</span>
    </label>
</div>
