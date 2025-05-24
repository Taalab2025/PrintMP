/**
 * Toast Notification System
 * File path: assets/js/toast-notifications.js
 */

const ToastNotifications = {

    // Configuration
    config: {
        defaultDuration: 5000,
        maxToasts: 5,
        position: 'top-right', // top-right, top-left, bottom-right, bottom-left
        animations: true
    },

    // Initialize toast system
    init(options = {}) {
        this.config = { ...this.config, ...options };
        this.createContainer();
        this.setupEventListeners();
    },

    // Create toast container if it doesn't exist
    createContainer() {
        let container = document.getElementById('toast-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = this.getContainerClasses();
            document.body.appendChild(container);
        }

        return container;
    },

    // Get container classes based on position
    getContainerClasses() {
        const baseClasses = 'fixed z-50 space-y-2 pointer-events-none';
        const positionClasses = {
            'top-right': 'top-4 right-4',
            'top-left': 'top-4 left-4',
            'bottom-right': 'bottom-4 right-4',
            'bottom-left': 'bottom-4 left-4'
        };

        return `${baseClasses} ${positionClasses[this.config.position] || positionClasses['top-right']}`;
    },

    // Setup event listeners
    setupEventListeners() {
        // Listen for custom toast events
        document.addEventListener('show-toast', (e) => {
            const { message, type, duration, title } = e.detail;
            this.show(message, type, duration, title);
        });

        // Listen for keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // ESC to dismiss all toasts
            if (e.key === 'Escape') {
                this.dismissAll();
            }
        });
    },

    // Show toast notification
    show(message, type = 'info', duration = null, title = null) {
        if (!message) return;

        const container = this.createContainer();
        duration = duration || this.config.defaultDuration;

        // Limit number of toasts
        this.limitToasts();

        // Create toast element
        const toast = this.createToast(message, type, title);

        // Add to container
        container.appendChild(toast);

        // Animate in
        if (this.config.animations) {
            this.animateIn(toast);
        }

        // Auto dismiss
        if (duration > 0) {
            setTimeout(() => {
                this.dismiss(toast.id);
            }, duration);
        }

        // Return toast ID for manual dismissal
        return toast.id;
    },

    // Create toast element
    createToast(message, type, title) {
        const toastId = 'toast-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
        const toast = document.createElement('div');

        toast.id = toastId;
        toast.className = this.getToastClasses(type);
        toast.innerHTML = this.getToastHTML(message, type, title, toastId);

        // Make it interactive
        toast.style.pointerEvents = 'auto';

        return toast;
    },

    // Get toast classes based on type
    getToastClasses(type) {
        const baseClasses = 'max-w-sm w-full shadow-lg rounded-lg pointer-events-auto transform transition-all duration-300 ease-in-out';

        const typeClasses = {
            success: 'bg-white border border-green-200',
            error: 'bg-white border border-red-200',
            warning: 'bg-white border border-yellow-200',
            info: 'bg-white border border-blue-200'
        };

        const animationClasses = this.config.animations ? 'translate-x-full opacity-0' : '';

        return `${baseClasses} ${typeClasses[type] || typeClasses.info} ${animationClasses}`;
    },

    // Get toast HTML content
    getToastHTML(message, type, title, toastId) {
        const isRtl = window.isRtl || document.dir === 'rtl';

        const icons = {
            success: 'fa-check-circle text-green-500',
            error: 'fa-exclamation-circle text-red-500',
            warning: 'fa-exclamation-triangle text-yellow-500',
            info: 'fa-info-circle text-blue-500'
        };

        const iconClass = icons[type] || icons.info;

        return `
            <div class="p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <i class="fas ${iconClass} text-xl"></i>
                    </div>
                    <div class="${isRtl ? 'mr-3' : 'ml-3'} w-0 flex-1">
                        ${title ? `<p class="text-sm font-medium text-gray-900 mb-1">${this.escapeHtml(title)}</p>` : ''}
                        <p class="text-sm text-gray-600 ${title ? '' : 'font-medium'}">${this.escapeHtml(message)}</p>
                    </div>
                    <div class="${isRtl ? 'mr-4' : 'ml-4'} flex-shrink-0 flex">
                        <button type="button"
                                onclick="ToastNotifications.dismiss('${toastId}')"
                                class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <span class="sr-only">Close</span>
                            <i class="fas fa-times text-sm"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;
    },

    // Animate toast in
    animateIn(toast) {
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 10);
    },

    // Animate toast out
    animateOut(toast, callback) {
        if (this.config.animations) {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(callback, 300);
        } else {
            callback();
        }
    },

    // Dismiss specific toast
    dismiss(toastId) {
        const toast = document.getElementById(toastId);
        if (!toast) return;

        this.animateOut(toast, () => {
            toast.remove();
        });
    },

    // Dismiss all toasts
    dismissAll() {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toasts = container.querySelectorAll('[id^="toast-"]');
        toasts.forEach(toast => {
            this.dismiss(toast.id);
        });
    },

    // Limit number of visible toasts
    limitToasts() {
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toasts = container.querySelectorAll('[id^="toast-"]');
        if (toasts.length >= this.config.maxToasts) {
            // Remove oldest toast
            this.dismiss(toasts[0].id);
        }
    },

    // Escape HTML to prevent XSS
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    // Convenience methods for different types
    success(message, duration, title) {
        return this.show(message, 'success', duration, title);
    },

    error(message, duration, title) {
        return this.show(message, 'error', duration, title);
    },

    warning(message, duration, title) {
        return this.show(message, 'warning', duration, title);
    },

    info(message, duration, title) {
        return this.show(message, 'info', duration, title);
    },

    // Show loading toast (persistent until dismissed)
    loading(message, title) {
        return this.show(message, 'info', 0, title || 'Loading...');
    },

    // Promise-based toast (shows loading, then success/error)
    async promise(promise, messages = {}) {
        const loadingId = this.loading(
            messages.loading || 'Processing...',
            messages.loadingTitle
        );

        try {
            const result = await promise;
            this.dismiss(loadingId);
            this.success(
                messages.success || 'Operation completed successfully!',
                null,
                messages.successTitle
            );
            return result;
        } catch (error) {
            this.dismiss(loadingId);
            this.error(
                messages.error || error.message || 'Operation failed!',
                null,
                messages.errorTitle
            );
            throw error;
        }
    }
};

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    ToastNotifications.init();
});

// Global shortcut functions
window.toast = ToastNotifications.show.bind(ToastNotifications);
window.toast.success = ToastNotifications.success.bind(ToastNotifications);
window.toast.error = ToastNotifications.error.bind(ToastNotifications);
window.toast.warning = ToastNotifications.warning.bind(ToastNotifications);
window.toast.info = ToastNotifications.info.bind(ToastNotifications);
window.toast.loading = ToastNotifications.loading.bind(ToastNotifications);
window.toast.promise = ToastNotifications.promise.bind(ToastNotifications);
window.toast.dismiss = ToastNotifications.dismiss.bind(ToastNotifications);
window.toast.dismissAll = ToastNotifications.dismissAll.bind(ToastNotifications);

// Export for module systems
window.ToastNotifications = ToastNotifications;
