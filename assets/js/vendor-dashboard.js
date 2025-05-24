/**
 * File path: assets/js/vendor-dashboard.js
 *
 * This file contains the JavaScript functionality for the vendor dashboard,
 * including common UI interactions and dashboard-specific features.
 */

document.addEventListener('DOMContentLoaded', function() {
    // Notification counter update
    updateNotificationCount();

    // Mobile sidebar navigation
    initializeMobileSidebar();

    // Dashboard charts (if on the analytics page)
    if (document.getElementById('quotesChart') && typeof Chart !== 'undefined') {
        initializeCharts();
    }

    // Initialize all dropdowns
    initializeDropdowns();

    // Initialize file upload previews
    initializeFileUploads();

    // Initialize tooltips
    initializeTooltips();

    // Initialize tabs
    initializeTabs();

    // Initialize option rows in service form
    initializeOptionRows();
});

/**
 * Update notification counter
 */
function updateNotificationCount() {
    // This would typically fetch from an API endpoint
    // For demonstration, we'll just use a random number
    const notificationBadge = document.querySelector('.notification-badge');
    if (notificationBadge) {
        // This is just a placeholder - in a real app, this would be fetched from the server
        const count = parseInt(notificationBadge.textContent) || 0;
        notificationBadge.textContent = count;
        notificationBadge.classList.toggle('hidden', count === 0);
    }
}

/**
 * Initialize mobile sidebar
 */
function initializeMobileSidebar() {
    const openSidebar = document.getElementById('openSidebar');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mobileSidebar = document.getElementById('mobileSidebar');

    if (!openSidebar || !closeSidebar || !sidebarOverlay || !mobileSidebar) {
        return;
    }

    openSidebar.addEventListener('click', () => {
        const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.getAttribute('dir') === 'rtl';
        mobileSidebar.classList.remove(isRtl ? "translate-x-full" : "-translate-x-full");
        sidebarOverlay.classList.remove('hidden');
    });

    function closeMobileSidebar() {
        const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.getAttribute('dir') === 'rtl';
        mobileSidebar.classList.add(isRtl ? "translate-x-full" : "-translate-x-full");
        sidebarOverlay.classList.add('hidden');
    }

    closeSidebar.addEventListener('click', closeMobileSidebar);
    sidebarOverlay.addEventListener('click', closeMobileSidebar);
}

/**
 * Initialize dashboard charts
 */
function initializeCharts() {
    // This is handled in the individual pages where charts are used
}

/**
 * Initialize dropdowns
 */
function initializeDropdowns() {
    // Get all dropdown toggle buttons
    const dropdownButtons = document.querySelectorAll('[data-dropdown-toggle]');

    dropdownButtons.forEach(button => {
        const targetId = button.getAttribute('data-dropdown-toggle');
        const target = document.getElementById(targetId);

        if (!target) return;

        button.addEventListener('click', (e) => {
            e.stopPropagation();
            target.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (e) => {
            if (!button.contains(e.target) && !target.contains(e.target)) {
                target.classList.add('hidden');
            }
        });
    });
}

/**
 * Initialize file upload previews
 */
function initializeFileUploads() {
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach(input => {
        const previewContainer = document.getElementById(`${input.id}Preview`);
        if (!previewContainer) return;

        input.addEventListener('change', function() {
            previewContainer.innerHTML = '';
            previewContainer.classList.remove('hidden');

            if (this.files.length > 0) {
                Array.from(this.files).forEach((file, index) => {
                    if (!file.type.match('image.*')) {
                        return;
                    }

                    const reader = new FileReader();

                    reader.onload = function(e) {
                        const preview = document.createElement('div');
                        preview.className = 'relative';

                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'h-32 w-full object-cover rounded-md';
                        img.alt = file.name;

                        const removeBtn = document.createElement('button');
                        removeBtn.type = 'button';
                        removeBtn.className = 'absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600';
                        removeBtn.innerHTML = '<i class="fas fa-times"></i>';

                        removeBtn.addEventListener('click', function() {
                            preview.remove();

                            if (previewContainer.children.length === 0) {
                                previewContainer.classList.add('hidden');
                            }
                        });

                        preview.appendChild(img);
                        preview.appendChild(removeBtn);
                        previewContainer.appendChild(preview);
                    };

                    reader.readAsDataURL(file);
                });
            } else {
                previewContainer.classList.add('hidden');
            }
        });
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltips = document.querySelectorAll('[data-tooltip]');

    tooltips.forEach(tooltip => {
        const tooltipText = tooltip.getAttribute('data-tooltip');
        const tooltipElement = document.createElement('div');
        tooltipElement.className = 'absolute z-10 p-2 text-xs text-white bg-gray-900 rounded opacity-0 invisible transition-opacity';
        tooltipElement.textContent = tooltipText;
        tooltip.appendChild(tooltipElement);

        tooltip.addEventListener('mouseenter', () => {
            tooltipElement.classList.remove('opacity-0', 'invisible');
            tooltipElement.classList.add('opacity-100');

            // Position the tooltip
            const rect = tooltip.getBoundingClientRect();
            tooltipElement.style.bottom = 'calc(100% + 5px)';
            tooltipElement.style.left = '50%';
            tooltipElement.style.transform = 'translateX(-50%)';
        });

        tooltip.addEventListener('mouseleave', () => {
            tooltipElement.classList.remove('opacity-100');
            tooltipElement.classList.add('opacity-0', 'invisible');
        });
    });
}

/**
 * Initialize tabs
 */
function initializeTabs() {
    const tabButtons = document.querySelectorAll('[role="tab"]');

    tabButtons.forEach(button => {
        button.addEventListener('click', () => {
            const tabsContainer = button.closest('[role="tablist"]').parentNode;
            const targetId = button.getAttribute('aria-controls');
            const target = document.getElementById(targetId);

            // Hide all tab panels
            tabsContainer.querySelectorAll('[role="tabpanel"]').forEach(panel => {
                panel.classList.add('hidden');
            });

            // Show the target panel
            if (target) {
                target.classList.remove('hidden');
            }

            // Update tab button states
            const tablist = button.closest('[role="tablist"]');
            tablist.querySelectorAll('[role="tab"]').forEach(tab => {
                tab.setAttribute('aria-selected', 'false');
                tab.classList.remove('border-blue-500', 'text-blue-600');
                tab.classList.add('border-transparent', 'text-gray-500');
            });

            button.setAttribute('aria-selected', 'true');
            button.classList.remove('border-transparent', 'text-gray-500');
            button.classList.add('border-blue-500', 'text-blue-600');
        });
    });
}

/**
 * Initialize option rows in service form
 */
function initializeOptionRows() {
    const optionsContainer = document.getElementById('optionsContainer');
    if (!optionsContainer) return;

    // Add click handlers to existing remove buttons
    const existingRows = optionsContainer.querySelectorAll('.option-row');
    existingRows.forEach(row => {
        const removeBtn = row.querySelector('.remove-option');
        if (removeBtn) {
            removeBtn.addEventListener('click', () => {
                row.remove();
            });
        }

        const typeSelect = row.querySelector('.option-type');
        if (typeSelect) {
            typeSelect.addEventListener('change', function() {
                const valuesField = row.querySelector('.option-values');
                if (valuesField) {
                    if (this.value === 'text' || this.value === 'number') {
                        valuesField.classList.add('hidden');
                    } else {
                        valuesField.classList.remove('hidden');
                    }
                }
            });
        }
    });

    // Add new option button
    const addOptionBtn = document.getElementById('addOption');
    if (addOptionBtn) {
        addOptionBtn.addEventListener('click', () => {
            const newRow = document.createElement('div');
            newRow.className = 'option-row bg-gray-50 p-4 rounded-md mb-4';

            const isRtl = document.documentElement.dir === 'rtl' || document.documentElement.getAttribute('dir') === 'rtl';
            const marginEnd = isRtl ? 'ml' : 'mr';

            newRow.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div class="md:col-span-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ${document.getElementById('option_name_label')?.textContent || 'Option Name'}
                        </label>
                        <input type="text" name="option_name[]" placeholder="${document.getElementById('option_name_placeholder')?.textContent || 'e.g., Size, Color, Material'}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ${document.getElementById('option_type_label')?.textContent || 'Option Type'}
                        </label>
                        <select name="option_type[]" class="option-type w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="select">${document.getElementById('option_type_select')?.textContent || 'Dropdown'}</option>
                            <option value="radio">${document.getElementById('option_type_radio')?.textContent || 'Radio Buttons'}</option>
                            <option value="checkbox">${document.getElementById('option_type_checkbox')?.textContent || 'Checkboxes'}</option>
                            <option value="number">${document.getElementById('option_type_number')?.textContent || 'Number Input'}</option>
                            <option value="text">${document.getElementById('option_type_text')?.textContent || 'Text Input'}</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 option-values">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            ${document.getElementById('option_values_label')?.textContent || 'Option Values'}
                        </label>
                        <input type="text" name="option_values[]" placeholder="${document.getElementById('option_values_placeholder')?.textContent || 'Comma-separated values'}" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        <p class="mt-1 text-xs text-gray-500">${document.getElementById('option_values_help')?.textContent || 'Separate values with commas'}</p>
                    </div>

                    <div class="md:col-span-1 flex items-end justify-center">
                        <button type="button" class="remove-option mb-1 text-red-500 hover:text-red-700">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;

            optionsContainer.appendChild(newRow);

            // Add event listeners to the new row
            const typeSelect = newRow.querySelector('.option-type');
            const removeBtn = newRow.querySelector('.remove-option');

            typeSelect.addEventListener('change', function() {
                const valuesField = newRow.querySelector('.option-values');
                if (this.value === 'text' || this.value === 'number') {
                    valuesField.classList.add('hidden');
                } else {
                    valuesField.classList.remove('hidden');
                }
            });

            removeBtn.addEventListener('click', function() {
                newRow.remove();
            });
        });
    }
}
