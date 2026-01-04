<?php
/**
 * Database Configuration
 * 
 * This file handles the database connection using PDO
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventra_db');
define('DB_USER', 'root');  // Change this to your MySQL username
define('DB_PASS', '');      // Change this to your MySQL password

class Database {
    private $host = DB_HOST;
    private $db_name = DB_NAME;
    private $username = DB_USER;
    private $password = DB_PASS;
    private $conn = null;
    
    /**
     * Get database connection
     * 
     * @return PDO|null
     */
    public function getConnection() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                )
            );
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            return null;
        }
        
        return $this->conn;
    }
    
    /**
     * Close database connection
     */
    public function closeConnection() {
        $this->conn = null;
    }
}
?>
