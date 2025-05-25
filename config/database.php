<?php
/**
 * Database Configuration
 * Egypt Printing Services Marketplace
 */

// Database settings
define('DB_HOST', 'localhost');
define('DB_NAME', 'u671773932_Matbaa');
define('DB_USER', 'u671773932_Matbaa');
define('DB_PASS', 'u671773932_M');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', '3306');

// Database connection settings
define('DB_DSN', "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET . ";port=" . DB_PORT);
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
]);
