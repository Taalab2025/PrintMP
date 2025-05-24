/**
 * Modal Dialog System
 * File path: assets/js/modal-dialogs.js
 */

const ModalDialogs = {

    // Configuration
    config: {
        closeOnBackdrop: true,
        closeOnEscape: true,
        animations: true,
        stackable: true
    },

    // Stack to manage multiple modals
    modalStack: [],

    // Initialize modal system
    init(options = {}) {
        this.config = { ...this.config, ...options };
        this.setupEventListeners();
        this.setupExistingModals();
    },

    // Setup global event listeners
    setupEventListeners() {
        // Escape key handler
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.config.closeOnEscape) {
                this.closeTop();
            }
        });

        // Handle modal trigger buttons
        document.addEventListener('click', (e) => {
            const trigger = e.target.closest('[data-modal-target]');
            if (trigger) {
                e.preventDefault();
                const targetId = trigger.getAttribute('data-modal-target');
                this.open(targetId);
            }

            const close = e.target.closest('[data-modal-close]');
            if (close) {
                e.preventDefault();
                const modalId = close.getAttribute('data-modal-close');
                if (modalId) {
                    this.close(modalId);
                } else {
                    this.closeTop();
                }
            }
        });
    },

    // Setup existing modals in DOM
    setupExistingModals() {
        document.querySelectorAll('[data-modal]').forEach(modal => {
            this.setupModal(modal);
        });
    },

    // Setup individual modal
    setupModal(modal) {
        const modalId = modal.id || modal.getAttribute('data-modal');

        // Ensure modal has proper structure
        if (!modal.querySelector('.modal-backdrop')) {
            modal.innerHTML = `
                <div class="modal-backdrop fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40"></div>
                <div class="modal-container fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="modal-content bg-white rounded-lg shadow-xl max-w-lg w-full mx-auto transform transition-all">
                        ${modal.innerHTML}
                    </div>
                </div>
            `;
        }

        // Hide modal initially
        modal.classList.add('hidden');

        // Setup backdrop click handler
        const backdrop = modal.querySelector('.modal-backdrop');
        if (backdrop && this.config.closeOnBackdrop) {
            backdrop.addEventListener('click', () => {
                this.close(modalId);
            });
        }
    },

    // Open modal
    open(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) {
            console.error(`Modal with ID "${modalId}" not found`);
            return false;
        }

        // Setup if not already done
        if (!modal.querySelector('.modal-backdrop')) {
            this.setupModal(modal);
        }

        // Add to stack
        this.modalStack.push({
            id: modalId,
            element: modal,
            options: options
        });

        // Show modal
        modal.classList.remove('hidden');
        document.body.classList.add('modal-open', 'overflow-hidden');

        // Focus management
        this.setFocus(modal);

        // Animation
        if (this.config.animations) {
            this.animateIn(modal);
        }

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:opened', {
            detail: { modalId, options }
        }));

        return true;
    },

    // Close specific modal
    close(modalId) {
        const stackIndex = this.modalStack.findIndex(item => item.id === modalId);
        if (stackIndex === -1) return false;

        const modalData = this.modalStack[stackIndex];
        const modal = modalData.element;

        // Animation
        if (this.config.animations) {
            this.animateOut(modal, () => {
                this.finalizeClose(modalData, stackIndex);
            });
        } else {
            this.finalizeClose(modalData, stackIndex);
        }

        return true;
    },

    // Close top modal
    closeTop() {
        if (this.modalStack.length === 0) return false;

        const topModal = this.modalStack[this.modalStack.length - 1];
        return this.close(topModal.id);
    },

    // Close all modals
    closeAll() {
        while (this.modalStack.length > 0) {
            this.closeTop();
        }
    },

    // Finalize modal close
    finalizeClose(modalData, stackIndex) {
        const modal = modalData.element;

        // Hide modal
        modal.classList.add('hidden');

        // Remove from stack
        this.modalStack.splice(stackIndex, 1);

        // Manage body class
        if (this.modalStack.length === 0) {
            document.body.classList.remove('modal-open', 'overflow-hidden');
        }

        // Restore focus
        this.restoreFocus();

        // Trigger custom event
        modal.dispatchEvent(new CustomEvent('modal:closed', {
            detail: { modalId: modalData.id }
        }));
    },

    // Animate modal in
    animateIn(modal) {
        const backdrop = modal.querySelector('.modal-backdrop');
        const content = modal.querySelector('.modal-content');

        if (backdrop) {
            backdrop.style.opacity = '0';
            setTimeout(() => {
                backdrop.style.opacity = '1';
            }, 10);
        }

        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95) translateY(-10px)';
            setTimeout(() => {
                content.style.opacity = '1';
                content.style.transform = 'scale(1) translateY(0)';
            }, 10);
        }
    },

    // Animate modal out
    animateOut(modal, callback) {
        const backdrop = modal.querySelector('.modal-backdrop');
        const content = modal.querySelector('.modal-content');

        if (backdrop) {
            backdrop.style.opacity = '0';
        }

        if (content) {
            content.style.opacity = '0';
            content.style.transform = 'scale(0.95) translateY(-10px)';
        }

        setTimeout(callback, 150);
    },

    // Set focus to modal
    setFocus(modal) {
        // Store currently focused element
        this.previouslyFocused = document.activeElement;

        // Focus first focusable element in modal
        const focusableElements = modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length > 0) {
            focusableElements[0].focus();
        }
    },

    // Restore focus
    restoreFocus() {
        if (this.previouslyFocused && this.modalStack.length === 0) {
            this.previouslyFocused.focus();
            this.previouslyFocused = null;
        }
    },

    // Create modal programmatically
    create(options = {}) {
        const defaults = {
            id: 'modal-' + Date.now(),
            title: '',
            content: '',
            size: 'medium', // small, medium, large, xl
            buttons: [],
            closeButton: true
        };

        const config = { ...defaults, ...options };
        const modalId = config.id;

        // Create modal HTML
        const modalHTML = this.createModalHTML(config);

        // Create modal element
        const modalElement = document.createElement('div');
        modalElement.id = modalId;
        modalElement.setAttribute('data-modal', modalId);
        modalElement.className = 'modal fixed inset-0 z-50 hidden';
        modalElement.innerHTML = modalHTML;

        // Add to DOM
        document.body.appendChild(modalElement);

        // Setup the modal
        this.setupModal(modalElement);

        return modalId;
    },

    // Create modal HTML
    createModalHTML(config) {
        const isRtl = window.isRtl || document.dir === 'rtl';

        const sizeClasses = {
            small: 'max-w-md',
            medium: 'max-w-lg',
            large: 'max-w-2xl',
            xl: 'max-w-4xl'
        };

        const sizeClass = sizeClasses[config.size] || sizeClasses.medium;

        const buttonsHTML = config.buttons.map(button => `
            <button type="button"
                    class="${button.class || 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium'}"
                    onclick="${button.onclick || ''}">
                ${button.text}
            </button>
        `).join('');

        return `
            <div class="modal-backdrop fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40"></div>
            <div class="modal-container fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="modal-content bg-white rounded-lg shadow-xl ${sizeClass} w-full mx-auto transform transition-all">
                    ${config.title ? `
                        <div class="modal-header px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                            <h3 class="text-lg font-medium text-gray-900">${config.title}</h3>
                            ${config.closeButton ? `
                                <button type="button" data-modal-close="${config.id}" class="text-gray-400 hover:text-gray-600">
                                    <i class="fas fa-times"></i>
                                </button>
                            ` : ''}
                        </div>
                    ` : ''}

                    <div class="modal-body px-6 py-4">
                        ${config.content}
                    </div>

                    ${config.buttons.length > 0 ? `
                        <div class="modal-footer px-6 py-4 bg-gray-50 ${isRtl ? 'text-left' : 'text-right'} rounded-b-lg">
                            <div class="flex ${isRtl ? 'flex-row-reverse' : 'flex-row'} space-x-3 ${isRtl ? 'space-x-reverse' : ''}">
                                ${buttonsHTML}
                            </div>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    },

    // Confirm dialog
    confirm(options = {}) {
        const defaults = {
            title: 'Confirm Action',
            message: 'Are you sure you want to continue?',
            confirmText: 'Confirm',
            cancelText: 'Cancel',
            confirmClass: 'bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium',
            cancelClass: 'bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium'
        };

        const config = { ...defaults, ...options };

        return new Promise((resolve) => {
            const modalId = this.create({
                title: config.title,
                content: `<p class="text-gray-600">${config.message}</p>`,
                buttons: [
                    {
                        text: config.cancelText,
                        class: config.cancelClass,
                        onclick: `ModalDialogs.close('${modalId}'); ModalDialogs.resolveConfirm(false);`
                    },
                    {
                        text: config.confirmText,
                        class: config.confirmClass,
                        onclick: `ModalDialogs.close('${modalId}'); ModalDialogs.resolveConfirm(true);`
                    }
                ]
            });

            this.confirmResolve = resolve;
            this.open(modalId);
        });
    },

    // Resolve confirm dialog
    resolveConfirm(result) {
        if (this.confirmResolve) {
            this.confirmResolve(result);
            this.confirmResolve = null;
        }
    },

    // Alert dialog
    alert(message, title = 'Alert') {
        return new Promise((resolve) => {
            const modalId = this.create({
                title: title,
                content: `<p class="text-gray-600">${message}</p>`,
                buttons: [
                    {
                        text: 'OK',
                        class: 'bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium',
                        onclick: `ModalDialogs.close('${modalId}'); ModalDialogs.resolveAlert();`
                    }
                ]
            });

            this.alertResolve = resolve;
            this.open(modalId);
        });
    },

    // Resolve alert dialog
    resolveAlert() {
        if (this.alertResolve) {
            this.alertResolve();
            this.alertResolve = null;
        }
    },

    // Loading modal
    loading(message = 'Loading...', title = null) {
        const modalId = this.create({
            title: title,
            content: `
                <div class="text-center py-8">
                    <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mb-4"></div>
                    <p class="text-gray-600">${message}</p>
                </div>
            `,
            closeButton: false
        });

        this.open(modalId);
        return modalId;
    },

    // Remove modal from DOM
    remove(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            this.close(modalId);
            setTimeout(() => {
                modal.remove();
            }, 200);
        }
    }
};

// Auto-initialize
document.addEventListener('DOMContentLoaded', function() {
    ModalDialogs.init();
});

// Global shortcuts
window.modal = {
    open: ModalDialogs.open.bind(ModalDialogs),
    close: ModalDialogs.close.bind(ModalDialogs),
    closeAll: ModalDialogs.closeAll.bind(ModalDialogs),
    create: ModalDialogs.create.bind(ModalDialogs),
    confirm: ModalDialogs.confirm.bind(ModalDialogs),
    alert: ModalDialogs.alert.bind(ModalDialogs),
    loading: ModalDialogs.loading.bind(ModalDialogs),
    remove: ModalDialogs.remove.bind(ModalDialogs)
};

// Export for module systems
window.ModalDialogs = ModalDialogs;
