<?php
// Test Image Upload
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Image Upload Test</h2>";

// Check PHP upload settings
echo "<h3>PHP Upload Configuration:</h3>";
echo "<ul>";
echo "<li>upload_max_filesize: " . ini_get('upload_max_filesize') . "</li>";
echo "<li>post_max_size: " . ini_get('post_max_size') . "</li>";
echo "<li>max_file_uploads: " . ini_get('max_file_uploads') . "</li>";
echo "<li>file_uploads: " . (ini_get('file_uploads') ? 'Enabled' : 'Disabled') . "</li>";
echo "<li>upload_tmp_dir: " . (ini_get('upload_tmp_dir') ?: 'Default') . "</li>";
echo "</ul>";

// Check directory
echo "<h3>Directory Check:</h3>";
$uploadDir = 'assets/events/';

if (file_exists($uploadDir)) {
    echo "<p style='color: green;'>✅ Directory exists: $uploadDir</p>";
    echo "<p>Writable: " . (is_writable($uploadDir) ? "✅ Yes" : "❌ No") . "</p>";
    echo "<p>Readable: " . (is_readable($uploadDir) ? "✅ Yes" : "❌ No") . "</p>";
    
    // Get permissions
    $perms = fileperms($uploadDir);
    $info = '';
    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ? 'x' : '-');
    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ? 'x' : '-');
    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ? 'x' : '-');
    
    echo "<p>Permissions: $info (" . substr(sprintf('%o', $perms), -4) . ")</p>";
} else {
    echo "<p style='color: red;'>❌ Directory does NOT exist: $uploadDir</p>";
    echo "<p>Attempting to create...</p>";
    if (mkdir($uploadDir, 0777, true)) {
        chmod($uploadDir, 0777);
        echo "<p style='color: green;'>✅ Directory created successfully!</p>";
    } else {
        echo "<p style='color: red;'>❌ Failed to create directory</p>";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['testImage'])) {
    echo "<h3>Upload Test Results:</h3>";
    
    $file = $_FILES['testImage'];
    
    echo "<p><strong>File Information:</strong></p>";
    echo "<ul>";
    echo "<li>Name: {$file['name']}</li>";
    echo "<li>Type: {$file['type']}</li>";
    echo "<li>Size: {$file['size']} bytes (" . round($file['size'] / 1024, 2) . " KB)</li>";
    echo "<li>Tmp Name: {$file['tmp_name']}</li>";
    echo "<li>Error: {$file['error']}";
    
    $errorMessages = [
        UPLOAD_ERR_OK => 'No error',
        UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
        UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
        UPLOAD_ERR_PARTIAL => 'File partially uploaded',
        UPLOAD_ERR_NO_FILE => 'No file uploaded',
        UPLOAD_ERR_NO_TMP_DIR => 'Missing temp directory',
        UPLOAD_ERR_CANT_WRITE => 'Failed to write to disk',
        UPLOAD_ERR_EXTENSION => 'PHP extension stopped upload'
    ];
    echo " (" . ($errorMessages[$file['error']] ?? 'Unknown') . ")</li>";
    echo "</ul>";
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $targetFile = $uploadDir . 'test_' . time() . '_' . basename($file['name']);
        
        echo "<p>Attempting to move file to: <code>$targetFile</code></p>";
        
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            chmod($targetFile, 0644);
            echo "<p style='color: green; font-size: 20px;'>✅ <strong>Upload successful!</strong></p>";
            echo "<p>File saved to: <code>$targetFile</code></p>";
            echo "<p><img src='$targetFile' style='max-width: 400px; border: 2px solid green;' alt='Uploaded image'></p>";
        } else {
            echo "<p style='color: red;'>❌ Failed to move uploaded file</p>";
            echo "<p>Possible reasons:</p>";
            echo "<ul>";
            echo "<li>Directory not writable</li>";
            echo "<li>Insufficient permissions</li>";
            echo "<li>Disk full</li>";
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>❌ Upload error occurred</p>";
    }
}

// Show upload form
echo "<hr>";
echo "<h3>Test Upload Form:</h3>";
echo "<form method='POST' enctype='multipart/form-data'>";
echo "<input type='file' name='testImage' accept='image/*' required>";
echo "<br><br>";
echo "<button type='submit' style='padding: 10px 20px; background: #4361EE; color: white; border: none; border-radius: 5px; cursor: pointer;'>Upload Test Image</button>";
echo "</form>";

echo "<hr>";
echo "<p><a href='create_event.php'>Back to Create Event</a></p>";
?>
