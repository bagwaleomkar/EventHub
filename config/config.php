<?php
/**
 * Main Configuration File
 * Bootstrap file that initializes all necessary configurations
 */

// Load path configuration
require_once __DIR__ . '/paths.php';

// Load database configuration
require_once __DIR__ . '/database.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('America/New_York');

// Application settings
define('APP_NAME', 'EventHub');
define('APP_VERSION', '1.0.0');

// Upload settings
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
define('UPLOAD_DIR', ASSETS_DIR . '/events');

/**
 * Redirect to a specific page
 * @param string $page Page name or full URL
 */
function redirect($page) {
    // Check if it's a full URL
    if (strpos($page, 'http') === 0) {
        header('Location: ' . $page);
    } else {
        // Handle relative paths
        if (strpos($page, '/') === 0) {
            header('Location: ' . BASE_URL . $page);
        } else {
            header('Location: ' . BASE_URL . '/' . $page);
        }
    }
    exit();
}

/**
 * Check if user is logged in
 * @return bool
 */
function is_logged_in() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Require login - redirect to login page if not logged in
 * @param string $redirect_to Page to redirect to after login
 */
function require_login($redirect_to = null) {
    if (!is_logged_in()) {
        if ($redirect_to) {
            $_SESSION['redirect_after_login'] = $redirect_to;
        }
        redirect('/views/auth/login.html');
    }
}

/**
 * Check if user has specific role
 * @param string $role Required role
 * @return bool
 */
function has_role($role) {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Require specific role - redirect to appropriate dashboard if not authorized
 * @param string $role Required role
 */
function require_role($role) {
    require_login();
    
    if (!has_role($role)) {
        if ($_SESSION['role'] === 'organizer') {
            redirect('/views/dashboard/organizer_dashboard.php');
        } else {
            redirect('/views/dashboard/attendee_dashboard.php');
        }
    }
}

/**
 * Sanitize output for HTML display
 * @param string $string String to sanitize
 * @return string Sanitized string
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}
?>
