<?php
// Database Configuration for Dropship System

class Database {
    private static $instance = null;
    private $connection;
    
    // Database configuration
    private $config = [
        'host' => 'localhost',
        'database' => 'dropship_db',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4'
    ];
    
    private function __construct() {
        try {
            // Check for environment variables (for Vercel)
            if (getenv('DB_HOST')) {
                $this->config['host'] = getenv('DB_HOST');
                $this->config['database'] = getenv('DB_NAME');
                $this->config['username'] = getenv('DB_USER');
                $this->config['password'] = getenv('DB_PASS');
            }
            
            // Create MySQLi connection
            $this->connection = new mysqli(
                $this->config['host'],
                $this->config['username'],
                $this->config['password'],
                $this->config['database']
            );
            
            // Check connection
            if ($this->connection->connect_error) {
                throw new Exception("Connection failed: " . $this->connection->connect_error);
            }
            
            // Set charset
            $this->connection->set_charset($this->config['charset']);
            
            // Create tables if they don't exist
            $this->createTablesIfNotExist();
            
        } catch (Exception $e) {
            error_log("Database Error: " . $e->getMessage());
            
            // Fallback to SQLite for demo purposes
            $this->setupSQLiteFallback();
        }
    }
    
    private function setupSQLiteFallback() {
        try {
            $sqlitePath = __DIR__ . '/../database/dropship.db';
            
            // Create directory if it doesn't exist
            if (!file_exists(dirname($sqlitePath))) {
                mkdir(dirname($sqlitePath), 0777, true);
            }
            
            // Connect to SQLite
            $this->connection = new SQLite3($sqlitePath);
            $this->connection->busyTimeout(5000);
            
            // Enable WAL mode for better concurrency
            $this->connection->exec('PRAGMA journal_mode = WAL;');
            
            // Create tables
            $this->createSQLiteTables();
            
            // Insert demo data
            $this->insertDemoData();
            
        } catch (Exception $e) {
            error_log("SQLite Error: " . $e->getMessage());
            die("Database connection failed. Please check your configuration.");
        }
    }
    
    private function createTablesIfNotExist() {
        // Check if using MySQLi or SQLite
        if ($this->connection instanceof mysqli) {
            $this->createMySQLTables();
        } else {
            $this->createSQLiteTables();
        }
    }
    
    private function createMySQLTables() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS admins (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                email VARCHAR(100) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                slug VARCHAR(100) UNIQUE NOT NULL,
                description TEXT,
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS suppliers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                contact_person VARCHAR(100),
                phone VARCHAR(20),
                email VARCHAR(100),
                address TEXT,
                api_key VARCHAR(255),
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200) NOT NULL,
                slug VARCHAR(200) UNIQUE NOT NULL,
                description TEXT,
                price DECIMAL(10,2) NOT NULL,
                cost_price DECIMAL(10,2) NOT NULL,
                stock INT DEFAULT 0,
                category_id INT,
                supplier_id INT,
                image VARCHAR(255) DEFAULT 'default.jpg',
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
                FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS customers (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                email VARCHAR(100) UNIQUE NOT NULL,
                phone VARCHAR(20),
                password VARCHAR(255) NOT NULL,
                address TEXT,
                city VARCHAR(50),
                province VARCHAR(50),
                postal_code VARCHAR(10),
                status ENUM('active', 'inactive') DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS orders (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_code VARCHAR(20) UNIQUE NOT NULL,
                customer_id INT,
                customer_name VARCHAR(100) NOT NULL,
                customer_email VARCHAR(100) NOT NULL,
                customer_phone VARCHAR(20) NOT NULL,
                shipping_address TEXT NOT NULL,
                shipping_city VARCHAR(50) NOT NULL,
                shipping_province VARCHAR(50) NOT NULL,
                shipping_postal_code VARCHAR(10) NOT NULL,
                total_amount DECIMAL(10,2) NOT NULL,
                shipping_cost DECIMAL(10,2) DEFAULT 0,
                status ENUM('pending', 'processing', 'shipped', 'completed', 'cancelled') DEFAULT 'pending',
                payment_method VARCHAR(50),
                payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (customer_id) REFERENCES customers(id) ON DELETE SET NULL
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",
            
            "CREATE TABLE IF NOT EXISTS order_items (
                id INT AUTO_INCREMENT PRIMARY KEY,
                order_id INT NOT NULL,
                product_id INT NOT NULL,
                product_name VARCHAR(200) NOT NULL,
                product_price DECIMAL(10,2) NOT NULL,
                quantity INT NOT NULL,
                total DECIMAL(10,2) NOT NULL,
                FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
        ];
        
        foreach ($tables as $sql) {
            $this->connection->query($sql);
        }
        
        // Create default admin if not exists
        $this->createDefaultAdmin();
    }
    
    private function createSQLiteTables() {
        $tables = [
            "CREATE TABLE IF NOT EXISTS admins (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT UNIQUE NOT NULL,
                password TEXT NOT NULL,
                email TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                status TEXT DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS suppliers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                contact_person TEXT,
                phone TEXT,
                email TEXT,
                address TEXT,
                api_key TEXT,
                status TEXT DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                slug TEXT UNIQUE NOT NULL,
                description TEXT,
                price REAL NOT NULL,
                cost_price REAL NOT NULL,
                stock INTEGER DEFAULT 0,
                category_id INTEGER,
                supplier_id INTEGER,
                image TEXT DEFAULT 'default.jpg',
                status TEXT DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS customers (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT UNIQUE NOT NULL,
                phone TEXT,
                password TEXT NOT NULL,
                address TEXT,
                city TEXT,
                province TEXT,
                postal_code TEXT,
                status TEXT DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS orders (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_code TEXT UNIQUE NOT NULL,
                customer_id INTEGER,
                customer_name TEXT NOT NULL,
                customer_email TEXT NOT NULL,
                customer_phone TEXT NOT NULL,
                shipping_address TEXT NOT NULL,
                shipping_city TEXT NOT NULL,
                shipping_province TEXT NOT NULL,
                shipping_postal_code TEXT NOT NULL,
                total_amount REAL NOT NULL,
                shipping_cost REAL DEFAULT 0,
                status TEXT DEFAULT 'pending',
                payment_method TEXT,
                payment_status TEXT DEFAULT 'pending',
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );",
            
            "CREATE TABLE IF NOT EXISTS order_items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                order_id INTEGER NOT NULL,
                product_id INTEGER NOT NULL,
                product_name TEXT NOT NULL,
                product_price REAL NOT NULL,
                quantity INTEGER NOT NULL,
                total REAL NOT NULL
            );"
        ];
        
        foreach ($tables as $sql) {
            $this->connection->exec($sql);
        }
        
        // Create default admin if not exists
        $this->createDefaultAdmin();
    }
    
    private function createDefaultAdmin() {
        $checkAdmin = $this->connection->query("SELECT COUNT(*) as count FROM admins");
        
        if ($this->connection instanceof mysqli) {
            $row = $checkAdmin->fetch_assoc();
        } else {
            $row = $checkAdmin->fetchArray(SQLITE3_ASSOC);
        }
        
        if ($row['count'] == 0) {
            $username = 'admin';
            $email = 'admin@dropship.local';
            $password = password_hash('admin123', PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO admins (username, email, password) VALUES (?, ?, ?)";
            
            if ($this->connection instanceof mysqli) {
                $stmt = $this->connection->prepare($sql);
                $stmt->bind_param("sss", $username, $email, $password);
                $stmt->execute();
            } else {
                $stmt = $this->connection->prepare($sql);
                $stmt->bindValue(1, $username, SQLITE3_TEXT
