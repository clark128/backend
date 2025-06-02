<?php
/**
 * This script serves images directly from storage, bypassing Laravel's routing system.
 * It's intended as a diagnostic/fallback tool and should be used only if regular storage:link isn't working.
 */

// Define allowed image types
$allowedTypes = [
    'jpg' => 'image/jpeg',
    'jpeg' => 'image/jpeg',
    'png' => 'image/png',
    'gif' => 'image/gif'
];

// Sanitize the path
$path = isset($_GET['path']) ? $_GET['path'] : '';
$path = str_replace('../', '', $path); // Remove any directory traversal attempts

// Parse the file type and validate
$extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
if (!isset($allowedTypes[$extension])) {
    header("HTTP/1.0 403 Forbidden");
    echo "Invalid file type";
    exit;
}

// Set the storage base path
$basePath = __DIR__ . '/../storage/app/public/';

// Construct the full file path
$filePath = $basePath . $path;

// Check if the file exists
if (!file_exists($filePath)) {
    header("HTTP/1.0 404 Not Found");
    echo "File not found: " . htmlspecialchars($path);
    exit;
}

// Set the content type header
header('Content-Type: ' . $allowedTypes[$extension]);

// Set cache control headers to improve performance
header('Cache-Control: public, max-age=86400');
header('Expires: ' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));

// Output the file
readfile($filePath);
exit; 