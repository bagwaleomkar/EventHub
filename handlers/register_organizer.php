<?php
/**
 * Organizer Registration Handler
 * 
 * Processes organizer registration form submissions
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
    $phone = sanitize_input($_POST['phone'] ?? '');
    $organization_name = sanitize_input($_POST['orgName'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirmPassword'] ?? '';
    
    // Validate required fields
    if (empty($first_name) || empty($last_name) || empty($email) || empty($password) || empty($organization_name) || empty($phone)) {
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
    
    // Validate phone number
    if (!validate_phone($phone)) {
        json_response(false, 'Invalid phone number');
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
    
    // Process event types (checkboxes)
    $event_types = [];
    if (isset($_POST['eventTypes']) && is_array($_POST['eventTypes'])) {
        $event_types = array_map('sanitize_input', $_POST['eventTypes']);
    }
    $event_types_json = json_encode($event_types);
    
    // Hash the password
    $password_hash = hash_password($password);
    
    // Prepare SQL statement
    $sql = "INSERT INTO users (
                first_name, 
                last_name, 
                email, 
                password_hash, 
                phone,
                organization_name,
                user_role, 
                event_types
            ) VALUES (
                :first_name, 
                :last_name, 
                :email, 
                :password_hash,
                :phone,
                :organization_name,
                'organizer', 
                :event_types
            )";
    
    $stmt = $conn->prepare($sql);
    
    // Bind parameters
    $stmt->bindParam(':first_name', $first_name);
    $stmt->bindParam(':last_name', $last_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password_hash', $password_hash);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':organization_name', $organization_name);
    $stmt->bindParam(':event_types', $event_types_json);
    
    // Execute the statement
    if ($stmt->execute()) {
        $user_id = $conn->lastInsertId();
        
        // Log activity
        log_activity("New organizer registered: $email (ID: $user_id, Org: $organization_name)");
        
        // Send welcome email (optional)
        send_welcome_email($email, "$first_name $last_name", 'Organizer');
        
        // Success response
        json_response(true, 'Registration successful! Welcome to EventHub.', [
            'user_id' => $user_id,
            'email' => $email,
            'role' => 'organizer',
            'organization' => $organization_name
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
