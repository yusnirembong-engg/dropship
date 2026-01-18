<?php
header('Content-Type: application/json');

$status = [
    'status' => 'ok',
    'message' => 'Dropship System API is running',
    'timestamp' => date('Y-m-d H:i:s'),
    'environment' => getenv('VERCEL') ? 'production' : 'development',
    'php_version' => PHP_VERSION,
    'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'Vercel'
];

echo json_encode($status, JSON_PRETTY_PRINT);
?>
