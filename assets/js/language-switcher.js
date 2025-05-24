/**
 * Language Switcher Handler
 * file path: assets/js/language-switcher.js
 */

// Wait for DOM to be ready
document.addEventListener('DOMContentLoaded', function() {
    // Get language switcher element
    const languageSwitcher = document.getElementById('language-switcher');
    if (!languageSwitcher) return;

    // Handle language change
    languageSwitcher.addEventListener('change', function(e) {
        const selectedLanguage = e.target.value;

        // Create or get existing language form
        let langForm = document.getElementById('language-form');
        if (!langForm) {
            langForm = document.createElement('form');
            langForm.method = 'POST';
            langForm.action = window.location.pathname;
            langForm.id = 'language-form';
            document.body.appendChild(langForm);
        }

        // Create language input
        let langInput = document.getElementById('language-input');
        if (!langInput) {
            langInput = document.createElement('input');
            langInput.type = 'hidden';
            langInput.name = 'language';
            langInput.id = 'language-input';
            langForm.appendChild(langInput);
        }

        // Set value and submit
        langInput.value = selectedLanguage;

        // Add CSRF token if applicable
        if (window.csrfToken) {
            let csrfInput = document.getElementById('csrf-token-input');
            if (!csrfInput) {
                csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.id = 'csrf-token-input';
                langForm.appendChild(csrfInput);
            }
            csrfInput.value = window.csrfToken;
        }

        // Submit form
        langForm.submit();
    });

    // Handle dropdown toggle for mobile
    const langToggle = document.getElementById('language-toggle');
    const langDropdown = document.getElementById('language-dropdown');

    if (langToggle && langDropdown) {
        langToggle.addEventListener('click', function(e) {
            e.preventDefault();
            langDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!langToggle.contains(e.target) && !langDropdown.contains(e.target)) {
                langDropdown.classList.add('hidden');
            }
        });
    }
});

// Function to update direction attribute based on language
function updateDirection(language) {
    const htmlElement = document.documentElement;
    if (language === 'ar') {
        htmlElement.setAttribute('dir', 'rtl');
        htmlElement.classList.add('rtl');
        htmlElement.lang = 'ar';
    } else {
        htmlElement.setAttribute('dir', 'ltr');
        htmlElement.classList.remove('rtl');
        htmlElement.lang = 'en';
    }
}
