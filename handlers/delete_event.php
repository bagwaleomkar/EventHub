<?php
/**
 * Delete Event Handler
 * Allows organizers to delete their events
 */

session_start();
header('Content-Type: application/json');

require_once __DIR__ . '/../../config/database.php';
require_once 'includes/functions.php';

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Please login to delete events']);
    exit();
}

// Check if user is organizer
if ($_SESSION['role'] !== 'organizer') {
    echo json_encode(['success' => false, 'message' => 'Only organizers can delete events']);
    exit();
}

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

try {
    $eventId = $_POST['event_id'] ?? '';
    $userId = $_SESSION['user_id'];
    
    if (empty($eventId) || !is_numeric($eventId)) {
        echo json_encode(['success' => false, 'message' => 'Invalid event ID']);
        exit();
    }
    
    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn === null) {
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }
    
    // Check if event exists and belongs to this organizer
    $stmt = $conn->prepare("SELECT event_image, event_name FROM events WHERE id = :event_id AND organizer_id = :user_id");
    $stmt->bindParam(':event_id', $eventId);
    $stmt->bindParam(':user_id', $userId);
    $stmt->execute();
    
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        echo json_encode(['success' => false, 'message' => 'Event not found or you do not have permission to delete it']);
        exit();
    }
    
    // Delete the event from database
    $stmt = $conn->prepare("DELETE FROM events WHERE id = :event_id AND organizer_id = :user_id");
    $stmt->bindParam(':event_id', $eventId);
    $stmt->bindParam(':user_id', $userId);
    
    if ($stmt->execute()) {
        // Delete event image if it's not the default
        if ($event['event_image'] !== 'default-event.jpg' && file_exists('assets/events/' . $event['event_image'])) {
            unlink('assets/events/' . $event['event_image']);
        }
        
        log_activity("Event deleted: " . $event['event_name'] . " (ID: $eventId) by user " . $_SESSION['email']);
        
        echo json_encode([
            'success' => true,
            'message' => 'Event deleted successfully!'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete event. Please try again.']);
    }
    
} catch (Exception $e) {
    log_activity("Event deletion error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'An error occurred. Please try again.']);
}
?>
