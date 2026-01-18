<?php
// Panel Admin Main Router
session_start();

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Define base path
define('BASE_PATH', '/panel');
define('ROOT_PATH', dirname(__DIR__));

// Include configuration
require_once ROOT_PATH . '/shared/config.php';

// Get route from query parameter or default to dashboard
$route = $_GET['route'] ?? 'dashboard';

// Check authentication for protected routes
$public_routes = ['login', 'logout'];

if (!in_array($route, $public_routes)) {
    if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: ' . BASE_PATH . '/login');
        exit;
    }
}

// Route mapping
$routes = [
    'dashboard' => 'dashboard.php',
    'login' => 'login.php',
    'logout' => 'logout.php',
    'products' => 'products.php',
    'orders' => 'orders.php',
    'customers' => 'customers.php',
    'suppliers' => 'suppliers.php',
    'reports' => 'reports.php'
];

// Check if route exists
if (isset($routes[$route])) {
    $page_file = $routes[$route];
} else {
    // 404 - Route not found
    $page_file = '404.php';
}

// Include the page
$page_path = __DIR__ . '/' . $page_file;
if (file_exists($page_path)) {
    require_once $page_path;
} else {
    // Fallback to dashboard
    require_once __DIR__ . '/dashboard.php';
}
?>
