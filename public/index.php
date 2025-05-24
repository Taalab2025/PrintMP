<?php
/**
 * Entry Point
 * Egypt Printing Services Marketplace
 */

// Load configuration
require_once '../config/config.php';

// Load core classes
require_once CORE_PATH . '/App.php';

// Get application instance
$app = App::getInstance();

// Run the application
$app->run();
