<?php
/**
 * Main Layout
 * file path: views/layouts/main.php
 */

/**
 * Function to render the main layout with content
 *
 * @param string $content The content to insert into the layout
 * @return string The complete HTML document
 */
function renderLayout($content) {
    global $app, $localization;
    $isRtl = $localization->isRtl();
    $direction = $isRtl ? 'rtl' : 'ltr';
    $lang = $localization->getCurrentLanguage();

    ob_start();
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>" dir="<?= $direction ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $localization->t('general.site_title') ?></title>

    <!-- Tailwind CSS -->
    <link rel="stylesheet" href="/assets/css/tailwind.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/custom.css">

    <!-- RTL CSS (conditionally loaded) -->
    <?php if ($isRtl): ?>
    <link rel="stylesheet" href="/assets/css/rtl.css">
    <?php endif; ?>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <!-- Alpine.js for interactive components -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- CSRF Token for JavaScript -->
    <script>
        window.csrfToken = "<?= $app->session->generateCsrfToken() ?>";
    </script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
    <!-- Header -->
    <?php include 'views/components/header.php'; ?>

    <!-- Main Content -->
    <main class="flex-grow">
        <?php
        // Flash Messages
        if ($app->session->hasFlash('success') || $app->session->hasFlash('error')):
        ?>
        <div class="container mx-auto px-4 mt-4">
            <?php if ($app->session->hasFlash('success')): ?>
              <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
                              <p><?= $app->session->getFlash('success') ?></p>
                          </div>
                          <?php endif; ?>

                          <?php if ($app->session->hasFlash('error')): ?>
                          <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
                              <p><?= $app->session->getFlash('error') ?></p>
                          </div>
                          <?php endif; ?>
                      </div>
                      <?php endif; ?>

                      <?= $content ?>
                  </main>

                  <!-- Footer -->
                  <?php include 'views/components/footer.php'; ?>

                  <!-- JavaScript -->
                  <script src="/assets/js/main.js"></script>
                  <script src="/assets/js/language-switcher.js"></script>

                  <script>
                      // Initialize language direction
                      updateDirection('<?= $lang ?>');
                  </script>
              </body>
              </html>
              <?php
                  return ob_get_clean();
              }
