<?php
// Check if storage symlink is properly created
$publicStorage = __DIR__ . '/storage';
$targetPath = __DIR__ . '/../storage/app/public';

echo "<h1>Storage Symlink Check</h1>";

if (file_exists($publicStorage)) {
    if (is_link($publicStorage)) {
        echo "<p style='color: green;'>✓ Storage symlink exists</p>";
        
        // Check if it's pointing to the correct location
        $actualTarget = readlink($publicStorage);
        echo "<p>Symlink target: {$actualTarget}</p>";
        echo "<p>Expected target: {$targetPath}</p>";
        
        if (realpath($actualTarget) === realpath($targetPath)) {
            echo "<p style='color: green;'>✓ Symlink is correctly configured</p>";
        } else {
            echo "<p style='color: red;'>✗ Symlink exists but points to the wrong location</p>";
            echo "<p>To fix: <code>php artisan storage:link</code> (after removing the existing symlink)</p>";
        }
    } else {
        echo "<p style='color: red;'>✗ A file/directory named 'storage' exists but is not a symlink</p>";
        echo "<p>To fix: Remove the file/directory and run <code>php artisan storage:link</code></p>";
    }
} else {
    echo "<p style='color: red;'>✗ Storage symlink does not exist</p>";
    echo "<p>To fix: <code>php artisan storage:link</code></p>";
}

// Check if storage directories exist
$checkDirs = ['sketches', 'signatures'];
echo "<h2>Storage Directories</h2>";

foreach ($checkDirs as $dir) {
    $dirPath = $targetPath . '/' . $dir;
    if (file_exists($dirPath) && is_dir($dirPath)) {
        echo "<p style='color: green;'>✓ Directory exists: {$dir}</p>";
        
        // List files in directory
        $files = array_diff(scandir($dirPath), ['.', '..']);
        echo "<p>Files in {$dir}: " . count($files) . "</p>";
        
        if (count($files) > 0) {
            echo "<ul>";
            $counter = 0;
            foreach ($files as $file) {
                echo "<li>" . htmlspecialchars($file) . "</li>";
                $counter++;
                if ($counter >= 5) {
                    echo "<li>... and " . (count($files) - 5) . " more</li>";
                    break;
                }
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color: red;'>✗ Directory does not exist: {$dir}</p>";
        echo "<p>Creating directory...</p>";
        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0755, true);
        }
        mkdir($dirPath, 0755, true);
        echo "<p style='color: green;'>✓ Directory created: {$dir}</p>";
    }
}

// Test if images are accessible
echo "<h2>Image Access Test</h2>";

// Find an image in each directory to test
$testImages = [];
foreach ($checkDirs as $dir) {
    $dirPath = $targetPath . '/' . $dir;
    if (file_exists($dirPath) && is_dir($dirPath)) {
        $files = array_diff(scandir($dirPath), ['.', '..']);
        if (count($files) > 0) {
            $testFile = reset($files);
            $testImages[$dir] = $testFile;
        }
    }
}

if (count($testImages) > 0) {
    echo "<div style='display: flex; flex-wrap: wrap;'>";
    foreach ($testImages as $dir => $filename) {
        echo "<div style='margin: 10px; border: 1px solid #ccc; padding: 10px; text-align: center;'>";
        echo "<h3>{$dir}/{$filename}</h3>";
        echo "<img src='/storage/{$dir}/{$filename}' style='max-width: 300px; max-height: 300px;' onerror=\"this.onerror=null; this.src='data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='; this.style.border='2px solid red'; this.alt='Failed to load';\">";
        echo "</div>";
    }
    echo "</div>";
} else {
    echo "<p>No test images found.</p>";
} 