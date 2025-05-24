/**
 * User Dashboard JavaScript
 * File path: assets/js/user-dashboard.js
 */

const UserDashboard = {

    // Initialize dashboard functionality
    init() {
        this.initializeDropdowns();
        this.initializeNotifications();
        this.initializeMobileMenu();
        this.initializeCharts();
        this.initializeLanguageSwitcher();
        this.loadNotifications();
    },

    // Initialize dropdown menus
    initializeDropdowns() {
        // User menu dropdown
        const userButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        if (userButton && userDropdown) {
            userButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown(userDropdown);
            });
        }

        // Language dropdown
        const languageButton = document.getElementById('language-button');
        const languageDropdown = document.getElementById('language-dropdown');

        if (languageButton && languageDropdown) {
            languageButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown(languageDropdown);
            });
        }

        // Notifications dropdown
        const notificationsButton = document.getElementById('notifications-button');
        const notificationsDropdown = document.getElementById('notifications-dropdown');

        if (notificationsButton && notificationsDropdown) {
            notificationsButton.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown(notificationsDropdown);
                this.loadNotifications();
            });
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', () => {
            this.closeAllDropdowns();
        });

        // Prevent dropdown close when clicking inside
        document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
            dropdown.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });
    },

    // Toggle dropdown visibility
    toggleDropdown(dropdown) {
        const isHidden = dropdown.classList.contains('hidden');
        this.closeAllDropdowns();

        if (isHidden) {
            dropdown.classList.remove('hidden');
        }
    },

    // Close all dropdowns
    closeAllDropdowns() {
        document.querySelectorAll('[id$="-dropdown"]').forEach(dropdown => {
            dropdown.classList.add('hidden');
        });
    },

    // Initialize mobile menu
    initializeMobileMenu() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenuOverlay = document.getElementById('mobile-menu-overlay');
        const mobileMenuClose = document.getElementById('mobile-menu-close');
        const mobileMenuBackdrop = document.getElementById('mobile-menu-backdrop');

        if (mobileMenuButton && mobileMenuOverlay) {
            mobileMenuButton.addEventListener('click', () => {
                mobileMenuOverlay.classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        }

        // Close mobile menu
        const closeMobileMenu = () => {
            if (mobileMenuOverlay) {
                mobileMenuOverlay.classList.add('hidden');
                document.body.classList.remove('overflow-hidden');
            }
        };

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', closeMobileMenu);
        }

        if (mobileMenuBackdrop) {
            mobileMenuBackdrop.addEventListener('click', closeMobileMenu);
        }

        // Close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeMobileMenu();
            }
        });
    },

    // Initialize notifications functionality
    initializeNotifications() {
        const markAllReadButton = document.getElementById('mark-all-read');

        if (markAllReadButton) {
            markAllReadButton.addEventListener('click', () => {
                this.markAllNotificationsRead();
            });
        }
    },

    // Load notifications
    async loadNotifications() {
        try {
            const response = await fetch('/user/notifications', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.renderNotifications(data.notifications);
                this.updateNotificationCount(data.unread_count);
            }
        } catch (error) {
            console.error('Error loading notifications:', error);
        }
    },

    // Render notifications in dropdown
    renderNotifications(notifications) {
        const notificationsList = document.getElementById('notifications-list');

        if (!notificationsList) return;

        if (notifications.length === 0) {
            notificationsList.innerHTML = `
                <div class="p-6 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-3xl mb-3"></i>
                    <p class="text-gray-500">${this.t('user.no_notifications')}</p>
                </div>
            `;
            return;
        }

        const notificationsHtml = notifications.map(notification => `
            <div class="p-3 hover:bg-gray-50 cursor-pointer ${notification.read_at ? '' : 'bg-blue-50'}"
                 onclick="UserDashboard.markNotificationRead(${notification.id})">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <div class="w-6 h-6 rounded-full bg-blue-100 flex items-center justify-center">
                            <i class="fas fa-bell text-blue-600 text-xs"></i>
                        </div>
                    </div>
                    <div class="${window.isRtl ? 'mr-3' : 'ml-3'} flex-1">
                        <p class="text-sm font-medium text-gray-900 line-clamp-1">
                            ${notification[`title_${window.currentLanguage}`]}
                        </p>
                        <p class="text-sm text-gray-600 line-clamp-2 mt-1">
                            ${notification[`message_${window.currentLanguage}`]}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">
                            ${this.formatDate(notification.created_at)}
                        </p>
                    </div>
                    ${!notification.read_at ? '<div class="w-2 h-2 bg-blue-600 rounded-full"></div>' : ''}
                </div>
            </div>
        `).join('');

        notificationsList.innerHTML = notificationsHtml;
    },

    // Update notification count badge
    updateNotificationCount(count) {
        const notificationCount = document.getElementById('notification-count');

        if (notificationCount) {
            if (count > 0) {
                notificationCount.textContent = count > 99 ? '99+' : count;
                notificationCount.style.display = 'flex';
            } else {
                notificationCount.style.display = 'none';
            }
        }
    },

    // Mark single notification as read
    async markNotificationRead(notificationId) {
        try {
            const response = await fetch('/user/notifications/mark-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    notification_id: notificationId
                })
            });

            if (response.ok) {
                this.loadNotifications();
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    },

    // Mark all notifications as read
    async markAllNotificationsRead() {
        try {
            const response = await fetch('/user/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (response.ok) {
                this.loadNotifications();
                this.showToast('All notifications marked as read', 'success');
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
            this.showToast('Error updating notifications', 'error');
        }
    },

    // Initialize language switcher
    initializeLanguageSwitcher() {
        document.querySelectorAll('[data-language]').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const language = e.target.getAttribute('data-language');
                this.switchLanguage(language);
            });
        });
    },

    // Switch language
    async switchLanguage(language) {
        try {
            const response = await fetch('/language/switch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    language: language
                })
            });

            if (response.ok) {
                window.location.reload();
            }
        } catch (error) {
            console.error('Error switching language:', error);
        }
    },

    // Initialize charts
    initializeCharts() {
        // Activity Chart
        const activityChart = document.getElementById('activityChart');
        if (activityChart && window.chartData) {
            this.initializeActivityChart(activityChart);
        }
    },

    // Initialize activity chart
    initializeActivityChart(canvas) {
        const ctx = canvas.getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: window.chartData.months,
                datasets: [{
                    label: this.t('user.quote_requests'),
                    data: window.chartData.quotes,
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    pointRadius: 3
                }, {
                    label: this.t('user.orders'),
                    data: window.chartData.orders,
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(16, 185, 129, 1)',
                    pointRadius: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: window.isRtl ? 'right' : 'left'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    },

    // Show toast notification
    showToast(message, type = 'info', duration = 3000) {
        const toastContainer = document.getElementById('toast-container');
        if (!toastContainer) return;

        const toastId = 'toast-' + Date.now();
        const iconClass = {
            success: 'fa-check-circle text-green-500',
            error: 'fa-exclamation-circle text-red-500',
            warning: 'fa-exclamation-triangle text-yellow-500',
            info: 'fa-info-circle text-blue-500'
        }[type] || 'fa-info-circle text-blue-500';

        const bgClass = {
            success: 'bg-green-50 border-green-200',
            error: 'bg-red-50 border-red-200',
            warning: 'bg-yellow-50 border-yellow-200',
            info: 'bg-blue-50 border-blue-200'
        }[type] || 'bg-blue-50 border-blue-200';

        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `${bgClass} border rounded-md p-4 shadow-lg transform transition-all duration-300 translate-x-full opacity-0`;

        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${iconClass} ${window.isRtl ? 'ml-3' : 'mr-3'}"></i>
                <p class="text-sm font-medium text-gray-900 flex-1">${message}</p>
                <button type="button" onclick="UserDashboard.removeToast('${toastId}')" class="${window.isRtl ? 'mr-2' : 'ml-2'} text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);

        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
        }, 100);

        // Auto remove
        setTimeout(() => {
            this.removeToast(toastId);
        }, duration);
    },

    // Remove toast notification
    removeToast(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                toast.remove();
            }, 300);
        }
    },

    // Format date for display
    formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffInMs = now - date;
        const diffInHours = diffInMs / (1000 * 60 * 60);

        if (diffInHours < 1) {
            const minutes = Math.floor(diffInMs / (1000 * 60));
            return `${minutes} ${this.t('general.minutes_ago')}`;
        } else if (diffInHours < 24) {
            const hours = Math.floor(diffInHours);
            return `${hours} ${this.t('general.hours_ago')}`;
        } else if (diffInHours < 168) { // 7 days
            const days = Math.floor(diffInHours / 24);
            return `${days} ${this.t('general.days_ago')}`;
        } else {
            return date.toLocaleDateString(window.currentLanguage === 'ar' ? 'ar-EG' : 'en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }
    },

    // Translation helper (placeholder - would integrate with actual translation system)
    t(key) {
        // This would normally integrate with the server-side translation system
        // For now, return the key as fallback
        const translations = {
            'user.no_notifications': 'No notifications',
            'user.quote_requests': 'Quote Requests',
            'user.orders': 'Orders',
            'general.minutes_ago': 'minutes ago',
            'general.hours_ago': 'hours ago',
            'general.days_ago': 'days ago'
        };

        return translations[key] || key;
    }
};

// Auto-initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    UserDashboard.init();
});

// Export for use in other scripts
window.UserDashboard = UserDashboard;
