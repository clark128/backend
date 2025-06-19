<?php

// Test file to check storage configuration and permissions

// 1. Check if storage link exists
$storageLink = __DIR__ . '/storage';
$storageTarget = __DIR__ . '/../storage/app/public';

echo "Storage Link Test:\n";
echo "Link path: " . $storageLink . "\n";
echo "Target path: " . $storageTarget . "\n";
echo "Link exists: " . (file_exists($storageLink) ? 'Yes' : 'No') . "\n";
echo "Link is symlink: " . (is_link($storageLink) ? 'Yes' : 'No') . "\n";
if (is_link($storageLink)) {
    echo "Link points to: " . readlink($storageLink) . "\n";
}

// 2. Check directory permissions
echo "\nPermissions Test:\n";
echo "Storage directory permissions: " . substr(sprintf('%o', fileperms($storageLink)), -4) . "\n";
echo "Public directory permissions: " . substr(sprintf('%o', fileperms(__DIR__)), -4) . "\n";

// 3. Check if directories exist and are writable
echo "\nDirectory Access Test:\n";
$directories = ['motorcycle_images', 'specification_images'];
foreach ($directories as $dir) {
    $path = $storageLink . '/' . $dir;
    echo "$dir directory exists: " . (file_exists($path) ? 'Yes' : 'No') . "\n";
    echo "$dir directory is writable: " . (is_writable($path) ? 'Yes' : 'No') . "\n";
}

// 4. List any existing images
echo "\nExisting Images:\n";
foreach ($directories as $dir) {
    $path = $storageLink . '/' . $dir;
    if (is_dir($path)) {
        $files = scandir($path);
        echo "\nIn $dir:\n";
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "- $file\n";
            }
        }
    }
}

// 5. Test direct file access
echo "\nDirect Access Test:\n";
$testUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/storage/motorcycle_images/';
echo "Test URL: $testUrl\n";
echo "Can be accessed via HTTP: ";
$headers = @get_headers($testUrl);
echo ($headers && strpos($headers[0], '200') !== false) ? 'Yes' : 'No'; 