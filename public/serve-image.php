<?php
/**
 * This script serves images directly from storage, bypassing Laravel's routing system.
 * It's intended as a diagnostic/fallback tool and should be used only if regular storage:link isn't working.
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Enable CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');

// Define allowed image types
$allowedTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
];

// Get and sanitize the path
$path = isset($_GET['path']) ? $_GET['path'] : '';

// Remove any URL components if a full URL was passed
if (filter_var($path, FILTER_VALIDATE_URL)) {
    $path = parse_url($path, PHP_URL_PATH);
    $path = ltrim($path, '/storage/');
} else {
    // Clean the path
    $path = str_replace('\\', '/', $path); // Convert Windows paths
    $path = preg_replace('|^/*storage/*|', '', $path); // Remove storage prefix
}

// Remove any directory traversal attempts
$path = str_replace('../', '', $path);

// Parse the file type and validate
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if (!isset($allowedTypes[$extension])) {
    header("HTTP/1.0 403 Forbidden");
    die("Invalid file type: " . htmlspecialchars($extension));
}

// Set the storage base path
$basePath = __DIR__ . '/../storage/app/public/';

// Construct the full file path
$filePath = $basePath . $path;

// Debug output if needed
if (isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    echo "Requested path: " . htmlspecialchars($_GET['path']) . "\n";
    echo "Cleaned path: " . htmlspecialchars($path) . "\n";
    echo "Full file path: " . htmlspecialchars($filePath) . "\n";
    echo "File exists: " . (file_exists($filePath) ? "Yes" : "No") . "\n";
    echo "Storage base path: " . htmlspecialchars($basePath) . "\n";
    echo "Directory exists: " . (is_dir($basePath) ? "Yes" : "No") . "\n";
    echo "Directory readable: " . (is_readable($basePath) ? "Yes" : "No") . "\n";
    if (file_exists($filePath)) {
        echo "File permissions: " . substr(sprintf('%o', fileperms($filePath)), -4) . "\n";
        echo "File size: " . filesize($filePath) . " bytes\n";
    }
    die();
}

// Check if the file exists
if (!file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    die("File not found: " . htmlspecialchars($path));
}

// Set the content type header
header('Content-Type: ' . $allowedTypes[$extension]);

// Set cache control headers to improve performance
header('Cache-Control: public, max-age=86400');
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

// Output the file
readfile($filePath);
exit; 