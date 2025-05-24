<?php
/**
 * 500 Internal Server Error Page
 * File path: views/errors/500.php
 */

// Set proper HTTP status code
http_response_code(500);

// Get localization instance (with fallback if system is broken)
try {
    $localization = $app->getLocalization();
    $isRtl = $localization->isRtl();
    $currentLanguage = $localization->getCurrentLanguage();
} catch (Exception $e) {
    // Fallback if localization system is broken
    $isRtl = false;
    $currentLanguage = 'en';
    $localization = null;
}

// Translation helper with fallbacks
function t($key, $fallback = null) {
    global $localization;

    if ($localization) {
        try {
            return $localization->t($key);
        } catch (Exception $e) {
            // Fall through to fallback
        }
    }

    // Fallback translations
    $fallbacks = [
        'errors.500_title' => 'Server Error',
        'errors.server_error' => 'Internal Server Error',
        'errors.500_description' => 'We\'re experiencing some technical difficulties. Please try again later.',
        'errors.go_home' => 'Go Home',
        'errors.go_back' => 'Go Back',
        'errors.try_again' => 'Try Again',
        'errors.contact_support' => 'Contact Support',
        'errors.error_id' => 'Error ID',
        'general.site_name' => 'PrintHub Egypt'
    ];

    return $fallbacks[$key] ?? $fallback ?? $key;
}

// Generate error ID for tracking
$errorId = uniqid('err_', true);
?>

<!DOCTYPE html>
<html lang="<?= $currentLanguage ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('errors.500_title') ?> - <?= t('general.site_name') ?></title>

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
                    <img class="h-12 w-auto" src="/assets/images/logo.svg" alt="<?= t('general.site_name') ?>" onerror="this.style.display='none'">
                    <span class="<?= $isRtl ? 'mr-3' : 'ml-3' ?> text-2xl font-bold text-gray-900">
                        <?= t('general.site_name') ?>
                    </span>
                </a>
            </div>

            <!-- Error Content -->
            <div class="bg-white py-8 px-4 shadow-lg sm:rounded-lg sm:px-10 text-center">
                <!-- Error Illustration -->
                <div class="mb-8">
                    <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-server text-red-600 text-3xl"></i>
                    </div>
                    <h1 class="text-6xl font-bold text-gray-900 mb-2">500</h1>
                    <h2 class="text-2xl font-semibold text-gray-700 mb-4">
                        <?= t('errors.server_error') ?>
                    </h2>
                    <p class="text-gray-600 mb-8">
                        <?= t('errors.500_description') ?>
                    </p>
                </div>

                <!-- Error ID -->
                <div class="mb-8 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">
                        <strong><?= t('errors.error_id') ?>:</strong>
                        <code class="text-xs font-mono bg-gray-200 px-2 py-1 rounded"><?= $errorId ?></code>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="space-y-4 sm:space-y-0 sm:space-x-4 sm:flex sm:justify-center <?= $isRtl ? 'sm:space-x-reverse' : '' ?>">
                    <button onclick="window.location.reload()"
                            class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-redo <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= t('errors.try_again') ?>
                    </button>

                    <a href="/"
                       class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <i class="fas fa-home <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= t('errors.go_home') ?>
                    </a>
                </div>

                <!-- Contact Support -->
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-600 mb-4">
                        If the problem persists, please contact our support team.
                    </p>
                    <a href="mailto:support@printhub-egypt.com?subject=Error%20Report&body=Error%20ID:%20<?= $errorId ?>"
                       class="inline-flex items-center text-blue-600 hover:text-blue-500 text-sm font-medium">
                        <i class="fas fa-envelope <?= $isRtl ? 'ml-2' : 'mr-2' ?>"></i>
                        <?= t('errors.contact_support') ?>
                    </a>
                </div>
            </div>

            <!-- Status Check -->
            <div class="mt-8 bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4 text-center">
                    System Status
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">Database Connection</span>
                        <span id="db-status" class="text-sm">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">File System</span>
                        <span id="fs-status" class="text-sm">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">External Services</span>
                        <span id="ext-status" class="text-sm">
                            <i class="fas fa-spinner fa-spin text-gray-400"></i>
                        </span>
                    </div>
                </div>

                <div class="mt-4 text-center">
                    <button onclick="checkSystemStatus()"
                            class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        <i class="fas fa-refresh <?= $isRtl ? 'ml-1' : 'mr-1' ?>"></i>
                        Check Status
                    </button>
                </div>
            </div>

            <!-- Recent Activity Note -->
            <div class="mt-6 text-center">
                <p class="text-xs text-gray-500">
                    We automatically monitor our systems and are likely already aware of this issue.
                </p>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Simple system status check
        function checkSystemStatus() {
            // Simulate status check
            setTimeout(() => {
                document.getElementById('db-status').innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            }, 500);

            setTimeout(() => {
                document.getElementById('fs-status').innerHTML = '<i class="fas fa-check-circle text-green-500"></i>';
            }, 1000);

            setTimeout(() => {
                document.getElementById('ext-status').innerHTML = '<i class="fas fa-exclamation-triangle text-yellow-500"></i>';
            }, 1500);
        }

        // Auto-retry after some time
        let retryCount = 0;
        const maxRetries = 3;

        function autoRetry() {
            if (retryCount < maxRetries) {
                retryCount++;
                setTimeout(() => {
                    // Try to make a simple request to check if server is back
                    fetch('/', {
                        method: 'HEAD',
                        cache: 'no-cache'
                    })
                    .then(response => {
                        if (response.ok) {
                            // Show success message and redirect
                            document.body.innerHTML = `
                                <div class="min-h-full flex items-center justify-center">
                                    <div class="text-center">
                                        <i class="fas fa-check-circle text-green-500 text-4xl mb-4"></i>
                                        <h2 class="text-xl font-semibold text-gray-900 mb-2">System Restored</h2>
                                        <p class="text-gray-600 mb-4">Redirecting you back...</p>
                                    </div>
                                </div>
                            `;
                            setTimeout(() => {
                                window.location.href = '/';
                            }, 2000);
                        } else {
                            autoRetry();
                        }
                    })
                    .catch(() => {
                        autoRetry();
                    });
                }, 10000 * retryCount); // Exponential backoff
            }
        }

        // Start auto-retry after 10 seconds
        setTimeout(autoRetry, 10000);

        // Initialize status check
        setTimeout(checkSystemStatus, 1000);

        // Track error in analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'server_error', {
                'event_category': 'Error',
                'event_label': '<?= $errorId ?>',
                'value': 500
            });
        }
    </script>
</body>
</html>
