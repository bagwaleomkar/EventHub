<?php
/**
 * Utility Functions
 * 
 * Common functions used across the application
 */

/**
 * Sanitize input data
 * 
 * @param string $data
 * @return string
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Validate email address
 * 
 * @param string $email
 * @return bool
 */
function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * 
 * @param string $password
 * @return array ['valid' => bool, 'message' => string]
 */
function validate_password($password) {
    $errors = [];
    
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    }
    
    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }
    
    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }
    
    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }
    
    if (empty($errors)) {
        return ['valid' => true, 'message' => 'Valid password'];
    } else {
        return ['valid' => false, 'message' => implode('. ', $errors)];
    }
}

/**
 * Validate phone number
 * 
 * @param string $phone
 * @return bool
 */
function validate_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    return strlen($phone) >= 10;
}

/**
 * Hash password securely
 * 
 * @param string $password
 * @return string
 */
function hash_password($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

/**
 * Verify password against hash
 * 
 * @param string $password
 * @param string $hash
 * @return bool
 */
function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate response JSON
 * 
 * @param bool $success
 * @param string $message
 * @param array $data
 * @return string
 */
function json_response($success, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Check if email exists in database
 * 
 * @param PDO $conn
 * @param string $email
 * @return bool
 */
function email_exists($conn, $email) {
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    return $stmt->rowCount() > 0;
}

/**
 * Log activity (optional for debugging)
 * 
 * @param string $message
 */
function log_activity($message) {
    $log_file = __DIR__ . '/../logs/activity.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($log_file, "[$timestamp] $message\n", FILE_APPEND);
}

/**
 * Send welcome email (placeholder function)
 * 
 * @param string $email
 * @param string $name
 * @param string $role
 * @return bool
 */
function send_welcome_email($email, $name, $role) {
    // TODO: Implement actual email sending using PHPMailer or similar
    // For now, just log it
    log_activity("Welcome email sent to $email ($name - $role)");
    return true;
}
?>
