<?php
// Website Main Entry Point
session_start();

// Set timezone
date_default_timezone_set('Asia/Jakarta');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once __DIR__ . '/../../shared/config/database.php';

// Define constants
define('SITE_URL', '/website-dropship');
define('BASE_PATH', dirname(__DIR__));
define('VIEWS_PATH', BASE_PATH . '/views');
define('INCLUDES_PATH', BASE_PATH . '/includes');

// Initialize cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Simple routing
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);

// Remove base path
$path = str_replace(SITE_URL, '', $path);
$path = trim($path, '/');

// Handle static assets
if (preg_match('/\.(css|js|jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$/', $path)) {
    $asset_path = BASE_PATH . '/public/assets/' . $path;
    if (file_exists($asset_path)) {
        $mime_types = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'ico' => 'image/x-icon',
            'svg' => 'image/svg+xml',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'eot' => 'application/vnd.ms-fontobject'
        ];
        
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        if (isset($mime_types[$ext])) {
            header('Content-Type: ' . $mime_types[$ext]);
        }
        
        readfile($asset_path);
        exit;
    }
}

// Route definitions
$routes = [
    // Public pages
    '' => 'home',
    'home' => 'home',
    'products' => 'products/list',
    'product' => 'products/detail',
    'cart' => 'cart/view',
    'checkout' => 'checkout/view',
    
    // Auth pages
    'login' => 'auth/login',
    'register' => 'auth/register',
    'logout' => 'auth/logout',
    
    // Account pages
    'account' => 'account/dashboard',
    'account/orders' => 'account/orders',
    'account/profile' => 'account/profile',
    
    // API endpoints
    'api/products' => 'api/products',
    'api/cart' => 'api/cart',
    'api/checkout' => 'api/checkout',
    'api/auth' => 'api/auth',
];

// Default to home if empty path
if (empty($path)) {
    $path = 'home';
}

// Find matching route
$route_found = false;
$route_handler = null;

foreach ($routes as $route => $handler) {
    if ($path === $route) {
        $route_found = true;
        $route_handler = $handler;
        break;
    }
    
    // Check for parameterized routes (e.g., product/123)
    if (strpos($path, $route . '/') === 0) {
        $route_found = true;
        $route_handler = $handler;
        $param = substr($path, strlen($route) + 1);
        $_GET['id'] = $param;
        break;
    }
}

if (!$route_found) {
    // Try direct view file
    $view_file = VIEWS_PATH . '/' . str_replace('/', DIRECTORY_SEPARATOR, $path) . '.php';
    if (file_exists($view_file)) {
        require_once $view_file;
        exit;
    }
    
    // 404 Not Found
    http_response_code(404);
    require_once VIEWS_PATH . '/404.php';
    exit;
}

// Handle the route
$handler_parts = explode('/', $route_handler);
$handler_type = $handler_parts[0];
$handler_action = $handler_parts[1] ?? 'index';

switch ($handler_type) {
    case 'home':
        require_once VIEWS_PATH . '/home.php';
        break;
        
    case 'products':
        if ($handler_action === 'list') {
            require_once VIEWS_PATH . '/products.php';
        } elseif ($handler_action === 'detail') {
            require_once VIEWS_PATH . '/product-detail.php';
        }
        break;
        
    case 'cart':
        require_once VIEWS_PATH . '/cart.php';
        break;
        
    case 'checkout':
        require_once VIEWS_PATH . '/checkout.php';
        break;
        
    case 'auth':
        require_once VIEWS_PATH . '/auth/' . $handler_action . '.php';
        break;
        
    case 'account':
        // Check if user is logged in
        if (!isset($_SESSION['customer_id'])) {
            header('Location: ' . SITE_URL . '/login');
            exit;
        }
        require_once VIEWS_PATH . '/account/' . $handler_action . '.php';
        break;
        
    case 'api':
        // API endpoints return JSON
        header('Content-Type: application/json');
        $api_file = __DIR__ . '/' . $handler_action . '.php';
        if (file_exists($api_file)) {
            require_once $api_file;
        } else {
            echo json_encode(['success' => false, 'message' => 'API endpoint not found']);
            http_response_code(404);
        }
        break;
        
    default:
        http_response_code(404);
        require_once VIEWS_PATH . '/404.php';
}
?>
