<?php
/**
 * Events Page - Display all events from database
 */

session_start(); // Start session for user status check
require_once 'config/database.php';

// Get database connection
$database = new Database();
$conn = $database->getConnection();

// Fetch all events ordered by latest first
$events = [];
$hasEvents = false;

if ($conn) {
    try {
        $stmt = $conn->prepare("
            SELECT 
                e.id,
                e.event_name,
                e.event_description,
                e.event_date,
                e.event_time,
                e.event_location,
                e.event_image,
                e.created_at,
                u.first_name,
                u.last_name,
                u.organization_name
            FROM events e
            INNER JOIN users u ON e.organizer_id = u.user_id
            WHERE u.account_status = 'active'
            ORDER BY e.created_at DESC
        ");
        $stmt->execute();
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $hasEvents = count($events) > 0;
    } catch (PDOException $e) {
        error_log("Error fetching events: " . $e->getMessage());
    }
}

// Function to format date
function formatEventDate($date) {
    return date('M d, Y', strtotime($date));
}

// Function to format time
function formatEventTime($time) {
    return date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Events</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .events-hero {
            background: linear-gradient(135deg, #4361EE 0%, #2EC4B6 100%);
            padding: 80px 0;
            color: white;
            text-align: center;
        }
        .events-hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            color: white;
        }
        .events-hero p {
            font-size: 20px;
            opacity: 0.9;
            max-width: 600px;
            margin: 0 auto;
        }
        .events-section {
            padding: 60px 0;
            background: #f5f7fa;
            min-height: 60vh;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }
        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.15);
        }
        .event-image {
            width: 100%;
            height: 220px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .event-content {
            padding: 25px;
        }
        .event-content h3 {
            font-size: 22px;
            margin-bottom: 12px;
            color: #333;
        }
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-bottom: 15px;
            font-size: 14px;
            color: #666;
        }
        .event-meta-item {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-meta-item i {
            color: #4361EE;
            width: 16px;
        }
        .event-description {
            color: #666;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .event-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        .organizer-info {
            font-size: 13px;
            color: #888;
        }
        .view-details-btn {
            padding: 10px 20px;
            background: #4361EE;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            transition: background 0.3s;
        }
        .view-details-btn:hover {
            background: #3755D8;
        }
        .no-events {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .no-events i {
            font-size: 80px;
            color: #ddd;
            margin-bottom: 25px;
        }
        .no-events h2 {
            font-size: 28px;
            color: #333;
            margin-bottom: 15px;
        }
        .no-events p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <!-- Header & Navbar -->
    <header>
        <div class="container">
            <nav>
                <div class="logo">
                    <a href="index.html">
                        <img src="assets/logo.png" alt="EventHub Logo">
                    </a>
                </div>
                <ul class="nav-links">
                    <li><a href="index.html">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="events.php" class="active">Events</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <?php if ($_SESSION['role'] === 'organizer'): ?>
                            <li><a href="create_event.php">Create Event</a></li>
                        <?php endif; ?>
                        <li><a href="my_events.php">My Events</a></li>
                    <?php endif; ?>
                </ul>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="user-menu">
                        <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['first_name']); ?> <i class="fas fa-chevron-down"></i></span>
                        <div class="dropdown-menu">
                            <a href="<?php echo ($_SESSION['role'] === 'organizer') ? 'organizer_dashboard.php' : 'attendee_dashboard.php'; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="register-btn">
                        <a href="register.html" class="btn">Register</a>
                    </div>
                <?php endif; ?>
                <div class="hamburger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Events Hero Section -->
    <section class="events-hero">
        <div class="container">
            <h1><i class="fas fa-calendar-alt"></i> Discover Events</h1>
            <p>Browse amazing events from talented organizers around the world</p>
        </div>
    </section>

    <!-- Events Display Section -->
    <section class="events-section">
        <div class="container">
            <?php if ($hasEvents): ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <img 
                                src="assets/events/<?php echo htmlspecialchars($event['event_image']); ?>" 
                                alt="<?php echo htmlspecialchars($event['event_name']); ?>"
                                class="event-image"
                                onerror="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.src=''"
                            >
                            <div class="event-content">
                                <h3><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                
                                <div class="event-meta">
                                    <div class="event-meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo formatEventDate($event['event_date']); ?></span>
                                    </div>
                                    <div class="event-meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo formatEventTime($event['event_time']); ?></span>
                                    </div>
                                    <?php if (!empty($event['event_location'])): ?>
                                    <div class="event-meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="event-description">
                                    <?php echo htmlspecialchars($event['event_description']); ?>
                                </p>
                                
                                <div class="event-footer">
                                    <div class="organizer-info">
                                        <i class="fas fa-user"></i>
                                        <?php 
                                            if (!empty($event['organization_name'])) {
                                                echo htmlspecialchars($event['organization_name']);
                                            } else {
                                                echo htmlspecialchars($event['first_name'] . ' ' . $event['last_name']);
                                            }
                                        ?>
                                    </div>
                                    <a href="event_details.php?id=<?php echo $event['id']; ?>" class="view-details-btn">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-events">
                    <i class="fas fa-calendar-times"></i>
                    <h2>No Events Available</h2>
                    <p>There are currently no events to display. Check back soon for exciting events!</p>
                    <a href="index.html" class="btn">Back to Home</a>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-logo">
                    <img src="assets/logo.png" alt="EventHub Logo">
                    <p>Your premier platform for discovering and creating unforgettable event experiences.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.html">Home</a></li>
                        <li><a href="about.html">About Us</a></li>
                        <li><a href="events.php">Events</a></li>
                        <li><a href="contact.html">Contact</a></li>
                        <li><a href="register.html">Register</a></li>
                    </ul>
                </div>
                
                <div class="footer-links">
                    <h3>Resources</h3>
                    <ul>
                        <li><a href="#">Help Center</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Blog</a></li>
                    </ul>
                </div>
                
                <div class="footer-contact">
                    <h3>Contact Us</h3>
                    <p><i class="fas fa-map-marker-alt"></i> 123 Event Street, New York, NY 10001</p>
                    <p><i class="fas fa-phone"></i> +1 (555) 123-4567</p>
                    <p><i class="fas fa-envelope"></i> info@eventhub.com</p>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; 2026 EventHub. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
