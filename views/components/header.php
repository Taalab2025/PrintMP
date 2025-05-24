<?php
/**
 * Header Component
 * file path: views/components/header.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

// Check if RTL direction is needed based on current language
$isRtl = $this->localization->isRtl();
$directionClass = $isRtl ? 'rtl' : 'ltr';
$lang = $this->localization->getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $directionClass ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? $this->localization->t('general.site_name') ?></title>

    <!-- Tailwind CSS -->
    <link href="/assets/css/tailwind.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="/assets/css/custom.css" rel="stylesheet">

    <!-- RTL Support -->
    <?php if ($isRtl): ?>
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Favicon -->
    <link rel="icon" href="/assets/images/favicon.ico">

    <!-- Alpine.js for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- CSRF Token for JavaScript -->
    <script>
        window.csrfToken = "<?= $this->session->generateCsrfToken() ?>";
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Top Header - Language Switcher & Auth Links -->
    <div class="bg-gray-800 text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <!-- Language Switcher -->
            <div class="flex items-center">
                <a href="?lang=en" class="<?= $lang === 'en' ? 'font-bold' : '' ?> mx-1">English</a>
                <span class="mx-1">|</span>
                <a href="?lang=ar" class="<?= $lang === 'ar' ? 'font-bold' : '' ?> mx-1">العربية</a>
            </div>

            <!-- Auth Links -->
            <div>
                <?php if ($this->auth->check()): ?>
                    <?php $user = $this->auth->user(); ?>
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <span class="<?= $isRtl ? 'ml-1' : 'mr-1' ?>"><?= htmlspecialchars($user['name']) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                            <div class="py-1">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $this->localization->t('nav.admin_dashboard') ?></a>
                                <?php elseif ($user['role'] === 'vendor'): ?>
                                    <a href="/vendor" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $this->localization->t('nav.vendor_dashboard') ?></a>
                                <?php else: ?>
                                    <a href="/user/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $this->localization->t('nav.dashboard') ?></a>
                                <?php endif; ?>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= $this->localization->t('nav.profile') ?></a>
                                <form action="/logout" method="POST" class="block">
                                    <input type="hidden" name="csrf_token" value="<?= $this->session->generateCsrfToken() ?>">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= $this->localization->t('nav.logout') ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="mx-2"><?= $this->localization->t('nav.login') ?></a>
                    <a href="/register" class="mx-2"><?= $this->localization->t('nav.register') ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="bg-white shadow-md py-3">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-between items-center">
                <!-- Logo -->
                <a href="/" class="flex items-center">
                    <img src="/assets/images/logo.svg" alt="<?= $this->localization->t('general.site_name') ?>" class="h-10">
                    <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-bold text-gray-800"><?= $this->localization->t('general.site_name') ?></span>
                </a>

                <!-- Mobile Menu Button -->
                <button id="mobile-menu-button" class="md:hidden flex items-center px-3 py-2 border rounded text-gray-600 border-gray-600 hover:text-gray-900 hover:border-gray-900">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <!-- Desktop Navigation Links -->
                <div id="nav-links" class="hidden md:flex flex-col md:flex-row w-full md:w-auto mt-2 md:mt-0">
                    <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.home') ?>
                    </a>
                    <a href="/categories" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.categories') ?>
                    </a>
                    <a href="/services" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.services') ?>
                    </a>
                    <a href="/vendors" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.vendors') ?>
                    </a>
                    <a href="/about" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.about') ?>
                    </a>
                    <a href="/contact" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= $this->localization->t('nav.contact') ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow">
        <?php
        // Flash Messages
        if ($this->session->hasFlash('success') || $this->session->hasFlash('error')):
        ?>
        <div class="container mx-auto px-4 mt-4">
            <?php if ($this->session->hasFlash('success')): ?>
            <div class="bg-green-100 border-<?= $isRtl ? 'r' : 'l' ?>-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                <p><?= $this->session->getFlash('success') ?></p>
            </div>
            <?php endif; ?>

            <?php if ($this->session->hasFlash('error')): ?>
            <div class="bg-red-100 border-<?= $isRtl ? 'r' : 'l' ?>-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                <p><?= $this->session->getFlash('error') ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
