<?php
// Authentication check for admin panel
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If it's an API request, return JSON error
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Unauthorized. Please login first.',
            'redirect' => '/panel-dropship-admin/login'
        ]);
        http_response_code(401);
    } else {
        // Redirect to login page
        header('Location: /panel-dropship-admin/login');
    }
    exit();
}

// Check session timeout (30 minutes)
$session_timeout = 1800; // 30 minutes in seconds
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $session_timeout)) {
    session_unset();
    session_destroy();
    header('Location: /panel-dropship-admin/login?timeout=1');
    exit();
}

// Update last activity time
$_SESSION['last_activity'] = time();

// Define admin info as constants for easy access
define('ADMIN_ID', $_SESSION['admin_id']);
define('ADMIN_USERNAME', $_SESSION['admin_username']);
define('ADMIN_EMAIL', $_SESSION['admin_email']);
?>
