<?php
/**
 * Admin Dashboard Layout
 * File path: views/layouts/admin.php
 */
?>
<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? $localization->t('admin.dashboard') ?> - <?= $localization->t('general.site_name') ?></title>

    <!-- Tailwind CSS -->
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
<body class="bg-gray-50 <?= $isRtl ? 'font-arabic' : '' ?>">
    <!-- Admin Header -->
    <header class="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-30">
        <div class="flex items-center justify-between px-4 py-3">
            <!-- Mobile menu button -->
            <button id="mobile-menu-btn" class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100">
                <i class="fas fa-bars text-xl"></i>
            </button>

            <!-- Logo -->
            <div class="flex items-center">
                <a href="/admin" class="flex items-center">
                    <img src="/assets/images/logo.svg" alt="Logo" class="h-8 w-auto">
                    <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-semibold text-gray-900 hidden sm:block">
                        <?= $localization->t('general.site_name') ?>
                    </span>
                </a>
            </div>

            <!-- Header actions -->
            <div class="flex items-center space-x-4 <?= $isRtl ? 'space-x-reverse' : '' ?>">
                <!-- Language Switcher -->
                <div class="relative">
                    <button id="language-toggle" class="flex items-center p-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-globe <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                        <span class="hidden sm:inline"><?= strtoupper($currentLanguage) ?></span>
                    </button>

                    <div id="language-menu" class="hidden absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="#" data-language="en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $currentLanguage === 'en' ? 'bg-gray-50 font-medium' : '' ?>">
                                <i class="fas fa-check <?= $isRtl ? 'ml-2' : 'mr-2' ?> <?= $currentLanguage === 'en' ? 'text-blue-600' : 'text-transparent' ?>"></i>
                                English
                            </a>
                            <a href="#" data-language="ar" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $currentLanguage === 'ar' ? 'bg-gray-50 font-medium' : '' ?>">
                                <i class="fas fa-check <?= $isRtl ? 'ml-2' : 'mr-2' ?> <?= $currentLanguage === 'ar' ? 'text-blue-600' : 'text-transparent' ?>"></i>
                                العربية
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Notifications -->
                <div class="relative">
                    <button class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <i class="fas fa-bell text-lg"></i>
                        <span class="absolute -top-1 -<?= $isRtl ? 'left' : 'right' ?>-1 h-4 w-4 bg-red-500 text-white text-xs rounded-full flex items-center justify-center">3</span>
                    </button>
                </div>

                <!-- Admin Profile -->
                <div class="relative">
                    <button id="admin-menu-btn" class="flex items-center p-2 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-md">
                        <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center <?= $isRtl ? 'ml-2' : 'mr-2' ?>">
                            <i class="fas fa-user text-white text-sm"></i>
                        </div>
                        <span class="hidden sm:inline"><?= $auth->getCurrentUser()['name'] ?></span>
                        <i class="fas fa-chevron-down <?= $isRtl ? 'mr-1' : 'ml-1' ?> text-xs"></i>
                    </button>

                    <div id="admin-menu" class="hidden absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5">
                        <div class="py-1">
                            <a href="/admin/settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                <?= $localization->t('admin.settings') ?>
                            </a>
                            <a href="/auth/logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                                <?= $localization->t('auth.logout') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside id="admin-sidebar" class="<?= $isRtl ? 'border-l' : 'border-r' ?> border-gray-200 bg-white shadow-md w-64 fixed inset-y-0 z-20 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full pt-16">
            <!-- Navigation -->
            <nav class="py-4 flex-grow overflow-y-auto">
                <ul class="space-y-1">
                    <li>
                        <a href="/admin" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'dashboard' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-tachometer-alt w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.dashboard') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/users" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'users' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-users w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.users') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/vendors" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'vendors' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-store w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.vendors') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/services" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'services' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-list w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.services') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/orders" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'orders' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-shopping-cart w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.orders') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/categories" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'categories' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-tags w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.categories') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <a href="/admin/reports" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'reports' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.reports') ?>
                            </span>
                        </a>
                    </li>

                    <li>
                        <hr class="my-2 border-gray-200">
                    </li>

                    <li>
                        <a href="/admin/settings" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 transition-colors <?= ($activeMenu ?? '') === 'settings' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700 hover:text-gray-900' ?>">
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <?= $localization->t('admin.settings') ?>
                            </span>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer -->
            <div class="p-4 border-t border-gray-200">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center <?= $isRtl ? 'ml-3' : 'mr-3' ?>">
                        <i class="fas fa-user text-white text-sm"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">
                            <?= $auth->getCurrentUser()['name'] ?>
                        </p>
                        <p class="text-xs text-gray-500">
                            <?= $localization->t('admin.administrator') ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="lg:<?= $isRtl ? 'pr' : 'pl' ?>-64 pt-16 min-h-screen">
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            <!-- Flash Messages -->
            <?php if ($session->hasFlash('success')): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <span><?= $session->getFlash('success') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($session->hasFlash('error')): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-md">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <span><?= $session->getFlash('error') ?></span>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Page Content -->
            <div class="content">
                <?php echo $content ?? ''; ?>
            </div>
        </div>
    </main>

    <!-- Mobile Sidebar Overlay -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-10 hidden lg:hidden"></div>

    <!-- Scripts -->
    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/admin-dashboard.js"></script>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu-btn').addEventListener('click', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });

        // Close sidebar when clicking overlay
        document.getElementById('sidebar-overlay').addEventListener('click', function() {
            const sidebar = document.getElementById('admin-sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });

        // Language toggle
        document.getElementById('language-toggle').addEventListener('click', function() {
            document.getElementById('language-menu').classList.toggle('hidden');
        });

        // Admin menu toggle
        document.getElementById('admin-menu-btn').addEventListener('click', function() {
            document.getElementById('admin-menu').classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            const languageToggle = document.getElementById('language-toggle');
            const languageMenu = document.getElementById('language-menu');
            const adminToggle = document.getElementById('admin-menu-btn');
            const adminMenu = document.getElementById('admin-menu');

            if (!languageToggle.contains(event.target)) {
                languageMenu.classList.add('hidden');
            }

            if (!adminToggle.contains(event.target)) {
                adminMenu.classList.add('hidden');
            }
        });

        // Language switching
        document.querySelectorAll('[data-language]').forEach(function(link) {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const selectedLanguage = this.getAttribute('data-language');

                // Create and submit form to change language
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '/language/switch';

                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'language';
                input.value = selectedLanguage;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = 'csrf_token';
                csrfInput.value = '<?= $csrfToken ?>';

                form.appendChild(input);
                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            });
        });
    </script>
</body>
</html>
