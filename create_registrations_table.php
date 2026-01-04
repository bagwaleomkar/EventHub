<?php
/**
 * Database Setup - Event Registrations Table
 * Run this file once to create the event_registrations table
 */

require_once 'config/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($conn) {
    try {
        // Create event_registrations table
        $sql = "CREATE TABLE IF NOT EXISTS event_registrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            event_id INT NOT NULL,
            attendee_id INT NOT NULL,
            registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            status ENUM('registered', 'cancelled') DEFAULT 'registered',
            FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
            FOREIGN KEY (attendee_id) REFERENCES users(user_id) ON DELETE CASCADE,
            UNIQUE KEY unique_registration (event_id, attendee_id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $conn->exec($sql);
        
        echo "✅ Success! Event registrations table created successfully.<br>";
        echo "The database is ready to track event registrations.";
        
    } catch (PDOException $e) {
        echo "❌ Error creating table: " . $e->getMessage();
    }
} else {
    echo "❌ Error: Could not connect to database.";
}
?>
