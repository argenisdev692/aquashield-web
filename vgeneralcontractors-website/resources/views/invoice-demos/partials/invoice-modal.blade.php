<!-- Invoice Demo Modal -->
<div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto"
    style="display: none;">

    <!-- Background overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity"></div>

    <!-- Modal container -->
    <div class="flex min-h-full items-center justify-center p-4">
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative bg-white rounded-2xl shadow-2xl w-full max-w-5xl max-h-[90vh] overflow-hidden">

            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 px-8 py-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-white"
                            x-text="isEditing ? 'Edit Invoice Demo' : 'Create New Invoice Demo'"></h3>
                        <p class="text-purple-100 mt-1">Fill in the details for the invoice demonstration</p>
                    </div>
                    <button @click="closeModal()"
                        class="text-white hover:text-purple-200 transition-colors duration-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="overflow-y-auto max-h-[calc(90vh-140px)]">
                <form @submit.prevent="submitForm()" class="p-8 space-y-6">

                    <!-- Invoice Header Section -->
                    <div class="bg-gray-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            Invoice Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Invoice Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Number *</label>
                                <div class="flex space-x-2">
                                    <input type="text" x-model="form.invoice_number"
                                        @input="formatInvoiceNumberInput($event)" @blur="checkInvoiceNumberExists()"
                                        :class="errors.invoice_number ?
                                            'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                            'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                        class="flex-1 rounded-xl shadow-sm transition-all duration-200"
                                        placeholder="VG-0001">
                                    <button type="button" @click="generateInvoiceNumber()"
                                        class="px-4 py-2 bg-purple-600 text-white rounded-xl hover:bg-purple-700 transition-colors duration-200">
                                        Generate
                                    </button>
                                </div>
                                <p x-show="errors.invoice_number" class="mt-1 text-sm text-red-600"
                                    x-text="errors.invoice_number"></p>
                                <p x-show="invoiceNumberExists" class="mt-1 text-sm text-red-600">This invoice number
                                    already exists</p>
                            </div>

                            <!-- Invoice Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Invoice Date *</label>
                                <input type="date" x-model="form.invoice_date"
                                    :class="errors.invoice_date ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                <p x-show="errors.invoice_date" class="mt-1 text-sm text-red-600"
                                    x-text="errors.invoice_date"></p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                                <select x-model="form.status"
                                    :class="errors.status ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                    <option value="">Select Status</option>
                                    <template x-for="status in formData.statuses" :key="status.value">
                                        <option :value="status.value" x-text="status.label"></option>
                                    </template>
                                </select>
                                <p x-show="errors.status" class="mt-1 text-sm text-red-600" x-text="errors.status"></p>
                            </div>
                        </div>
                    </div>

                    <!-- Bill To Section -->
                    <div class="bg-blue-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Bill To Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Bill To Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bill To Name *</label>
                                <input type="text" x-model="form.bill_to_name" @input="formatNameInput($event)"
                                    :class="errors.bill_to_name ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200"
                                    placeholder="John Doe">
                                <p x-show="errors.bill_to_name" class="mt-1 text-sm text-red-600"
                                    x-text="errors.bill_to_name"></p>
                            </div>

                            <!-- Bill To Phone -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bill To Phone *</label>
                                <input type="tel" x-model="form.bill_to_phone" @input="formatPhoneInput($event)"
                                    :class="errors.bill_to_phone ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200"
                                    placeholder="(555) 123-4567">
                                <p x-show="errors.bill_to_phone" class="mt-1 text-sm text-red-600"
                                    x-text="errors.bill_to_phone"></p>
                            </div>
                        </div>

                        <!-- Bill To Address -->
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Bill To Address *</label>
                            <textarea x-model="form.bill_to_address" rows="3" @input="formatAddressInput($event)"
                                :class="errors.bill_to_address ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                    'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                class="w-full rounded-xl shadow-sm transition-all duration-200" placeholder="123 Main St, City, State 12345"></textarea>
                            <p x-show="errors.bill_to_address" class="mt-1 text-sm text-red-600"
                                x-text="errors.bill_to_address"></p>
                        </div>
                    </div>

                    <!-- Insurance & Claim Information Section -->
                    <div class="bg-green-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            </svg>
                            Insurance & Claim Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Claim Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Claim Number *</label>
                                <input type="text" x-model="form.claim_number"
                                    @input="formatUppercaseInput($event, 'claim_number')"
                                    :class="errors.claim_number ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200"
                                    placeholder="CLM-2025-001">
                                <p x-show="errors.claim_number" class="mt-1 text-sm text-red-600"
                                    x-text="errors.claim_number"></p>
                            </div>

                            <!-- Policy Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Policy Number *</label>
                                <input type="text" x-model="form.policy_number"
                                    @input="formatUppercaseInput($event, 'policy_number')"
                                    :class="errors.policy_number ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200"
                                    placeholder="POL-2025-001">
                                <p x-show="errors.policy_number" class="mt-1 text-sm text-red-600"
                                    x-text="errors.policy_number"></p>
                            </div>

                            <!-- Insurance Company -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Insurance Company *</label>
                                <div class="flex space-x-2">
                                    <input type="text" x-model="form.insurance_company" list="insurance_companies"
                                        :class="errors.insurance_company ?
                                            'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                            'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                        class="flex-1 rounded-xl shadow-sm transition-all duration-200"
                                        placeholder="State Farm, Allstate, etc.">
                                    <button type="button" @click="showAddInsuranceModal = true"
                                        class="px-3 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200 flex items-center justify-center"
                                        title="Add new insurance company">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <datalist id="insurance_companies">
                                    <template x-for="company in formData.common_insurance_companies"
                                        :key="company">
                                        <option :value="company"></option>
                                    </template>
                                </datalist>
                                <p x-show="errors.insurance_company" class="mt-1 text-sm text-red-600"
                                    x-text="errors.insurance_company"></p>
                            </div>

                            <!-- Type of Loss -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type of Loss *</label>
                                <div class="flex space-x-2">
                                    <input type="text" x-model="form.type_of_loss" list="type_of_loss_options"
                                        :class="errors.type_of_loss ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                            'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                        class="flex-1 rounded-xl shadow-sm transition-all duration-200"
                                        placeholder="Wind, Hail, Fire, etc.">
                                    <button type="button" @click="showAddTypeOfLossModal = true"
                                        class="px-3 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 transition-colors duration-200 flex items-center justify-center"
                                        title="Add new type of loss">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                    </button>
                                </div>
                                <datalist id="type_of_loss_options">
                                    <template x-for="loss_type in formData.type_of_loss_options"
                                        :key="loss_type">
                                        <option :value="loss_type"></option>
                                    </template>
                                </datalist>
                                <p x-show="errors.type_of_loss" class="mt-1 text-sm text-red-600"
                                    x-text="errors.type_of_loss"></p>
                            </div>

                            <!-- Date of Loss -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Loss *</label>
                                <input type="date" x-model="form.date_of_loss"
                                    :class="errors.date_of_loss ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                <p x-show="errors.date_of_loss" class="mt-1 text-sm text-red-600"
                                    x-text="errors.date_of_loss"></p>
                            </div>

                            <!-- Date Received - Removed from invoice -->
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Received</label>
                                <input type="datetime-local" x-model="form.date_received"
                                    :class="errors.date_received ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                <p x-show="errors.date_received" class="mt-1 text-sm text-red-600"
                                    x-text="errors.date_received"></p>
                            </div> --}}

                            <!-- Date Inspected - Removed from invoice -->
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Inspected</label>
                                <input type="datetime-local" x-model="form.date_inspected"
                                    :class="errors.date_inspected ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                <p x-show="errors.date_inspected" class="mt-1 text-sm text-red-600"
                                    x-text="errors.date_inspected"></p>
                            </div> --}}

                            <!-- Date Entered - Removed from invoice -->
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date Entered</label>
                                <input type="datetime-local" x-model="form.date_entered"
                                    :class="errors.date_entered ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200">
                                <p x-show="errors.date_entered" class="mt-1 text-sm text-red-600"
                                    x-text="errors.date_entered"></p>
                            </div> --}}

                            <!-- Price List Code - Removed from invoice -->
                            {{-- <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price List Code</label>
                                <input type="text" x-model="form.price_list_code"
                                    :class="errors.price_list_code ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                        'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                    class="w-full rounded-xl shadow-sm transition-all duration-200"
                                    placeholder="PLC-001">
                                <p x-show="errors.price_list_code" class="mt-1 text-sm text-red-600"
                                    x-text="errors.price_list_code"></p>
                            </div> --}}
                        </div>
                    </div>

                    <!-- Invoice Items Section -->
                    <div class="bg-indigo-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                                Invoice Items *
                            </div>
                            <button type="button" @click="addItem()"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-xl hover:bg-indigo-700 transition-colors duration-200 text-sm">
                                Add Item
                            </button>
                        </h4>

                        <div x-show="form.items.length === 0" class="text-center py-8 text-gray-500">
                            <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                            <p>No items added yet. Click "Add Item" to get started.</p>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in form.items" :key="index">
                                <div class="bg-white rounded-lg p-4 border border-gray-200">
                                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 items-start">
                                        <!-- Service Name -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Service/Item</label>
                                            <input type="text" x-model="item.service_name"
                                                @input="formatServiceDescriptionInput($event, index)"
                                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm"
                                                placeholder="Service name">
                                        </div>

                                        <!-- Description -->
                                        <div>
                                            <label
                                                class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea x-model="item.description" rows="2" @input="formatItemDescriptionInput($event, index)"
                                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm" placeholder="Description"></textarea>
                                        </div>

                                        <!-- Quantity -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Qty</label>
                                            <input type="text" x-model="item.quantity"
                                                @input="item.quantity = item.quantity.replace(/[^0-9]/g, ''); calculateTotals()"
                                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm"
                                                placeholder="1">
                                        </div>

                                        <!-- Rate -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Rate</label>
                                            <input type="text" x-model="item.rate"
                                                @input="formatCurrencyInput($event, index)"
                                                class="w-full rounded-lg border-gray-300 shadow-sm text-sm"
                                                placeholder="0.00">
                                        </div>

                                        <!-- Amount & Remove Button -->
                                        <div class="flex items-end space-x-2">
                                            <div class="flex-1">
                                                <label
                                                    class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                                                <div class="text-sm font-semibold text-gray-900 py-2 px-3 bg-gray-50 rounded-lg"
                                                    x-text="formatCurrency(item.amount || 0)">
                                                </div>
                                            </div>
                                            <button type="button" @click="removeItem(index)"
                                                class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Financial Information Section -->
                    <div class="bg-yellow-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1">
                                </path>
                            </svg>
                            Financial Information
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <!-- Subtotal -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Subtotal</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">$</span>
                                    <input type="text" x-model="form.subtotal"
                                        @input="formatGeneralCurrencyInput($event, 'subtotal')"
                                        :class="errors.subtotal ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                            'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                        class="w-full pl-8 rounded-xl shadow-sm transition-all duration-200"
                                        placeholder="0.00">
                                </div>
                                <p x-show="errors.subtotal" class="mt-1 text-sm text-red-600"
                                    x-text="errors.subtotal"></p>
                            </div>

                            <!-- Tax Amount -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tax Amount</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-3 text-gray-500">$</span>
                                    <input type="text" x-model="form.tax_amount"
                                        @input="formatGeneralCurrencyInput($event, 'tax_amount')"
                                        :class="errors.tax_amount ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                            'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                        class="w-full pl-8 rounded-xl shadow-sm transition-all duration-200"
                                        placeholder="0.00">
                                </div>
                                <p x-show="errors.tax_amount" class="mt-1 text-sm text-red-600"
                                    x-text="errors.tax_amount"></p>
                            </div>

                            <!-- Balance Due (Auto-calculated) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Balance Due</label>
                                <div class="text-lg font-bold text-purple-600 py-3 px-4 bg-white rounded-xl border-2 border-purple-300"
                                    x-text="formatCurrency(form.balance_due)"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes Section -->
                    <div class="bg-orange-50 rounded-xl p-6">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-orange-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                </path>
                            </svg>
                            Additional Notes
                        </h4>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                            <textarea x-model="form.notes" rows="4" @input="formatNotesInput($event)"
                                :class="errors.notes ? 'border-red-300 focus:ring-red-500 focus:border-red-500' :
                                    'border-gray-300 focus:ring-purple-500 focus:border-purple-500'"
                                class="w-full rounded-xl shadow-sm transition-all duration-200" placeholder="Any additional notes or comments..."></textarea>
                            <p x-show="errors.notes" class="mt-1 text-sm text-red-600" x-text="errors.notes"></p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <button type="button" @click="closeModal()"
                            class="px-6 py-3 border border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors duration-200">
                            Cancel
                        </button>
                        <button type="submit" :disabled="submitting || invoiceNumberExists"
                            class="px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-xl hover:from-purple-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 flex items-center">
                            <svg x-show="submitting" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            <span
                                x-text="submitting ? 'Saving...' : (isEditing ? 'Update Invoice' : 'Create Invoice')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add Insurance Company Modal -->
<div x-show="showAddInsuranceModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="addNewInsuranceCompany()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Add New Insurance Company
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                <input type="text" x-model="newInsuranceCompany.name" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Enter insurance company name">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Company
                    </button>
                    <button type="button" @click="showAddInsuranceModal = false; newInsuranceCompany.name = ''"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Type of Loss Modal -->
<div x-show="showAddTypeOfLossModal" x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        <div
            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form @submit.prevent="addNewTypeOfLoss()">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4"></path>
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Add New Type of Loss
                            </h3>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type of Loss</label>
                                <input type="text" x-model="newTypeOfLoss.name" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                    placeholder="Enter type of loss (e.g., Wind, Hail, Fire)">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Add Type
                    </button>
                    <button type="button" @click="showAddTypeOfLossModal = false; newTypeOfLoss.name = ''"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
