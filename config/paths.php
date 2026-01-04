<?php
/**
 * Path Configuration
 * Central configuration for all file paths in the application
 * This ensures consistent path management across the entire system
 */

// Define the root directory
define('ROOT_DIR', dirname(__FILE__));

// Define main directories
define('CONFIG_DIR', ROOT_DIR . '/config');
define('INCLUDES_DIR', ROOT_DIR . '/includes');
define('ASSETS_DIR', ROOT_DIR . '/assets');
define('LOGS_DIR', ROOT_DIR . '/logs');

// Define public directories
define('PUBLIC_DIR', ROOT_DIR . '/public');
define('CSS_DIR', PUBLIC_DIR . '/css');
define('JS_DIR', PUBLIC_DIR . '/js');

// Define views directories
define('VIEWS_DIR', ROOT_DIR . '/views');
define('AUTH_VIEWS_DIR', VIEWS_DIR . '/auth');
define('DASHBOARD_VIEWS_DIR', VIEWS_DIR . '/dashboard');
define('EVENT_VIEWS_DIR', VIEWS_DIR . '/events');

// Define API directory
define('API_DIR', ROOT_DIR . '/api');

// Define handlers directory
define('HANDLERS_DIR', ROOT_DIR . '/handlers');

// Define database directory
define('DATABASE_DIR', ROOT_DIR . '/database');

// Define tests directory
define('TESTS_DIR', ROOT_DIR . '/tests');

// Define docs directory
define('DOCS_DIR', ROOT_DIR . '/docs');

/**
 * Get the base URL for the application
 * @return string Base URL
 */
function get_base_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
    return $protocol . $host . $script;
}

// Define base URL constant
define('BASE_URL', rtrim(get_base_url(), '/'));

/**
 * Get relative path from root for use in HTML
 * @param string $path Absolute path
 * @return string Relative path
 */
function get_relative_path($path) {
    return str_replace(ROOT_DIR, '', $path);
}

/**
 * Include a file with error handling
 * @param string $file File path
 * @return bool Success status
 */
function safe_include($file) {
    if (file_exists($file)) {
        require_once $file;
        return true;
    } else {
        error_log("File not found: " . $file);
        return false;
    }
}
?>
