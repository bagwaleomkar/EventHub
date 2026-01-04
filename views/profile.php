<?php
/**
 * User Profile Page
 * Access restricted to logged-in users
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.html');
    exit();
}

$username = $_SESSION['username'];
$first_name = $_SESSION['first_name'];
$email = $_SESSION['email'];
$role = $_SESSION['role'];
$isOrganizer = ($role === 'organizer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHub - My Profile</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-section {
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
        .profile-container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #f5f7fa;
        }
        .profile-avatar {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #4361EE, #2EC4B6);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 40px;
            color: white;
        }
        .profile-info {
            margin-bottom: 30px;
        }
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f5f7fa;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .info-item i {
            width: 30px;
            color: #4361EE;
            font-size: 18px;
        }
        .info-item label {
            font-weight: 600;
            color: #666;
            min-width: 100px;
        }
        .info-item span {
            color: #333;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }
        .role-badge.organizer {
            background: #E3F2FD;
            color: #1976D2;
        }
        .role-badge.attendee {
            background: #F3E5F5;
            color: #7B1FA2;
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
                    <li><a href="views/events/my_events.php">My Events</a></li>
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

    <section class="profile-section">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-user-circle"></i> My Profile</h1>
            </div>
            
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <h2><?php echo htmlspecialchars($username); ?></h2>
                    <span class="role-badge <?php echo $role; ?>">
                        <i class="fas fa-<?php echo $isOrganizer ? 'user-tie' : 'user'; ?>"></i>
                        <?php echo ucfirst($role); ?>
                    </span>
                </div>

                <div class="profile-info">
                    <div class="info-item">
                        <i class="fas fa-user"></i>
                        <label>Name:</label>
                        <span><?php echo htmlspecialchars($username); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-envelope"></i>
                        <label>Email:</label>
                        <span><?php echo htmlspecialchars($email); ?></span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-id-badge"></i>
                        <label>Role:</label>
                        <span><?php echo ucfirst($role); ?></span>
                    </div>
                </div>

                <div style="text-align: center;">
                    <p style="color: #666; margin-bottom: 20px;">
                        <i class="fas fa-info-circle"></i> Profile editing feature coming soon!
                    </p>
                    <a href="<?php echo $isOrganizer ? 'organizer_dashboard.php' : 'attendee_dashboard.php'; ?>" class="btn">
                        <i class="fas fa-arrow-left"></i> Back to Dashboard
                    </a>
                </div>
            </div>
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
</body>
</html>
