<?php
// Products API for website
header('Content-Type: application/json');

// Get database connection
require_once __DIR__ . '/../../shared/config/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get products with filters
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 12;
        $offset = ($page - 1) * $limit;
        
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
        
        // Build WHERE clause
        $where = "WHERE p.status = 'active' AND p.stock > 0";
        
        if (!empty($search)) {
            $where .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
        }
        
        if ($category_id > 0) {
            $where .= " AND p.category_id = $category_id";
        }
        
        // Build ORDER BY clause
        $order_by = "ORDER BY ";
        switch ($sort) {
            case 'price_low':
                $order_by .= "p.price ASC";
                break;
            case 'price_high':
                $order_by .= "p.price DESC";
                break;
            case 'name':
                $order_by .= "p.name ASC";
                break;
            default: // newest
                $order_by .= "p.created_at DESC";
        }
        
        // Get total count
        $count_sql = "SELECT COUNT(*) as total FROM products p $where";
        $count_result = $conn->query($count_sql);
        $total = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);
        
        // Get products
        $sql = "
            SELECT p.*, c.name as category_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            $where
            $order_by
            LIMIT $limit OFFSET $offset
        ";
        
        $result = $conn->query($sql);
        $products = [];
        
        while ($row = $result->fetch_assoc()) {
            // Format product data
            $products[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'description' => $row['description'],
                'price' => (float)$row['price'],
                'stock' => (int)$row['stock'],
                'image' => $row['image'] ?: 'default.jpg',
                'category' => $row['category_name'],
                'created_at' => $row['created_at']
            ];
        }
        
        // Get categories for filter
        $categories_result = $conn->query("SELECT id, name FROM categories WHERE status = 'active'");
        $categories = [];
        while ($cat = $categories_result->fetch_assoc()) {
            $categories[] = $cat;
        }
        
        echo json_encode([
            'success' => true,
            'data' => $products,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => $total_pages
            ],
            'filters' => [
                'categories' => $categories,
                'sort_options' => [
                    ['value' => 'newest', 'label' => 'Terbaru'],
                    ['value' => 'price_low', 'label' => 'Harga Terendah'],
                    ['value' => 'price_high', 'label' => 'Harga Tertinggi'],
                    ['value' => 'name', 'label' => 'Nama A-Z']
                ]
            ]
        ]);
        break;
        
    case 'POST':
        // Get single product details
        $data = json_decode(file_get_contents('php://input'), true);
        $product_id = $data['id'] ?? 0;
        
        if ($product_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Product ID required']);
            exit;
        }
        
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = ? AND p.status = 'active'";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $product = $result->fetch_assoc();
            echo json_encode([
                'success' => true,
                'data' => $product
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
        }
        break;
        
    default:
        echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        http_response_code(405);
}
?>
