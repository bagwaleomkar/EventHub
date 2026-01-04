<?php
/**
 * Logout Handler
 * Destroys user session and redirects to home
 */

session_start();

// Log activity before destroying session
if (isset($_SESSION['email'])) {
    require_once 'includes/functions.php';
    log_activity("User logout: " . $_SESSION['email']);
}

// Destroy all session data
session_unset();
session_destroy();

// Redirect to home page
header('Location: index.html');
exit();
?>
