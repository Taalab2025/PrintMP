/**
 * Main JavaScript File
 * File path: assets/js/main.js
 *
 * Main JavaScript functionality for the site
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

// Document ready function
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all dropdown menus
    initDropdowns();

    // Initialize mobile menu
    initMobileMenu();

    // Initialize form validations
    initFormValidations();

    // Initialize language switcher
    initLanguageSwitcher();

    // Initialize flash message dismissal
    initFlashMessages();

    // Initialize RTL support
    initRtlSupport();
});

/**
 * Initialize dropdown menus
 */
function initDropdowns() {
    const dropdownButtons = document.querySelectorAll('[data-dropdown]');

    dropdownButtons.forEach(button => {
        const targetId = button.getAttribute('data-dropdown');
        const target = document.getElementById(targetId);

        if (!target) return;

        // Toggle dropdown on click
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            target.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!target.contains(e.target) && !button.contains(e.target)) {
                target.classList.add('hidden');
            }
        });
    });
}

/**
 * Initialize mobile menu
 */
function initMobileMenu() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const navLinks = document.getElementById('nav-links');

    if (mobileMenuButton && navLinks) {
        mobileMenuButton.addEventListener('click', function() {
            navLinks.classList.toggle('hidden');
        });
    }
}

/**
 * Initialize form validations
 */
function initFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            // Clear previous error messages
            const errorMessages = form.querySelectorAll('.form-error');
            errorMessages.forEach(message => message.remove());

            // Check each required field
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    displayError(field, 'This field is required');
                } else if (field.type === 'email' && !isValidEmail(field.value)) {
                    isValid = false;
                    displayError(field, 'Please enter a valid email address');
                } else if (field.type === 'password' && field.minLength && field.value.length < field.minLength) {
                    isValid = false;
                    displayError(field, `Password must be at least ${field.minLength} characters`);
                } else if (field.dataset.match) {
                    const matchField = document.getElementById(field.dataset.match);
                    if (matchField && field.value !== matchField.value) {
                        isValid = false;
                        displayError(field, 'Fields do not match');
                    }
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    });
}

/**
 * Display error message below a form field
 */
function displayError(field, message) {
    // Remove any existing error for this field
    const parentElement = field.parentElement;
    const existingError = parentElement.querySelector('.form-error');
    if (existingError) {
        existingError.remove();
    }

    // Add red border to the field
    field.classList.add('border-red-500');

    // Create and append error message
    const errorElement = document.createElement('p');
    errorElement.className = 'form-error text-red-500 text-sm mt-1';
    errorElement.textContent = message;
    parentElement.appendChild(errorElement);

    // Remove error styling when field is changed
    field.addEventListener('input', function() {
        field.classList.remove('border-red-500');
        const error = parentElement.querySelector('.form-error');
        if (error) {
            error.remove();
        }
    });
}

/**
 * Validate email format
 */
function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

/**
 * Initialize language switcher
 */
function initLanguageSwitcher() {
    const langLinks = document.querySelectorAll('a[href*="?lang="]');

    langLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();

            const lang = this.href.split('lang=')[1];

            // Set language cookie
            document.cookie = `lang=${lang}; path=/; max-age=31536000`; // 1 year

            // Redirect to the same page with updated language
            const currentUrl = window.location.href.split('?')[0];
            window.location.href = `${currentUrl}?lang=${lang}`;
        });
    });
}

/**
 * Initialize flash messages
 */
function initFlashMessages() {
    const flashMessages = document.querySelectorAll('.alert');

    flashMessages.forEach(message => {
        // Add close button
        const closeButton = document.createElement('button');
        closeButton.className = 'ml-4 text-gray-600 hover:text-gray-800 focus:outline-none';
        closeButton.innerHTML = '&times;';
        closeButton.addEventListener('click', function() {
            message.remove();
        });

        message.appendChild(closeButton);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            message.classList.add('opacity-0');
            setTimeout(() => {
                message.remove();
            }, 300);
        }, 5000);
    });
}

/**
 * Initialize RTL support
 */
function initRtlSupport() {
    // Check HTML dir attribute
    const isRtl = document.documentElement.dir === 'rtl';

    if (isRtl) {
        // Flip icons that need flipping
        const iconsToFlip = document.querySelectorAll('.should-flip-rtl');
        iconsToFlip.forEach(icon => {
            icon.classList.add('rtl-flip');
        });

        // Adjust any positioning that needs RTL specific handling
        const rtlAdjustElements = document.querySelectorAll('[data-rtl-adjust]');
        rtlAdjustElements.forEach(element => {
            const adjustType = element.dataset.rtlAdjust;
            if (adjustType === 'flip-float') {
                if (element.classList.contains('float-left')) {
                    element.classList.remove('float-left');
                    element.classList.add('float-right');
                } else if (element.classList.contains('float-right')) {
                    element.classList.remove('float-right');
                    element.classList.add('float-left');
                }
            }
        });
    }
}
