<?php

// Error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Function to check and create directories
function ensureDirectoryExists($path) {
    if (!file_exists($path)) {
        if (!mkdir($path, 0755, true)) {
            die("Failed to create directory: " . $path);
        }
        echo "Created directory: " . $path . "\n";
    }
}

// Function to check and create symlink
function ensureSymlinkExists($target, $link) {
    if (file_exists($link)) {
        if (is_link($link)) {
            echo "Symlink exists: " . $link . "\n";
            return;
        }
        unlink($link);
    }
    
    if (!symlink($target, $link)) {
        die("Failed to create symlink from " . $target . " to " . $link);
    }
    echo "Created symlink: " . $link . " -> " . $target . "\n";
}

// Base paths
$basePath = __DIR__ . '/..';
$storagePath = $basePath . '/storage/app/public';
$publicStoragePath = __DIR__ . '/storage';

// Ensure storage directories exist
ensureDirectoryExists($basePath . '/storage');
ensureDirectoryExists($basePath . '/storage/app');
ensureDirectoryExists($storagePath);
ensureDirectoryExists($storagePath . '/motorcycle_images');
ensureDirectoryExists($storagePath . '/specification_images');

// Create symlink
ensureSymlinkExists($storagePath, $publicStoragePath);

// Set permissions
chmod($basePath . '/storage', 0755);
chmod($basePath . '/storage/app', 0755);
chmod($storagePath, 0755);
chmod($storagePath . '/motorcycle_images', 0755);
chmod($storagePath . '/specification_images', 0755);

echo "Storage setup completed successfully!\n";

// Test file access
$testFile = $storagePath . '/motorcycle_images/test.txt';
file_put_contents($testFile, 'Test file access');
if (file_exists($testFile)) {
    echo "Successfully wrote test file\n";
    unlink($testFile);
} else {
    echo "Failed to write test file\n";
} 