<?php
/**
 * Create Event Handler
 * Processes event creation form submissions
 */

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in JSON response
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/logs/php_errors.log');

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login to create events']);
    exit();
}

// Check if user is organizer
if ($_SESSION['role'] !== 'organizer') {
    echo json_encode(['success' => false, 'message' => 'Only organizers can create events']);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    // Get and sanitize input
    $eventName = sanitize_input($_POST['eventName'] ?? '');
    $eventDescription = sanitize_input($_POST['eventDescription'] ?? '');
    $eventDate = sanitize_input($_POST['eventDate'] ?? '');
    $eventTime = sanitize_input($_POST['eventTime'] ?? '');
    $eventLocation = sanitize_input($_POST['eventLocation'] ?? '');
    $organizerId = $_SESSION['user_id'];
    
    // Validate required fields
    if (empty($eventName) || empty($eventDescription) || empty($eventDate) || empty($eventTime) || empty($eventLocation)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
        exit();
    }
    
    // Validate event name length
    if (strlen($eventName) < 3) {
        echo json_encode(['success' => false, 'message' => 'Event name must be at least 3 characters']);
        exit();
    }
    
    if (strlen($eventName) > 255) {
        echo json_encode(['success' => false, 'message' => 'Event name is too long (max 255 characters)']);
        exit();
    }
    
    // Validate event description length
    if (strlen($eventDescription) < 10) {
        echo json_encode(['success' => false, 'message' => 'Event description must be at least 10 characters']);
        exit();
    }
    
    // Validate date is not in the past
    $eventDateTime = strtotime($eventDate);
    $today = strtotime(date('Y-m-d'));
    
    if ($eventDateTime < $today) {
        echo json_encode(['success' => false, 'message' => 'Event date cannot be in the past']);
        exit();
    }
    
    // Validate date format
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $eventDate)) {
        echo json_encode(['success' => false, 'message' => 'Invalid date format']);
        exit();
    }
    
    // Validate time format
    if (!preg_match('/^\d{2}:\d{2}$/', $eventTime)) {
        echo json_encode(['success' => false, 'message' => 'Invalid time format']);
        exit();
    }
    
    // Handle image upload
    $imageName = 'default-event.jpg';
    $uploadSuccess = true;
    $uploadError = '';
    
    if (isset($_FILES['eventImage']) && $_FILES['eventImage']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['eventImage'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        // Validate file type
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadSuccess = false;
            $uploadError = 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed';
        }
        
        // Validate file size
        if ($file['size'] > $maxSize) {
            $uploadSuccess = false;
            $uploadError = 'File is too large. Maximum size is 5MB';
        }
        
        if ($uploadSuccess) {
            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $imageName = 'event_' . time() . '_' . uniqid() . '.' . $extension;
            $uploadPath = 'assets/events/' . $imageName;
            
            // Create directory if it doesn't exist
            if (!file_exists('assets/events')) {
                mkdir('assets/events', 0755, true);
            }
            
            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                $uploadSuccess = false;
                $uploadError = 'Failed to upload image';
            }
        }
        
        if (!$uploadSuccess) {
            echo json_encode(['success' => false, 'message' => $uploadError]);
            exit();
        }
    }
    
    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn === null) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Insert event into database
    $stmt = $conn->prepare("
        INSERT INTO events (
            organizer_id,
            event_name,
            event_description,
            event_date,
            event_time,
            event_location,
            event_image
        ) VALUES (
            :organizer_id,
            :event_name,
            :event_description,
            :event_date,
            :event_time,
            :event_location,
            :event_image
        )
    ");
    
    $stmt->bindParam(':organizer_id', $organizerId);
    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':event_description', $eventDescription);
    $stmt->bindParam(':event_date', $eventDate);
    $stmt->bindParam(':event_time', $eventTime);
    $stmt->bindParam(':event_location', $eventLocation);
    $stmt->bindParam(':event_image', $imageName);
    
    if ($stmt->execute()) {
        $eventId = $conn->lastInsertId();
        
        // Log activity
        log_activity("Event created: $eventName (ID: $eventId) by user " . $_SESSION['email']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Event created successfully!',
            'event_id' => $eventId
        ]);
    } else {
        // If database insert failed and image was uploaded, delete the image
        if ($imageName !== 'default-event.jpg' && file_exists('assets/events/' . $imageName)) {
            unlink('assets/events/' . $imageName);
        }
        
        echo json_encode(['success' => false, 'message' => 'Failed to create event. Please try again.']);
    }
    
} catch (Exception $e) {
    log_activity("Event creation error: " . $e->getMessage());
    error_log("Event creation exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
    echo json_encode([
        'success' => false, 
        'message' => 'An error occurred. Please try again.',
        'debug' => ($_SERVER['SERVER_NAME'] === 'localhost' ? $e->getMessage() : '')
    ]);
}
?>
