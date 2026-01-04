<?php
/**
 * Organizer Dashboard
 * Access restricted to logged-in organizers only
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: login.html');
    exit();
}

// Check if user is organizer
if ($_SESSION['role'] !== 'organizer') {
    header('Location: attendee_dashboard.php');
    exit();
}

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - Organizer Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .dashboard-section {
            min-height: 80vh;
            padding: 50px 0;
            background: #f5f7fa;
        }
        .dashboard-header {
            margin-bottom: 40px;
        }
        .dashboard-header h1 {
            color: #333;
            font-size: 32px;
            margin-bottom: 10px;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            gap: 20px;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .stat-icon.blue { background: #4361EE; }
        .stat-icon.green { background: #2EC4B6; }
        .stat-icon.orange { background: #FF9F1C; }
        .stat-icon.purple { background: #9C27B0; }
        .stat-info h3 {
            font-size: 28px;
            color: #333;
            margin-bottom: 5px;
        }
        .stat-info p {
            color: #666;
            font-size: 14px;
        }
        .quick-actions {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        .quick-actions h2 {
            margin-bottom: 25px;
            color: #333;
        }
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }
        .action-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
            padding: 20px;
            background: #f5f7fa;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            transition: 0.3s;
        }
        .action-btn:hover {
            background: #4361EE;
            color: white;
            transform: translateY(-5px);
        }
        .action-btn.primary {
            background: linear-gradient(135deg, #4361EE, #3755D8);
            color: white;
        }
        .action-btn i {
            font-size: 32px;
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
                    <li><a href="events.php">Events</a></li>
                    <li><a href="contact.html">Contact Us</a></li>
                    <li><a href="create_event.php">Create Event</a></li>
                    <li><a href="my_events.php">My Events</a></li>
                </ul>
                <div class="user-menu">
                    <span class="user-greeting">Hi, <?php echo htmlspecialchars($first_name); ?> <i class="fas fa-chevron-down"></i></span>
                    <div class="dropdown-menu">
                        <a href="organizer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
                <div class="hamburger">
                    <div class="line"></div>
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </nav>
        </div>
    </header>

    <!-- Dashboard Section -->
    <section class="dashboard-section">
        <div class="container">
            <div class="dashboard-header">
                <h1><i class="fas fa-chart-line"></i> Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
                <p>Manage your events and track performance</p>
            </div>

            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon blue">
                        <i class="fas fa-calendar-plus"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="stat-total">0</h3>
                        <p>Total Events</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="stat-registrations">0</h3>
                        <p>Total Registrations</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="stat-upcoming">0</h3>
                        <p>Upcoming Events</p>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon purple">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-info">
                        <h3 id="stat-past">0</h3>
                        <p>Past Events</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions">
                <h2><i class="fas fa-bolt"></i> Quick Actions</h2>
                <div class="action-buttons">
                    <a href="create_event.php" class="action-btn primary">
                        <i class="fas fa-plus-circle"></i>
                        <span>Create New Event</span>
                    </a>
                    <a href="my_events.php" class="action-btn">
                        <i class="fas fa-list"></i>
                        <span>Manage Events</span>
                    </a>
                    <a href="profile.php" class="action-btn">
                        <i class="fas fa-user-edit"></i>
                        <span>Edit Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2026 EventHub. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="script.js"></script>
    <script>
        // Load dashboard statistics
        fetch('get_dashboard_stats.php')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.stats) {
                    document.getElementById('stat-total').textContent = data.stats.total_events;
                    document.getElementById('stat-registrations').textContent = data.stats.total_registrations;
                    document.getElementById('stat-upcoming').textContent = data.stats.upcoming_events;
                    document.getElementById('stat-past').textContent = data.stats.past_events;
                }
            })
            .catch(error => console.error('Error loading stats:', error));
    </script>
</body>
</html>
