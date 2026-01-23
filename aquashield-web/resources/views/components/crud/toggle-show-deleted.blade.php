@props([
    'id' => 'showDeleted',
    'label' => 'Show Inactive Items',
    'managerName' => 'crudManager',
    'class' => '',
])

<div class="flex items-center {{ $class }}">
    <label for="{{ $id }}" class="mr-2 text-sm font-medium text-white/90 capitalize">
        🗑️ {{ $label }}
    </label>
    <label for="{{ $id }}" class="flex items-center cursor-pointer mt-1">
        <div class="relative">
            <input type="checkbox" id="{{ $id }}" class="sr-only">
            <div class="block bg-black/50 border border-white/10 w-10 h-6 rounded-full transition-colors duration-200">
            </div>
            <div
                class="dot absolute left-1 top-1 bg-white/90 w-4 h-4 rounded-full transition-transform duration-200 shadow-sm shadow-purple-500/20">
            </div>
        </div>
    </label>
</div>

@push('styles')
    <style>
        /* Toggle CSS for {{ $id }} */
        #{{ $id }}:checked~.dot {
            transform: translateX(100%);
            background-color: #8B5CF6;
            /* Tailwind purple-500 */
            box-shadow: 0 0 10px rgba(139, 92, 246, 0.5);
        }

        #{{ $id }}:checked~.block {
            background-color: rgba(139, 92, 246, 0.2);
            border-color: rgba(139, 92, 246, 0.5);
        }

        /* Hover effects */
        label[for="{{ $id }}"] .block {
            transition: all 0.3s ease;
        }

        label[for="{{ $id }}"]:hover .block {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2);
        }

        #{{ $id }}:checked+label[for="{{ $id }}"]:hover .block {
            background-color: rgba(139, 92, 246, 0.3);
            border-color: rgba(139, 92, 246, 0.6);
        }

        /* Add glow effect when active */
        #{{ $id }}:checked~.dot {
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.7);
        }
    </style>
@endpush
