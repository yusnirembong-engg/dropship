<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Produk - Dropship Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        .product-card {
            transition: transform 0.3s;
            height: 100%;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .filter-sidebar {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
        }
        .price-range-slider {
            width: 100%;
        }
        .category-badge {
            cursor: pointer;
            transition: all 0.3s;
        }
        .category-badge:hover {
            background-color: #0d6efd !important;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation (same as index.php) -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/">
                <i class="bi bi-shop text-primary"></i> 
                <span class="fw-bold">Dropship</span><span class="text-primary">Store</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">Tentang</a>
                    </li>
                </ul>
                
                <form class="d-flex me-3" action="/products" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari produk..." name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <div class="d-flex align-items-center">
                    <a href="/cart" class="btn btn-outline-primary position-relative me-3">
                        <i class="bi bi-cart"></i>
                        <span class="cart-count"><?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?></span>
                    </a>
                    
                    <?php if (isset($_SESSION['customer_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> Akun
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="/account">Dashboard</a></li>
                            <li><a class="dropdown-item" href="/account/orders">Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/logout">Logout</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <div class="btn-group">
                        <a href="/login" class="btn btn-outline-primary">Login</a>
                        <a href="/register" class="btn btn-primary">Daftar</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <div class="filter-sidebar">
                    <h5 class="mb-4">Filter Produk</h5>
                    
                    <!-- Categories -->
                    <div class="mb-4">
                        <h6>Kategori</h6>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-primary category-badge" onclick="filterByCategory('all')">Semua</span>
                            <span class="badge bg-secondary category-badge" onclick="filterByCategory('elektronik')">Elektronik</span>
                            <span class="badge bg-secondary category-badge" onclick="filterByCategory('fashion')">Fashion</span>
                            <span class="badge bg-secondary category-badge" onclick="filterByCategory('rumah-tangga')">Rumah Tangga</span>
                            <span class="badge bg-secondary category-badge" onclick="filterByCategory('kecantikan')">Kecantikan</span>
                        </div>
                    </div>
                    
                    <!-- Price Range -->
                    <div class="mb-4">
                        <h6>Rentang Harga</h6>
                        <input type="range" class="form-range price-range-slider" min="0" max="5000000" value="5000000" id="priceRange">
                        <div class="d-flex justify-content-between">
                            <span>Rp 0</span>
                            <span id="maxPrice">Rp 5.000.000</span>
                        </div>
                    </div>
                    
                    <!-- Stock Availability -->
                    <div class="mb-4">
                        <h6>Ketersediaan Stok</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="inStockOnly" checked>
                            <label class="form-check-label" for="inStockOnly">
                                Tampilkan produk tersedia saja
                            </label>
                        </div>
                    </div>
                    
                    <!-- Sort -->
                    <div class="mb-4">
                        <h6>Urutkan</h6>
                        <select class="form-select" id="sortBy">
                            <option value="newest">Terbaru</option>
                            <option value="price-low">Harga Terendah</option>
                            <option value="price-high">Harga Tertinggi</option>
                            <option value="name">Nama A-Z</option>
                        </select>
                    </div>
                    
                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-funnel"></i> Terapkan Filter
                    </button>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Katalog Produk</h2>
                    <div class="d-flex align-items-center">
                        <span class="me-3 text-muted" id="productCount">12 produk</span>
                        <div class="btn-group">
                            <button class="btn btn-outline-secondary active" onclick="changeView('grid')">
                                <i class="bi bi-grid"></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="changeView('list')">
                                <i class="bi bi-list"></i>
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row" id="productsGrid">
                    <?php
                    // Sample products data
                    $products = [
                        ['id' => 1, 'name' => 'Smartphone Android 2024', 'price' => 2500000, 'image' => 'default.jpg', 'category' => 'Elektronik', 'stock' => 50],
                        ['id' => 2, 'name' => 'Laptop Gaming RTX 3060', 'price' => 15000000, 'image' => 'default.jpg', 'category' => 'Elektronik', 'stock' => 20],
                        ['id' => 3, 'name' => 'Kaos Polo Premium Cotton', 'price' => 150000, 'image' => 'default.jpg', 'category' => 'Fashion', 'stock' => 100],
                        ['id' => 4, 'name' => 'Blender Multifungsi 5in1', 'price' => 350000, 'image' => 'default.jpg', 'category' => 'Rumah Tangga', 'stock' => 30],
                        ['id' => 5, 'name' => 'Lipstik Matte Tahan Lama', 'price' => 75000, 'image' => 'default.jpg', 'category' => 'Kecantikan', 'stock' => 200],
                        ['id' => 6, 'name' => 'Headset Wireless Bluetooth', 'price' => 500000, 'image' => 'default.jpg', 'category' => 'Elektronik', 'stock' => 45],
                        ['id' => 7, 'name' => 'Sepatu Sneakers Premium', 'price' => 450000, 'image' => 'default.jpg', 'category' => 'Fashion', 'stock' => 60],
                        ['id' => 8, 'name' => 'Panci Stainless Steel', 'price' => 280000, 'image' => 'default.jpg', 'category' => 'Rumah Tangga', 'stock' => 80],
                    ];
                    
                    foreach ($products as $product):
                    ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card product-card">
                            <img src="assets/images/products/<?php echo $product['image']; ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 200px; object-fit: cover;">
                            <div class="card-body">
                                <span class="badge bg-info mb-2"><?php echo $product['category']; ?></span>
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-muted small mb-3">
                                    Deskripsi singkat produk yang menarik dan informatif...
                                </p>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="h5 text-primary mb-0">Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></span>
                                    <span class="badge bg-success">Stok: <?php echo $product['stock']; ?></span>
                                </div>
                                <div class="d-grid gap-2">
                                    <a href="/product/<?php echo $product['id']; ?>" class="btn btn-primary">Detail</a>
                                    <button class="btn btn-outline-success add-to-cart" 
                                            data-id="<?php echo $product['id']; ?>"
                                            data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                            data-price="<?php echo $product['price']; ?>">
                                        <i class="bi bi-cart-plus"></i> Tambah Keranjang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Update max price display
    const priceRange = document.getElementById('priceRange');
    const maxPriceDisplay = document.getElementById('maxPrice');
    
    priceRange.addEventListener('input', function() {
        const price = parseInt(this.value);
        maxPriceDisplay.textContent = 'Rp ' + price.toLocaleString('id-ID');
    });
    
    // Filter functions
    function filterByCategory(category) {
        // Update active category badge
        document.querySelectorAll('.category-badge').forEach(badge => {
            badge.classList.remove('bg-primary');
            badge.classList.add('bg-secondary');
        });
        
        event.target.classList.remove('bg-secondary');
        event.target.classList.add('bg-primary');
        
        // In a real app, this would filter products via AJAX
        console.log('Filter by category:', category);
    }
    
    function applyFilters() {
        const maxPrice = priceRange.value;
        const inStockOnly = document.getElementById('inStockOnly').checked;
        const sortBy = document.getElementById('sortBy').value;
        
        // In a real app, this would send filter request via AJAX
        console.log('Applying filters:', { maxPrice, inStockOnly, sortBy });
        
        showToast('Filter diterapkan!', 'success');
    }
    
    function changeView(view) {
        const productsGrid = document.getElementById('productsGrid');
        const gridButtons = document.querySelectorAll('.btn-group .btn');
        
        // Update active button
        gridButtons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Change view
        if (view === 'grid') {
            productsGrid.className = 'row';
            document.querySelectorAll('.product-card .card-body p').forEach(p => {
                p.style.display = 'block';
            });
        } else {
            productsGrid.className = 'row list-view';
            document.querySelectorAll('.product-card').forEach(card => {
                card.classList.add('flex-row');
            });
            document.querySelectorAll('.product-card img').forEach(img => {
                img.style.width = '150px';
                img.style.height = '150px';
            });
            document.querySelectorAll('.product-card .card-body p').forEach(p => {
                p.style.display = 'none';
            });
        }
    }
    
    // Cart functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add to cart buttons
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-id');
                const productName = this.getAttribute('data-name');
                const productPrice = this.getAttribute('data-price');
                
                addToCart(productId, productName, productPrice);
            });
        });
    });
    
    function addToCart(productId, productName, productPrice) {
        // Send AJAX request to add item to cart
        fetch('/api/cart.php?action=add', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId,
                product_name: productName,
                price: productPrice,
                quantity: 1
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Produk ditambahkan ke keranjang!', 'success');
                updateCartCount(data.cart_count);
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Terjadi kesalahan. Silakan coba lagi.', 'danger');
        });
    }
    
    function updateCartCount(count) {
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }
    
    function showToast(message, type = 'info') {
        const toastContainer = document.querySelector('.toast-container');
        const toastId = 'toast-' + Date.now();
        
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" 
                        data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
    
    // Initialize cart count
    updateCartCount(<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>);
    </script>
</body>
</html>
