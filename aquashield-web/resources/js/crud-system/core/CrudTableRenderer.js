// Renderizado de tablas y paginación
export class CrudTableRenderer {
    constructor(tableHeaders, translations = {}) {
        this.tableHeaders = tableHeaders;
        this.tableSelector = "#crud-table-body"; // Puedes parametrizar esto
        this.paginationSelector = "#pagination"; // Puedes parametrizar esto

        // Default translations
        this.translations = {
            showing: "Showing",
            to: "to",
            of: "of",
            results: "results",
            total_records: "Total Records",
            previous: "Previous",
            next: "Next",
            ...translations,
        };
    }

    renderTable(data) {
        const tbody = document.querySelector(this.tableSelector);
        if (!tbody) return;
        tbody.innerHTML = "";
        if (!data || !data.data || data.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="${this.tableHeaders.length}" class="text-center">No records found</td></tr>`;
            return;
        }
        data.data.forEach((entity) => {
            const row = document.createElement("tr");
            this.tableHeaders.forEach((header) => {
                const td = document.createElement("td");
                td.innerHTML =
                    entity[header.field] !== undefined
                        ? entity[header.field]
                        : "";
                row.appendChild(td);
            });
            tbody.appendChild(row);
        });
    }

    renderPagination(data) {
        const container = document.querySelector(this.paginationSelector);
        if (!container) return;

        // Always show record information
        const recordInfo = this.generateRecordInfo(data);

        if (!data || data.last_page <= 1) {
            // Single page case - show only record information
            container.innerHTML = `<div class="pagination-wrapper single-page">
                <div class="record-info-single">${recordInfo}</div>
            </div>`;
            return;
        }

        let html = `<div class="pagination-wrapper">`;
        html += `<div class="flex space-x-1">`;

        if (data.current_page > 1) {
            html += `<button class="pagination-btn" data-page="${
                data.current_page - 1
            }">${this.translations.previous || "Previous"}</button>`;
        }

        for (let i = 1; i <= data.last_page; i++) {
            html += `<button class="pagination-btn${
                i === data.current_page ? " font-bold" : ""
            }" data-page="${i}">${i}</button>`;
        }

        if (data.current_page < data.last_page) {
            html += `<button class="pagination-btn" data-page="${
                data.current_page + 1
            }">${this.translations.next || "Next"}</button>`;
        }

        html += `</div>`;
        html += `<div class="record-info">${recordInfo}</div>`;
        html += `</div>`;
        container.innerHTML = html;
    }

    /**
     * Generate record information text
     */
    generateRecordInfo(data) {
        const from = data.from || 0;
        const to = data.to || 0;
        const total = data.total || 0;

        if (total === 0) {
            return `${this.translations.showing || "Showing"} 0 ${
                this.translations.results || "results"
            }`;
        }

        const showingText = this.translations.showing || "Showing";
        const toText = this.translations.to || "to";
        const ofText = this.translations.of || "of";
        const recordsText = this.translations.total_records || "Total Records";

        return `${showingText} ${from}-${to} ${ofText} ${total} ${recordsText}`;
    }

    renderTableHeaders() {
        // Si necesitas renderizar los headers dinámicamente
    }
}
