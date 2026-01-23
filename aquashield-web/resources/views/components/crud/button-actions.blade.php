@props([
    'entityId',
    'showEdit' => true,
    'showDelete' => true,
    'showRestore' => false,
    'isDeleted' => false,
    'editClass' => 'edit-btn',
    'deleteClass' => 'delete-btn',
    'restoreClass' => 'restore-btn',
    'size' => 'sm',
])

@php
    $buttonSizes = [
        'xs' => 'w-7 h-7',
        'sm' => 'w-9 h-9',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
    ];

    $iconSizes = [
        'xs' => 'h-3 w-3',
        'sm' => 'h-4 w-4',
        'md' => 'h-5 w-5',
        'lg' => 'h-6 w-6',
    ];

    $buttonSize = $buttonSizes[$size] ?? $buttonSizes['sm'];
    $iconSize = $iconSizes[$size] ?? $iconSizes['sm'];
@endphp

<div class="flex justify-center space-x-2">
    @if ($showEdit)
        <button data-id="{{ $entityId }}"
            class="{{ $editClass }} inline-flex items-center justify-center {{ $buttonSize }} bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
            title="Edit">
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
        </button>
    @endif

    @if ($isDeleted && $showRestore)
        <button data-id="{{ $entityId }}"
            class="{{ $restoreClass }} inline-flex items-center justify-center {{ $buttonSize }} bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
            title="Restore">
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
        </button>
    @elseif(!$isDeleted && $showDelete)
        <button data-id="{{ $entityId }}"
            class="{{ $deleteClass }} inline-flex items-center justify-center {{ $buttonSize }} bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
            title="Delete">
            <svg xmlns="http://www.w3.org/2000/svg" class="{{ $iconSize }}" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
        </button>
    @endif

    {{ $slot }}
</div>
