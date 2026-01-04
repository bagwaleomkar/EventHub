<?php
/**
 * Create Event Page
 * Access restricted to organizers only
 */

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header('Location: ../auth/login.html');
    exit();
}

// Check if user is organizer
if ($_SESSION['role'] !== 'organizer') {
    header('Location: index.html?error=unauthorized');
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
    <title>EventHub - Create Event</title>
    <link rel="stylesheet" href="../public/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .create-event-section {
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
        .event-form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 15px;
        }
        .form-group label .required {
            color: #EF4444;
        }
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #4361EE;
        }
        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .image-upload-area {
            border: 2px dashed #ddd;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s;
        }
        .image-upload-area:hover {
            border-color: #4361EE;
        }
        .image-upload-area i {
            font-size: 48px;
            color: #999;
            margin-bottom: 15px;
        }
        .image-preview {
            max-width: 300px;
            max-height: 200px;
            margin: 15px auto;
            border-radius: 8px;
            display: none;
        }
        .btn-submit {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #4361EE, #3755D8);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.3s;
        }
        .btn-submit:hover {
            opacity: 0.9;
        }
        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .alert.success {
            background: #D1FAE5;
            color: #065F46;
            border-left: 4px solid #10B981;
        }
        .alert.error {
            background: #FEE2E2;
            color: #991B1B;
            border-left: 4px solid #EF4444;
        }
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
            .event-form-container {
                padding: 25px;
            }
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
                    <li><a href="views/events/create_event.php">Create Event</a></li>
                    <li><a href="views/events/my_events.php">My Events</a></li>
                </ul>
                <div class="user-menu">
                    <span class="user-greeting">Hi, <?php echo htmlspecialchars($first_name); ?> <i class="fas fa-chevron-down"></i></span>
                    <div class="dropdown-menu">
                        <a href="views/dashboard/organizer_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                        <a href="views/profile.php"><i class="fas fa-user"></i> Profile</a>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    </div>
                </div>
            </nav>
        </div>
    </header>

    <section class="create-event-section">
        <div class="container">
            <div class="page-header">
                <h1><i class="fas fa-plus-circle"></i> Create New Event</h1>
                <p>Fill in the details below to create your event</p>
            </div>
            
            <div class="event-form-container">
                <div id="message" class="alert"></div>

                <form id="createEventForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="eventName">Event Name <span class="required">*</span></label>
                        <input type="text" id="eventName" name="eventName" required placeholder="Enter event name">
                    </div>

                    <div class="form-group">
                        <label for="eventDescription">Event Description <span class="required">*</span></label>
                        <textarea id="eventDescription" name="eventDescription" required placeholder="Describe your event in detail..."></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="eventDate">Event Date <span class="required">*</span></label>
                            <input type="date" id="eventDate" name="eventDate" required>
                        </div>

                        <div class="form-group">
                            <label for="eventTime">Event Time <span class="required">*</span></label>
                            <input type="time" id="eventTime" name="eventTime" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="eventLocation">Event Location <span class="required">*</span></label>
                        <input type="text" id="eventLocation" name="eventLocation" required placeholder="e.g., Convention Center, New York">
                    </div>

                    <div class="form-group">
                        <label for="eventImage">Event Image (Optional)</label>
                        <input type="file" id="eventImage" name="eventImage" accept="image/*" style="display: none;">
                        <div class="image-upload-area" onclick="document.getElementById('eventImage').click()">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <p>Click to upload event image</p>
                            <small>Supported: JPG, PNG, GIF (Max 5MB)</small>
                        </div>
                        <img id="imagePreview" class="image-preview" alt="Preview">
                    </div>

                    <button type="submit" class="btn-submit">
                        <i class="fas fa-calendar-plus"></i> Create Event
                    </button>
                </form>
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
    <script>
        // Set minimum date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('eventDate').setAttribute('min', today);

        // Image preview
        document.getElementById('eventImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('createEventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const messageDiv = document.getElementById('message');
            const submitBtn = this.querySelector('.btn-submit');
            const formData = new FormData(this);
            
            // Validate date is not in the past
            const eventDate = new Date(document.getElementById('eventDate').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (eventDate < today) {
                messageDiv.className = 'alert error';
                messageDiv.style.display = 'block';
                messageDiv.textContent = 'Event date cannot be in the past';
                return;
            }
            
            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creating Event...';
            
            try {
                const response = await fetch('create_event_handler.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    messageDiv.className = 'alert success';
                    messageDiv.style.display = 'block';
                    messageDiv.textContent = result.message;
                    
                    // Reset form
                    this.reset();
                    document.getElementById('imagePreview').style.display = 'none';
                    
                    // Redirect after 2 seconds
                    setTimeout(() => {
                        window.location.href = 'my_events.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'alert error';
                    messageDiv.style.display = 'block';
                    messageDiv.textContent = result.message;
                    
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-calendar-plus"></i> Create Event';
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.className = 'alert error';
                messageDiv.style.display = 'block';
                messageDiv.textContent = 'An error occurred. Please try again.';
                
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-calendar-plus"></i> Create Event';
            }
        });
    </script>
</body>
</html>
