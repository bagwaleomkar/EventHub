<?php
// Test Event Creation Handler
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

echo "<h2>Event Creation Test</h2>";

// Simulate logged in organizer
$_SESSION['logged_in'] = true;
$_SESSION['role'] = 'organizer';
$_SESSION['user_id'] = 2; // Use the organizer ID from your database
$_SESSION['email'] = 'test@example.com';

require_once 'config/database.php';
require_once 'includes/functions.php';

echo "<p>Session check: " . ($_SESSION['logged_in'] ? "✅ Logged in" : "❌ Not logged in") . "</p>";
echo "<p>Role check: " . ($_SESSION['role'] === 'organizer' ? "✅ Organizer" : "❌ Not organizer") . "</p>";
echo "<p>User ID: " . $_SESSION['user_id'] . "</p>";

// Simulate POST data
$_POST = [
    'eventName' => 'Test Event ' . date('H:i:s'),
    'eventDescription' => 'This is a test event description to verify the event creation functionality',
    'eventDate' => '2026-02-15',
    'eventTime' => '14:00',
    'eventLocation' => 'Test Location, Test City'
];

echo "<h3>POST Data:</h3><pre>";
print_r($_POST);
echo "</pre>";

try {
    $eventName = sanitize_input($_POST['eventName']);
    $eventDescription = sanitize_input($_POST['eventDescription']);
    $eventDate = sanitize_input($_POST['eventDate']);
    $eventTime = sanitize_input($_POST['eventTime']);
    $eventLocation = sanitize_input($_POST['eventLocation']);
    $organizerId = $_SESSION['user_id'];
    
    echo "<p>✅ Data sanitized successfully</p>";
    
    // Connect to database
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn === null) {
        echo "<p style='color: red;'>❌ Database connection failed</p>";
        exit();
    }
    
    echo "<p>✅ Database connected</p>";
    
    $imageName = 'default-event.jpg';
    
    // Prepare insert statement
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
    
    echo "<p>✅ Statement prepared</p>";
    
    $stmt->bindParam(':organizer_id', $organizerId);
    $stmt->bindParam(':event_name', $eventName);
    $stmt->bindParam(':event_description', $eventDescription);
    $stmt->bindParam(':event_date', $eventDate);
    $stmt->bindParam(':event_time', $eventTime);
    $stmt->bindParam(':event_location', $eventLocation);
    $stmt->bindParam(':event_image', $imageName);
    
    echo "<p>✅ Parameters bound</p>";
    
    if ($stmt->execute()) {
        $eventId = $conn->lastInsertId();
        echo "<p style='color: green; font-size: 20px;'>✅ <strong>Event created successfully!</strong></p>";
        echo "<p>Event ID: <strong>{$eventId}</strong></p>";
        echo "<p>Event Name: <strong>{$eventName}</strong></p>";
        
        // Verify it was inserted
        $verifyStmt = $conn->prepare("SELECT * FROM events WHERE id = :id");
        $verifyStmt->bindParam(':id', $eventId);
        $verifyStmt->execute();
        $event = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($event) {
            echo "<h3>Verified Event Data:</h3><pre>";
            print_r($event);
            echo "</pre>";
        }
    } else {
        echo "<p style='color: red;'>❌ Failed to execute insert</p>";
        echo "<p>Error: " . print_r($stmt->errorInfo(), true) . "</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Exception: " . $e->getMessage() . "</p>";
    echo "<p>File: " . $e->getFile() . "</p>";
    echo "<p>Line: " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";
echo "<p><a href='events.php'>View Events Page</a> | <a href='my_events.php'>View My Events</a></p>";
?>
