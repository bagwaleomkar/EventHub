<?php
/**
 * Check if user is registered for an event
 * Returns JSON with registration status
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode([
        'logged_in' => false,
        'is_registered' => false
    ]);
    exit();
}

$event_id = isset($_GET['event_id']) ? intval($_GET['event_id']) : 0;

if ($event_id <= 0) {
    echo json_encode([
        'error' => 'Invalid event ID'
    ]);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode([
        'error' => 'Database connection failed'
    ]);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    // Check if user is the organizer of this event
    $stmt = $conn->prepare("
        SELECT organizer_id FROM events WHERE id = :event_id
    ");
    $stmt->bindParam(':event_id', $event_id);
    $stmt->execute();
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $is_organizer = ($event && $event['organizer_id'] == $user_id);
    
    // Check registration status for attendees
    $is_registered = false;
    if ($role === 'attendee' && !$is_organizer) {
        $stmt = $conn->prepare("
            SELECT id FROM event_registrations 
            WHERE event_id = :event_id AND attendee_id = :user_id AND status = 'registered'
        ");
        $stmt->bindParam(':event_id', $event_id);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $is_registered = ($stmt->fetch() !== false);
    }
    
    echo json_encode([
        'logged_in' => true,
        'role' => $role,
        'is_registered' => $is_registered,
        'is_organizer' => $is_organizer
    ]);
    
} catch (PDOException $e) {
    error_log("Check registration error: " . $e->getMessage());
    echo json_encode([
        'error' => 'An error occurred'
    ]);
}
?>
