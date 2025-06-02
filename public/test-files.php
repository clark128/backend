<?php
// Display errors for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Storage File Test</h1>";

// Get list of files in storage/signatures
echo "<h2>Files in storage/signatures directory:</h2>";
$signatureFiles = scandir(__DIR__ . '/../storage/app/public/signatures');
echo "<ul>";
foreach ($signatureFiles as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>{$file}</li>";
        // Check if the file exists and is readable
        $fullPath = __DIR__ . '/../storage/app/public/signatures/' . $file;
        if (file_exists($fullPath)) {
            echo " - File exists on disk ✅";
            if (is_readable($fullPath)) {
                echo " - Is readable ✅";
            } else {
                echo " - Not readable ❌";
            }
        } else {
            echo " - File does not exist on disk ❌";
        }
        
        // Check if file exists through symlink
        $symlinkPath = __DIR__ . '/storage/signatures/' . $file;
        if (file_exists($symlinkPath)) {
            echo " - File exists through symlink ✅";
            if (is_readable($symlinkPath)) {
                echo " - Is readable through symlink ✅";
            } else {
                echo " - Not readable through symlink ❌";
            }
        } else {
            echo " - File does not exist through symlink ❌";
        }
    }
}
echo "</ul>";

// Get list of files in storage/sketches
echo "<h2>Files in storage/sketches directory:</h2>";
$sketchFiles = scandir(__DIR__ . '/../storage/app/public/sketches');
echo "<ul>";
foreach ($sketchFiles as $file) {
    if ($file != '.' && $file != '..') {
        echo "<li>{$file}</li>";
        // Check if the file exists and is readable
        $fullPath = __DIR__ . '/../storage/app/public/sketches/' . $file;
        if (file_exists($fullPath)) {
            echo " - File exists on disk ✅";
            if (is_readable($fullPath)) {
                echo " - Is readable ✅";
            } else {
                echo " - Not readable ❌";
            }
        } else {
            echo " - File does not exist on disk ❌";
        }
        
        // Check if file exists through symlink
        $symlinkPath = __DIR__ . '/storage/sketches/' . $file;
        if (file_exists($symlinkPath)) {
            echo " - File exists through symlink ✅";
            if (is_readable($symlinkPath)) {
                echo " - Is readable through symlink ✅";
            } else {
                echo " - Not readable through symlink ❌";
            }
        } else {
            echo " - File does not exist through symlink ❌";
        }
    }
}
echo "</ul>";

// Check the storage symlink
echo "<h2>Storage Symlink Status:</h2>";
$storagePath = __DIR__ . '/storage';
if (is_link($storagePath)) {
    echo "Storage symlink exists ✅<br>";
    $target = readlink($storagePath);
    echo "Symlink target: {$target}<br>";
    if (is_dir($target)) {
        echo "Symlink target is a directory ✅<br>";
    } else {
        echo "Symlink target is NOT a directory ❌<br>";
    }
} else {
    echo "Storage is NOT a symlink ❌<br>";
    if (is_dir($storagePath)) {
        echo "Storage is a regular directory<br>";
    } else {
        echo "Storage path doesn't exist<br>";
    }
}

// Try to access one specific file for testing
if (!empty($signatureFiles) && count($signatureFiles) > 2) {
    $testFile = $signatureFiles[2]; // Get the first real file (after . and ..)
    echo "<h2>Test accessing a specific file: {$testFile}</h2>";
    
    // Original path
    $originalPath = __DIR__ . '/../storage/app/public/signatures/' . $testFile;
    echo "Original path: {$originalPath}<br>";
    if (file_exists($originalPath)) {
        echo "File exists at original path ✅<br>";
        echo "File size: " . filesize($originalPath) . " bytes<br>";
    } else {
        echo "File DOES NOT exist at original path ❌<br>";
    }
    
    // Symlink path
    $symlinkPath = __DIR__ . '/storage/signatures/' . $testFile;
    echo "Symlink path: {$symlinkPath}<br>";
    if (file_exists($symlinkPath)) {
        echo "File exists through symlink ✅<br>";
        echo "File size through symlink: " . filesize($symlinkPath) . " bytes<br>";
    } else {
        echo "File DOES NOT exist through symlink ❌<br>";
    }
    
    // Try to output the image
    echo "<div style='margin-top: 20px; border: 1px solid #ccc; padding: 10px;'>";
    echo "<h3>Image from original path:</h3>";
    echo "<img src='/storage/signatures/{$testFile}' style='max-width: 300px;' alt='Test Image'>";
    echo "</div>";
}
?>

<h2>Try different URL formats</h2>
<div id="imageTest"></div>

<script>
// Test various URL formats for the same image
window.onload = function() {
    // Get first signature file from PHP
    const signaturesFiles = <?php echo json_encode($signatureFiles); ?>;
    const testFile = signaturesFiles[2]; // First real file
    
    const urlFormats = [
        `/storage/signatures/${testFile}`,
        `/storage/${testFile}`,
        `http://127.0.0.1:8000/storage/signatures/${testFile}`,
        `http://127.0.0.1:8000/storage/${testFile}`,
        `http://localhost/SYSTEM update/be-copy/public/storage/signatures/${testFile}`,
        `http://localhost/SYSTEM update/be-copy/public/storage/${testFile}`
    ];
    
    const container = document.getElementById('imageTest');
    
    urlFormats.forEach(url => {
        const wrapper = document.createElement('div');
        wrapper.style.marginBottom = '20px';
        
        const title = document.createElement('h4');
        title.textContent = url;
        wrapper.appendChild(title);
        
        const img = document.createElement('img');
        img.src = url;
        img.style.maxWidth = '300px';
        img.style.border = '1px solid #ccc';
        img.alt = 'Test image';
        
        img.onerror = function() {
            const error = document.createElement('p');
            error.textContent = `❌ Failed to load: ${url}`;
            error.style.color = 'red';
            wrapper.appendChild(error);
        };
        
        img.onload = function() {
            const success = document.createElement('p');
            success.textContent = `✅ Successfully loaded: ${url}`;
            success.style.color = 'green';
            wrapper.appendChild(success);
        };
        
        wrapper.appendChild(img);
        container.appendChild(wrapper);
    });
};
</script> 