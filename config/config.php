<?php
/**
 * Main Configuration File
 * Egypt Printing Services Marketplace
 */

// Define environment
define('ENVIRONMENT', 'development'); // 'development', 'production', 'testing'

// Error reporting
if (ENVIRONMENT === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Base paths
define('BASE_PATH', dirname(__DIR__));
define('CONFIG_PATH', BASE_PATH . '/config');
define('CORE_PATH', BASE_PATH . '/core');
define('MODELS_PATH', BASE_PATH . '/models');
define('CONTROLLERS_PATH', BASE_PATH . '/controllers');
define('VIEWS_PATH', BASE_PATH . '/views');
define('ASSETS_PATH', BASE_PATH . '/assets');
define('UPLOADS_PATH', ASSETS_PATH . '/uploads');
define('LANG_PATH', BASE_PATH . '/lang');

// URL paths
define('BASE_URL', 'http://localhost/egypt-printing-marketplace');
define('ASSETS_URL', BASE_URL . '/assets');
define('UPLOADS_URL', ASSETS_URL . '/uploads');

// Session settings
define('SESSION_NAME', 'egypt_printing_marketplace');
define('SESSION_LIFETIME', 7200); // 2 hours
define('SESSION_PATH', '/');
define('SESSION_DOMAIN', '');
define('SESSION_SECURE', false);
define('SESSION_HTTP_ONLY', true);

// Default settings
define('DEFAULT_LANGUAGE', 'en'); // en or ar
define('DEFAULT_TIMEZONE', 'Africa/Cairo');
define('DEFAULT_CONTROLLER', 'Home');
define('DEFAULT_ACTION', 'index');

// Translation settings
define('TRANSLATE_VIEWS', true);

// Vendor settings
define('FREE_QUOTE_LIMIT', 10);

// Load other configuration files
require_once CONFIG_PATH . '/database.php';
