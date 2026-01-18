<?php
header('Content-Type: application/json');

$response = [
    'success' => true,
    'message' => 'API Test Endpoint',
    'data' => [
        'test' => 'Hello from Vercel PHP!',
        'timestamp' => date('Y-m-d H:i:s'),
        'request_method' => $_SERVER['REQUEST_METHOD'],
        'query_params' => $_GET
    ]
];

echo json_encode($response, JSON_PRETTY_PRINT);
?>
