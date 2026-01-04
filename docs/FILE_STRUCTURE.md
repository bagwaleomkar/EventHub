# EventHub - Organized File Structure

## 📁 Project Structure

```
OMI/
├── index.html                          # Homepage (root level)
├── .htaccess                          # Apache configuration
├── config/                            # Configuration files
│   ├── config.php                     # Main configuration
│   ├── paths.php                      # Path management
│   └── database.php                   # Database connection
├── includes/                          # Shared PHP includes
│   └── functions.php                  # Helper functions
├── public/                            # Public assets
│   ├── css/
│   │   └── styles.css                 # Main stylesheet
│   └── js/
│       └── script.js                  # Main JavaScript file
├── assets/                            # Uploaded files & images
│   ├── logo.png
│   ├── events/                        # Event images
│   └── ...
├── views/                             # View templates
│   ├── about.html                     # About page
│   ├── contact.html                   # Contact page
│   ├── profile.php                    # User profile
│   ├── auth/                          # Authentication views
│   │   ├── login.html                 # Login page
│   │   └── register.html              # Registration page
│   ├── dashboard/                     # Dashboard views
│   │   ├── attendee_dashboard.php     # Attendee dashboard
│   │   └── organizer_dashboard.php    # Organizer dashboard
│   └── events/                        # Event-related views
│       ├── events.php                 # Events listing
│       ├── event_details.php          # Event details
│       ├── my_events.php              # User's events
│       └── create_event.php           # Create event form
├── api/                               # API endpoints (JSON responses)
│   ├── check_session.php              # Session status
│   ├── check_registration.php         # Registration status
│   ├── get_dashboard_stats.php        # Dashboard statistics
│   └── get_registered_events.php      # Registered events list
├── handlers/                          # Form handlers & actions
│   ├── login.php                      # Login handler
│   ├── logout.php                     # Logout handler
│   ├── register_attendee.php          # Attendee registration
│   ├── register_organizer.php         # Organizer registration
│   ├── create_event_handler.php       # Event creation
│   ├── delete_event.php               # Event deletion
│   └── register_for_event.php         # Event registration handler
├── database/                          # Database scripts
│   ├── database.sql                   # Main database schema
│   ├── setup_events_table.sql         # Events table setup
│   └── create_registrations_table.php # Registration table setup
├── logs/                              # Application logs
├── tests/                             # Test files
│   ├── test_db.php
│   ├── test_upload.php
│   └── test_create_event.php
└── docs/                              # Documentation
    ├── README.md                      # This file
    ├── AUDIT_REPORT.md                # Code audit report
    ├── BUG_FIXES.md                   # Bug fixes documentation
    ├── TESTING_GUIDE.md               # Testing guide
    ├── REGISTRATION_SYSTEM_DOCS.md    # Registration system docs
    └── system_complete.html           # Feature completion guide
```

## 🎯 Directory Purpose

### `/config`
Contains all configuration files including database connection, path management, and application settings.

### `/includes`
Shared PHP functions and utilities used across multiple pages.

### `/public`
Publicly accessible static assets (CSS, JavaScript, images).

### `/assets`
User-uploaded content and application media files.

### `/views`
All user-facing pages organized by functionality:
- **auth/** - Login and registration pages
- **dashboard/** - User dashboards
- **events/** - Event-related pages

### `/api`
RESTful API endpoints that return JSON responses for AJAX requests.

### `/handlers`
Backend processing scripts that handle form submissions and actions.

### `/database`
SQL scripts and database setup files.

### `/logs`
Application logs and error logs.

### `/tests`
Test scripts for database, uploads, and functionality testing.

### `/docs`
Project documentation and guides.

## 🔧 Configuration

### Path Management
All paths are managed through `config/paths.php`. This ensures:
- Consistent path references across the application
- Easy maintenance and updates
- No hardcoded paths in individual files

### Usage Example:
```php
// Include configuration
require_once __DIR__ . '/config/config.php';

// Access paths
$css_path = CSS_DIR . '/styles.css';
$db_config = CONFIG_DIR . '/database.php';
```

## 🚀 Key Features

1. **Organized Structure** - Logical separation of concerns
2. **Centralized Configuration** - Single point for path management
3. **Security** - Handlers and API separated from views
4. **Maintainability** - Easy to locate and update files
5. **Scalability** - Structure supports growth

## 📝 Path Updates

All file paths have been updated to reflect the new structure:
- CSS/JS references updated in all HTML/PHP files
- Image paths updated to use new asset locations
- Include statements updated to use config paths
- Redirects updated to use new view locations

## 🔗 URL Structure

- **Homepage:** `/index.html`
- **Events:** `/views/events/events.php`
- **Login:** `/views/auth/login.html`
- **Register:** `/views/auth/register.html`
- **Dashboards:** `/views/dashboard/`
- **API:** `/api/`

## 🛠️ Development

### Adding New Files:

1. **Views** - Place in appropriate `/views` subdirectory
2. **API Endpoints** - Add to `/api`
3. **Handlers** - Add to `/handlers`
4. **Assets** - Add to `/public` or `/assets`

### Path References:

Always use the configuration:
```php
require_once __DIR__ . '/config/config.php';
```

Then use predefined constants for paths.

## ✅ Benefits

✓ **Clear Organization** - Easy to find files
✓ **Maintainable** - Centralized configuration
✓ **Secure** - Proper separation of concerns
✓ **Professional** - Industry-standard structure
✓ **Scalable** - Ready for growth

## 📚 Documentation

See `/docs` folder for comprehensive documentation:
- System features
- Bug fixes
- Testing guides
- API documentation

---

**Version:** 1.0.0
**Last Updated:** January 4, 2026
