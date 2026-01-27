@props([
    'id' => 'crud-advanced-table',
    'columns' => [],
    'managerName' => 'crudManager',
    'loadingText' => __('loading'),
    'noDataText' => __('no_data'),
    'responsive' => true,
    'sortable' => true,
    'darkMode' => true,
])

<div class="glassmorphism-container {{ $responsive ? 'responsive-container' : '' }}">
    <div class="glassmorphism-table-wrapper glassmorphism-scroll-wrapper">
        <table id="{{ $id }}" class="glassmorphism-table">
            <thead class="glassmorphism-header">
                <tr>
                    @foreach ($columns as $column)
                        <th class="glassmorphism-th {{ $sortable && ($column['sortable'] ?? true) ? 'sortable-header' : '' }}"
                            @if ($sortable && ($column['sortable'] ?? true)) data-field="{{ $column['field'] }}" @endif>
                            <div class="th-content">
                                <span class="column-text">{{ $column['label'] }}</span>
                                @if ($sortable && ($column['sortable'] ?? true))
                                    <span class="sort-indicator">
                                        <svg class="sort-icon" viewBox="0 0 24 24">
                                            <path d="M7 14l5-5 5 5z" />
                                            <path d="M7 10l5 5 5-5z" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </th>
                    @endforeach
                </tr>
            </thead>
            <tbody id="{{ $id }}-body" class="glassmorphism-body">
                <!-- Loading row -->
                <tr id="loadingRow" class="loading-row">
                    <td colspan="{{ count($columns) }}" class="loading-cell">
                        <div class="loading-content">
                            <div class="loading-spinner">
                                <div class="spinner-ring"></div>
                                <div class="spinner-ring"></div>
                                <div class="spinner-ring"></div>
                            </div>
                            <span class="loading-text">{{ $loadingText }}</span>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<style>
    /* Premium Glassmorphism Table 2025 with Purple Shadows */
    .glassmorphism-container {
        position: relative;
        margin: 1rem 0;
        border-radius: 20px;
        padding: 1.5rem;

        /* Crystal Glass Background with Premium Transparency */
        background: rgba(0, 0, 0, 0.78);

        /* Premium Purple Box Shadow System */
        box-shadow:
            0 8px 32px 0 rgba(138, 43, 226, 0.25),
            0 16px 64px 0 rgba(128, 0, 255, 0.18),
            0 4px 16px 0 rgba(75, 0, 130, 0.3),
            0 2px 8px 0 rgba(147, 51, 234, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.15),
            inset 0 -1px 0 rgba(255, 255, 255, 0.08);

        /* Advanced Blur Effects */
        backdrop-filter: blur(20px) saturate(1.3);
        -webkit-backdrop-filter: blur(20px) saturate(1.3);

        /* Refined Border for Glass Effect */
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-top: 1px solid rgba(255, 255, 255, 0.25);

        /* Enhanced Animation */
        animation: fadeInUp 0.8s cubic-bezier(0.4, 0, 0.2, 1);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .glassmorphism-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 20px;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(255, 255, 255, 0.05) 25%,
                transparent 50%,
                rgba(138, 43, 226, 0.08) 75%,
                rgba(128, 0, 255, 0.12) 100%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .glassmorphism-container:hover {
        transform: translateY(-3px);
        box-shadow:
            0 12px 48px 0 rgba(138, 43, 226, 0.35),
            0 24px 80px 0 rgba(128, 0, 255, 0.25),
            0 6px 24px 0 rgba(75, 0, 130, 0.4),
            0 3px 12px 0 rgba(147, 51, 234, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.2),
            inset 0 -1px 0 rgba(255, 255, 255, 0.1);
    }

    .glassmorphism-container:hover::before {
        opacity: 1;
    }

    .glassmorphism-table-wrapper {
        /* Enhanced Crystal Glass Background */
        background: rgba(0, 0, 0, 0.82);
        border-radius: 16px;

        /* Premium Purple Box Shadow System */
        box-shadow:
            0 6px 24px 0 rgba(138, 43, 226, 0.22),
            0 12px 48px 0 rgba(128, 0, 255, 0.15),
            0 2px 12px 0 rgba(75, 0, 130, 0.25),
            0 1px 6px 0 rgba(147, 51, 234, 0.18),
            inset 0 1px 0 rgba(255, 255, 255, 0.12),
            inset 0 -1px 0 rgba(255, 255, 255, 0.06);

        /* Enhanced Blur Effects */
        backdrop-filter: blur(16px) saturate(1.2);
        -webkit-backdrop-filter: blur(16px) saturate(1.2);

        /* Premium Glass Border */
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-top: 1px solid rgba(255, 255, 255, 0.18);

        /* Mejorar la gestión del scroll */
        overflow-x: auto;
        overflow-y: auto;
        max-height: 70vh;
        position: relative;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        /* Aislar el contexto de apilamiento para evitar interferencias */
        isolation: isolate;
        /* Optimizar el scroll para mejor rendimiento */
        scroll-behavior: smooth;
        -webkit-overflow-scrolling: touch;
    }

    /* Forzar scroll horizontal en responsive */
    .responsive-container {
        overflow-x: auto !important;
        overflow-y: visible;
        width: 100%;
    }

    /* Extra: Si quieres scroll vertical en mobile también */
    .glassmorphism-scroll-wrapper {
        overflow-x: auto;
        overflow-y: auto;
        max-height: 70vh;
        width: 100%;
        /* Permitir scrollbars reales, pero el shimmer sigue visible porque el overflow: hidden está en el wrapper interno */
    }

    @media (max-width: 768px) {

        .glassmorphism-table-wrapper,
        .glassmorphism-scroll-wrapper {
            max-height: 50vh;
            overflow-x: auto;
            overflow-y: auto;
        }
    }

    /* Shimmer effect animation */
    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }

        100% {
            background-position: 1000px 0;
        }
    }

    .glassmorphism-table-wrapper::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: 16px;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.08) 0%,
                rgba(255, 255, 255, 0.03) 25%,
                transparent 50%,
                rgba(138, 43, 226, 0.06) 75%,
                rgba(128, 0, 255, 0.1) 100%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .glassmorphism-table-wrapper:hover::after {
        opacity: 1;
    }

    .glassmorphism-table {
        width: 100%;
        border-collapse: collapse;
        color: rgba(255, 255, 255, 0.95);
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    .glassmorphism-header {
        /* Premium Glass Header Background */
        background: rgba(0, 0, 0, 0.85);

        /* Enhanced Purple Shadow for Header */
        box-shadow:
            0 2px 16px 0 rgba(138, 43, 226, 0.15),
            0 4px 24px 0 rgba(128, 0, 255, 0.1),
            0 1px 6px 0 rgba(75, 0, 130, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);

        /* Advanced Blur for Header */
        backdrop-filter: blur(12px) saturate(1.1);
        -webkit-backdrop-filter: blur(12px) saturate(1.1);

        border-bottom: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 16px 16px 0 0;
        position: relative;
        transition: all 0.3s ease;
        /* Mejorado el aislamiento del overflow para el shimmer */
        overflow: hidden;
        isolation: isolate;
        /* Crear nuevo contexto de apilamiento */
    }

    /* Shimmer animated effect for table header */
    .glassmorphism-header::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        /* Cambiado para usar con transform */
        width: 40%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        animation: shimmer-header 2.2s infinite;
        pointer-events: none;
        z-index: 1;
        /* Reducido de 2 a 1 para no interferir con scroll */
        /* Aislamiento del shimmer solo al header */
        clip-path: inset(0 0 0 0);
        will-change: transform;
        transform: translateX(-100%) translateZ(0);
        /* Force hardware acceleration */
    }

    .glassmorphism-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.12) 0%,
                rgba(255, 255, 255, 0.06) 25%,
                transparent 50%,
                rgba(138, 43, 226, 0.08) 75%,
                rgba(128, 0, 255, 0.12) 100%);
        border-radius: 16px 16px 0 0;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .glassmorphism-header:hover::before {
        opacity: 1;
    }

    .glassmorphism-th {
        padding: 1rem 1.5rem;
        text-align: center;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: rgba(255, 255, 255, 0.85);
        border-right: none;
        position: relative;
        transition: all 0.3s ease;
    }

    .glassmorphism-th:last-child {
        border-right: none;
    }

    .glassmorphism-th:hover {
        background: rgba(255, 255, 255, 0.12);
        color: rgba(255, 255, 255, 0.98);
        box-shadow:
            0 2px 8px 0 rgba(138, 43, 226, 0.2),
            0 4px 16px 0 rgba(128, 0, 255, 0.1);
    }

    .th-content {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .sortable-header {
        cursor: pointer;
        user-select: none;
    }

    .sortable-header:hover {
        background: rgba(255, 255, 255, 0.15);
        box-shadow:
            0 2px 12px 0 rgba(138, 43, 226, 0.25),
            0 4px 20px 0 rgba(128, 0, 255, 0.15);
    }

    .sort-indicator {
        display: flex;
        align-items: center;
        opacity: 0.6;
        transition: all 0.3s ease;
    }

    .sort-icon {
        width: 16px;
        height: 16px;
        fill: currentColor;
        filter: drop-shadow(0 0 6px rgba(138, 43, 226, 0.5));
    }

    .sortable-header:hover .sort-indicator {
        opacity: 1;
        transform: scale(1.1);
        filter: drop-shadow(0 0 8px rgba(138, 43, 226, 0.7));
    }

    .sortable-header.sort-asc .sort-indicator {
        opacity: 1;
        color: #A855F7;
        transform: rotate(0deg);
        filter: drop-shadow(0 0 8px rgba(168, 85, 247, 0.8));
    }

    .sortable-header.sort-desc .sort-indicator {
        opacity: 1;
        color: #A855F7;
        transform: rotate(180deg);
        filter: drop-shadow(0 0 8px rgba(168, 85, 247, 0.8));
    }

    .glassmorphism-body {
        background: rgba(0, 0, 0, 0.4);
    }

    .glassmorphism-body tr {
        border-bottom: none;
        transition: all 0.3s ease;
        animation: fadeIn 0.5s ease-out;
    }

    .glassmorphism-body tr:hover {
        background: rgba(255, 255, 255, 0.08);
        transform: translateY(-1px);
        box-shadow:
            0 4px 20px rgba(138, 43, 226, 0.15),
            0 8px 32px rgba(128, 0, 255, 0.1);
    }

    /* Deleted/Soft Deleted Row Styles - Premium Red Glass Effect */
    .glassmorphism-body tr.deleted-row {
        position: relative;
        background: rgba(220, 38, 38, 0.15) !important;
        backdrop-filter: blur(12px) saturate(1.1);
        -webkit-backdrop-filter: blur(12px) saturate(1.1);
        border: 1px solid rgba(220, 38, 38, 0.25);
        border-radius: 8px;
        box-shadow:
            0 2px 16px rgba(220, 38, 38, 0.2),
            0 4px 24px rgba(239, 68, 68, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        opacity: 0.75;
        transform: scale(0.995);
        overflow: hidden;
    }

    .glassmorphism-body tr.deleted-row::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg,
                rgba(220, 38, 38, 0.1) 0%,
                rgba(239, 68, 68, 0.08) 25%,
                transparent 50%,
                rgba(185, 28, 28, 0.12) 75%,
                rgba(220, 38, 38, 0.15) 100%);
        pointer-events: none;
        border-radius: 8px;
    }

    .glassmorphism-body tr.deleted-row::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 5%;
        right: 5%;
        height: 2px;
        background: linear-gradient(90deg,
                transparent 0%,
                rgba(220, 38, 38, 0.3) 10%,
                rgba(220, 38, 38, 0.8) 20%,
                rgba(239, 68, 68, 1) 50%,
                rgba(220, 38, 38, 0.8) 80%,
                rgba(220, 38, 38, 0.3) 90%,
                transparent 100%);
        transform: translateY(-50%);
        pointer-events: none;
        border-radius: 1px;
        box-shadow:
            0 0 8px rgba(220, 38, 38, 0.6),
            0 0 16px rgba(239, 68, 68, 0.4);
        z-index: 1;
        animation: deletedGlow 2s ease-in-out infinite alternate;
    }

    .glassmorphism-body tr.deleted-row:hover {
        background: rgba(220, 38, 38, 0.2) !important;
        transform: scale(0.995) translateY(-1px);
        box-shadow:
            0 4px 24px rgba(220, 38, 38, 0.3),
            0 8px 40px rgba(239, 68, 68, 0.2),
            inset 0 1px 0 rgba(255, 255, 255, 0.15);
        opacity: 0.85;
    }

    .glassmorphism-body tr.deleted-row td {
        color: rgba(255, 255, 255, 0.7) !important;
        text-decoration: line-through;
        text-decoration-color: rgba(220, 38, 38, 0.8);
        text-decoration-thickness: 1.5px;
        text-shadow: 0 1px 3px rgba(220, 38, 38, 0.3);
        position: relative;
        z-index: 2;
    }

    .glassmorphism-body tr.deleted-row td:hover {
        color: rgba(255, 255, 255, 0.85) !important;
    }

    /* Deleted row animation */
    @keyframes deletedGlow {
        0% {
            opacity: 0.6;
            box-shadow:
                0 0 8px rgba(220, 38, 38, 0.6),
                0 0 16px rgba(239, 68, 68, 0.4);
        }

        100% {
            opacity: 1;
            box-shadow:
                0 0 12px rgba(220, 38, 38, 0.8),
                0 0 24px rgba(239, 68, 68, 0.6);
        }
    }

    @keyframes deletedPulse {

        0%,
        100% {
            transform: scale(0.995);
            opacity: 0.75;
        }

        50% {
            transform: scale(1);
            opacity: 0.85;
        }
    }

    /* Enhanced deleted row for better visibility */
    .glassmorphism-body tr.deleted-row .btn,
    .glassmorphism-body tr.deleted-row button,
    .glassmorphism-body tr.deleted-row .action-button {
        opacity: 0.8;
        filter: grayscale(20%);
        transition: all 0.3s ease;
    }

    .glassmorphism-body tr.deleted-row .btn:hover,
    .glassmorphism-body tr.deleted-row button:hover,
    .glassmorphism-body tr.deleted-row .action-button:hover {
        opacity: 1;
        filter: grayscale(0%);
        transform: scale(1.05);
    }

    /* Special styling for restore button in deleted rows */
    .glassmorphism-body tr.deleted-row .restore-btn {
        background: rgba(34, 197, 94, 0.3) !important;
        border-color: rgba(34, 197, 94, 0.5) !important;
        box-shadow:
            0 2px 12px rgba(34, 197, 94, 0.2),
            0 4px 20px rgba(34, 197, 94, 0.1) !important;
        opacity: 1 !important;
        filter: none !important;
        animation: restoreButtonGlow 2s ease-in-out infinite alternate;
    }

    .glassmorphism-body tr.deleted-row .restore-btn:hover {
        background: rgba(34, 197, 94, 0.5) !important;
        transform: scale(1.1) !important;
        box-shadow:
            0 4px 16px rgba(34, 197, 94, 0.3),
            0 8px 24px rgba(34, 197, 94, 0.2) !important;
        animation: none;
    }

    @keyframes restoreButtonGlow {
        0% {
            box-shadow:
                0 2px 12px rgba(34, 197, 94, 0.2),
                0 4px 20px rgba(34, 197, 94, 0.1);
        }

        100% {
            box-shadow:
                0 4px 16px rgba(34, 197, 94, 0.4),
                0 8px 28px rgba(34, 197, 94, 0.2);
        }
    }

    /* Additional UX improvements for deleted rows */
    .glassmorphism-body tr.deleted-row {
        animation: deletedPulse 3s ease-in-out infinite;
    }

    .glassmorphism-body tr.deleted-row:hover {
        animation: none;
    }

    .glassmorphism-body td {
        padding: 1rem 1.5rem;
        text-align: center;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.875rem;
        border-right: none;
        transition: all 0.3s ease;
    }

    .glassmorphism-body td:last-child {
        border-right: none;
    }

    .loading-row {
        animation: pulse 2s infinite;
    }

    .loading-cell {
        padding: 3rem 1.5rem !important;
    }

    .loading-content {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .loading-spinner {
        position: relative;
        width: 60px;
        height: 60px;
    }

    .spinner-ring {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 3px solid transparent;
        border-top: 3px solid #A855F7;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        filter: drop-shadow(0 0 8px rgba(168, 85, 247, 0.6));
    }

    .spinner-ring:nth-child(2) {
        animation-delay: -0.15s;
        border-top-color: #8B5CF6;
        filter: drop-shadow(0 0 8px rgba(139, 92, 246, 0.6));
    }

    .spinner-ring:nth-child(3) {
        animation-delay: -0.3s;
        border-top-color: #7C3AED;
        filter: drop-shadow(0 0 8px rgba(124, 58, 237, 0.6));
    }

    .loading-text {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        text-shadow: 0 0 10px rgba(168, 85, 247, 0.3);
    }

    .responsive-container {
        overflow-x: auto;
        scrollbar-width: thin;
        scrollbar-color: rgba(138, 43, 226, 0.5) transparent;
    }

    .responsive-container::-webkit-scrollbar {
        height: 8px;
    }

    .responsive-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 4px;
    }

    .responsive-container::-webkit-scrollbar-thumb {
        background: linear-gradient(90deg, rgba(138, 43, 226, 0.6), rgba(128, 0, 255, 0.8));
        border-radius: 4px;
        transition: background 0.3s ease;
    }

    .responsive-container::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(90deg, rgba(138, 43, 226, 0.8), rgba(128, 0, 255, 1));
    }

    /* Enhanced Glassmorphism Pagination */
    .pagination-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin: 2rem 0;
        gap: 1rem;
    }

    /* Record information styling */
    .record-info,
    .record-info-single {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.875rem;
        font-weight: 500;
        text-align: center;
        background: rgba(0, 0, 0, 0.6);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
        box-shadow:
            0 2px 12px rgba(138, 43, 226, 0.15),
            0 4px 20px rgba(128, 0, 255, 0.1);
    }

    .record-info-single {
        margin: 1rem 0;
    }

    .single-page {
        margin: 1rem 0;
    }

    .pagination {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        background: rgba(0, 0, 0, 0.8);
        border-radius: 12px;
        padding: 0.5rem;
        box-shadow:
            0 4px 24px rgba(138, 43, 226, 0.2),
            0 8px 40px rgba(128, 0, 255, 0.1);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .pagination .page-item {
        margin: 0;
    }

    .pagination .page-link {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        padding: 0;
        border: none;
        background: transparent;
        color: rgba(255, 255, 255, 0.7);
        text-decoration: none;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        cursor: pointer;
    }

    .pagination .page-link::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg,
                rgba(255, 255, 255, 0.1) 0%,
                rgba(138, 43, 226, 0.1) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pagination .page-link:hover:not(:disabled) {
        color: rgba(255, 255, 255, 0.95);
        transform: translateY(-2px);
        box-shadow:
            0 4px 16px rgba(138, 43, 226, 0.3),
            0 8px 24px rgba(128, 0, 255, 0.2);
    }

    .pagination .page-link:hover:not(:disabled)::before {
        opacity: 1;
    }

    /* Estilo para página activa */
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #A855F7 0%, #8B5CF6 100%);
        color: white !important;
        box-shadow:
            0 4px 20px rgba(168, 85, 247, 0.4),
            0 8px 32px rgba(139, 92, 246, 0.3);
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link::before {
        opacity: 0;
    }

    .pagination .page-item.active .page-link:hover {
        transform: translateY(-1px);
        box-shadow:
            0 6px 24px rgba(168, 85, 247, 0.5),
            0 12px 40px rgba(139, 92, 246, 0.4);
    }

    /* Estilo para páginas deshabilitadas */
    .pagination .page-item.disabled .page-link {
        color: rgba(255, 255, 255, 0.3) !important;
        cursor: not-allowed;
        transform: none;
        background: transparent !important;
    }

    .pagination .page-item.disabled .page-link:hover {
        transform: none;
        box-shadow: none;
    }

    .pagination .page-item.disabled .page-link::before {
        opacity: 0;
    }

    .pagination .page-link span {
        position: relative;
        z-index: 1;
    }

    /* Enhanced Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes shimmer-header {
        0% {
            transform: translateX(-100%);
        }

        100% {
            transform: translateX(100%);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }

        100% {
            transform: rotate(360deg);
        }
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .glassmorphism-container {
            padding: 1rem;
            margin: 0.5rem 0;
        }

        .glassmorphism-th,
        .glassmorphism-body td {
            padding: 0.75rem 1rem;
            font-size: 0.8rem;
        }

        .loading-cell {
            padding: 2rem 1rem !important;
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
        }

        .pagination .page-link {
            width: 36px;
            height: 36px;
            font-size: 0.8rem;
        }

        .pagination-wrapper {
            margin: 1rem 0;
            gap: 0.75rem;
        }

        .record-info,
        .record-info-single {
            font-size: 0.75rem;
            padding: 0.375rem 0.75rem;
        }
    }

    /* Dark mode enhancements */
    @media (prefers-color-scheme: dark) {
        .glassmorphism-container {
            background: rgba(0, 0, 0, 0.85);
            border: 1px solid rgba(255, 255, 255, 0.15);
        }

        .glassmorphism-table-wrapper {
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }

        .glassmorphism-header {
            background: rgba(0, 0, 0, 0.9);
        }

        .pagination {
            background: rgba(0, 0, 0, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.12);
        }
    }
</style>
