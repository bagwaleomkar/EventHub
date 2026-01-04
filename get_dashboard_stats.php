<?php
/**
 * Get Dashboard Statistics
 * Returns JSON with dashboard stats for both attendees and organizers
 */

session_start();
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['error' => 'Not logged in']);
    exit();
}

require_once 'config/database.php';
$database = new Database();
$conn = $database->getConnection();

if (!$conn) {
    echo json_encode(['error' => 'Database connection failed']);
    exit();
}

try {
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if ($role === 'organizer') {
        // Get organizer statistics
        
        // Total events created
        $stmt = $conn->prepare("SELECT COUNT(*) as total FROM events WHERE organizer_id = :user_id");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $total_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Upcoming events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM events 
            WHERE organizer_id = :user_id 
            AND event_date >= CURDATE()
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $upcoming_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Past events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM events 
            WHERE organizer_id = :user_id 
            AND event_date < CURDATE()
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $past_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Total registrations for all events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            WHERE e.organizer_id = :user_id 
            AND er.status = 'registered'
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $total_registrations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'success' => true,
            'role' => 'organizer',
            'stats' => [
                'total_events' => $total_events,
                'upcoming_events' => $upcoming_events,
                'past_events' => $past_events,
                'total_registrations' => $total_registrations
            ]
        ]);
        
    } else {
        // Get attendee statistics
        
        // Total registered events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM event_registrations 
            WHERE attendee_id = :user_id 
            AND status = 'registered'
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $registered_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Upcoming registered events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            WHERE er.attendee_id = :user_id 
            AND er.status = 'registered'
            AND e.event_date >= CURDATE()
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $upcoming_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Past registered events
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            WHERE er.attendee_id = :user_id 
            AND er.status = 'registered'
            AND e.event_date < CURDATE()
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $past_events = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Recent registrations (last 30 days)
        $stmt = $conn->prepare("
            SELECT COUNT(*) as total 
            FROM event_registrations 
            WHERE attendee_id = :user_id 
            AND status = 'registered'
            AND registration_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $recent_registrations = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        echo json_encode([
            'success' => true,
            'role' => 'attendee',
            'stats' => [
                'registered_events' => $registered_events,
                'upcoming_events' => $upcoming_events,
                'past_events' => $past_events,
                'recent_registrations' => $recent_registrations
            ]
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    echo json_encode(['error' => 'An error occurred']);
}
?>
