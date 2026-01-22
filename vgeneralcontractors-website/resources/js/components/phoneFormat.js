// resources/js/components/phoneFormat.js

/**
 * Phone Formatting Utilities
 * Handles consistent phone number formatting
 */
export default {
    /**
     * Format a phone number for display
     * @param {string} phone - Phone number to format
     * @returns {string} Formatted phone number
     */
    formatForDisplay(phone) {
        if (!phone) return "";

        // Remove non-numeric characters
        let cleaned = ("" + phone).replace(/\D/g, "");

        // Handle different formats based on length and country code
        if (phone.startsWith("+1") || cleaned.length === 10) {
            // US format: (XXX) XXX-XXXX
            if (cleaned.length === 10) {
                return `(${cleaned.substring(0, 3)}) ${cleaned.substring(
                    3,
                    6
                )}-${cleaned.substring(6, 10)}`;
            } else if (cleaned.length === 11 && cleaned.startsWith("1")) {
                // Format with country code
                return `+1 (${cleaned.substring(1, 4)}) ${cleaned.substring(
                    4,
                    7
                )}-${cleaned.substring(7, 11)}`;
            }
        }

        // For non-US or unusual formats, return with basic formatting
        if (cleaned.length > 10) {
            return `+${cleaned.substring(
                0,
                cleaned.length - 10
            )} ${cleaned.substring(cleaned.length - 10)}`;
        }

        return phone; // Return original if we couldn't format it
    },

    /**
     * Format a phone number for database storage
     * @param {string} phone - Phone number to format
     * @returns {string} Formatted phone number for storage
     */
    formatForStorage(phone) {
        if (!phone) return null;

        // Remove all non-numeric characters
        let cleaned = ("" + phone).replace(/\D/g, "");

        // Add US country code if not present and length is 10
        if (cleaned.length === 10) {
            return "+1" + cleaned;
        }

        // If already has country code (assume that's why it's longer than 10)
        if (cleaned.length > 10) {
            // Check if it already has the + prefix
            return cleaned.startsWith("+") ? cleaned : "+" + cleaned;
        }

        // For other formats, just ensure + prefix
        return phone.startsWith("+") ? phone : "+" + phone;
    },

    /**
     * Setup phone input masking
     * @param {string} selector - CSS selector for phone inputs
     */
    setupPhoneMasks(selector = ".phone-input") {
        document.querySelectorAll(selector).forEach((input) => {
            input.addEventListener("input", (e) => {
                let value = e.target.value.replace(/\D/g, "");

                // Apply US format as user types
                if (value.length <= 3) {
                    e.target.value = value.length ? `(${value}` : value;
                } else if (value.length <= 6) {
                    e.target.value = `(${value.substring(
                        0,
                        3
                    )}) ${value.substring(3)}`;
                } else {
                    e.target.value = `(${value.substring(
                        0,
                        3
                    )}) ${value.substring(3, 6)}-${value.substring(6, 10)}`;
                }
            });
        });
    },

    /**
     * Format a phone number as (XXX) XXX-XXXX
     * @param {Event} e - Input event
     * @param {Object} form - Form object containing phone property
     * @param {Object} wire - Livewire connection
     */
    formatPhoneInput(e, form, wire = null) {
        if (!e || !e.target) return;

        // Handle backspace/delete
        if (e.inputType === "deleteContentBackward") {
            let value = form.phone.replace(/\D/g, "");
            value = value.substring(0, value.length - 1);

            if (value.length === 0) {
                form.phone = "";
            } else if (value.length <= 3) {
                form.phone = `(${value}`;
            } else if (value.length <= 6) {
                form.phone = `(${value.substring(0, 3)}) ${value.substring(3)}`;
            } else {
                form.phone = `(${value.substring(0, 3)}) ${value.substring(
                    3,
                    6
                )}-${value.substring(6)}`;
            }

            if (wire) wire.set("phone", form.phone);
            return;
        }

        // Format as user types
        let value = e.target.value.replace(/\D/g, "").substring(0, 10);
        if (value.length >= 6) {
            value = `(${value.substring(0, 3)}) ${value.substring(
                3,
                6
            )}-${value.substring(6)}`;
        } else if (value.length >= 3) {
            value = `(${value.substring(0, 3)}) ${value.substring(3)}`;
        } else if (value.length > 0) {
            value = `(${value}`;
        }

        e.target.value = value;
        form.phone = value;
        if (wire) wire.set("phone", value);
    },

    /**
     * Validate a phone number
     * @param {string} phone - Phone number to validate
     * @returns {boolean} - True if valid
     */
    validatePhone(phone) {
        if (!phone) return true; // Phone is optional

        // Check for complete phone number format
        return /^\(\d{3}\) \d{3}-\d{4}$/.test(phone);
    },

    /**
     * Format a phone number string
     * @param {string} phone - Raw phone number
     * @returns {string} - Formatted phone number
     */
    formatPhoneString(phone) {
        if (!phone) return "";

        // Remove non-digits
        const cleaned = phone.replace(/\D/g, "");

        // Format the phone number
        if (cleaned.length >= 10) {
            return `(${cleaned.substring(0, 3)}) ${cleaned.substring(
                3,
                6
            )}-${cleaned.substring(6, 10)}`;
        } else if (cleaned.length >= 7) {
            return `(${cleaned.substring(0, 3)}) ${cleaned.substring(
                3,
                6
            )}-${cleaned.substring(6)}`;
        } else if (cleaned.length >= 4) {
            return `(${cleaned.substring(0, 3)}) ${cleaned.substring(3)}`;
        } else if (cleaned.length > 0) {
            return `(${cleaned}`;
        }

        return "";
    },
};
