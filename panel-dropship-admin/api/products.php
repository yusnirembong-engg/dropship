<?php
// Products API
require_once __DIR__ . '/../includes/auth-check.php';
header('Content-Type: application/json');

// Get database connection
require_once __DIR__ . '/../../shared/config/database.php';
$db = Database::getInstance();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get products with pagination and filters
        $page = isset($_GET['page']) ? intval($_GET['page']) : 1;
        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;
        $offset = ($page - 1) * $limit;
        
        $search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
        $category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
        $status = isset($_GET['status']) ? $_GET['status'] : 'active';
        
        // Build WHERE clause
        $where = "WHERE 1=1";
        if (!empty($search)) {
            $where .= " AND (p.name LIKE '%$search%' OR p.description LIKE '%$search%')";
        }
        if ($category_id > 0) {
            $where .= " AND p.category_id = $category_id";
        }
        if (in_array($status, ['active', 'inactive'])) {
            $where .= " AND p.status = '$status'";
        }
        
        // Get total count
        $count_result = $conn->query("SELECT COUNT(*) as total FROM products p $where");
        $total = $count_result->fetch_assoc()['total'];
        $total_pages = ceil($total / $limit);
        
        // Get products
        $sql = "
            SELECT p.*, c.name as category_name, s.name as supplier_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN suppliers s ON p.supplier_id = s.id
            $where
            ORDER BY p.created_at DESC
            LIMIT $limit OFFSET $offset
        ";
        
        $result = $conn->query($sql);
        $products = [];
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        
        // Get categories for filter
        $categories_result = $conn->query("SELECT id, name FROM categories WHERE status = 'active'");
        $categories = [];
        while ($row = $categories_result->fetch_assoc()) {
            $categories[] = $row;
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
                'categories' => $categories
            ]
        ]);
        break;
        
    case 'POST':
        // Create new product
        $data = json_decode(file_get_contents('php://input'), true);
        
        $required_fields = ['name', 'price', 'stock'];
        foreach ($required_fields as $field) {
            if (empty($data[$field])) {
                echo json_encode([
                    'success' => false,
                    'message' => "Field $field harus diisi"
                ]);
                exit;
            }
        }
        
        $name = $conn->real_escape_string($data['name']);
        $description = $conn->real_escape_string($data['description'] ?? '');
        $price = floatval($data['price']);
        $cost_price = floatval($data['cost_price'] ?? $price * 0.7);
        $stock = intval($data['stock']);
        $category_id = intval($data['category_id'] ?? 0);
        $supplier_id = intval($data['supplier_id'] ?? 0);
        $status = in_array($data['status'] ?? 'active', ['active', 'inactive']) ? $data['status'] : 'active';
        $image = $conn->real_escape_string($data['image'] ?? 'default.jpg');
        
        $sql = "INSERT INTO products (
            name, description, price, cost_price, stock, 
            category_id, supplier_id, image, status, created_at
        ) VALUES (
            '$name', '$description', $price, $cost_price, $stock,
            $category_id, $supplier_id, '$image', '$status', NOW()
        )";
        
        if ($conn->query($sql)) {
            $product_id = $conn->insert_id;
            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'product_id' => $product_id
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan produk: ' . $conn->error
            ]);
        }
        break;
        
    case 'PUT':
        // Update product
        parse_str(file_get_contents('php://input'), $data);
        $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($product_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID produk tidak valid'
            ]);
            exit;
        }
        
        $updates = [];
        $allowed_fields = ['name', 'description', 'price', 'cost_price', 'stock', 'category_id', 'supplier_id', 'image', 'status'];
        
        foreach ($allowed_fields as $field) {
            if (isset($data[$field])) {
                $value = $conn->real_escape_string($data[$field]);
                if (is_numeric($value) && $field !== 'name' && $field !== 'description' && $field !== 'image') {
                    $updates[] = "$field = $value";
                } else {
                    $updates[] = "$field = '$value'";
                }
            }
        }
        
        if (empty($updates)) {
            echo json_encode([
                'success' => false,
                'message' => 'Tidak ada data yang diupdate'
            ]);
            exit;
        }
        
        $sql = "UPDATE products SET " . implode(', ', $updates) . " WHERE id = $product_id";
        
        if ($conn->query($sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil diupdate'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengupdate produk: ' . $conn->error
            ]);
        }
        break;
        
    case 'DELETE':
        // Delete product (soft delete)
        $product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        
        if ($product_id <= 0) {
            echo json_encode([
                'success' => false,
                'message' => 'ID produk tidak valid'
            ]);
            exit;
        }
        
        $sql = "UPDATE products SET status = 'inactive' WHERE id = $product_id";
        
        if ($conn->query($sql)) {
            echo json_encode([
                'success' => true,
                'message' => 'Produk berhasil dihapus'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus produk: ' . $conn->error
            ]);
        }
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        http_response_code(405);
}
?>
