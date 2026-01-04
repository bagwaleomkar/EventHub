# EventHub Setup Instructions

## Problem
You're seeing CORS errors because PHP files need to run on a web server, not directly from the file system.

## Solution: Install and Setup XAMPP

### Step 1: Install XAMPP
1. Download XAMPP from: https://www.apachefriends.org/download.html
2. Install it (recommended location: `C:\xampp`)
3. Follow the installation wizard

### Step 2: Setup Database
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services
3. Click **Admin** button next to MySQL (opens phpMyAdmin)
4. Create a new database:
   - Click "New" in the left sidebar
   - Database name: `eventra_db`
   - Collation: `utf8mb4_general_ci`
   - Click "Create"
5. Select the `eventra_db` database
6. Click "Import" tab
7. Choose the `database.sql` file from your project
8. Click "Go" to import

### Step 3: Move Project Files
1. Copy the entire `OMI` folder
2. Paste it into: `C:\xampp\htdocs\`
3. Your path should be: `C:\xampp\htdocs\OMI\`

### Step 4: Update Database Configuration (if needed)
Edit `C:\xampp\htdocs\OMI\config\database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventra_db');
define('DB_USER', 'root');
define('DB_PASS', '');  // Leave empty for XAMPP default
```

### Step 5: Access Your Website
Open your browser and go to:
- **Homepage**: http://localhost/OMI/index.html
- **Registration**: http://localhost/OMI/register.html
- **About**: http://localhost/OMI/about.html

### Step 6: Test Registration
1. Go to http://localhost/OMI/register.html
2. Fill in the registration form
3. Submit - it should now work without CORS errors!

## Alternative: Use PHP Built-in Server (After Installing PHP)

If you install PHP separately, you can use the built-in server:

1. Open PowerShell/Command Prompt
2. Navigate to your project:
   ```
   cd C:\Users\Lenovo\Desktop\Eventorg\OMI
   ```
3. Start PHP server:
   ```
   php -S localhost:8000
   ```
4. Access via: http://localhost:8000/register.html

## Troubleshooting

### XAMPP Apache won't start
- Port 80 might be in use
- Check if Skype or other programs are using port 80
- Change Apache port in XAMPP config to 8080

### Database connection fails
- Make sure MySQL is running in XAMPP
- Verify database name is `eventra_db`
- Check username/password in `config/database.php`

### Still getting CORS errors
- Make sure you're using `http://localhost/` NOT `file://`
- Clear browser cache
- Try a different browser

## Need Help?
- XAMPP Documentation: https://www.apachefriends.org/docs.html
- Check Apache error log: `C:\xampp\apache\logs\error.log`
- Check PHP error log: `C:\xampp\php\logs\php_error_log`
