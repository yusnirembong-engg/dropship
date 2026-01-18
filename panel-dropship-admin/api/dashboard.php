<?php
// Dashboard API
require_once __DIR__ . '/../includes/auth-check.php';
header('Content-Type: application/json');

// Get database connection
require_once __DIR__ . '/../../shared/config/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get dashboard statistics
    $stats = [];
    
    // Total orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders");
    $stats['total_orders'] = $result->fetch_assoc()['total'];
    
    // Pending orders
    $result = $conn->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending'");
    $stats['pending_orders'] = $result->fetch_assoc()['total'];
    
    // Total products
    $result = $conn->query("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
    $stats['total_products'] = $result->fetch_assoc()['total'];
    
    // Total sales
    $result = $conn->query("SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'");
    $stats['total_sales'] = $result->fetch_assoc()['total'] ?? 0;
    
    // Recent orders
    $recent_orders = [];
    $result = $conn->query("
        SELECT o.*, c.name as customer_name 
        FROM orders o 
        LEFT JOIN customers c ON o.customer_id = c.id 
        ORDER BY o.created_at DESC 
        LIMIT 5
    ");
    while ($row = $result->fetch_assoc()) {
        $recent_orders[] = $row;
    }
    
    // Top products
    $top_products = [];
    $result = $conn->query("
        SELECT p.name, SUM(oi.quantity) as total_sold
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        GROUP BY p.id
        ORDER BY total_sold DESC
        LIMIT 5
    ");
    while ($row = $result->fetch_assoc()) {
        $top_products[] = $row;
    }
    
    echo json_encode([
        'success' => true,
        'stats' => $stats,
        'recent_orders' => $recent_orders,
        'top_products' => $top_products,
        'timestamp' => date('Y-m-d H:i:s')
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed'
    ]);
    http_response_code(405);
}
?>
