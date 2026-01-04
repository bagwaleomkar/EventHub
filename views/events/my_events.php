<?php
/**
 * My Events Page
 * Shows events based on user role
 * - Attendees: Events they registered for
 * - Organizers: Events they created
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.html');
    exit();
}

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];
$isOrganizer = ($role === 'organizer');

// Connect to database
require_once __DIR__ . '/../../config/database.php';
$database = new Database();
$conn = $database->getConnection();

// Fetch events based on role
$events = [];
if ($conn !== null) {
    if ($isOrganizer) {
        // Get events created by this organizer
        $stmt = $conn->prepare("
            SELECT * FROM events 
            WHERE organizer_id = :user_id 
            ORDER BY event_date DESC, event_time DESC
        ");
        $stmt->bindParam(':user_id', $user_id);
    } else {
        // Get events registered by this attendee
        $stmt = $conn->prepare("
            SELECT e.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as organizer_name,
                   er.registration_date
            FROM event_registrations er
            JOIN events e ON er.event_id = e.id
            JOIN users u ON e.organizer_id = u.user_id
            WHERE er.attendee_id = :user_id AND er.status = 'registered'
            ORDER BY e.event_date ASC, e.event_time ASC
        ");
        $stmt->bindParam(':user_id', $user_id);
    }
    
    $stmt->execute();
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - My Events</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .my-events-section {
            min-height: 80vh;
            padding: 50px 0;
            background: #f5f7fa;
        }
        .page-header {
            margin-bottom: 40px;
        }
        .page-header h1 {
            color: #333;
            font-size: 32px;
        }
        .empty-state {
            background: white;
            padding: 60px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .empty-state i {
            font-size: 80px;
            color: #ccc;
            margin-bottom: 30px;
        }
        .empty-state h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .empty-state p {
            color: #666;
            font-size: 16px;
            margin-bottom: 30px;
        }
        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .event-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .event-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        }
        .event-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .event-content {
            padding: 20px;
        }
        .event-title {
            font-size: 22px;
            color: #333;
            margin-bottom: 10px;
            font-weight: 600;
        }
        .event-meta {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 15px;
        }
        .meta-item {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #666;
            font-size: 14px;
        }
        .meta-item i {
            color: #667eea;
            width: 20px;
        }
        .event-description {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .event-actions {
            display: flex;
            gap: 10px;
        }
        .btn-edit, .btn-delete, .btn-view {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background 0.3s;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .btn-edit {
            background: #667eea;
            color: white;
        }
        .btn-edit:hover {
            background: #5568d3;
        }
        .btn-delete {
            background: #ef4444;
            color: white;
        }
        .btn-delete:hover {
            background: #dc2626;
        }
        .btn-view {
            background: #10b981;
            color: white;
        }
        .btn-view:hover {
            background: #059669;
        }
        .event-stats {
            background: #f8fafc;
            padding: 10px;
            border-radius: 5px;
            margin-top: 15px;
            font-size: 14px;
            color: #666;
        }
    </style>
</head>
<body>
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
                    <?php if ($isOrganizer): ?>
                    <li><a href="views/events/create_event.php">Create Event</a></li>
                    <?php endif; ?>
                    <li><a href="views/events/my_events.php" class="active">My Events</a></li>
                </ul>
                <div class="user-menu">
                    <span class="user-greeting">Hi, <?php echo htmlspecialchars($first_name); ?> <i class="fas fa-chevron-down"></i></span>
                    <div class="dropdown-menu">
                        <a href="<?php echo $isOrganizer ? 'organizer_dashboard.php' : 'attendee_dashboard.php'; ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="views/profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <section class="my-events-section">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-calendar-alt"></i> My Events</h1>
                <p><?php echo $isOrganizer ? 'Manage all your created events' : 'View all events you\'re registered for'; ?></p>
            </div>
            
            <?php if (count($events) === 0): ?>
                <div class="empty-state">
                    <i class="fas fa-calendar-times"></i>
                    <h2>No Events Yet</h2>
                    <p><?php echo $isOrganizer ? 'You haven\'t created any events yet. Start by creating your first event!' : 'You haven\'t registered for any events yet. Browse events to find something interesting!'; ?></p>
                    <a href="<?php echo $isOrganizer ? 'create_event.php' : 'events.php'; ?>" class="btn">
                        <i class="fas fa-<?php echo $isOrganizer ? 'plus-circle' : 'search'; ?>"></i>
                        <?php echo $isOrganizer ? 'Create Event' : 'Browse Events'; ?>
                    </a>
                </div>
            <?php else: ?>
                <div class="events-grid">
                    <?php foreach ($events as $event): ?>
                        <div class="event-card">
                            <img src="assets/events/<?php echo htmlspecialchars($event['event_image']); ?>" 
                                 alt="<?php echo htmlspecialchars($event['event_name']); ?>" 
                                 class="event-image"
                                 onerror="this.src='assets/event-placeholder.jpg'">
                            <div class="event-content">
                                <h3 class="event-title"><?php echo htmlspecialchars($event['event_name']); ?></h3>
                                
                                <div class="event-meta">
                                    <div class="meta-item">
                                        <i class="fas fa-calendar"></i>
                                        <span><?php echo date('F j, Y', strtotime($event['event_date'])); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?php echo date('g:i A', strtotime($event['event_time'])); ?></span>
                                    </div>
                                    <div class="meta-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?php echo htmlspecialchars($event['event_location']); ?></span>
                                    </div>
                                    <?php if (!$isOrganizer): ?>
                                    <div class="meta-item">
                                        <i class="fas fa-user"></i>
                                        <span>Organized by <?php echo htmlspecialchars($event['organizer_name']); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <p class="event-description"><?php echo htmlspecialchars($event['event_description']); ?></p>
                                
                                <div class="event-actions">
                                    <?php if ($isOrganizer): ?>
                                        <a href="edit_event.php?id=<?php echo $event['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <button class="btn-delete" onclick="deleteEvent(<?php echo $event['id']; ?>, '<?php echo htmlspecialchars(addslashes($event['event_name'])); ?>')">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    <?php else: ?>
                                        <a href="views/events/event_details.php?id=<?php echo $event['id']; ?>" class="btn-view">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($isOrganizer): ?>
                                <div class="event-stats">
                                    <i class="fas fa-info-circle"></i> Created on <?php echo date('M j, Y', strtotime($event['created_at'])); ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 EventHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="../public/js/script.js"></script>
    <script>
        function deleteEvent(eventId, eventName) {
            if (!confirm(`Are you sure you want to delete "${eventName}"?\n\nThis action cannot be undone.`)) {
                return;
            }
            
            // Show loading state
            event.target.disabled = true;
            event.target.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
            
            // Send delete request
            fetch('delete_event.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'event_id=' + eventId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                    event.target.disabled = false;
                    event.target.innerHTML = '<i class="fas fa-trash"></i> Delete';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the event');
                event.target.disabled = false;
                event.target.innerHTML = '<i class="fas fa-trash"></i> Delete';
            });
        }
    </script>
</body>
</html>
