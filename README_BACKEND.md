# EventHub Backend - Setup Instructions

## 📋 Prerequisites

- **XAMPP** (or similar) with PHP 7.4+ and MySQL
- Web browser
- Text editor

## 🚀 Setup Steps

### 1. Database Setup

1. Start XAMPP (Apache and MySQL)
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Import the database:
   - Click "Import" tab
   - Choose `database.sql` file
   - Click "Go"
   
   OR run these commands in SQL tab:
   ```sql
   CREATE DATABASE IF NOT EXISTS eventra_db;
   USE eventra_db;
   ```
   Then copy and paste the contents of `database.sql`

### 2. Configure Database Connection

1. Open `config/database.php`
2. Update credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'eventra_db');
   define('DB_USER', 'root');      // Your MySQL username
   define('DB_PASS', '');          // Your MySQL password
   ```

### 3. File Structure

Make sure you have this structure:
```
OMI/
├── config/
│   └── database.php
├── includes/
│   └── functions.php
├── logs/
│   └── activity.log
├── assets/
├── register_attendee.php
├── register_organizer.php
├── register.html
├── script.js
└── database.sql
```

### 4. Testing

1. Open: `http://localhost/OMI/register.html`
2. Try registering as:
   - **Attendee** - Fill out attendee form
   - **Organizer** - Click organizer tab and fill form

### 5. Verify Registration

Check database:
```sql
USE eventra_db;
SELECT * FROM users;
```

## 🔒 Security Features

✅ Password hashing with bcrypt
✅ SQL injection prevention (PDO prepared statements)
✅ XSS protection (input sanitization)
✅ Email validation
✅ Password strength requirements
✅ CSRF protection ready

## 📊 Database Schema

**users table:**
- user_id (Primary Key)
- first_name, last_name
- email (unique)
- password_hash
- phone
- user_role (attendee/organizer)
- organization_name
- interests (JSON)
- event_types (JSON)
- newsletter_subscribed
- account_status
- email_verified
- created_at, updated_at, last_login

## 🐛 Troubleshooting

**Connection Error:**
- Check XAMPP is running
- Verify database credentials in `config/database.php`
- Ensure database `eventra_db` exists

**Form not submitting:**
- Check browser console for errors
- Verify PHP files are in correct location
- Check Apache error logs

**Permission errors:**
- Ensure `logs/` folder is writable
- On Linux/Mac: `chmod 755 logs/`

## 📧 Next Steps

To enable email notifications:
1. Install PHPMailer: `composer require phpmailer/phpmailer`
2. Update `send_welcome_email()` function in `includes/functions.php`
3. Configure SMTP settings

## 🔐 Password Requirements

- Minimum 8 characters
- At least 1 uppercase letter
- At least 1 lowercase letter  
- At least 1 number

## 📝 Testing Credentials

After registration, test login with:
- Email: [your registered email]
- Password: [your password]

## 🎯 API Endpoints

- `POST /register_attendee.php` - Register attendee
- `POST /register_organizer.php` - Register organizer

Both return JSON:
```json
{
  "success": true/false,
  "message": "Message",
  "data": {...}
}
```
