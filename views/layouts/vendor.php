<?php
/**
 * File path: views/layouts/vendor.php
 * Vendor Dashboard Layout
 *
 * This file serves as the main layout template for the vendor dashboard area.
 * It includes the necessary structure, sidebar navigation, and common elements
 * for all vendor dashboard pages.
 */

// Get current language and direction
$isRtl = $this->localization->isRtl();
$dirAttribute = $isRtl ? 'dir="rtl"' : '';
$language = $this->localization->getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?= $language ?>" <?= $dirAttribute ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? $this->localization->t('vendor.dashboard') ?> - <?= $this->localization->t('general.site_name') ?></title>
    <link rel="stylesheet" href="/assets/css/tailwind.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php if ($isRtl): ?>
    <link rel="stylesheet" href="/assets/css/rtl.css">
    <?php endif; ?>
    <link rel="stylesheet" href="/assets/css/custom.css">
</head>
<body class="bg-gray-100 min-h-screen">
    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="<?= $isRtl ? 'border-l' : 'border-r' ?> border-gray-200 bg-white shadow-md w-64 fixed inset-y-0 z-10 hidden lg:block">
            <div class="flex flex-col h-full">
                <!-- Logo and brand -->
                <div class="p-4 border-b border-gray-200">
                    <a href="/" class="flex items-center">
                        <img src="/assets/images/logo.svg" alt="Logo" class="h-8 w-auto">
                        <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-semibold"><?= $this->localization->t('general.site_name') ?></span>
                    </a>
                </div>

                <!-- Vendor info -->
                <div class="p-4 border-b border-gray-200">
                    <?php
                    $vendor = $this->vendor->getByUserId($this->auth->getCurrentUser()['id'], $language);
                    $vendorName = $vendor["company_name_{$language}"] ?? $this->auth->getCurrentUser()['name'];
                    ?>
                    <div class="flex items-center">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            <?= substr($vendorName, 0, 1) ?>
                        </div>
                        <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                            <p class="font-medium"><?= htmlspecialchars($vendorName) ?></p>
                            <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.dashboard') ?></p>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="py-4 flex-grow">
                    <ul>
                        <li>
                            <a href="/vendor/dashboard" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'dashboard' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.dashboard') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/services" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'services' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-print w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.services') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/quotes" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'quotes' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.quote_requests') ?></span>
                                <?php if (isset($pendingQuotes) && $pendingQuotes > 0): ?>
                                <span class="<?= $isRtl ? 'mr-auto' : 'ml-auto' ?> bg-red-500 text-white text-xs rounded-full px-2 py-1"><?= $pendingQuotes ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/orders" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'orders' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-shopping-cart w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.orders') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/analytics" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'analytics' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-chart-line w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.analytics') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/subscription" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'subscription' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-star w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.subscription') ?></span>
                            </a>
                        </li>
                        <li>
                            <a href="/vendor/profile" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'profile' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                <i class="fas fa-user-cog w-5 text-center"></i>
                                <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.profile') ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>

                <!-- Logout -->
                <div class="p-4 border-t border-gray-200 mt-auto">
                    <a href="/auth/logout" class="flex items-center text-red-600 hover:text-red-800">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('auth.logout') ?></span>
                    </a>
                </div>
            </div>
        </aside>

        <!-- Mobile sidebar -->
        <div class="lg:hidden">
            <div class="fixed inset-0 z-20 bg-black bg-opacity-50 transition-opacity hidden" id="sidebarOverlay"></div>

            <aside class="<?= $isRtl ? 'right-0 border-l' : 'left-0 border-r' ?> border-gray-200 bg-white shadow-md w-64 fixed inset-y-0 z-30 transform transition-transform duration-300 -translate-x-full" id="mobileSidebar">
                <div class="flex flex-col h-full">
                    <!-- Close button -->
                    <div class="p-4 flex justify-between items-center border-b border-gray-200">
                        <a href="/" class="flex items-center">
                            <img src="/assets/images/logo.svg" alt="Logo" class="h-8 w-auto">
                            <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-semibold"><?= $this->localization->t('general.site_name') ?></span>
                        </a>
                        <button id="closeSidebar" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Vendor info -->
                    <div class="p-4 border-b border-gray-200">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                <?= substr($vendorName, 0, 1) ?>
                            </div>
                            <div class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>">
                                <p class="font-medium"><?= htmlspecialchars($vendorName) ?></p>
                                <p class="text-sm text-gray-500"><?= $this->localization->t('vendor.dashboard') ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation -->
                    <nav class="py-4 flex-grow">
                        <ul>
                            <li>
                                <a href="/vendor/dashboard" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'dashboard' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-tachometer-alt w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.dashboard') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/services" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'services' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-print w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.services') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/quotes" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'quotes' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-file-invoice-dollar w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.quote_requests') ?></span>
                                    <?php if (isset($pendingQuotes) && $pendingQuotes > 0): ?>
                                    <span class="<?= $isRtl ? 'mr-auto' : 'ml-auto' ?> bg-red-500 text-white text-xs rounded-full px-2 py-1"><?= $pendingQuotes ?></span>
                                    <?php endif; ?>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/orders" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'orders' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-shopping-cart w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.orders') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/analytics" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'analytics' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-chart-line w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.analytics') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/subscription" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'subscription' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-star w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.subscription') ?></span>
                                </a>
                            </li>
                            <li>
                                <a href="/vendor/profile" class="flex items-center px-4 py-3 <?= $isRtl ? 'pr-6' : 'pl-6' ?> hover:bg-blue-50 <?= $currentPage === 'profile' ? 'bg-blue-50 text-blue-600 border-r-4 border-blue-600' : 'text-gray-700' ?>">
                                    <i class="fas fa-user-cog w-5 text-center"></i>
                                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('vendor.profile') ?></span>
                                </a>
                            </li>
                        </ul>
                    </nav>

                    <!-- Logout -->
                    <div class="p-4 border-t border-gray-200 mt-auto">
                        <a href="/auth/logout" class="flex items-center text-red-600 hover:text-red-800">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?>"><?= $this->localization->t('auth.logout') ?></span>
                        </a>
                    </div>
                </div>
            </aside>
        </div>

        <!-- Main content -->
        <div class="lg:ml-64 flex-1">
            <!-- Top bar -->
            <header class="bg-white shadow-sm h-16 flex items-center px-6 sticky top-0 z-10">
                <button id="openSidebar" class="lg:hidden text-gray-500 hover:text-gray-700 mr-4">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <h1 class="text-2xl font-semibold text-gray-800"><?= $pageTitle ?? $this->localization->t('vendor.dashboard') ?></h1>
                    </div>

                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <div class="relative">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bell"></i>
                                <?php if (isset($notificationCount) && $notificationCount > 0): ?>
                                <span class="absolute top-0 right-0 -mt-1 -mr-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center"><?= $notificationCount > 9 ? '9+' : $notificationCount ?></span>
                                <?php endif; ?>
                            </button>
                        </div>

                        <!-- Language selector -->
                        <div class="relative">
                            <button class="text-gray-500 hover:text-gray-700 focus:outline-none" id="languageDropdownButton">
                                <i class="fas fa-globe"></i>
                                <span class="ml-1"><?= strtoupper($language) ?></span>
                            </button>
                            <div class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-lg hidden z-20" id="languageDropdown">
                                <a href="?lang=en" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $language === 'en' ? 'bg-gray-100' : '' ?>">English</a>
                                <a href="?lang=ar" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 <?= $language === 'ar' ? 'bg-gray-100' : '' ?>">العربية</a>
                            </div>
                        </div>

                        <!-- Profile dropdown -->
                        <div class="relative">
                            <button class="flex items-center text-gray-500 hover:text-gray-700 focus:outline-none" id="profileDropdownButton">
                                <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                    <?= substr($vendorName, 0, 1) ?>
                                </div>
                                <span class="ml-2"><?= htmlspecialchars($vendorName) ?></span>
                                <i class="fas fa-chevron-down ml-2"></i>
                            </button>
                            <div class="absolute right-0 mt-2 py-2 w-48 bg-white rounded-md shadow-lg hidden z-20" id="profileDropdown">
                                <a href="/vendor/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    <i class="fas fa-user-cog mr-2"></i> <?= $this->localization->t('vendor.profile') ?>
                                </a>
                                <a href="/auth/logout" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                    <i class="fas fa-sign-out-alt mr-2"></i> <?= $this->localization->t('auth.logout') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page content -->
            <main class="p-6">
                <?php if (isset($flashMessage)): ?>
                <div class="bg-<?= $flashMessage['type'] ?>-100 border-l-4 border-<?= $flashMessage['type'] ?>-500 text-<?= $flashMessage['type'] ?>-700 p-4 mb-6">
                    <p><?= $flashMessage['message'] ?></p>
                </div>
                <?php endif; ?>

                <?= $content ?>
            </main>
        </div>
    </div>

    <script src="/assets/js/main.js"></script>
    <script src="/assets/js/vendor-dashboard.js"></script>
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
        <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Mobile sidebar toggle
        const openSidebar = document.getElementById('openSidebar');
        const closeSidebar = document.getElementById('closeSidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const mobileSidebar = document.getElementById('mobileSidebar');

        openSidebar.addEventListener('click', () => {
            mobileSidebar.classList.remove('<?= $isRtl ? "translate-x-full" : "-translate-x-full" ?>');
            sidebarOverlay.classList.remove('hidden');
        });

        function closeMobileSidebar() {
            mobileSidebar.classList.add('<?= $isRtl ? "translate-x-full" : "-translate-x-full" ?>');
            sidebarOverlay.classList.add('hidden');
        }

        closeSidebar.addEventListener('click', closeMobileSidebar);
        sidebarOverlay.addEventListener('click', closeMobileSidebar);

        // Language dropdown
        const languageDropdownButton = document.getElementById('languageDropdownButton');
        const languageDropdown = document.getElementById('languageDropdown');

        languageDropdownButton.addEventListener('click', () => {
            languageDropdown.classList.toggle('hidden');
        });

        // Profile dropdown
        const profileDropdownButton = document.getElementById('profileDropdownButton');
        const profileDropdown = document.getElementById('profileDropdown');

        profileDropdownButton.addEventListener('click', () => {
            profileDropdown.classList.toggle('hidden');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', (event) => {
            if (!languageDropdownButton.contains(event.target) && !languageDropdown.contains(event.target)) {
                languageDropdown.classList.add('hidden');
            }

            if (!profileDropdownButton.contains(event.target) && !profileDropdown.contains(event.target)) {
                profileDropdown.classList.add('hidden');
            }
        });
    </script>
</body>
</html>
