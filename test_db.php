<?php
// Database connection test
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

echo "<h2>Database Connection Test</h2>";

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    echo "<p style='color: green;'>✅ Database connection: SUCCESS</p>";
    
    // Check if events table exists
    try {
        $stmt = $conn->query("SHOW TABLES LIKE 'events'");
        $result = $stmt->fetch();
        if ($result) {
            echo "<p style='color: green;'>✅ Events table exists</p>";
            
            // Check table structure
            $stmt = $conn->query("DESCRIBE events");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "<h3>Events Table Structure:</h3><ul>";
            foreach ($columns as $col) {
                echo "<li>{$col['Field']} - {$col['Type']}</li>";
            }
            echo "</ul>";
            
            // Count events
            $stmt = $conn->query("SELECT COUNT(*) as count FROM events");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Total events in database: <strong>{$result['count']}</strong></p>";
            
            // Show recent events
            $stmt = $conn->query("SELECT * FROM events ORDER BY created_at DESC LIMIT 5");
            $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (count($events) > 0) {
                echo "<h3>Recent Events:</h3>";
                foreach ($events as $event) {
                    echo "<p>- {$event['event_name']} (ID: {$event['id']})</p>";
                }
            } else {
                echo "<p style='color: orange;'>⚠️ No events found in database</p>";
            }
            
        } else {
            echo "<p style='color: red;'>❌ Events table does NOT exist</p>";
            echo "<p>Run database.sql to create the table</p>";
        }
        
        // Check users table
        $stmt = $conn->query("SHOW TABLES LIKE 'users'");
        $result = $stmt->fetch();
        if ($result) {
            echo "<p style='color: green;'>✅ Users table exists</p>";
            
            $stmt = $conn->query("SELECT COUNT(*) as count FROM users");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            echo "<p>Total users in database: <strong>{$result['count']}</strong></p>";
        } else {
            echo "<p style='color: red;'>❌ Users table does NOT exist</p>";
        }
        
    } catch (PDOException $e) {
        echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ Database connection: FAILED</p>";
    echo "<p>Check your database credentials in config/database.php</p>";
}
?>
