<?php
/**
 * Attendee Registration Handler
 * 
 * Processes attendee registration form submissions
 */

session_start();
require_once __DIR__ . '/../../config/database.php';
require_once 'includes/functions.php';

// Set header for JSON response
header('Content-Type: application/json');

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, 'Invalid request method');
}

try {
    // Get database connection
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        json_response(false, 'Database connection failed');
    }
    
    // Retrieve and sanitize input data
    $first_name = sanitize_input($_POST['firstName'] ?? '');
    $last_name = sanitize_input($_POST['lastName'] ?? '');
    $email = sanitize_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';
    $newsletter = isset($_POST['newsletter']) ? 1 : 0;
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password)) {
        json_response(false, 'All required fields must be filled');
    }
    
    // Validate email
    if (!validate_email($email)) {
        json_response(false, 'Invalid email address');
    }
    
    // Check if email already exists
    if (email_exists($conn, $email)) {
        json_response(false, 'Email address already registered');
    }
    
    // Validate password
    $password_validation = validate_password($password);
    if (!$password_validation['valid']) {
        json_response(false, $password_validation['message']);
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        json_response(false, 'Passwords do not match');
    }
    
    // Process interests (checkboxes)
    $interests = [];
    if (isset($_POST['interests']) && is_array($_POST['interests'])) {
        $interests = array_map('sanitize_input', $_POST['interests']);
    }
    $interests_json = json_encode($interests);
    
    // Hash the password
    $password_hash = hash_password($password);
    
    // Prepare SQL statement
    $sql = "INSERT INTO users (
                first_name, 
                last_name, 
                email, 
                password_hash, 
                user_role, 
                interests, 
                newsletter_subscribed
            ) VALUES (
                :first_name, 
                :last_name, 
                :email, 
                :password_hash, 
                'attendee', 
                :interests, 
                :newsletter
            )";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':interests', $interests_json);
    $stmt->bindParam(':newsletter', $newsletter);
    
    // Execute the statement
    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        
        // Log activity
        log_activity("New attendee registered: $email (ID: $user_id)");
        
        // Send welcome email (optional)
        send_welcome_email($email, "$first_name $last_name", 'Attendee');
        
        // Success response
        json_response(true, 'Registration successful! Welcome to EventHub.', [
            'user_id' => $user_id,
            'email' => $email,
            'role' => 'attendee'
        ]);
    } else {
        json_response(false, 'Registration failed. Please try again.');
    }
    
} catch (PDOException $e) {
    error_log("Registration Error: " . $e->getMessage());
    json_response(false, 'An error occurred during registration. Please try again later.');
} finally {
    // Close database connection
    if (isset($database)) {
        $database->closeConnection();
    }
}
?>
