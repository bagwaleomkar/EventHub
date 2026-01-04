# 🧪 EVENTRA TESTING GUIDE

## Quick Start Testing Instructions

### Prerequisites
1. ✅ XAMPP is running (Apache + MySQL)
2. ✅ Database `eventra_db` exists
3. ✅ Tables `users` and `events` are created
4. ✅ Application accessible at: `http://localhost/Eventorg/OMI/`

---

## 🎯 CRITICAL PATH TESTING

### Test 1: Organizer Registration & Event Creation (5 minutes)

**Step 1: Register as Organizer**
1. Open: `http://localhost/Eventorg/OMI/register.html`
2. Click "Organizer" tab
3. Fill the form:
   - First Name: Test
   - Last Name: Organizer
   - Email: test.organizer@example.com
   - Phone: 1234567890
   - Organization: Test Events Inc
   - Password: Test@123
   - Confirm Password: Test@123
   - Check at least one Event Type
4. Click "Register as Organizer"
5. **Expected:** Success message appears

**Step 2: Login**
1. Click "Login" link or go to: `http://localhost/Eventorg/OMI/login.html`
2. Enter:
   - Email: test.organizer@example.com
   - Password: Test@123
3. Click "Login"
4. **Expected:** 
   - Redirect to Organizer Dashboard
   - Navigation shows "Hi, Test" with dropdown
   - "Create Event" link visible in navigation

**Step 3: Create Event**
1. Click "Create Event" in navigation
2. Fill the form:
   - Event Name: Tech Conference 2026
   - Description: An amazing tech conference with industry leaders
   - Date: Select any future date (e.g., 2026-02-15)
   - Time: 14:00
   - Location: Convention Center, San Francisco
   - Image: Upload any image (optional)
3. Click "Create Event"
4. **Expected:**
   - Success message appears
   - Automatically redirected to "My Events" page
   - See newly created event with Edit/Delete buttons

**Step 4: View Event on Public Page**
1. Click "Events" in navigation
2. **Expected:**
   - See "Tech Conference 2026" event card
   - Event shows correct date, time, location
   - "View Details" button visible
3. Click "View Details"
4. **Expected:**
   - Full event details page loads
   - Hero image displayed (or gradient if no image)
   - All event information visible
   - Organizer info card shows "Test Events Inc"

---

### Test 2: Attendee Registration & Browsing (3 minutes)

**Step 1: Logout**
1. Click dropdown menu (Hi, Test)
2. Click "Logout"
3. **Expected:** Return to homepage with "Register" button

**Step 2: Register as Attendee**
1. Go to: `http://localhost/Eventorg/OMI/register.html`
2. Stay on "Attendee" tab (default)
3. Fill the form:
   - First Name: John
   - Last Name: Attendee
   - Email: john.attendee@example.com
   - Password: Attend@123
   - Confirm Password: Attend@123
   - Check at least one Interest
   - Check "Subscribe to Newsletter" (optional)
   - Check "I agree to Terms & Conditions"
4. Click "Register as Attendee"
5. **Expected:** Success message appears

**Step 3: Login as Attendee**
1. Go to login page
2. Enter:
   - Email: john.attendee@example.com
   - Password: Attend@123
3. Click "Login"
4. **Expected:**
   - Redirect to Attendee Dashboard
   - Navigation shows "Hi, John" with dropdown
   - NO "Create Event" link (organizers only)
   - "My Events" link visible

**Step 4: Browse Events**
1. Click "Events" in navigation
2. **Expected:**
   - See all published events
   - Can click "View Details" on any event
3. Click "My Events"
4. **Expected:**
   - See list of upcoming events (for future registration feature)

---

### Test 3: Access Control Validation (2 minutes)

**Test 3a: Attendee Cannot Create Events**
1. While logged in as attendee (john.attendee@example.com)
2. Try to access: `http://localhost/Eventorg/OMI/create_event.php`
3. **Expected:** Redirected to homepage (unauthorized)

**Test 3b: Cannot Access Without Login**
1. Logout
2. Try to access: `http://localhost/Eventorg/OMI/organizer_dashboard.php`
3. **Expected:** Redirected to login page

**Test 3c: Role-Based Dashboard Access**
1. Login as organizer (test.organizer@example.com)
2. Try to access: `http://localhost/Eventorg/OMI/attendee_dashboard.php`
3. **Expected:** Redirected to organizer_dashboard.php
4. Logout, login as attendee (john.attendee@example.com)
5. Try to access: `http://localhost/Eventorg/OMI/organizer_dashboard.php`
6. **Expected:** Redirected to attendee_dashboard.php

---

### Test 4: Event Management (2 minutes)

**Step 1: View My Events (as Organizer)**
1. Login as organizer (test.organizer@example.com)
2. Click "My Events" in navigation
3. **Expected:**
   - See all events created by this organizer
   - Each event has Edit and Delete buttons
   - Creation date displayed

**Step 2: Delete Event**
1. Click "Delete" button on any event
2. **Expected:** Confirmation dialog appears
3. Click "OK" to confirm
4. **Expected:**
   - Success message
   - Event removed from list
   - Event also removed from public Events page

**Step 3: Create Another Event**
1. Click "Create Event"
2. Create a quick test event
3. **Expected:** Appears in My Events and public Events page

---

## ✅ EXPECTED RESULTS SUMMARY

### All Tests Should Pass With:
- ✅ No 404 errors
- ✅ No database connection errors
- ✅ No "undefined variable" PHP warnings
- ✅ No JavaScript console errors
- ✅ Proper redirects based on login status
- ✅ Role-based access control working
- ✅ Dynamic navigation updating correctly
- ✅ Forms submitting successfully
- ✅ Data persisting in database
- ✅ Images uploading correctly (if provided)

---

## 🐛 TROUBLESHOOTING

### Issue: "Database connection failed"
**Solution:**
1. Open XAMPP Control Panel
2. Check MySQL is running (green highlight)
3. Click "Shell" button
4. Run: `mysql -u root -p`
5. Run: `SHOW DATABASES;`
6. Verify `eventra_db` exists
7. If not, run: `CREATE DATABASE eventra_db;`
8. Import schema: `mysql -u root eventra_db < C:/xampp/htdocs/Eventorg/OMI/database.sql`

### Issue: "Table 'eventra_db.users' doesn't exist"
**Solution:**
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Select `eventra_db` database
3. Click "Import" tab
4. Choose file: `C:/xampp/htdocs/Eventorg/OMI/database.sql`
5. Click "Go"

### Issue: "Cannot upload image"
**Solution:**
1. Check folder exists: `C:/xampp/htdocs/Eventorg/OMI/assets/events/`
2. If not, create it manually
3. Check folder permissions (should be writable)

### Issue: "Password does not meet requirements"
**Solution:**
Password must have:
- At least 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
Example: `Test@123`

### Issue: Navigation doesn't show user name after login
**Solution:**
1. Check browser console (F12) for JavaScript errors
2. Verify `check_session.php` is accessible
3. Try clearing browser cache and cookies
4. Make sure cookies are enabled in browser

### Issue: Events page shows "No events available"
**Solution:**
1. Verify you created an event successfully
2. Check database:
   - Open phpMyAdmin
   - Go to `eventra_db` → `events` table
   - Click "Browse" to see records
3. If empty, create an event through the UI

---

## 🎓 TEST CREDENTIALS (After Registration)

### Organizer Account
- Email: test.organizer@example.com
- Password: Test@123
- Role: Organizer
- Can: Create, Edit, Delete events

### Attendee Account  
- Email: john.attendee@example.com
- Password: Attend@123
- Role: Attendee
- Can: Browse and view events (registration feature coming soon)

---

## 📊 VERIFICATION CHECKLIST

After running all tests above, verify:

- [ ] Both organizer and attendee can register successfully
- [ ] Both roles can login successfully
- [ ] Navigation updates dynamically after login
- [ ] Organizers can create events
- [ ] Events appear on public events page
- [ ] Event details page shows all information
- [ ] Organizers can delete their own events
- [ ] Attendees cannot access create event page
- [ ] Role-based dashboards redirect correctly
- [ ] Logout works and returns to homepage
- [ ] No PHP errors in `logs/php_errors.log`
- [ ] Activity logged in `logs/activity.log`

---

## 🚀 READY FOR USE

If all tests pass:
✅ **Application is fully functional and ready for production deployment!**

If any tests fail:
1. Check the troubleshooting section above
2. Review `BUG_FIXES.md` for known issues
3. Check log files in `logs/` directory
4. Verify database schema matches `database.sql`

---

*Testing Guide Created: January 4, 2026*
*For: Eventra Event Management Platform v1.0*
