<?php
/**
 * Login Handler
 * Authenticates users and creates sessions
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once 'includes/functions.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Get and sanitize input
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($email) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Please fill in all fields']);
        exit();
    }
    
    if (!validate_email($email)) {
        echo json_encode(['success' => false, 'message' => 'Invalid email address']);
        exit();
    }
    
    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn === null) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Check if user exists
    $stmt = $conn->prepare("
        SELECT user_id, first_name, last_name, email, password_hash, user_role, account_status 
        FROM users 
        WHERE email = :email
    ");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Check account status
    if ($user['account_status'] !== 'active') {
        echo json_encode(['success' => false, 'message' => 'Your account is not active']);
        exit();
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
        exit();
    }
    
    // Update last login
    $updateStmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = :user_id");
    $updateStmt->bindParam(':user_id', $user['user_id']);
    $updateStmt->execute();
    
    // Set session variables
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['username'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['first_name'] = $user['first_name'];
    $_SESSION['last_name'] = $user['last_name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['role'] = $user['user_role'];
    $_SESSION['logged_in'] = true;
    
    // Determine redirect based on role
    $redirect = ($user['user_role'] === 'organizer') ? 'organizer_dashboard.php' : 'attendee_dashboard.php';
    
    // Log activity
    log_activity("User login successful: " . $email . " (Role: " . $user['user_role'] . ")");
    
    echo json_encode([
        'success' => true,
        'message' => 'Login successful! Redirecting...',
        'redirect' => $redirect,
        'role' => $user['user_role']
    ]);
    
} catch (Exception $e) {
    log_activity("Login error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
