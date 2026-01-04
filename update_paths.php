
<?php
/**
 * Path Update Script
 * Updates all file paths to match the new organized structure
 */

echo "🔧 Starting path updates...\n\n";

// Define path mappings (old => new)
$path_replacements = [
    // CSS
    'href="styles.css"' => 'href="../public/css/styles.css"',
    'href="../../styles.css"' => 'href="../../public/css/styles.css"',
    
    // JavaScript
    'src="script.js"' => 'src="../public/js/script.js"',
    'src="../../script.js"' => 'src="../../public/js/script.js"',
    
    // Navigation links
    'href="about.html"' => 'href="views/about.html"',
    'href="contact.html"' => 'href="views/contact.html"',
    'href="events.php"' => 'href="views/events/events.php"',
    'href="login.html"' => 'href="views/auth/login.html"',
    'href="register.html"' => 'href="views/auth/register.html"',
    'href="my_events.php"' => 'href="views/events/my_events.php"',
    'href="create_event.php"' => 'href="views/events/create_event.php"',
    'href="event_details.php' => 'href="views/events/event_details.php',
    'href="attendee_dashboard.php"' => 'href="views/dashboard/attendee_dashboard.php"',
    'href="organizer_dashboard.php"' => 'href="views/dashboard/organizer_dashboard.php"',
    'href="profile.php"' => 'href="views/profile.php"',
    
    // Form actions
    'action="login.php"' => 'action="../../handlers/login.php"',
    'action="register_attendee.php"' => 'action="../../handlers/register_attendee.php"',
    'action="register_organizer.php"' => 'action="../../handlers/register_organizer.php"',
    'action="create_event_handler.php"' => 'action="../../handlers/create_event_handler.php"',
    
    // API calls
    'fetch(\'check_session.php\')' => 'fetch(\'../api/check_session.php\')',
    'fetch(\'check_registration.php' => 'fetch(\'../api/check_registration.php',
    'fetch(\'get_dashboard_stats.php\')' => 'fetch(\'../api/get_dashboard_stats.php\')',
    'fetch(\'get_registered_events.php\')' => 'fetch(\'../api/get_registered_events.php\')',
    'fetch(\'register_for_event.php' => 'fetch(\'../../handlers/register_for_event.php',
    
    // PHP includes
    'require_once \'config/database.php\'' => 'require_once __DIR__ . \'/../../config/database.php\'',
    'require_once\'config/database.php\'' => 'require_once __DIR__ . \'/../../config/database.php\'',
    
    // Location headers
    'Location: login.html' => 'Location: ../auth/login.html',
    'Location: events.php' => 'Location: ../events/events.php',
    'Location: attendee_dashboard.php' => 'Location: ../dashboard/attendee_dashboard.php',
    'Location: organizer_dashboard.php' => 'Location: ../dashboard/organizer_dashboard.php',
];

$files_updated = 0;
$total_replacements = 0;

function update_paths_in_file($file, $replacements) {
    global $files_updated, $total_replacements;
    
    if (!file_exists($file)) {
        return;
    }
    
    $content = file_get_contents($file);
    $original_content = $content;
    $replacements_in_file = 0;
    
    foreach ($replacements as $old => $new) {
        $count = 0;
        $content = str_replace($old, $new, $content, $count);
        $replacements_in_file += $count;
    }
    
    if ($content !== $original_content) {
        file_put_contents($file, $content);
        $files_updated++;
        $total_replacements += $replacements_in_file;
        echo "✓ Updated: " . basename($file) . " ($replacements_in_file replacements)\n";
    }
}

// Get all PHP and HTML files
$directories = [
    __DIR__ . '/views',
    __DIR__ . '/api',
    __DIR__ . '/handlers',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) continue;
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir)
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile() && (
            $file->getExtension() === 'php' || 
            $file->getExtension() === 'html' ||
            $file->getExtension() === 'js'
        )) {
            update_paths_in_file($file->getPathname(), $path_replacements);
        }
    }
}

echo "\n✅ Path update complete!\n";
echo "📊 Files updated: $files_updated\n";
echo "📊 Total replacements: $total_replacements\n";
?>
