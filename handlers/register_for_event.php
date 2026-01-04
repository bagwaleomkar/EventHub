<?php
/**
 * Event Registration Handler
 * Handles attendee registration for events
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in and is an attendee
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to register for events'
    ]);
    exit();
}

if ($_SESSION['role'] !== 'attendee') {
    echo json_encode([
        'success' => false,
        'message' => 'Only attendees can register for events'
    ]);
    exit();
}

// Get event ID from POST request
$event_id = isset($_POST['event_id']) ? intval($_POST['event_id']) : 0;
$action = isset($_POST['action']) ? $_POST['action'] : 'register';

if ($event_id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid event ID'
    ]);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode([
        'success' => false,
        'message' => 'Database connection failed'
    ]);
    exit();
}

try {
    $attendee_id = $_SESSION['user_id'];
    
    if ($action === 'register') {
        // Check if event exists and is in the future
        $stmt = $conn->prepare("
            SELECT event_date, event_time, event_name 
            FROM events 
            WHERE id = :event_id
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            echo json_encode([
                'success' => false,
                'message' => 'Event not found'
            ]);
            exit();
        }
        
        // Check if already registered
        $stmt = $conn->prepare("
            SELECT id FROM event_registrations 
            WHERE event_id = :event_id AND attendee_id = :attendee_id AND status = 'registered'
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':attendee_id', $attendee_id);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'You are already registered for this event'
            ]);
            exit();
        }
        
        // Register for the event
        $stmt = $conn->prepare("
            INSERT INTO event_registrations (event_id, attendee_id, status)
            VALUES (:event_id, :attendee_id, 'registered')
            ON DUPLICATE KEY UPDATE status = 'registered', registration_date = CURRENT_TIMESTAMP
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':attendee_id', $attendee_id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'Successfully registered for ' . htmlspecialchars($event['event_name']),
                'action' => 'registered'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to register for event'
            ]);
        }
        
    } elseif ($action === 'unregister') {
        // Cancel registration
        $stmt = $conn->prepare("
            UPDATE event_registrations 
            SET status = 'cancelled' 
            WHERE event_id = :event_id AND attendee_id = :attendee_id
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':attendee_id', $attendee_id);
        
        if ($stmt->execute() && $stmt->rowCount() > 0) {
            echo json_encode([
                'success' => true,
                'message' => 'Registration cancelled successfully',
                'action' => 'unregistered'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Failed to cancel registration'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid action'
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred. Please try again later.'
    ]);
}
?>
