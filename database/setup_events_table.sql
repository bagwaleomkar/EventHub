-- ============================================
-- Quick Setup Script for Events Table
-- Run this in phpMyAdmin SQL tab
-- ============================================

USE eventra_db;

-- Create Events Table
CREATE TABLE IF NOT EXISTS events (
    id INT AUTO_INCREMENT PRIMARY KEY,
    organizer_id INT NOT NULL,
    event_name VARCHAR(255) NOT NULL,
    event_description TEXT NOT NULL,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    event_location VARCHAR(255),
    event_image VARCHAR(255) DEFAULT 'default-event.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Key
    FOREIGN KEY (organizer_id) REFERENCES users(user_id) ON DELETE CASCADE,
    
    -- Indexes
    INDEX idx_organizer (organizer_id),
    INDEX idx_date (event_date),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================
-- Sample Events (Optional - for testing)
-- Uncomment after you have at least one organizer registered
-- ============================================
/*
-- First, get an organizer user_id (replace 1 with actual organizer user_id)
INSERT INTO events (organizer_id, event_name, event_description, event_date, event_time, event_location) VALUES
(1, 'Tech Conference 2026', 'Join us for the biggest tech conference of the year featuring industry leaders, workshops, and networking opportunities.', '2026-02-15', '09:00:00', 'Convention Center, New York'),
(1, 'Music Festival Summer', 'Experience amazing live performances from top artists. Food, drinks, and great vibes all day long!', '2026-03-20', '18:00:00', 'Central Park, Los Angeles'),
(1, 'Business Networking Event', 'Connect with entrepreneurs, investors, and business professionals in your area.', '2026-02-10', '19:00:00', 'Hilton Hotel, San Francisco');
*/

-- ============================================
-- Verify Table Creation
-- ============================================
SHOW TABLES LIKE 'events';
DESCRIBE events;
