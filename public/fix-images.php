<?php
/**
 * This script creates a direct-access directory and copies all images from storage/app/public 
 * to public/direct-access so they can be accessed without relying on symlinks.
 */

// Define the target directory
$directAccessDir = __DIR__ . '/direct-access';

// Define the source directories
$sourceBaseDir = __DIR__ . '/../storage/app/public';
$sourceDirs = ['signatures', 'sketches'];

echo "<h1>Image Copy Utility</h1>";

// Create the direct access directory if it doesn't exist
if (!file_exists($directAccessDir)) {
    if (mkdir($directAccessDir, 0755)) {
        echo "<p style='color: green;'>✓ Created direct-access directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create direct-access directory</p>";
        exit;
    }
} else {
    echo "<p>Direct-access directory already exists</p>";
}

// Create subdirectories and copy files
foreach ($sourceDirs as $subDir) {
    $sourceDir = $sourceBaseDir . '/' . $subDir;
    $targetDir = $directAccessDir . '/' . $subDir;
    
    // Skip if source directory doesn't exist
    if (!file_exists($sourceDir) || !is_dir($sourceDir)) {
        echo "<p style='color: orange;'>⚠️ Source directory does not exist: {$sourceDir}</p>";
        continue;
    }
    
    // Create target subdirectory if needed
    if (!file_exists($targetDir)) {
        if (mkdir($targetDir, 0755, true)) {
            echo "<p style='color: green;'>✓ Created directory: {$subDir}</p>";
        } else {
            echo "<p style='color: red;'>✗ Failed to create directory: {$subDir}</p>";
            continue;
        }
    } else {
        echo "<p>Directory already exists: {$subDir}</p>";
    }
    
    // Copy files
    $files = array_diff(scandir($sourceDir), ['.', '..']);
    $filesCopied = 0;
    $filesSkipped = 0;
    
    foreach ($files as $file) {
        $sourcePath = $sourceDir . '/' . $file;
        $targetPath = $targetDir . '/' . $file;
        
        if (is_file($sourcePath)) {
            if (!file_exists($targetPath) || filemtime($sourcePath) > filemtime($targetPath)) {
                if (copy($sourcePath, $targetPath)) {
                    $filesCopied++;
                } else {
                    echo "<p style='color: red;'>✗ Failed to copy: {$sourcePath}</p>";
                }
            } else {
                $filesSkipped++;
            }
        }
    }
    
    echo "<p>Directory {$subDir}: {$filesCopied} files copied, {$filesSkipped} files skipped (already exist)</p>";
}

echo "<h2>Direct Access URLs</h2>";
echo "<p>You can now access your images using the following URL pattern:</p>";
echo "<code>http://yoursite.com/direct-access/{type}/{filename}</code>";
echo "<p>Example:</p>";
echo "<code>http://127.0.0.1:8000/direct-access/signatures/1747545681_applicant_signature_Honda_TMX125_Alpha.png</code>";

echo "<h2>Frontend Code Update</h2>";
echo "<p>To update your frontend code to use direct access URLs, add a helper function:</p>";

echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 5px;'>";
echo htmlspecialchars("// Function to convert storage URLs to direct access URLs
const getDirectAccessUrl = (url) => {
  if (!url) return null;
  
  // If it's already a direct access URL, return it as is
  if (url.includes('/direct-access/')) {
    return url;
  }
  
  // Convert storage URL to direct access URL
  return url.replace('/storage/', '/direct-access/');
};");
echo "</pre>";

// Test images
echo "<h2>Image Test</h2>";
foreach ($sourceDirs as $subDir) {
    $directAccessPath = $directAccessDir . '/' . $subDir;
    if (file_exists($directAccessPath) && is_dir($directAccessPath)) {
        $files = array_diff(scandir($directAccessPath), ['.', '..']);
        if (count($files) > 0) {
            $testFile = reset($files);
            echo "<div style='margin: 10px; border: 1px solid #ccc; padding: 10px;'>";
            echo "<h3>{$subDir}/{$testFile}</h3>";
            echo "<p>Direct Access URL: <code>/direct-access/{$subDir}/{$testFile}</code></p>";
            echo "<img src='/direct-access/{$subDir}/{$testFile}' style='max-width: 300px; max-height: 300px;' onerror=\"this.onerror=null; this.src='data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='; this.style.border='2px solid red'; this.alt='Failed to load';\">";
            echo "</div>";
        }
    }
}

// Create .htaccess file to ensure direct access is allowed
$htaccessPath = $directAccessDir . '/.htaccess';
if (!file_exists($htaccessPath)) {
    $htaccessContent = <<<EOT
# Allow direct access to images
<IfModule mod_rewrite.c>
    RewriteEngine Off
</IfModule>

# Set correct MIME types
AddType image/jpeg .jpg .jpeg
AddType image/png .png
AddType image/gif .gif

# Cache control
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
</IfModule>
EOT;

    if (file_put_contents($htaccessPath, $htaccessContent)) {
        echo "<p style='color: green;'>✓ Created .htaccess file for direct access directory</p>";
    } else {
        echo "<p style='color: red;'>✗ Failed to create .htaccess file</p>";
    }
} 