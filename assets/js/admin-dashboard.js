/**
 * Admin Dashboard JavaScript
 * File path: assets/js/admin-dashboard.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize admin dashboard functionality
    initializeAdminDashboard();
});

function initializeAdminDashboard() {
    // Initialize tooltips
    initializeTooltips();

    // Initialize confirmation dialogs
    initializeConfirmations();

    // Initialize date pickers
    initializeDatePickers();

    // Initialize bulk actions
    initializeBulkActions();

    // Initialize auto-refresh
    initializeAutoRefresh();

    // Initialize search functionality
    initializeSearch();
}

function initializeTooltips() {
    // Add tooltips to elements with data-tooltip attribute
    const tooltipElements = document.querySelectorAll('[data-tooltip]');

    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(event) {
    const element = event.target;
    const tooltipText = element.getAttribute('data-tooltip');

    if (!tooltipText) return;

    const tooltip = document.createElement('div');
    tooltip.className = 'admin-tooltip';
    tooltip.textContent = tooltipText;

    document.body.appendChild(tooltip);

    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';

    element.tooltipElement = tooltip;
}

function hideTooltip(event) {
    const element = event.target;
    if (element.tooltipElement) {
        document.body.removeChild(element.tooltipElement);
        element.tooltipElement = null;
    }
}

function initializeConfirmations() {
    // Add confirmation to dangerous actions
    const dangerousActions = document.querySelectorAll('[data-confirm]');

    dangerousActions.forEach(element => {
        element.addEventListener('click', function(event) {
            const confirmMessage = this.getAttribute('data-confirm');

            if (!confirm(confirmMessage)) {
                event.preventDefault();
                return false;
            }
        });
    });
}

function initializeDatePickers() {
    // Initialize date range pickers for reports
    const dateInputs = document.querySelectorAll('input[type="date"]');

    dateInputs.forEach(input => {
        // Set max date to today
        const today = new Date().toISOString().split('T')[0];
        if (!input.getAttribute('max')) {
            input.setAttribute('max', today);
        }

        // Add change event listener
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if (form && this.getAttribute('data-auto-submit') === 'true') {
                form.submit();
            }
        });
    });
}

function initializeBulkActions() {
    // Initialize bulk action functionality
    const bulkActionSelects = document.querySelectorAll('.bulk-action-select');
    const masterCheckbox = document.querySelector('#select-all');
    const itemCheckboxes = document.querySelectorAll('.item-checkbox');
    const bulkActionForm = document.querySelector('#bulk-action-form');

    // Master checkbox functionality
    if (masterCheckbox && itemCheckboxes.length > 0) {
        masterCheckbox.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionVisibility();
        });

        // Individual checkbox functionality
        itemCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateMasterCheckbox();
                updateBulkActionVisibility();
            });
        });
    }

    function updateMasterCheckbox() {
        if (!masterCheckbox) return;

        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const totalCount = itemCheckboxes.length;

        if (checkedCount === 0) {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = false;
        } else if (checkedCount === totalCount) {
            masterCheckbox.checked = true;
            masterCheckbox.indeterminate = false;
        } else {
            masterCheckbox.checked = false;
            masterCheckbox.indeterminate = true;
        }
    }

    function updateBulkActionVisibility() {
        const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
        const bulkActionBar = document.querySelector('#bulk-action-bar');

        if (bulkActionBar) {
            if (checkedCount > 0) {
                bulkActionBar.classList.remove('hidden');
                document.querySelector('#selected-count').textContent = checkedCount;
            } else {
                bulkActionBar.classList.add('hidden');
            }
        }
    }
}

function initializeAutoRefresh() {
    // Auto-refresh functionality for dashboard stats
    const autoRefreshElements = document.querySelectorAll('[data-auto-refresh]');

    autoRefreshElements.forEach(element => {
        const interval = parseInt(element.getAttribute('data-auto-refresh')) * 1000;
        const endpoint = element.getAttribute('data-refresh-endpoint');

        if (interval && endpoint) {
            setInterval(() => {
                refreshElement(element, endpoint);
            }, interval);
        }
    });
}

function refreshElement(element, endpoint) {
    fetch(endpoint)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                element.innerHTML = data.html;
            }
        })
        .catch(error => {
            console.error('Auto-refresh failed:', error);
        });
}

function initializeSearch() {
    // Initialize live search functionality
    const searchInputs = document.querySelectorAll('.admin-search');

    searchInputs.forEach(input => {
        let searchTimeout;

        input.addEventListener('input', function() {
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(() => {
                const form = this.closest('form');
                if (form) {
                    form.submit();
                }
            }, 500); // 500ms delay
        });
    });
}

// Chart utilities
function createChart(canvasId, type, data, options = {}) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;

    const ctx = canvas.getContext('2d');

    const defaultOptions = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top'
            }
        }
    };

    const chartOptions = { ...defaultOptions, ...options };

    return new Chart(ctx, {
        type: type,
        data: data,
        options: chartOptions
    });
}

// Export functions for global use
window.adminDashboard = {
    createChart: createChart,
    refreshElement: refreshElement
};

// Status update functionality
function updateStatus(type, id, status) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('type', type);
    formData.append('id', id);
    formData.append('status', status);
    formData.append('csrf_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'));

    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload page to reflect changes
            window.location.reload();
        } else {
            alert(data.message || 'An error occurred');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating status');
    });
}

// Modal functionality
function showModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');

        // Focus first input in modal
        const firstInput = modal.querySelector('input, select, textarea');
        if (firstInput) {
            setTimeout(() => firstInput.focus(), 100);
        }
    }
}

function hideModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('modal-overlay')) {
        const modal = event.target.closest('.modal');
        if (modal) {
            hideModal(modal.id);
        }
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        const visibleModals = document.querySelectorAll('.modal:not(.hidden)');
        visibleModals.forEach(modal => {
            hideModal(modal.id);
        });
    }
});

// Export utility functions
window.showModal = showModal;
window.hideModal = hideModal;
window.updateStatus = updateStatus;

// Loading state management
function showLoading(element) {
    element.classList.add('loading');
    element.disabled = true;

    const originalText = element.textContent;
    element.setAttribute('data-original-text', originalText);
    element.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;

    const originalText = element.getAttribute('data-original-text');
    if (originalText) {
        element.textContent = originalText;
        element.removeAttribute('data-original-text');
    }
}

// Add loading states to forms
document.addEventListener('submit', function(event) {
    const form = event.target;
    const submitButton = form.querySelector('button[type="submit"]');

    if (submitButton && !form.hasAttribute('data-no-loading')) {
        showLoading(submitButton);
    }
});

// Export loading functions
window.showLoading = showLoading;
window.hideLoading = hideLoading;
