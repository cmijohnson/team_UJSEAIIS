<?php
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Serve static files (uploads)
if (strpos($uri, '/uploads/') === 0) {
    if (file_exists(__DIR__ . $uri)) {
        return false; // serve the requested resource as-is.
    }
}

// Route /api requests to api/index.php
if (strpos($uri, '/api') === 0) {
    require __DIR__ . '/api/index.php';
    return true;
}

// Default: return 404
http_response_code(404);
echo "Not Found";
?>