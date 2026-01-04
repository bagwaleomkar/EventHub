<?php
/**
 * Event Details Page
 * Display full details of a specific event
 */

session_start();
require_once __DIR__ . '/../../config/database.php';

// Get event ID from URL
$event_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($event_id <= 0) {
    header('Location: ../events/events.php');
    exit();
}

// Get database connection
$database = new Database();
$conn = $database->getConnection();

$event = null;
$organizer = null;
$is_registered = false;
$is_organizer = false;

if ($conn) {
    try {
        // Fetch event details with organizer info
        $stmt = $conn->prepare("
            SELECT 
                e.*,
                u.first_name,
                u.last_name,
                u.email as organizer_email,
                u.phone as organizer_phone,
                u.organization_name
            FROM events e
            INNER JOIN users u ON e.organizer_id = u.user_id
            WHERE e.id = :event_id AND u.account_status = 'active'
        ");
        $stmt->bindParam(':event_id', $event_id, PDO::PARAM_INT);
        $stmt->execute();
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$event) {
            header('Location: ../events/events.php?error=event_not_found');
            exit();
        }
        
        // Check if user is logged in and check registration status
        if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
            $user_id = $_SESSION['user_id'];
            $role = $_SESSION['role'];
            
            // Check if user is the organizer
            $is_organizer = ($event['organizer_id'] == $user_id);
            
            // Check if attendee is registered
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
        }
        
    } catch (PDOException $e) {
        error_log("Error fetching event details: " . $e->getMessage());
        header('Location: ../events/events.php?error=database_error');
        exit();
    }
} else {
    header('Location: ../events/events.php?error=connection_failed');
    exit();
}

// Format date and time
function formatEventDate($date) {
    return date('l, F j, Y', strtotime($date));
}

function formatEventTime($time) {
    return date('g:i A', strtotime($time));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($event['event_name']); ?> - EventHub</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .event-details-section {
            padding: 60px 0;
            background: #f5f7fa;
            min-height: 80vh;
        }
        .event-hero {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .event-hero-image {
            width: 100%;
            height: 400px;
            object-fit: cover;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .event-details-content {
            padding: 40px;
        }
        .event-title {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
        }
        .event-meta-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            padding: 20px 0;
            border-top: 2px solid #eee;
            border-bottom: 2px solid #eee;
            margin-bottom: 30px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 16px;
        }
        .meta-item i {
            color: #4361EE;
            font-size: 20px;
            width: 25px;
        }
        .meta-item strong {
            color: #333;
        }
        .event-description {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .event-description h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }
        .event-description p {
            color: #666;
            line-height: 1.8;
            font-size: 16px;
        }
        .organizer-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .organizer-card h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 20px;
        }
        .organizer-info {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .organizer-info-item {
            display: flex;
            align-items: center;
            gap: 15px;
            color: #666;
            font-size: 16px;
        }
        .organizer-info-item i {
            color: #4361EE;
            width: 25px;
            font-size: 18px;
        }
        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        .btn-register, .btn-back {
            padding: 15px 30px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }
        .btn-register {
            background: #4361EE;
            color: white;
        }
        .btn-register:hover {
            background: #3755D8;
        }
        .btn-back {
            background: #f0f0f0;
            color: #333;
        }
        .btn-back:hover {
            background: #e0e0e0;
        }
        .grid-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 30px;
        }
        @media (max-width: 968px) {
            .grid-layout {
                grid-template-columns: 1fr;
            }
            .event-hero-image {
                height: 300px;
            }
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
                    <li><a href="views/about.html">About</a></li>
                    <li><a href="views/events/events.php">Events</a></li>
                    <li><a href="views/contact.html">Contact Us</a></li>
                    <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                        <?php if ($_SESSION['role'] === 'organizer'): ?>
                            <li><a href="views/events/create_event.php">Create Event</a></li>
                        <?php endif; ?>
                        <li><a href="views/events/my_events.php">My Events</a></li>
                    <?php endif; ?>
                </ul>
                <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                    <div class="user-menu">
                        <span class="user-greeting">Hi, <?php echo htmlspecialchars($_SESSION['first_name']); ?> <i class="fas fa-chevron-down"></i></span>
                        <div class="dropdown-menu">
                            <a href="<?php echo ($_SESSION['role'] === 'organizer') ? 'organizer_dashboard.php' : 'attendee_dashboard.php'; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                            <a href="views/profile.php"><i class="fas fa-user"></i> Profile</a>
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="register-btn">
                        <a href="views/auth/register.html" class="btn">Register</a>
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

    <!-- Event Details Section -->
    <section class="event-details-section">
        <div class="container">
            <div class="event-hero">
                <img 
                    src="assets/events/<?php echo htmlspecialchars($event['event_image']); ?>" 
                    alt="<?php echo htmlspecialchars($event['event_name']); ?>"
                    class="event-hero-image"
                    onerror="this.style.background='linear-gradient(135deg, #667eea 0%, #764ba2 100%)'; this.src=''"
                >
                <div class="event-details-content">
                    <h1 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h1>
                    
                    <div class="event-meta-bar">
                        <div class="meta-item">
                            <i class="fas fa-calendar"></i>
                            <span><strong>Date:</strong> <?php echo formatEventDate($event['event_date']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Time:</strong> <?php echo formatEventTime($event['event_time']); ?></span>
                        </div>
                        <div class="meta-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span><strong>Location:</strong> <?php echo htmlspecialchars($event['event_location']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="grid-layout">
                <div>
                    <div class="event-description">
                        <h2><i class="fas fa-info-circle"></i> About This Event</h2>
                        <p><?php echo nl2br(htmlspecialchars($event['event_description'])); ?></p>
                    </div>
                    
                    <div class="action-buttons">
                        <?php if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true): ?>
                            <?php if ($_SESSION['role'] === 'attendee' && !$is_organizer): ?>
                                <?php if ($is_registered): ?>
                                    <button class="btn-register" id="registrationBtn" onclick="unregisterFromEvent(<?php echo $event['id']; ?>)" style="background: #28a745;">
                                        <i class="fas fa-check-circle"></i> Already Registered
                                    </button>
                                <?php else: ?>
                                    <button class="btn-register" id="registrationBtn" onclick="registerForEvent(<?php echo $event['id']; ?>)">
                                        <i class="fas fa-ticket-alt"></i> Register for Event
                                    </button>
                                <?php endif; ?>
                            <?php elseif ($is_organizer): ?>
                                <button class="btn-register" style="background: #6c757d; cursor: not-allowed;" disabled>
                                    <i class="fas fa-user-tie"></i> You are the Organizer
                                </button>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="views/auth/login.html" class="btn-register">
                                <i class="fas fa-sign-in-alt"></i> Login to Register
                            </a>
                        <?php endif; ?>
                        <a href="views/events/events.php" class="btn-back">
                            <i class="fas fa-arrow-left"></i> Back to Events
                        </a>
                    </div>
                </div>
                
                <div>
                    <div class="organizer-card">
                        <h2><i class="fas fa-user-tie"></i> Organizer</h2>
                        <div class="organizer-info">
                            <?php if (!empty($event['organization_name'])): ?>
                            <div class="organizer-info-item">
                                <i class="fas fa-building"></i>
                                <span><?php echo htmlspecialchars($event['organization_name']); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="organizer-info-item">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($event['first_name'] . ' ' . $event['last_name']); ?></span>
                            </div>
                            <div class="organizer-info-item">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($event['organizer_email']); ?></span>
                            </div>
                            <?php if (!empty($event['organizer_phone'])): ?>
                            <div class="organizer-info-item">
                                <i class="fas fa-phone"></i>
                                <span><?php echo htmlspecialchars($event['organizer_phone']); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 EventHub. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../public/js/script.js"></script>
    <script>
        function registerForEvent(eventId) {
            const btn = document.getElementById('registrationBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registering...';
            
            fetch('../../handlers/register_for_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'event_id=' + eventId + '&action=register'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.style.background = '#28a745';
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Already Registered';
                    btn.onclick = function() { unregisterFromEvent(eventId); };
                    alert(data.message);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-ticket-alt"></i> Register for Event';
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-ticket-alt"></i> Register for Event';
                alert('An error occurred. Please try again.');
            });
        }
        
        function unregisterFromEvent(eventId) {
            if (!confirm('Are you sure you want to cancel your registration for this event?')) {
                return;
            }
            
            const btn = document.getElementById('registrationBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Cancelling...';
            
            fetch('../../handlers/register_for_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'event_id=' + eventId + '&action=unregister'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.style.background = '#4361EE';
                    btn.innerHTML = '<i class="fas fa-ticket-alt"></i> Register for Event';
                    btn.onclick = function() { registerForEvent(eventId); };
                    alert(data.message);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-check-circle"></i> Already Registered';
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-check-circle"></i> Already Registered';
                alert('An error occurred. Please try again.');
            });
        }
    </script>
</body>
</html>
