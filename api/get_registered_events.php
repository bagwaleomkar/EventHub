<?php
/**
 * Get Registered Events for Attendee
 * Returns JSON with list of events the attendee is registered for
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit();
}

require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    
    // Get registered events
    $stmt = $conn->prepare("
        SELECT 
            e.id,
            e.event_name,
            e.event_description,
            e.event_date,
            e.event_time,
            e.event_location,
            e.event_image,
            er.registration_date,
            CONCAT(u.first_name, ' ', u.last_name) as organizer_name
        FROM event_registrations er
        JOIN events e ON er.event_id = e.id
        JOIN users u ON e.organizer_id = u.user_id
        WHERE er.attendee_id = :user_id 
        AND er.status = 'registered'
        ORDER BY e.event_date ASC, e.event_time ASC
    ");
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for display
    foreach ($events as &$event) {
        $event['event_date_formatted'] = date('F j, Y', strtotime($event['event_date']));
        $event['event_time_formatted'] = date('g:i A', strtotime($event['event_time']));
    }
    
    echo json_encode([
        'success' => true,
        'events' => $events
    ]);
    
} catch (PDOException $e) {
    error_log("Get registered events error: " . $e->getMessage());
    echo json_encode(['success' => false, 'error' => 'An error occurred']);
}
?>
