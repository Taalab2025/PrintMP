<?php
/**
 * User Dashboard Layout
 * File path: views/layouts/dashboard.php
 */
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" class="h-full bg-gray-50">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? $this->localization->t('general.site_name')) ?> - <?= $this->localization->t('general.site_name') ?></title>

    <!-- CSS -->
    <link href="/assets/css/tailwind-input.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
    <?php if ($isRtl): ?>
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        <!-- Dashboard Header -->
        <header class="bg-white shadow-sm border-b border-gray-200">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo and Navigation Toggle -->
                    <div class="flex items-center">
                        <button type="button" class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100" id="mobile-menu-button">
                            <span class="sr-only"><?= $this->localization->t('general.open_menu') ?></span>
                            <i class="fas fa-bars text-lg"></i>
                        </button>

                        <div class="<?= $isRtl ? 'mr-4 lg:mr-0' : 'ml-4 lg:ml-0' ?> flex-shrink-0 flex items-center">
                            <a href="/" class="flex items-center">
                                <img class="h-8 w-auto" src="/assets/images/logo.svg" alt="<?= $this->localization->t('general.site_name') ?>">
                                <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-semibold text-gray-900 hidden sm:block">
                                    <?= $this->localization->t('general.site_name') ?>
                                </span>
                            </a>
                        </div>
                    </div>

                    <!-- Header Actions -->
                    <div class="flex items-center space-x-4 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                        <!-- Language Switcher -->
                        <div class="relative">
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-500" id="language-button">
                                <i class="fas fa-globe text-lg"></i>
                                <span class="<?= $isRtl ? 'mr-1' : 'ml-1' ?> text-sm font-medium">
                                    <?= strtoupper($currentLanguage) ?>
                                </span>
                            </button>

                            <div class="hidden absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10" id="language-dropdown">
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-language="en">
                                    <span class="fi fi-us <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></span>
                                    English
                                </a>
                                <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" data-language="ar">
                                    <span class="fi fi-eg <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></span>
                                    العربية
                                </a>
                            </div>
                        </div>

                        <!-- Notifications -->
                        <div class="relative">
                            <button type="button" class="p-2 text-gray-400 hover:text-gray-500 relative" id="notifications-button">
                                <i class="fas fa-bell text-lg"></i>
                                <span class="absolute -top-1 <?= $isRtl ? '-left-1' : '-right-1' ?> h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center" id="notification-count" style="display: none;">0</span>
                            </button>

                            <div class="hidden absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-80 bg-white rounded-md shadow-lg py-1 z-10" id="notifications-dropdown">
                                <div class="px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                                    <h3 class="text-sm font-medium text-gray-900"><?= $this->localization->t('user.notifications') ?></h3>
                                    <button type="button" class="text-xs text-blue-600 hover:text-blue-500" id="mark-all-read">
                                        <?= $this->localization->t('user.mark_all_read') ?>
                                    </button>
                                </div>
                                <div class="max-h-64 overflow-y-auto" id="notifications-list">
                                    <!-- Notifications will be loaded here -->
                                </div>
                            </div>
                        </div>

                        <!-- User Menu -->
                        <div class="relative">
                            <button type="button" class="flex items-center p-2 text-sm rounded-full text-gray-400 hover:text-gray-500" id="user-menu-button">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-gray-700 font-medium hidden sm:block">
                                    <?= htmlspecialchars($user['name']) ?>
                                </span>
                                <i class="fas fa-chevron-down <?= $isRtl ? 'mr-1' : 'ml-1' ?> text-xs"></i>
                            </button>

                            <div class="hidden absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10" id="user-dropdown">
                                <a href="/user/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                    <?= $this->localization->t('user.profile') ?>
                                </a>
                                <a href="/quotes/history" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-file-alt <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                    <?= $this->localization->t('user.my_quotes') ?>
                                </a>
                                <a href="/orders/history" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-shopping-bag <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                    <?= $this->localization->t('user.my_orders') ?>
                                </a>
                                <div class="border-t border-gray-100"></div>
                                <a href="/auth/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                    <?= $this->localization->t('auth.logout') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Dashboard Sidebar and Content -->
        <div class="flex">
            <!-- Sidebar -->
            <aside class="hidden lg:block lg:w-64 bg-white shadow-sm border-r border-gray-200 min-h-screen">
                <nav class="mt-8 px-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="/user/dashboard" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md <?= $currentPage === 'dashboard' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                                <i class="fas fa-tachometer-alt <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                <?= $this->localization->t('user.dashboard') ?>
                            </a>
                        </li>
                        <li>
                            <a href="/user/profile" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md <?= $currentPage === 'profile' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                                <i class="fas fa-user <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                <?= $this->localization->t('user.profile') ?>
                            </a>
                        </li>
                        <li>
                            <a href="/quotes/history" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md <?= $currentPage === 'quotes' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                                <i class="fas fa-file-alt <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                <?= $this->localization->t('user.my_quotes') ?>
                                <span class="<?= $isRtl ? 'mr-auto' : 'ml-auto' ?> bg-gray-200 text-gray-600 text-xs rounded-full px-2 py-1">
                                    <?= $stats['total_quote_requests'] ?? 0 ?>
                                </span>
                            </a>
                        </li>
                        <li>
                            <a href="/orders/history" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md <?= $currentPage === 'orders' ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900' ?>">
                                <i class="fas fa-shopping-bag <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                <?= $this->localization->t('user.my_orders') ?>
                                <span class="<?= $isRtl ? 'mr-auto' : 'ml-auto' ?> bg-gray-200 text-gray-600 text-xs rounded-full px-2 py-1">
                                    <?= $stats['total_orders'] ?? 0 ?>
                                </span>
                            </a>
                        </li>
                    </ul>

                    <!-- Browse Section -->
                    <div class="mt-8">
                        <h3 class="px-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <?= $this->localization->t('user.browse') ?>
                        </h3>
                        <ul class="mt-3 space-y-2">
                            <li>
                                <a href="/services" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-th-large <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                    <?= $this->localization->t('nav.services') ?>
                                </a>
                            </li>
                            <li>
                                <a href="/vendors" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-store <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                    <?= $this->localization->t('nav.vendors') ?>
                                </a>
                            </li>
                            <li>
                                <a href="/categories" class="group flex items-center px-4 py-2 text-sm font-medium rounded-md text-gray-600 hover:bg-gray-50 hover:text-gray-900">
                                    <i class="fas fa-tags <?= $isRtl ? 'ml-3' : 'mr-3' ?> flex-shrink-0 h-4 w-4"></i>
                                    <?= $this->localization->t('nav.categories') ?>
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>
            </aside>

            <!-- Mobile Menu Overlay -->
            <div class="lg:hidden fixed inset-0 z-40 hidden" id="mobile-menu-overlay">
                <div class="fixed inset-0 bg-gray-600 bg-opacity-75" id="mobile-menu-backdrop"></div>
                <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                    <div class="absolute top-0 <?= $isRtl ? 'left-12' : 'right-12' ?> -mr-12 pt-2">
                        <button type="button" class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white" id="mobile-menu-close">
                            <span class="sr-only"><?= $this->localization->t('general.close_menu') ?></span>
                            <i class="fas fa-times text-white text-lg"></i>
                        </button>
                    </div>

                    <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                        <nav class="px-4">
                            <!-- Mobile navigation content (same as desktop) -->
                        </nav>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <main class="flex-1 min-h-screen bg-gray-50">
                <div class="py-6">
                    <?php include "views/pages/$view.php"; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Toast Notification Container -->
    <div id="toast-container" class="fixed top-4 <?= $isRtl ? 'left-4' : 'right-4' ?> z-50 space-y-2">
        <!-- Toast notifications will be inserted here -->
    </div>

    <!-- Scripts -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/user-dashboard.js"></script>

    <script>
        // Set RTL direction for JavaScript
        window.isRtl = <?= $isRtl ? 'true' : 'false' ?>;
        window.currentLanguage = '<?= $currentLanguage ?>';

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            UserDashboard.init();
        });
    </script>
</body>
</html>
