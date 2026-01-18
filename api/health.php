<?php
// Health check endpoint for Vercel
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => getenv('VERCEL') ? 'production' : 'development',
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
];

try {
    // Test database connection
    require_once __DIR__ . '/../shared/config/database.php';
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $status['database'] = 'connected';
    $status['database_type'] = $db->getConfig()['type'] ?? 'unknown';
    
} catch (Exception $e) {
    $status['database'] = 'error';
    $status['database_error'] = $e->getMessage();
}

echo json_encode($status, JSON_PRETTY_PRINT);
?>
