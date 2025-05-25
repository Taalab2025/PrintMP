<?php
// Attempt to force error display AT THE VERY TOP for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "DEBUG: Reached top of public/index.php<br>"; // DEBUG LINE

/**
 * Entry Point
 * Egypt Printing Services Marketplace
 */

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load core classes
require_once CORE_PATH . '/App.php';

// Get application instance
$app = App::getInstance();

// Run the application
$app->run();