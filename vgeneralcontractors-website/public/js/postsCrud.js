/**
 * PostsCrudManager - CRUD Manager para posts con búsqueda en tiempo real
 * Basado en CrudManagerModal pero adaptado para posts
 */
class PostsCrudManager {
    constructor(options) {
        console.log(
            "🚀 PostsCrudManager constructor called with options:",
            options
        );

        // Configuración básica
        this.entityName = "Post";
        this.entityNamePlural = "Posts";
        this.routes = options.routes || {};

        // Selectores del DOM
        this.tableSelector = "#postsTable";
        this.searchSelector = "#searchInput";
        this.perPageSelector = "#perPage";
        this.showDeletedSelector = "#showDeletedToggle";
        this.paginationSelector = "#pagination";
        this.alertSelector = "#alertMessage";

        // Estado
        this.currentPage = 1;
        this.perPage = 10;
        this.searchTerm = "";
        this.showDeleted = false;
        this.sortField = "created_at";
        this.sortDirection = "desc";

        // Debounce timer para búsqueda
        this.searchTimeout = null;

        console.log("✅ PostsCrudManager initialized with state:", {
            routes: this.routes,
            searchSelector: this.searchSelector,
            tableSelector: this.tableSelector,
        });

        // Inicializar
        this.init();
    }

    init() {
        console.log("🔧 PostsCrudManager.init() starting...");
        this.setupEventHandlers();
        this.initializeExistingPagination();

        // NO cargar posts automáticamente en la inicialización
        // Solo configurar los event handlers para que funcionen cuando el usuario interactúe
        console.log(
            "✅ PostsCrudManager.init() completed - Event handlers ready"
        );
    }

    setupEventHandlers() {
        const self = this;

        console.log("🎯 Setting up event handlers...");
        console.log(
            "🔍 Search input element:",
            $(this.searchSelector).length ? "Found" : "NOT FOUND"
        );
        console.log(
            "📄 Per page element:",
            $(this.perPageSelector).length ? "Found" : "NOT FOUND"
        );
        console.log(
            "🔄 Show deleted element:",
            $(this.showDeletedSelector).length ? "Found" : "NOT FOUND"
        );

        // Búsqueda en tiempo real con debounce mejorado
        $(this.searchSelector).on(
            "input",
            debounce(() => {
                self.searchTerm = $(self.searchSelector).val();
                self.currentPage = 1;
                console.log("🔍 Starting search with term:", self.searchTerm);
                self.loadPosts();
                self.updateURL();
            }, 300)
        );

        // Per page
        $(this.perPageSelector).on("change", () => {
            self.perPage = $(self.perPageSelector).val();
            self.currentPage = 1;
            self.loadPosts();
        });

        // Show deleted
        $(this.showDeletedSelector).on("change", () => {
            self.showDeleted = $(self.showDeletedSelector).is(":checked");
            self.currentPage = 1;
            self.loadPosts();
            self.updateURL();

            // Update toggle appearance
            const toggle = $(self.showDeletedSelector)[0];
            const background = toggle.nextElementSibling;
            const dot = background.nextElementSibling;

            if (toggle.checked) {
                background.classList.remove("bg-gray-600");
                background.classList.add("bg-yellow-500");
                dot.classList.add("transform", "translate-x-6");
            } else {
                background.classList.remove("bg-yellow-500");
                background.classList.add("bg-gray-600");
                dot.classList.remove("transform", "translate-x-6");
            }
        });

        // Dynamic buttons - usando SweetAlert2
        $(document).on("click", ".delete-btn", function () {
            const uuid = $(this).data("uuid");
            const title = $(this).data("title");
            self.deletePost(uuid, title);
        });

        $(document).on("click", ".restore-btn", function () {
            const uuid = $(this).data("uuid");
            const title = $(this).data("title");
            self.restorePost(uuid, title);
        });
    }

    loadPosts(page = 1) {
        this.currentPage = page;
        const self = this;

        // Mostrar loading usando la misma lógica que CrudManagerModal
        this.showTableLoading();

        const requestData = {
            page: this.currentPage,
            per_page: this.perPage,
            sort_field: this.sortField,
            sort_direction: this.sortDirection,
            search: this.searchTerm,
            show_deleted: this.showDeleted ? "true" : "false",
        };

        console.log(
            "🔍 PostsCrudManager.loadPosts() - Request data:",
            requestData
        );

        return $.ajax({
            url: this.routes.index,
            type: "GET",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                Accept: "application/json",
            },
            data: requestData,
            success: (response) => {
                console.log("✅ AJAX Success - Response received:", response);
                self.renderTable(response);
                self.renderPagination(response);
            },
            error: (xhr) => {
                console.error("❌ Error loading posts:", xhr.responseText);
                self.showAlert("error", "Error loading posts");
                self.hideTableLoading();
            },
        });
    }

    renderTable(data) {
        // Extraer posts de la respuesta de paginación de Laravel
        const posts = data.data || [];

        console.log("🎨 renderTable() called with:", {
            dataType: typeof data,
            postsType: typeof posts,
            isArray: Array.isArray(posts),
            postsLength: posts.length,
            paginationInfo: {
                current_page: data.current_page,
                total: data.total,
                per_page: data.per_page,
            },
        });

        let html = "";

        // Validar que posts sea un array válido
        if (!Array.isArray(posts)) {
            console.error("Posts data is not an array:", posts);
            html = `
                <tr>
                    <td colspan="7" class="text-center py-8 text-red-400">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="text-sm">Error: Datos de posts inválidos</span>
                            <button onclick="window.postsCrudManager.loadPosts()" class="mt-2 px-3 py-1 bg-red-600 hover:bg-red-700 text-white text-xs rounded transition-colors">
                                Reintentar
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            $(this.tableSelector).html(html);
            return;
        }

        if (posts.length === 0) {
            // Mensaje más descriptivo dependiendo si hay búsqueda activa o no
            const emptyMessage = this.searchTerm
                ? `No se encontraron posts que coincidan con "${this.searchTerm}"`
                : this.showDeleted
                ? "No hay posts eliminados"
                : "No hay posts disponibles";

            const emptyIcon = this.searchTerm
                ? `<svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                   </svg>`
                : `<svg class="w-12 h-12 text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                     <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                   </svg>`;

            html = `
                <tr>
                    <td colspan="7" class="text-center py-12 text-gray-400">
                        <div class="flex flex-col items-center justify-center space-y-3">
                            ${emptyIcon}
                            <span class="text-lg font-medium">${emptyMessage}</span>
                            ${
                                this.searchTerm
                                    ? `<button onclick="$('#searchInput').val('').trigger('input')" class="mt-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white text-sm rounded transition-colors">
                                    Limpiar búsqueda
                                </button>`
                                    : !this.showDeleted
                                    ? `<a href="/posts-crud/create" class="mt-2 px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-gray-900 text-sm rounded transition-colors">
                                        Crear primer post
                                    </a>`
                                    : ""
                            }
                        </div>
                    </td>
                </tr>
            `;
        } else {
            posts.forEach((post) => {
                // Validar que el post tenga la estructura mínima esperada
                if (!post || typeof post !== "object") {
                    console.warn("Invalid post object:", post);
                    return; // Saltar este post
                }

                const isDeleted = post.deleted_at !== null;
                const rowClass = isDeleted ? "opacity-60" : "";

                // Imagen
                let imgHtml = `<div class="h-12 w-12 flex items-center justify-center bg-gray-700 rounded-md">
                    <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                    </svg>
                </div>`;

                if (post.post_image) {
                    const imageUrl = post.post_image.startsWith("http://")
                        ? post.post_image.replace("http://", "https://")
                        : post.post_image;
                    imgHtml = `<img class="h-12 w-12 object-cover rounded-md" src="${imageUrl}" alt="Post image" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="h-12 w-12 flex items-center justify-center bg-gray-700 rounded-md" style="display:none;">
                            <svg class="w-6 h-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14"></path>
                            </svg>
                        </div>`;
                }

                // Status badge
                let statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-500/20 text-gray-400">${
                    post.post_status || "N/A"
                }</span>`;
                if (post.post_status === "published") {
                    statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-500/20 text-green-400">Published</span>`;
                } else if (post.post_status === "scheduled") {
                    statusBadge = `<span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-500/20 text-orange-400">Scheduled</span>`;
                }

                // Actions - Orden: Show → Edit → Delete/Restore
                let actionsHtml = '<div class="flex justify-center space-x-2">';

                if (!isDeleted) {
                    // Show button (solo para posts publicados) - PRIMERO
                    if (
                        post.post_status === "published" &&
                        post.post_title_slug
                    ) {
                        actionsHtml += `<a href="/blog/${post.post_title_slug}" target="_blank"
                            class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            title="View post">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                        </a>`;
                    }

                    // Edit button - SEGUNDO
                    if (post.uuid) {
                        actionsHtml += `<a href="/posts-crud/${post.uuid}/edit"
                            class="inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg hover:from-yellow-600 hover:to-yellow-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            title="Edit post">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </a>`;
                    }

                    // Delete button - TERCERO
                    if (post.uuid && post.post_title) {
                        actionsHtml += `<button data-uuid="${
                            post.uuid
                        }" data-title="${(post.post_title || "").replace(
                            /"/g,
                            "&quot;"
                        )}"
                            class="delete-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-lg hover:from-red-600 hover:to-red-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            title="Delete post">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </button>`;
                    }
                } else {
                    // Restore button
                    if (post.uuid && post.post_title) {
                        actionsHtml += `<button data-uuid="${
                            post.uuid
                        }" data-title="${(post.post_title || "").replace(
                            /"/g,
                            "&quot;"
                        )}"
                            class="restore-btn inline-flex items-center justify-center w-9 h-9 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-105"
                            title="Restore post">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </button>`;
                    }
                }

                actionsHtml += "</div>";

                // Truncate content with safe handling
                const postTitle = post.post_title || "Sin título";
                const truncatedTitle =
                    postTitle.length > 50
                        ? postTitle.substring(0, 50) + "..."
                        : postTitle;

                const postContent = post.post_content || "";
                const truncatedContent = postContent
                    ? postContent.replace(/<[^>]*>/g, "").length > 80
                        ? postContent.replace(/<[^>]*>/g, "").substring(0, 80) +
                          "..."
                        : postContent.replace(/<[^>]*>/g, "")
                    : "";

                html += `
                    <tr class="hover:bg-gray-750 ${rowClass}">
                        <td class="px-4 py-3 text-sm text-gray-400">${imgHtml}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-200">${truncatedTitle}</div>
                            <div class="text-sm text-gray-400">${truncatedContent}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-500/20 text-blue-400">
                                ${post.category?.blog_category_name || "N/A"}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">${statusBadge}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            ${
                                post.created_at
                                    ? new Date(
                                          post.created_at
                                      ).toLocaleDateString()
                                    : "N/A"
                            }
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                            ${post.user?.name || "N/A"}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                            ${actionsHtml}
                        </td>
                    </tr>
                `;
            });
        }

        $(this.tableSelector).html(html);
    }

    renderPagination(data) {
        let paginationHtml = "";

        if (data.last_page > 1) {
            paginationHtml += '<div class="flex items-center justify-between">';
            paginationHtml += `<div class="text-sm text-gray-300">Mostrando ${
                data.from || 0
            } a ${data.to || 0} de ${data.total || 0} resultados</div>`;
            paginationHtml += '<div class="flex space-x-1">';

            // Botón anterior
            if (data.current_page > 1) {
                paginationHtml += `<button class="pagination-btn px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700" data-page="${
                    data.current_page - 1
                }">Anterior</button>`;
            }

            // Números de página
            for (
                let i = Math.max(1, data.current_page - 2);
                i <= Math.min(data.last_page, data.current_page + 2);
                i++
            ) {
                const activeClass =
                    i === data.current_page
                        ? "bg-blue-500 text-white"
                        : "bg-white text-gray-700 hover:bg-gray-50";
                paginationHtml += `<button class="pagination-btn px-3 py-2 text-sm border border-gray-300 rounded-md ${activeClass}" data-page="${i}">${i}</button>`;
            }

            // Botón siguiente
            if (data.current_page < data.last_page) {
                paginationHtml += `<button class="pagination-btn px-3 py-2 text-sm bg-white border border-gray-300 rounded-md hover:bg-gray-50 text-gray-700" data-page="${
                    data.current_page + 1
                }">Siguiente</button>`;
            }

            paginationHtml += "</div></div>";
        } else if (data.total > 0) {
            paginationHtml += `<div class="text-sm text-gray-300">Mostrando ${
                data.from || 0
            } a ${data.to || 0} de ${data.total || 0} resultados</div>`;
        }

        $(this.paginationSelector).html(paginationHtml);

        // Event listener para paginación
        $(".pagination-btn")
            .off("click")
            .on("click", (e) => {
                const page = $(e.target).data("page");
                this.loadPosts(page);
            });
    }

    // Delete con SweetAlert2
    deletePost(uuid, title) {
        const self = this;

        Swal.fire({
            title: "¿Estás seguro?",
            html: `¿Deseas eliminar el post: <strong>"${title}"</strong>?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/posts-crud/${uuid}`,
                    method: "DELETE",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: "success",
                            title: "Eliminado!",
                            html: `El post <strong>"${title}"</strong> ha sido eliminado exitosamente.`,
                            confirmButtonColor: "#3B82F6",
                        });
                        self.loadPosts();
                    },
                    error: function (xhr) {
                        console.error("Error deleting post:", xhr);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "No se pudo eliminar el post.",
                            confirmButtonColor: "#3B82F6",
                        });
                    },
                });
            }
        });
    }

    // Restore con SweetAlert2
    restorePost(uuid, title) {
        const self = this;

        Swal.fire({
            title: "¿Restaurar post?",
            html: `¿Deseas restaurar el post: <strong>"${title}"</strong>?`,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Sí, restaurar",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/posts-crud/${uuid}/restore`,
                    method: "PATCH",
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                            "content"
                        ),
                        Accept: "application/json",
                    },
                    success: function (response) {
                        Swal.fire({
                            icon: "success",
                            title: "Restaurado!",
                            html: `El post <strong>"${title}"</strong> ha sido restaurado exitosamente.`,
                            confirmButtonColor: "#3B82F6",
                        });
                        self.loadPosts();
                    },
                    error: function (xhr) {
                        console.error("Error restoring post:", xhr);
                        Swal.fire({
                            icon: "error",
                            title: "Error!",
                            text: "No se pudo restaurar el post.",
                            confirmButtonColor: "#3B82F6",
                        });
                    },
                });
            }
        });
    }

    updateURL() {
        const url = new URL(window.location);

        if (this.searchTerm) {
            url.searchParams.set("search", this.searchTerm);
        } else {
            url.searchParams.delete("search");
        }

        if (this.showDeleted) {
            url.searchParams.set("show_deleted", "true");
        } else {
            url.searchParams.delete("show_deleted");
        }

        // Update URL without reloading
        window.history.replaceState({}, "", url);
    }

    showAlert(message, type = "success") {
        const alertClass =
            type === "success"
                ? "bg-green-500/20 text-green-400 border-green-400"
                : "bg-red-500/20 text-red-400 border-red-400";

        const alertHtml = `
            <div class="mb-4 p-4 rounded-lg border ${alertClass} transition-all duration-300 ease-in-out">
                ${message}
            </div>
        `;

        $(this.alertSelector).html(alertHtml);

        // Auto-hide after 5 seconds with fade effect
        setTimeout(() => {
            $(this.alertSelector + " > div").fadeOut(500, function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Mostrar loading en tabla
     */
    showTableLoading() {
        const loadingHtml = `
            <tr id="loadingRow">
                <td colspan="7" class="px-6 py-4 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <svg class="animate-spin h-8 w-8 text-yellow-500" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-gray-400">Cargando posts...</span>
                    </div>
                </td>
            </tr>
        `;
        $(this.tableSelector).html(loadingHtml);
    }

    /**
     * Ocultar loading en tabla
     */
    hideTableLoading() {
        $("#loadingRow").remove();
    }

    /**
     * Mostrar alerta con estilos específicos de posts-crud
     */
    showAlert(type, message) {
        const alertClass =
            type === "success"
                ? "bg-green-500/20 border border-green-500/30 text-green-400"
                : "bg-red-500/20 border border-red-500/30 text-red-400";

        const alertHtml = `
            <div class="mb-6 ${alertClass} px-4 py-3 rounded-lg transition-all duration-300 ease-in-out">
                ${message}
            </div>
        `;

        $(this.alertSelector).html(alertHtml);

        // Auto-hide después de 5 segundos con fade effect
        setTimeout(() => {
            $(this.alertSelector + " > div").fadeOut(500, function () {
                $(this).remove();
            });
        }, 5000);
    }

    /**
     * Inicializar paginación existente renderizada por el servidor
     */
    initializeExistingPagination() {
        console.log("📄 Initializing existing pagination...");

        // Interceptar clics en los enlaces de paginación de Laravel para usar AJAX
        $(document).on("click", ".pagination a", function (e) {
            e.preventDefault();
            const url = $(this).attr("href");
            if (url && window.postsCrudManager) {
                console.log("📄 Pagination link clicked:", url);
                const urlObj = new URL(url);
                const page = urlObj.searchParams.get("page") || 1;
                window.postsCrudManager.currentPage = parseInt(page);
                window.postsCrudManager.loadPosts();
            }
        });

        console.log("✅ Existing pagination initialized");
    }
}

/**
 * Función debounce para optimizar búsquedas
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Hacer disponible globalmente la clase
window.PostsCrudManager = PostsCrudManager;
