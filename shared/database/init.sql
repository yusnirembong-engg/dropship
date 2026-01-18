-- Initialize Database for Dropship System

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS dropship_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dropship_db;

-- Admins table
CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert default admin (password: admin123)
INSERT IGNORE INTO admins (username, password, email) VALUES 
('admin', '$2y$10$YourHashedPasswordHere', 'admin@dropship.local');

-- Categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample categories
INSERT IGNORE INTO categories (name, slug) VALUES 
('Elektronik', 'elektronik'),
('Fashion', 'fashion'),
('Rumah Tangga', 'rumah-tangga'),
('Kecantikan', 'kecantikan'),
('Olahraga', 'olahraga');

-- Suppliers table
CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_person VARCHAR(100),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    api_key VARCHAR(255),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Insert sample suppliers
INSERT IGNORE INTO suppliers (name, contact_person, phone, email) VALUES 
('Supplier Utama', 'Budi Santoso', '08123456789', 'budi@supplier.com'),
('Supplier Fashion', 'Sari Dewi', '08234567890', 'sari@fashion.com');

-- Products table
CREATE TABLE IF NOT EXISTS products (
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
) ENGINE=InnoDB;

-- Insert sample products
INSERT IGNORE INTO products (name, slug, description, price, cost_price, stock, category_id, supplier_id, image) VALUES 
('Smartphone Android 2024', 'smartphone-android-2024', 'Smartphone Android terbaru dengan RAM 8GB', 3500000, 2800000, 50, 1, 1, 'phone.jpg'),
('Laptop Gaming', 'laptop-gaming', 'Laptop gaming dengan GPU RTX 3060', 15000000, 12000000, 20, 1, 1, 'laptop.jpg'),
('Kaos Polo Premium', 'kaos-polo-premium', 'Kaos polo bahan katun premium', 250000, 150000, 100, 2, 2, 'polo.jpg'),
('Blender Multifungsi', 'blender-multifungsi', 'Blender dengan 5 fungsi berbeda', 500000, 350000, 30, 3, 1, 'blender.jpg'),
('Lipstik Matte', 'lipstik-matte', 'Lipstik matte tahan lama', 75000, 45000, 200, 4, 2, 'lipstik.jpg');

-- Customers table
CREATE TABLE IF NOT EXISTS customers (
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
) ENGINE=InnoDB;

-- Insert sample customer (password: customer123)
INSERT IGNORE INTO customers (name, email, phone, password, address) VALUES 
('John Doe', 'john@example.com', '08123456789', '$2y$10$YourHashedPasswordHere', 'Jl. Contoh No. 123');

-- Orders table
CREATE TABLE IF NOT EXISTS orders (
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
) ENGINE=InnoDB;

-- Order items table
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Create indexes for better performance
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_products_status ON products(status);
CREATE INDEX idx_orders_status ON orders(status);
CREATE INDEX idx_orders_customer ON orders(customer_id);
CREATE INDEX idx_order_items_order ON order_items(order_id);
