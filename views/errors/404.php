<?php
/**
 * 404 Error Page
 * File path: views/errors/404.php
 */

// Set proper HTTP status code
http_response_code(404);

// Get localization instance
$localization = $app->getLocalization();
$isRtl = $localization->isRtl();
$currentLanguage = $localization->getCurrentLanguage();
?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $localization->t('errors.404_title') ?> - <?= $localization->t('general.site_name') ?></title>

    <!-- CSS -->
    <link href="/assets/css/tailwind-input.css" rel="stylesheet">
    <link href="/assets/css/custom.css" rel="stylesheet">
    <?php if ($isRtl): ?>
    <link href="/assets/css/rtl.css" rel="stylesheet">
    <?php endif; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="h-full bg-gray-50">
    <div class="min-h-full flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Logo -->
            <div class="text-center mb-8">
                <a href="/" class="inline-flex items-center">
                    <img class="h-12 w-auto" src="/assets/images/logo.svg" alt="<?= $localization->t('general.site_name') ?>">
                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> text-2xl font-bold text-gray-900">
                        <?= $localization->t('general.site_name') ?>
                    </span>
                </a>
            </div>

            <!-- Error Content -->
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 text-center">
                <!-- Error Illustration -->
                <div class="mb-8">
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl"></i>
                    </div>
                    <h1 class="text-6xl font-bold text-gray-900 mb-2">404</h1>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">
                        <?= $localization->t('errors.page_not_found') ?>
                    </h2>
                    <p class="text-gray-600 mb-8">
                        <?= $localization->t('errors.404_description') ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center <?= $isRtl ? 'sm:space-x-reverse' : '' ?>">
                    <a href="/"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-home <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= $localization->t('errors.go_home') ?>
                    </a>

                    <button onclick="window.history.back()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-arrow-left <?= $isRtl ? 'ml-2 fa-flip-horizontal' : 'mr-2' ?>"></i>
                        <?= $localization->t('errors.go_back') ?>
                    </button>
                </div>

                <!-- Help Links -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">
                        <?= $localization->t('errors.need_help') ?>
                    </p>
                    <div class="flex flex-col sm:flex-row justify-center space-y-2 sm:space-y-0 sm:space-x-6 <?= $isRtl ? 'sm:space-x-reverse' : '' ?>">
                        <a href="/services" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            <i class="fas fa-th-large <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                            <?= $localization->t('nav.services') ?>
                        </a>
                        <a href="/vendors" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            <i class="fas fa-store <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                            <?= $localization->t('nav.vendors') ?>
                        </a>
                        <a href="/contact" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            <i class="fas fa-envelope <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                            <?= $localization->t('nav.contact') ?>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Search Suggestion -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">
                    <?= $localization->t('errors.try_searching') ?>
                </h3>
                <form action="/search" method="GET" class="max-w-md mx-auto">
                    <div class="flex">
                        <input type="text"
                               name="q"
                               placeholder="<?= $localization->t('general.search_placeholder') ?>"
                               class="flex-1 min-w-0 block w-full px-3 py-2 rounded-none rounded-l-md border-gray-300 focus:ring-blue-500 focus:border-blue-500 sm:text-sm <?= $isRtl ? 'text-right' : 'text-left' ?>">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-r-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Popular Categories -->
            <div class="mt-8 text-center">
                <h3 class="text-sm font-medium text-gray-500 mb-4">
                    <?= $localization->t('errors.popular_categories') ?>
                </h3>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="/categories/business-cards" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 hover:bg-blue-200 transition-colors">
                        <?= $localization->t('categories.business_cards') ?>
                    </a>
                    <a href="/categories/banners" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 hover:bg-green-200 transition-colors">
                        <?= $localization->t('categories.banners') ?>
                    </a>
                    <a href="/categories/flyers" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 hover:bg-purple-200 transition-colors">
                        <?= $localization->t('categories.flyers') ?>
                    </a>
                    <a href="/categories/branded-gifts" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 hover:bg-yellow-200 transition-colors">
                        <?= $localization->t('categories.branded_gifts') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/assets/js/main.js"></script>

    <!-- Analytics tracking for 404 errors -->
    <script>
        // Track 404 error in analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'page_not_found', {
                'event_category': 'Error',
                'event_label': window.location.pathname,
                'value': 404
            });
        }
    </script>
</body>
</html>
