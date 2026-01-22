/**
 * Portfolio CRUD Manager - Extiende CrudManagerModal para manejo de imágenes múltiples
 * Basado en la lógica de Portfolios.php (Livewire) pero adaptado para AJAX
 */
class PortfolioCrudManager extends CrudManagerModal {
    constructor(options) {
        console.log(
            "PortfolioCrudManager constructor called with options:",
            options
        );
        super(options);

        // Configuración específica para portfolios
        this.maxFiles = options.maxFiles || 10;
        this.maxSizeKb = options.maxSizeKb || 5120; // 5MB
        this.maxTotalSizeKb = options.maxTotalSizeKb || 20480; // 20MB

        // Estado de imágenes
        this.existingImages = [];
        this.pendingNewImages = [];
        this.imagesToDelete = [];
        this.imageOrder = [];

        // Configuración de sortable
        this.sortableConfig = {
            animation: 150,
            ghostClass: "sortable-ghost",
            chosenClass: "sortable-chosen",
            dragClass: "sortable-drag",
        };

        // Inicializar eventos específicos de imágenes
        this.initImageEvents();

        console.log("PortfolioCrudManager initialization complete");
    }

    /**
     * Inicializar eventos específicos de imágenes
     */
    initImageEvents() {
        // Event listeners para manejo de imágenes
        $(document).on("change", "#image_files", (e) => {
            this.handleNewImageFiles(e.target.files);
        });

        $(document).on("click", ".remove-pending-image", (e) => {
            const index = $(e.currentTarget).data("index");
            this.removePendingImage(index);
        });

        $(document).on("click", ".mark-for-deletion", (e) => {
            const imageId = $(e.currentTarget).data("image-id");
            this.markImageForDeletion(imageId);
        });

        $(document).on("click", ".unmark-for-deletion", (e) => {
            const imageId = $(e.currentTarget).data("image-id");
            this.unmarkImageForDeletion(imageId);
        });
    }

    /**
     * Generar HTML del formulario con manejo de imágenes
     */
    generateFormHtml(entity = null) {
        const isEditMode = entity !== null;

        let html = '<div class="portfolio-crud-form">';

        // Campos básicos del formulario
        html += this.generateBasicFields(entity);

        // Sección de imágenes
        html += this.generateImageSection(entity);

        html += "</div>";

        return html;
    }

    /**
     * Generar campos básicos del formulario
     */
    generateBasicFields(entity = null) {
        const isEditMode = entity !== null;

        let html = '<div class="basic-fields mb-6">';

        // Title
        const titleValue =
            isEditMode && entity.project_type
                ? entity.project_type.title || ""
                : "";
        html += `
            <div class="form-group mb-4">
                <label for="title" class="block text-sm font-medium text-gray-700 mb-2">${this.getTranslation(
                    "project_title",
                    "Título del Proyecto"
                )} *</label>
                <input type="text" id="title" name="title" value="${titleValue}" required 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent auto-capitalize" 
                       placeholder="${this.getTranslation(
                           "project_title",
                           "Ingrese el título del proyecto"
                       )}">
                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-title"></div>
            </div>
        `;

        // Description
        const descriptionValue =
            isEditMode && entity.project_type
                ? entity.project_type.description || ""
                : "";
        html += `
            <div class="form-group mb-4">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">${this.getTranslation(
                    "project_description",
                    "Descripción"
                )} *</label>
                <textarea id="description" name="description" required rows="3" 
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent auto-capitalize" 
                          placeholder="${this.getTranslation(
                              "project_description",
                              "Describe el proyecto"
                          )}">${descriptionValue}</textarea>
                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-description"></div>
            </div>
        `;

        // Service Category
        const categoryValue =
            isEditMode && entity.project_type
                ? entity.project_type.service_category_id || ""
                : "";
        html += `
            <div class="form-group mb-4">
                <label for="service_category_id" class="block text-sm font-medium text-gray-700 mb-2">${this.getTranslation(
                    "service_category",
                    "Categoría de Servicio"
                )} *</label>
                <select id="service_category_id" name="service_category_id" required 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    <option value="">${this.getTranslation(
                        "select_category",
                        "Seleccione una categoría"
                    )}</option>
                </select>
                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-service_category_id"></div>
            </div>
        `;

        html += "</div>";
        return html;
    }

    /**
     * Generar sección de imágenes
     */
    generateImageSection(entity = null) {
        const isEditMode = entity !== null;

        let html = '<div class="image-section">';
        html += `<h3 class="text-lg font-medium text-gray-900 mb-4">${this.getTranslation(
            "image_management",
            "Gestión de Imágenes"
        )}</h3>`;

        // Input para nuevas imágenes
        html += `
            <div class="form-group mb-4">
                <label for="image_files" class="block text-sm font-medium text-gray-700 mb-2">
                    ${
                        isEditMode
                            ? this.getTranslation(
                                  "add_new_images",
                                  "Agregar Nuevas Imágenes"
                              )
                            : this.getTranslation(
                                  "portfolio_images",
                                  "Imágenes del Portfolio"
                              ) + " *"
                    }
                </label>
                <input type="file" id="image_files" name="image_files" multiple accept="image/*" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <div class="text-sm text-gray-500 mt-1">
                    ${this.getTranslation("portfolio_maximum", "Máximo")} ${
            this.maxFiles
        } ${this.getTranslation(
            "portfolio_images_text",
            "imágenes"
        )}. ${this.getTranslation(
            "portfolio_max_size_per_image",
            "Tamaño máximo por imagen"
        )}: ${this.maxSizeKb / 1024}MB. 
                    ${this.getTranslation(
                        "portfolio_formats",
                        "Formatos"
                    )}: ${this.getTranslation(
            "portfolio_supported_formats",
            "JPEG, PNG, JPG, GIF, WEBP"
        )}.
                </div>
                <div class="error-message text-red-500 text-sm mt-1 hidden" id="error-image_files"></div>
            </div>
        `;

        // Contenedor para imágenes existentes (solo en modo edición)
        if (isEditMode) {
            html += `
                <div class="existing-images-section mb-6">
                    <h4 id="existing-images-title" class="text-md font-medium text-gray-800 mb-3">
                        ${this.getTranslation(
                            "current_images",
                            "Imágenes Actuales ({count} visibles)"
                        ).replace("{count}", "0")}
                    </h4>
                    <div id="existing-images-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <!-- Se llenarán dinámicamente -->
                    </div>
                </div>
            `;
        }

        // Contenedor para nuevas imágenes pendientes
        html += `
            <div class="pending-images-section mb-4">
                <h4 id="pending-images-title" class="text-md font-medium text-gray-800 mb-3">
                    ${this.getTranslation(
                        "new_images_pending",
                        "Nuevas Imágenes ({count} pendientes)"
                    ).replace("{count}", "0")}
                </h4>
                <div id="pending-images-container" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Se llenarán dinámicamente -->
                </div>
            </div>
        `;

        // Información de límites
        html += `
            <div class="image-limits-info bg-blue-50 p-3 rounded-md">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="text-sm text-blue-700">
                        <div>${this.getTranslation(
                            "portfolio_image_limits",
                            "Límites"
                        )}: ${this.getTranslation(
            "portfolio_maximum",
            "Máximo"
        )} <span id="current-image-count">0</span>/${
            this.maxFiles
        } ${this.getTranslation("portfolio_images_text", "imágenes")}</div>
                        <div>${this.getTranslation(
                            "portfolio_total_size",
                            "Tamaño total"
                        )}: <span id="current-total-size">0</span>MB / ${
            this.maxTotalSizeKb / 1024
        }MB</div>
                    </div>
                </div>
            </div>
        `;

        html += "</div>";
        return html;
    }

    /**
     * Mostrar modal de creación
     */
    async showCreateModal() {
        this.isEditing = false;
        this.currentEntity = null;
        this.resetImageState();

        const formHtml = this.generateFormHtml();

        const result = await Swal.fire({
            title: this.getTranslation("create_portfolio", "Crear Portfolio"),
            html: formHtml,
            width: "900px",
            showCloseButton: true,
            showCancelButton: true,
            confirmButtonText: this.getTranslation("create", "Crear Portfolio"),
            cancelButtonText: this.getTranslation("cancel", "Cancelar"),
            confirmButtonColor: "#10B981",
            customClass: {
                container: "swal-modal-container",
                popup: "swal-modal-popup swal-create",
                content: "swal-modal-content",
            },
            preConfirm: () => {
                return this.validateAndGetPortfolioData();
            },
            didOpen: () => {
                this.initializePortfolioForm();
                this.applyHeaderColor("create");
            },
        });

        if (result.isConfirmed && result.value) {
            await this.createPortfolio(result.value);
        }
    }

    /**
     * Mostrar modal de edición
     */
    async showEditModal(id) {
        this.isEditing = true;
        this.resetImageState();

        try {
            // Cargar datos del portfolio
            const response = await $.ajax({
                url: this.routes.edit.replace(":id", id),
                type: "GET",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
            });

            this.currentEntity =
                response.portfolio || response.data || response;
            this.existingImages = this.currentEntity.images || [];

            const formHtml = this.generateFormHtml(this.currentEntity);

            const result = await Swal.fire({
                title: this.getTranslation(
                    "edit_portfolio",
                    "Editar Portfolio"
                ),
                html: formHtml,
                width: "900px",
                showCloseButton: true,
                showCancelButton: true,
                confirmButtonText: this.getTranslation(
                    "update",
                    "Actualizar Portfolio"
                ),
                cancelButtonText: this.getTranslation("cancel", "Cancelar"),
                confirmButtonColor: "#3B82F6",
                customClass: {
                    container: "swal-modal-container",
                    popup: "swal-modal-popup swal-edit",
                    content: "swal-modal-content",
                },
                preConfirm: () => {
                    return this.validateAndGetPortfolioData();
                },
                didOpen: () => {
                    this.initializePortfolioForm();
                    this.populatePortfolioForm(this.currentEntity);
                    this.renderExistingImages();
                    this.applyHeaderColor("edit");
                },
            });

            if (result.isConfirmed && result.value) {
                await this.updatePortfolio(id, result.value);
            }
        } catch (error) {
            console.error("Error loading portfolio for edit:", error);
            this.showAlert(
                "error",
                this.getTranslation(
                    "error_loading_portfolio",
                    "Error al cargar los datos del portfolio"
                )
            );
        }
    }

    /**
     * Inicializar formulario de portfolio
     */
    initializePortfolioForm() {
        // Configurar capitalización automática
        this.setupAutoCapitalization();

        // Configurar validación en tiempo real
        this.setupPortfolioValidation();

        // Cargar categorías de servicio
        this.loadServiceCategories();

        // Actualizar contadores de límites
        this.updateImageLimits();
    }

    /**
     * Configurar validación específica para portfolios
     */
    setupPortfolioValidation() {
        // Validación de título
        const titleField = document.getElementById("title");
        if (titleField) {
            let titleTimeout;
            titleField.addEventListener("input", (e) => {
                clearTimeout(titleTimeout);
                titleTimeout = setTimeout(() => {
                    this.validateTitleField(e.target.value);
                }, 500);
            });
        }
    }

    /**
     * Validar campo de título en tiempo real
     */
    async validateTitleField(title) {
        if (!title) {
            this.clearFieldError("title");
            return;
        }

        if (title.length < 3) {
            this.showFieldError(
                "title",
                this.getTranslation(
                    "title_min_length",
                    "El título debe tener al menos 3 caracteres"
                )
            );
            return;
        }

        try {
            const response = await $.ajax({
                url: this.routes.checkTitle,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                data: {
                    title: title,
                    exclude_uuid:
                        this.isEditing && this.currentEntity
                            ? this.currentEntity.uuid
                            : null,
                },
            });

            if (response.exists) {
                this.showFieldError(
                    "title",
                    this.getTranslation(
                        "title_already_exists",
                        "Ya existe un portfolio con este título"
                    )
                );
            } else {
                this.clearFieldError("title");
                this.showFieldSuccess(
                    "title",
                    this.getTranslation("title_available", "Título disponible")
                );
            }
        } catch (error) {
            console.error("Error validating title:", error);
        }
    }

    /**
     * Cargar categorías de servicio
     */
    async loadServiceCategories() {
        try {
            const response = await $.ajax({
                url: "/api/service-categories",
                type: "GET",
                headers: {
                    Accept: "application/json",
                },
            });

            if (response.success && response.data) {
                const select = $("#service_category_id");
                select.find("option:not(:first)").remove(); // Mantener la opción por defecto

                response.data.forEach((category) => {
                    select.append(
                        `<option value="${category.id}">${category.service_category_name}</option>`
                    );
                });
            }
        } catch (error) {
            console.error("Error loading service categories:", error);
            this.showAlert(
                "error",
                this.getTranslation(
                    "error_loading_service_categories",
                    "Error al cargar categorías de servicio"
                )
            );
        }
    }

    /**
     * Poblar formulario con datos del portfolio
     */
    populatePortfolioForm(entity) {
        if (entity.project_type) {
            $("#title").val(entity.project_type.title || "");
            $("#description").val(entity.project_type.description || "");
            $("#service_category_id").val(
                entity.project_type.service_category_id || ""
            );
        }
    }

    /**
     * Manejar archivos de imagen nuevos
     */
    handleNewImageFiles(files) {
        if (!files || files.length === 0) return;

        // Validar cada archivo
        Array.from(files).forEach((file) => {
            if (this.validateImageFile(file)) {
                this.pendingNewImages.push(file);
            }
        });

        // Renderizar imágenes pendientes
        this.renderPendingImages();
        this.updateImageLimits();
        this.validateImageLimits();

        // Limpiar el input
        $("#image_files").val("");
    }

    /**
     * Validar archivo de imagen individual
     */
    validateImageFile(file) {
        // Validar tipo
        const allowedTypes = [
            "image/jpeg",
            "image/png",
            "image/jpg",
            "image/gif",
            "image/webp",
        ];
        if (!allowedTypes.includes(file.type)) {
            this.showAlert(
                "error",
                this.getTranslation(
                    "invalid_file_type",
                    `Archivo ${file.name}: Tipo no permitido. Solo se permiten: JPEG, PNG, JPG, GIF, WEBP`
                ).replace("{name}", file.name)
            );
            return false;
        }

        // Validar tamaño
        const maxSizeBytes = this.maxSizeKb * 1024;
        if (file.size > maxSizeBytes) {
            this.showAlert(
                "error",
                this.getTranslation(
                    "file_too_large",
                    `Archivo ${file.name}: Tamaño excede ${
                        this.maxSizeKb / 1024
                    }MB`
                )
                    .replace("{name}", file.name)
                    .replace("{max}", this.maxSizeKb / 1024)
            );
            return false;
        }

        return true;
    }

    /**
     * Renderizar imágenes existentes (modo edición)
     */
    renderExistingImages() {
        const container = $("#existing-images-container");
        if (!container.length || !this.existingImages.length) return;

        let html = "";
        this.existingImages.forEach((image, index) => {
            const isMarkedForDeletion = this.imagesToDelete.includes(image.id);
            const overlayClass = isMarkedForDeletion
                ? "opacity-50 bg-red-100"
                : "";

            html += `
                <div class="existing-image-item relative ${overlayClass}" data-image-id="${
                image.id
            }">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                        <img src="${
                            image.path
                        }" alt="Portfolio Image" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute top-2 right-2 flex space-x-1">
                        ${
                            isMarkedForDeletion
                                ? `
                            <button type="button" class="unmark-for-deletion bg-green-600 text-white rounded-full p-1 hover:bg-green-700" 
                                    data-image-id="${image.id}" title="Restaurar imagen">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                            </button>
                        `
                                : `
                            <button type="button" class="mark-for-deletion bg-red-600 text-white rounded-full p-1 hover:bg-red-700" 
                                    data-image-id="${image.id}" title="Marcar para eliminar">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        `
                        }
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                        ${index + 1}
                    </div>
                </div>
            `;
        });

        container.html(html);

        // Inicializar sortable para reordering
        this.initializeExistingImagesSortable();
    }

    /**
     * Renderizar imágenes pendientes
     */
    renderPendingImages() {
        const container = $("#pending-images-container");
        if (!container.length) return;

        let html = "";
        this.pendingNewImages.forEach((file, index) => {
            const objectURL = URL.createObjectURL(file);

            html += `
                <div class="pending-image-item relative" data-index="${index}">
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-lg overflow-hidden">
                        <img src="${objectURL}" alt="Nueva imagen" class="w-full h-full object-cover">
                    </div>
                    <div class="absolute top-2 right-2">
                        <button type="button" class="remove-pending-image bg-red-600 text-white rounded-full p-1 hover:bg-red-700" 
                                data-index="${index}" title="Eliminar imagen">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="absolute bottom-2 left-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                        ${file.name.substring(0, 15)}${
                file.name.length > 15 ? "..." : ""
            }
                    </div>
                </div>
            `;
        });

        container.html(html);

        // Inicializar sortable para reordering
        this.initializePendingImagesSortable();
    }

    /**
     * Inicializar sortable para imágenes existentes
     */
    initializeExistingImagesSortable() {
        const container = document.getElementById("existing-images-container");
        if (!container || typeof Sortable === "undefined") return;

        new Sortable(container, {
            ...this.sortableConfig,
            onEnd: (evt) => {
                this.handleExistingImagesReorder(evt);
            },
        });
    }

    /**
     * Inicializar sortable para imágenes pendientes
     */
    initializePendingImagesSortable() {
        const container = document.getElementById("pending-images-container");
        if (!container || typeof Sortable === "undefined") return;

        new Sortable(container, {
            ...this.sortableConfig,
            onEnd: (evt) => {
                this.handlePendingImagesReorder(evt);
            },
        });
    }

    /**
     * Manejar reordenamiento de imágenes existentes
     */
    handleExistingImagesReorder(evt) {
        const oldIndex = evt.oldIndex;
        const newIndex = evt.newIndex;

        if (oldIndex !== newIndex) {
            // Reordenar array de imágenes existentes
            const movedImage = this.existingImages.splice(oldIndex, 1)[0];
            this.existingImages.splice(newIndex, 0, movedImage);

            console.log(
                "Existing images reordered:",
                this.existingImages.map((img) => img.id)
            );
        }
    }

    /**
     * Manejar reordenamiento de imágenes pendientes
     */
    handlePendingImagesReorder(evt) {
        const oldIndex = evt.oldIndex;
        const newIndex = evt.newIndex;

        if (oldIndex !== newIndex) {
            // Reordenar array de imágenes pendientes
            const movedFile = this.pendingNewImages.splice(oldIndex, 1)[0];
            this.pendingNewImages.splice(newIndex, 0, movedFile);

            console.log(
                "Pending images reordered:",
                this.pendingNewImages.map((file) => file.name)
            );
        }
    }

    /**
     * Remover imagen pendiente
     */
    removePendingImage(index) {
        if (index >= 0 && index < this.pendingNewImages.length) {
            // Liberar URL del objeto
            const file = this.pendingNewImages[index];
            if (file) {
                const container = $("#pending-images-container");
                const imgElement = container.find(
                    `[data-index="${index}"] img`
                );
                if (imgElement.length) {
                    URL.revokeObjectURL(imgElement.attr("src"));
                }
            }

            this.pendingNewImages.splice(index, 1);
            this.renderPendingImages();
            this.updateImageLimits();
            this.validateImageLimits();
        }
    }

    /**
     * Marcar imagen para eliminación
     */
    markImageForDeletion(imageId) {
        if (!this.imagesToDelete.includes(imageId)) {
            this.imagesToDelete.push(imageId);
            this.renderExistingImages();
            this.updateImageLimits();
            this.validateImageLimits();
        }
    }

    /**
     * Desmarcar imagen para eliminación
     */
    unmarkImageForDeletion(imageId) {
        const index = this.imagesToDelete.indexOf(imageId);
        if (index > -1) {
            this.imagesToDelete.splice(index, 1);
            this.renderExistingImages();
            this.updateImageLimits();
            this.validateImageLimits();
        }
    }

    /**
     * Actualizar contadores de límites
     */
    updateImageLimits() {
        const visibleExistingCount =
            this.existingImages.length - this.imagesToDelete.length;
        const pendingCount = this.pendingNewImages.length;
        const totalCount = visibleExistingCount + pendingCount;

        // Calcular tamaño total de nuevas imágenes
        const totalSizeMB = this.pendingNewImages.reduce((total, file) => {
            return total + file.size / 1024 / 1024;
        }, 0);

        $("#current-image-count").text(totalCount);
        $("#current-total-size").text(totalSizeMB.toFixed(2));

        // Actualizar títulos con contadores dinámicos
        this.updateImageTitles();

        // Actualizar colores según límites
        const countElement = $("#current-image-count");
        const sizeElement = $("#current-total-size");

        if (totalCount > this.maxFiles) {
            countElement.addClass("text-red-600 font-bold");
        } else {
            countElement.removeClass("text-red-600 font-bold");
        }

        if (totalSizeMB > this.maxTotalSizeKb / 1024) {
            sizeElement.addClass("text-red-600 font-bold");
        } else {
            sizeElement.removeClass("text-red-600 font-bold");
        }
    }

    /**
     * Actualizar títulos con contadores dinámicos
     */
    updateImageTitles() {
        // Actualizar título de imágenes existentes
        const existingTitle = document.getElementById("existing-images-title");
        if (existingTitle) {
            const visibleCount =
                this.existingImages.length - this.imagesToDelete.length;
            const titleText = this.getTranslation(
                "current_images",
                "Imágenes Actuales ({count} visibles)"
            ).replace("{count}", visibleCount);
            existingTitle.textContent = titleText;
        }

        // Actualizar título de imágenes pendientes
        const pendingTitle = document.getElementById("pending-images-title");
        if (pendingTitle) {
            const pendingCount = this.pendingNewImages.length;
            const titleText = this.getTranslation(
                "portfolio_new_images_pending",
                "Nuevas Imágenes ({count} pendientes)"
            ).replace("{count}", pendingCount);
            pendingTitle.textContent = titleText;
        }
    }

    /**
     * Validar límites de imágenes
     */
    validateImageLimits() {
        const visibleExistingCount =
            this.existingImages.length - this.imagesToDelete.length;
        const pendingCount = this.pendingNewImages.length;
        const totalCount = visibleExistingCount + pendingCount;

        // Limpiar errores previos
        this.clearFieldError("image_files");

        // Validar conteo total
        if (totalCount > this.maxFiles) {
            this.showFieldError(
                "image_files",
                this.getTranslation(
                    "max_images_exceeded",
                    `Total de imágenes (${totalCount}) excede el límite de ${this.maxFiles}`
                )
                    .replace("{total}", totalCount)
                    .replace("{max}", this.maxFiles)
            );
            return false;
        }

        // Validar que haya al menos una imagen
        if (totalCount === 0) {
            this.showFieldError(
                "image_files",
                this.getTranslation(
                    "images_required",
                    "Se requiere al menos una imagen para el portfolio"
                )
            );
            return false;
        }

        // Validar tamaño total
        const totalSizeMB = this.pendingNewImages.reduce((total, file) => {
            return total + file.size / 1024 / 1024;
        }, 0);

        if (totalSizeMB > this.maxTotalSizeKb / 1024) {
            this.showFieldError(
                "image_files",
                this.getTranslation(
                    "max_size_exceeded",
                    `Tamaño total de nuevas imágenes (${totalSizeMB.toFixed(
                        2
                    )}MB) excede el límite de ${this.maxTotalSizeKb / 1024}MB`
                )
                    .replace("{size}", totalSizeMB.toFixed(2))
                    .replace("{max}", this.maxTotalSizeKb / 1024)
            );
            return false;
        }

        return true;
    }

    /**
     * Validar y obtener datos del portfolio
     */
    validateAndGetPortfolioData() {
        // Validar campos básicos
        const basicData = this.validateBasicFields();
        if (!basicData) return false;

        // Validar límites de imágenes
        if (!this.validateImageLimits()) return false;

        // Preparar datos para envío
        const formData = new FormData();

        // Agregar campos básicos
        Object.keys(basicData).forEach((key) => {
            formData.append(key, basicData[key]);
        });

        // Agregar nuevas imágenes
        this.pendingNewImages.forEach((file, index) => {
            formData.append(`images[${index}]`, file);
        });

        // Agregar imágenes a eliminar (solo en modo edición)
        if (this.isEditing && this.imagesToDelete.length > 0) {
            this.imagesToDelete.forEach((imageId, index) => {
                formData.append(`images_to_delete[${index}]`, imageId);
            });
        }

        // Agregar orden de imágenes existentes (solo en modo edición)
        if (this.isEditing && this.existingImages.length > 0) {
            this.existingImages.forEach((image, index) => {
                formData.append(`existing_images_order[${image.id}]`, index);
            });
        }

        return formData;
    }

    /**
     * Validar campos básicos del formulario
     */
    validateBasicFields() {
        const data = {};
        let isValid = true;

        // Limpiar errores previos
        $(".error-message").addClass("hidden").text("");

        // Validar título
        const title = $("#title").val().trim();
        if (!title) {
            this.showFieldError(
                "title",
                this.getTranslation("title_required", "El título es requerido")
            );
            isValid = false;
        } else if (title.length < 3) {
            this.showFieldError(
                "title",
                this.getTranslation(
                    "title_min_length",
                    "El título debe tener al menos 3 caracteres"
                )
            );
            isValid = false;
        } else {
            data.title = title;
        }

        // Validar descripción
        const description = $("#description").val().trim();
        if (!description) {
            this.showFieldError(
                "description",
                this.getTranslation(
                    "description_required",
                    "La descripción es requerida"
                )
            );
            isValid = false;
        } else {
            data.description = description;
        }

        // Validar categoría de servicio
        const serviceCategoryId = $("#service_category_id").val();
        if (!serviceCategoryId) {
            this.showFieldError(
                "service_category_id",
                this.getTranslation(
                    "category_required",
                    "La categoría de servicio es requerida"
                )
            );
            isValid = false;
        } else {
            data.service_category_id = serviceCategoryId;
        }

        return isValid ? data : false;
    }

    /**
     * Crear portfolio
     */
    async createPortfolio(formData) {
        try {
            Swal.showLoading();

            const response = await $.ajax({
                url: this.routes.store,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                data: formData,
                processData: false,
                contentType: false,
            });

            Swal.close();
            this.showAlert(
                "success",
                this.getTranslation(
                    "created_successfully",
                    "Portfolio creado exitosamente"
                )
            );
            this.loadEntities();
            this.resetImageState();
        } catch (error) {
            Swal.close();
            console.error("Error creating portfolio:", error);

            if (error.status === 422 && error.responseJSON?.errors) {
                this.showValidationErrors(error.responseJSON.errors);
            } else {
                this.showAlert(
                    "error",
                    error.responseJSON?.message ||
                        this.getTranslation(
                            "error_creating",
                            "Error al crear el portfolio"
                        )
                );
            }
        }
    }

    /**
     * Actualizar portfolio
     */
    async updatePortfolio(id, formData) {
        try {
            Swal.showLoading();

            // Para PUT requests con archivos, necesitamos usar POST con _method
            formData.append("_method", "PUT");

            const response = await $.ajax({
                url: this.routes.update.replace(":id", id),
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                    Accept: "application/json",
                },
                data: formData,
                processData: false,
                contentType: false,
            });

            Swal.close();
            this.showAlert(
                "success",
                this.getTranslation(
                    "updated_successfully",
                    "Portfolio actualizado exitosamente"
                )
            );
            this.loadEntities();
            this.resetImageState();
        } catch (error) {
            Swal.close();
            console.error("Error updating portfolio:", error);

            if (error.status === 422 && error.responseJSON?.errors) {
                this.showValidationErrors(error.responseJSON.errors);
            } else {
                this.showAlert(
                    "error",
                    error.responseJSON?.message ||
                        "Error al actualizar el portfolio"
                );
            }
        }
    }

    /**
     * Resetear estado de imágenes
     */
    resetImageState() {
        this.existingImages = [];
        this.pendingNewImages = [];
        this.imagesToDelete = [];
        this.imageOrder = [];

        // Liberar URLs de objetos
        $("#pending-images-container img").each(function () {
            const src = $(this).attr("src");
            if (src && src.startsWith("blob:")) {
                URL.revokeObjectURL(src);
            }
        });
    }

    /**
     * Aplicar color al header del modal
     */
    applyHeaderColor(mode) {
        setTimeout(() => {
            const popup = document.querySelector(".swal2-popup");
            if (popup) {
                popup.classList.remove("swal-create", "swal-edit");
                popup.classList.add(`swal-${mode}`);
            }
        }, 10);
    }

    /**
     * Renderizar tabla con traducción personalizada
     */
    renderTable(data) {
        const entities = data.data;
        let html = "";

        if (entities.length === 0) {
            html = `<tr><td colspan="${
                this.tableHeaders.length
            }" class="px-6 py-4 text-center text-sm text-gray-500">${this.getTranslation(
                "portfolio_no_records_found",
                "No se encontraron registros"
            )}</td></tr>`;
        } else {
            entities.forEach((entity) => {
                const isDeleted = entity.deleted_at !== null;
                const rowClass = isDeleted
                    ? "bg-red-50 dark:bg-red-900 opacity-60"
                    : "";

                // Almacenar datos de la entidad como JSON en atributo data
                const entityData = JSON.stringify(entity).replace(
                    /"/g,
                    "&quot;"
                );

                html += `<tr class="${rowClass}" data-entity="${entityData}">`;

                this.tableHeaders.forEach((header) => {
                    let value = header.getter
                        ? header.getter(entity)
                        : entity[header.field];
                    html += `<td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100 text-center">${value}</td>`;
                });

                html += `</tr>`;
            });
        }

        $(this.tableSelector).html(html);
    }

    /**
     * Obtener traducción con fallback
     */
    getTranslation(key, fallback = "") {
        // Intentar obtener la traducción del objeto global de Laravel
        if (
            typeof window.translations !== "undefined" &&
            window.translations[key]
        ) {
            return window.translations[key];
        }

        // Fallback a traducciones hardcodeadas en español
        const translations = {
            // Títulos y acciones principales
            create_portfolio: "Crear Portafolio",
            edit_portfolio: "Editar Portafolio",
            delete_portfolio: "Eliminar Portafolio",
            restore_portfolio: "Restaurar Portafolio",

            // Mensajes de confirmación
            confirm_delete: "¿Está seguro?",
            confirm_restore: "¿Restaurar portafolio?",
            delete_message: "¿Desea eliminar este portafolio?",
            restore_message: "¿Desea restaurar este portafolio?",

            // Botones
            yes_delete: "Sí, eliminar",
            yes_restore: "Sí, restaurar",
            cancel: "Cancelar",
            save: "Guardar",
            update: "Actualizar",
            create: "Crear",

            // Estados y mensajes
            success: "Éxito",
            error: "Error",
            saving: "Guardando",
            loading: "Cargando",
            deleted_successfully: "eliminado exitosamente",
            restored_successfully: "restaurado exitosamente",
            created_successfully: "creado exitosamente",
            updated_successfully: "actualizado exitosamente",

            // Errores
            error_deleting: "Error al eliminar el portafolio",
            error_restoring: "Error al restaurar el portafolio",
            error_creating: "Error al crear el portafolio",
            error_updating: "Error al actualizar el portafolio",
            error_loading_service_categories:
                "Error al cargar categorías de servicio",

            // Campos del formulario
            project_title: "Título del Proyecto",
            project_description: "Descripción del Proyecto",
            service_category: "Categoría de Servicio",
            select_category: "Seleccione una categoría",

            // Gestión de imágenes
            image_management: "Gestión de Imágenes",
            add_new_images: "Agregar Nuevas Imágenes",
            portfolio_images: "Imágenes del Portfolio",
            current_images: "Imágenes Actuales",
            new_images_to_upload: "Nuevas Imágenes a Subir",
            max_images_info:
                "Máximo {maxFiles} imágenes. Tamaño máximo por imagen: {maxSize}MB. Formatos: JPEG, PNG, JPG, GIF, WEBP.",
            image_limits: "Límites",
            total_size: "Tamaño total",
            mark_for_deletion: "Marcar para eliminar",
            restore_image: "Restaurar imagen",
            remove_image: "Eliminar imagen",

            // Validaciones
            title_required: "El título es requerido",
            title_min_length: "El título debe tener al menos 3 caracteres",
            title_already_exists: "Ya existe un portafolio con este título",
            title_available: "Título disponible",
            description_required: "La descripción es requerida",
            category_required: "La categoría de servicio es requerida",
            images_required:
                "Se requiere al menos una imagen para el portafolio",
            max_images_exceeded:
                "Total de imágenes ({total}) excede el límite de {max}",
            max_size_exceeded:
                "Tamaño total de nuevas imágenes ({size}MB) excede el límite de {max}MB",
            invalid_file_type:
                "Archivo {name}: Tipo no permitido. Solo se permiten: JPEG, PNG, JPG, GIF, WEBP",
            file_too_large: "Archivo {name}: Tamaño excede {max}MB",

            // Estados de datos
            no_title: "Sin título",
            no_description: "Sin descripción",
            no_category: "Sin categoría",
            no_images: "Sin imágenes",
            images_count: "{count} imagen{plural}",
        };

        return translations[key] || fallback || key;
    }
}

// Hacer disponible globalmente
window.PortfolioCrudManager = PortfolioCrudManager;
