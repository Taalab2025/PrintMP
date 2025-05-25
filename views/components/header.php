<?php
/**
 * Header Component
 * file path: views/components/header.php
 *
 * @package Egypt Printing Services Marketplace
 * @author  Development Team
 */

// $app variable is now available in this scope, passed from App::renderView()
// $pageTitle variable is also expected to be passed in the data array to renderView

$isRtl = $app->getLocalization()->isRtl();
$directionClass = $isRtl ? 'rtl' : 'ltr';
$lang = $app->getLocalization()->getCurrentLanguage();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($lang) ?>" dir="<?= htmlspecialchars($directionClass) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : htmlspecialchars($app->getLocalization()->t('general.site_name')) ?></title>

    <link href="/assets/css/tailwind.css" rel="stylesheet">

    <link href="/assets/css/custom.css" rel="stylesheet">

    <?php if ($isRtl): ?>
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <?php endif; ?>

    <link rel="icon" href="/assets/images/favicon.ico">

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <script>
        window.csrfToken = "<?= htmlspecialchars($app->getSession()->generateCSRFToken()) ?>";
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <div class="bg-gray-800 text-white py-2">
        <div class="container mx-auto px-4 flex justify-between items-center">
            <div class="flex items-center">
                <a href="?lang=en" class="<?= $lang === 'en' ? 'font-bold' : '' ?> mx-1">English</a>
                <span class="mx-1">|</span>
                <a href="?lang=ar" class="<?= $lang === 'ar' ? 'font-bold' : '' ?> mx-1">العربية</a>
            </div>

            <div>
                <?php if ($app->getAuth()->check()): ?>
                    <?php $user = $app->getAuth()->user(); ?>
                    <div class="relative inline-block text-left" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center focus:outline-none">
                            <span class="<?= $isRtl ? 'ml-1' : 'mr-1' ?>"><?= htmlspecialchars($user['name']) ?></span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="origin-top-right absolute <?= $isRtl ? 'left-0' : 'right-0' ?> mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50" style="display: none;">
                            <div class="py-1">
                                <?php if ($user['role'] === 'admin'): ?>
                                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= htmlspecialchars($app->getLocalization()->t('nav.admin_dashboard')) ?></a>
                                <?php elseif ($user['role'] === 'vendor'): ?>
                                    <a href="/vendor/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= htmlspecialchars($app->getLocalization()->t('nav.vendor_dashboard')) ?></a>
                                <?php else: ?>
                                    <a href="/user/dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= htmlspecialchars($app->getLocalization()->t('nav.dashboard')) ?></a>
                                <?php endif; ?>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><?= htmlspecialchars($app->getLocalization()->t('nav.profile')) ?></a>
                                <form action="/logout" method="POST" class="block">
                                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($app->getSession()->generateCSRFToken()) ?>">
                                    <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <?= htmlspecialchars($app->getLocalization()->t('nav.logout')) ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/login" class="mx-2"><?= htmlspecialchars($app->getLocalization()->t('nav.login')) ?></a>
                    <a href="/register" class="mx-2"><?= htmlspecialchars($app->getLocalization()->t('nav.register')) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <nav class="bg-white shadow-md py-3">
        <div class="container mx-auto px-4">
            <div class="flex flex-wrap justify-between items-center">
                <a href="/" class="flex items-center">
                    <img src="/assets/images/logo.svg" alt="<?= htmlspecialchars($app->getLocalization()->t('general.site_name')) ?>" class="h-10">
                    <span class="<?= $isRtl ? 'mr-2' : 'ml-2' ?> text-xl font-bold text-gray-800"><?= htmlspecialchars($app->getLocalization()->t('general.site_name')) ?></span>
                </a>

                <button id="mobile-menu-button" class="md:hidden flex items-center px-3 py-2 border rounded text-gray-600 border-gray-600 hover:text-gray-900 hover:border-gray-900">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div id="nav-links" class="hidden md:flex flex-col md:flex-row w-full md:w-auto mt-2 md:mt-0">
                    <a href="/" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.home')) ?>
                    </a>
                    <a href="/categories" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.categories')) ?>
                    </a>
                    <a href="/services" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.services')) ?>
                    </a>
                    <a href="/vendors" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.vendors')) ?>
                    </a>
                    <a href="/about" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.about')) ?>
                    </a>
                    <a href="/contact" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:text-gray-900 hover:bg-gray-100">
                        <?= htmlspecialchars($app->getLocalization()->t('nav.contact')) ?>
                    </a>
                </div>
            </div>
        </div>
    </nav>

    