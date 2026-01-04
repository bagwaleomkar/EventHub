<?php
/**
 * Session Checker
 * Returns current user session status and info
 */

session_start();
header('Content-Type: application/json');

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    echo json_encode([
        'logged_in' => true,
        'username' => $_SESSION['username'] ?? '',
        'first_name' => $_SESSION['first_name'] ?? '',
        'last_name' => $_SESSION['last_name'] ?? '',
        'role' => $_SESSION['role'] ?? 'attendee',
        'user_id' => $_SESSION['user_id'] ?? 0
    ]);
} else {
    echo json_encode(['logged_in' => false]);
}
?>
