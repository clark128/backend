<?php
/**
 * Comprehensive Image Loading Test Page
 * Tests all available methods for loading images from the Laravel storage
 */

// Define the storage paths
$publicStorage = __DIR__ . '/storage';
$storageAppPublic = __DIR__ . '/../storage/app/public';
$directAccessDir = __DIR__ . '/direct-access';
$imageDirs = ['signatures', 'sketches'];

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Comprehensive Image Loading Test</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 1200px; margin: 0 auto; padding: 20px; }
        h1, h2 { color: #333; }
        .success { color: green; }
        .warning { color: orange; }
        .error { color: red; }
        .test-container { margin-bottom: 30px; }
        .image-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
        .image-card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; }
        .image-container { height: 200px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px; }
        img { max-width: 100%; max-height: 200px; object-fit: contain; }
        code { background-color: #f5f5f5; padding: 2px 5px; border-radius: 3px; font-size: 0.9em; }
        .method-selector { margin-bottom: 20px; }
        button { padding: 8px 15px; margin-right: 10px; cursor: pointer; }
        .active { background-color: #4CAF50; color: white; border: none; }
    </style>
</head>
<body>
    <h1>Comprehensive Image Loading Test</h1>";

// 1. System Status Check
echo "<div class='test-container'>
    <h2>1. System Status Check</h2>";

// Check storage symlink
echo "<h3>Storage Symlink</h3>";
if (file_exists($publicStorage)) {
    if (is_link($publicStorage)) {
        echo "<p class='success'>✓ Storage symlink exists</p>";
        
        $actualTarget = readlink($publicStorage);
        echo "<p>Target: <code>{$actualTarget}</code></p>";
        
        if (realpath($actualTarget) === realpath($storageAppPublic)) {
            echo "<p class='success'>✓ Symlink correctly configured</p>";
        } else {
            echo "<p class='warning'>⚠️ Symlink exists but points to wrong location</p>";
        }
    } else {
        echo "<p class='error'>✗ A file/directory named 'storage' exists but is not a symlink</p>";
    }
} else {
    echo "<p class='error'>✗ Storage symlink does not exist</p>";
    echo "<p>Try running: <code>php artisan storage:link</code></p>";
}

// Check direct-access directory
echo "<h3>Direct Access Directory</h3>";
if (file_exists($directAccessDir) && is_dir($directAccessDir)) {
    echo "<p class='success'>✓ Direct access directory exists</p>";
    
    $missingDirs = [];
    foreach ($imageDirs as $dir) {
        if (!file_exists("{$directAccessDir}/{$dir}") || !is_dir("{$directAccessDir}/{$dir}")) {
            $missingDirs[] = $dir;
        }
    }
    
    if (empty($missingDirs)) {
        echo "<p class='success'>✓ All required subdirectories exist</p>";
    } else {
        echo "<p class='warning'>⚠️ Missing subdirectories: " . implode(", ", $missingDirs) . "</p>";
        echo "<p>Please run <a href='fix-images.php'>fix-images.php</a> to create them</p>";
    }
} else {
    echo "<p class='error'>✗ Direct access directory does not exist</p>";
    echo "<p>Please run <a href='fix-images.php'>fix-images.php</a> to create it</p>";
}

// Check serve-image.php
echo "<h3>Serve Image Script</h3>";
if (file_exists(__DIR__ . '/serve-image.php')) {
    echo "<p class='success'>✓ serve-image.php exists</p>";
} else {
    echo "<p class='error'>✗ serve-image.php does not exist</p>";
}

echo "</div>";

// 2. Find test images
$testImages = [];
foreach ($imageDirs as $dir) {
    $dirPath = $storageAppPublic . '/' . $dir;
    if (file_exists($dirPath) && is_dir($dirPath)) {
        $files = array_diff(scandir($dirPath), ['.', '..']);
        if (!empty($files)) {
            // Get up to 3 images for testing
            $count = 0;
            foreach ($files as $file) {
                if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
                    $testImages[$dir][] = $file;
                    $count++;
                    if ($count >= 3) break;
                }
            }
        }
    }
}

if (empty($testImages)) {
    echo "<div class='test-container'>
        <h2>No Test Images Found</h2>
        <p>There are no images available in the storage directories to test.</p>
    </div>";
} else {
    // 3. Test Image Display Methods
    echo "<div class='test-container'>
        <h2>2. Image Loading Tests</h2>
        
        <div class='method-selector'>
            <button class='method-btn active' data-method='all'>All Methods</button>
            <button class='method-btn' data-method='standard'>Standard URLs</button>
            <button class='method-btn' data-method='direct'>Direct Access</button>
            <button class='method-btn' data-method='serve'>Serve-Image Script</button>
        </div>
        
        <div class='image-grid'>";
    
    $baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'];
    
    foreach ($testImages as $dir => $files) {
        foreach ($files as $file) {
            // Generate URLs for each method
            $standardUrl = "{$baseUrl}/storage/{$dir}/{$file}";
            $directAccessUrl = "{$baseUrl}/direct-access/{$dir}/{$file}";
            $serveImageUrl = "{$baseUrl}/serve-image.php?path={$dir}/{$file}";
            
            echo "<div class='image-card'>
                <h3>" . htmlspecialchars($file) . "</h3>
                <p>Type: {$dir}</p>
                
                <div class='image-container method-standard'>
                    <img src='{$standardUrl}' alt='Standard URL' 
                         onerror=\"this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';\" />
                    <div style='display:none; color:red;'>Failed to load</div>
                    <div class='method-caption'>Standard URL</div>
                </div>
                
                <div class='image-container method-direct'>
                    <img src='{$directAccessUrl}' alt='Direct Access' 
                         onerror=\"this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';\" />
                    <div style='display:none; color:red;'>Failed to load</div>
                    <div class='method-caption'>Direct Access</div>
                </div>
                
                <div class='image-container method-serve'>
                    <img src='{$serveImageUrl}' alt='Serve-Image Script' 
                         onerror=\"this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';\" />
                    <div style='display:none; color:red;'>Failed to load</div>
                    <div class='method-caption'>Serve-Image Script</div>
                </div>
                
                <details>
                    <summary>URL Details</summary>
                    <p><strong>Standard URL:</strong> <code>{$standardUrl}</code></p>
                    <p><strong>Direct Access URL:</strong> <code>{$directAccessUrl}</code></p>
                    <p><strong>Serve Image URL:</strong> <code>{$serveImageUrl}</code></p>
                </details>
            </div>";
        }
    }
    
    echo "</div></div>";
}

// 4. Add JavaScript for method selector
echo "<script>
document.addEventListener('DOMContentLoaded', function() {
    const methodButtons = document.querySelectorAll('.method-btn');
    const imageContainers = {
        'standard': document.querySelectorAll('.method-standard'),
        'direct': document.querySelectorAll('.method-direct'),
        'serve': document.querySelectorAll('.method-serve')
    };
    
    methodButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Remove active class from all buttons
            methodButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            const method = this.getAttribute('data-method');
            
            if (method === 'all') {
                // Show all methods
                Object.values(imageContainers).forEach(containers => {
                    containers.forEach(container => container.style.display = 'flex');
                });
            } else {
                // Hide all methods
                Object.values(imageContainers).forEach(containers => {
                    containers.forEach(container => container.style.display = 'none');
                });
                
                // Show only the selected method
                imageContainers[method].forEach(container => container.style.display = 'flex');
            }
        });
    });
});
</script>";

echo "</body></html>"; 