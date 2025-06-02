<?php
/**
 * Image Path Diagnostics Tool
 * This script tests different approaches to accessing images and helps diagnose storage path issues.
 */

// Set content type to HTML
header('Content-Type: text/html');

// Define the image types we want to test
$imageTypes = ['signatures', 'sketches'];

// Define the storage base path
$baseStoragePath = __DIR__ . '/../storage/app/public/';

// Function to check if a file is an image
function isImageFile($filePath) {
    $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
    return in_array($extension, $imageTypes);
}

// Function to list all image files in a directory
function listImageFiles($directory) {
    $files = [];
    if (is_dir($directory)) {
        foreach (scandir($directory) as $file) {
            if ($file !== '.' && $file !== '..') {
                $filePath = $directory . '/' . $file;
                if (is_file($filePath) && isImageFile($filePath)) {
                    $files[] = $file;
                }
            }
        }
    }
    return $files;
}

// Generate HTML for testing a single image with various approaches
function generateImageTestHtml($baseUrl, $type, $filename) {
    $normalizedPath = "{$type}/{$filename}";
    
    // Different URL approaches to test
    $urls = [
        'Direct Storage' => "{$baseUrl}/storage/{$normalizedPath}",
        'Serve Image Script' => "{$baseUrl}/serve-image.php?path=" . urlencode($normalizedPath),
        'Direct Access' => "{$baseUrl}/direct-access/{$normalizedPath}"
    ];
    
    $html = "<div style='border: 1px solid #ddd; margin-bottom: 20px; padding: 15px;'>";
    $html .= "<h3>Testing {$type}/{$filename}</h3>";
    
    foreach ($urls as $label => $url) {
        $html .= "<div style='margin-bottom: 15px;'>";
        $html .= "<strong>{$label}:</strong><br>";
        $html .= "<a href='{$url}' target='_blank'>{$url}</a><br>";
        $html .= "<img src='{$url}' alt='{$label}' style='max-width:200px; max-height:150px; margin-top:5px; border:1px solid #eee;'>";
        $html .= "</div>";
    }
    
    $html .= "</div>";
    
    return $html;
}

// Collect image files for each type
$imagesByType = [];
foreach ($imageTypes as $type) {
    $typeDir = $baseStoragePath . $type;
    $imagesByType[$type] = listImageFiles($typeDir);
}

// Get server base URL
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$baseUrl = $protocol . $host;

?>
<!DOCTYPE html>
<html>
<head>
    <title>Image Access Diagnostics</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; }
        h1 { color: #333; }
        h2 { color: #666; margin-top: 30px; }
        .image-grid { display: flex; flex-wrap: wrap; gap: 20px; }
        .image-test { border: 1px solid #ddd; padding: 15px; margin-bottom: 20px; width: 300px; }
        .image-test img { display: block; max-width: 100%; max-height: 150px; margin: 10px 0; }
        .error { color: #e53e3e; }
        .success { color: #38a169; }
        pre { background: #f7f7f7; padding: 10px; overflow: auto; }
    </style>
</head>
<body>
    <h1>Image Access Diagnostics</h1>
    
    <div class="server-info">
        <h2>Server Information</h2>
        <ul>
            <li><strong>Base URL:</strong> <?php echo $baseUrl; ?></li>
            <li><strong>Storage Path:</strong> <?php echo $baseStoragePath; ?></li>
            <li><strong>Storage Link Exists:</strong> 
                <?php echo is_link(__DIR__ . '/storage') ? 
                    '<span class="success">Yes</span>' : 
                    '<span class="error">No</span>'; ?>
            </li>
            <li><strong>Storage Link Target:</strong> 
                <?php echo is_link(__DIR__ . '/storage') ? 
                    readlink(__DIR__ . '/storage') : 
                    '<span class="error">No link found</span>'; ?>
            </li>
        </ul>
    </div>
    
    <?php foreach ($imageTypes as $type): ?>
        <h2>Testing <?php echo ucfirst($type); ?> Images</h2>
        
        <?php if (empty($imagesByType[$type])): ?>
            <p class="error">No images found in <?php echo $type; ?> directory.</p>
        <?php else: ?>
            <p>Found <?php echo count($imagesByType[$type]); ?> images in the <?php echo $type; ?> directory.</p>
            
            <?php 
            // Display tests for up to 5 random images of this type
            $filesToTest = array_slice($imagesByType[$type], 0, min(5, count($imagesByType[$type])));
            foreach ($filesToTest as $file) {
                echo generateImageTestHtml($baseUrl, $type, $file);
            }
            ?>
        <?php endif; ?>
    <?php endforeach; ?>
    
    <h2>Troubleshooting</h2>
    <p>If images are not loading properly, check the following:</p>
    <ol>
        <li>Verify that the storage symbolic link is set up correctly</li>
        <li>Check file permissions on the storage directory and files</li>
        <li>Make sure the image files exist in the expected locations</li>
        <li>Check your web server configuration for proper handling of static files</li>
    </ol>
    
    <script>
        // Add load/error event listeners to all images
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('img').forEach(function(img) {
                img.addEventListener('load', function() {
                    this.style.border = '2px solid green';
                    const status = document.createElement('div');
                    status.className = 'success';
                    status.textContent = '✓ Loaded successfully';
                    this.parentNode.appendChild(status);
                });
                
                img.addEventListener('error', function() {
                    this.style.border = '2px solid red';
                    const status = document.createElement('div');
                    status.className = 'error';
                    status.textContent = '✗ Failed to load';
                    this.parentNode.appendChild(status);
                });
            });
        });
    </script>
</body>
</html> 