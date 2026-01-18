<?php
// Database Configuration
define('DB_TYPE', 'sqlite'); // sqlite or mysql
define('DB_SQLITE_PATH', __DIR__ . '/database.sqlite');

// For MySQL (uncomment and fill if using MySQL)
// define('DB_HOST', 'localhost');
// define('DB_NAME', 'dropship_db');
// define('DB_USER', 'root');
// define('DB_PASS', '');

// Site Configuration
define('SITE_NAME', 'Dropship Store');
define('SITE_URL', 'https://' . ($_SERVER['HTTP_HOST'] ?? 'localhost'));
define('ADMIN_EMAIL', 'admin@dropshipstore.com');

// Session Configuration
ini_set('session.cookie_lifetime', 86400); // 24 hours
ini_set('session.gc_maxlifetime', 86400);

// Error Reporting
if (getenv('VERCEL')) {
    // Production on Vercel
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
} else {
    // Local development
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
}

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Database Connection Function
function getDatabase() {
    if (DB_TYPE === 'sqlite') {
        try {
            $db = new SQLite3(DB_SQLITE_PATH);
            $db->busyTimeout(5000);
            return $db;
        } catch (Exception $e) {
            error_log('SQLite Error: ' . $e->getMessage());
            return null;
        }
    } else {
        // MySQL connection
        try {
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
            if ($conn->connect_error) {
                error_log('MySQL Connection Error: ' . $conn->connect_error);
                return null;
            }
            $conn->set_charset('utf8mb4');
            return $conn;
        } catch (Exception $e) {
            error_log('MySQL Error: ' . $e->getMessage());
            return null;
        }
    }
}

// Initialize SQLite database if not exists
function initDatabase() {
    if (DB_TYPE === 'sqlite' && !file_exists(DB_SQLITE_PATH)) {
        $db = new SQLite3(DB_SQLITE_PATH);
        
        // Create tables
        $db->exec('CREATE TABLE IF NOT EXISTS products (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            description TEXT,
            price REAL NOT NULL,
            stock INTEGER DEFAULT 0,
            category TEXT,
            image TEXT,
            status TEXT DEFAULT "active",
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
        
        $db->exec('CREATE TABLE IF NOT EXISTS customers (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            phone TEXT,
            address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
        
        $db->exec('CREATE TABLE IF NOT EXISTS orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_code TEXT UNIQUE NOT NULL,
            customer_id INTEGER,
            total_amount REAL NOT NULL,
            status TEXT DEFAULT "pending",
            shipping_address TEXT,
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )');
        
        $db->exec('CREATE TABLE IF NOT EXISTS order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            order_id INTEGER NOT NULL,
            product_id INTEGER NOT NULL,
            quantity INTEGER NOT NULL,
            price REAL NOT NULL,
            total REAL NOT NULL
        )');
        
        // Insert sample data
        $products = [
            ['Smartphone Android', 'Smartphone dengan spesifikasi tinggi', 2500000, 50, 'Elektronik', 'default.jpg'],
            ['Laptop Gaming', 'Laptop gaming RTX 3060', 15000000, 20, 'Elektronik', 'default.jpg'],
            ['Kaos Polo Premium', 'Kaos bahan katun premium', 150000, 100, 'Fashion', 'default.jpg'],
            ['Blender Multifungsi', 'Blender 5in1', 350000, 30, 'Rumah Tangga', 'default.jpg']
        ];
        
        $stmt = $db->prepare('INSERT INTO products (name, description, price, stock, category, image) 
                               VALUES (?, ?, ?, ?, ?, ?)');
        
        foreach ($products as $product) {
            $stmt->bindValue(1, $product[0]);
            $stmt->bindValue(2, $product[1]);
            $stmt->bindValue(3, $product[2]);
            $stmt->bindValue(4, $product[3]);
            $stmt->bindValue(5, $product[4]);
            $stmt->bindValue(6, $product[5]);
            $stmt->execute();
        }
        
        $db->close();
    }
}

// Call init on first run
initDatabase();
?>
