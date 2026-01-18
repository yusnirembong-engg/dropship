<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dropship Store - Jualan Tanpa Stok</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
            --success-color: #198754;
        }
        
        .hero-section {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), 
                        url('assets/images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 100px 0;
        }
        
        .product-card {
            transition: transform 0.3s, box-shadow 0.3s;
            border: 1px solid #e0e0e0;
            height: 100%;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        
        .category-card {
            text-align: center;
            padding: 20px;
            border-radius: 10px;
            background: #f8f9fa;
            transition: all 0.3s;
        }
        
        .category-card:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }
        
        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
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
                        <a class="nav-link active" href="/">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/categories">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/about">Tentang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/contact">Kontak</a>
                    </li>
                </ul>
                
                <!-- Search -->
                <form class="d-flex me-3" action="/products" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari produk..." name="search">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Cart & Account -->
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

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-3">Jualan Tanpa Stok, Untung Tanpa Batas</h1>
                    <p class="lead mb-4">Ribuan produk siap dikirim langsung ke customer. Mulai bisnis dropship Anda sekarang!</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="/products" class="btn btn-primary btn-lg px-4">
                            <i class="bi bi-cart-plus"></i> Belanja Sekarang
                        </a>
                        <a href="/register" class="btn btn-outline-light btn-lg px-4">
                            <i class="bi bi-rocket-takeoff"></i> Daftar Reseller
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Kategori Produk</h2>
            <div class="row g-4">
                <div class="col-md-3 col-6">
                    <a href="/products?category=elektronik" class="text-decoration-none">
                        <div class="category-card">
                            <i class="bi bi-phone fs-1 mb-3"></i>
                            <h5>Elektronik</h5>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="/products?category=fashion" class="text-decoration-none">
                        <div class="category-card">
                            <i class="bi bi-tshirt fs-1 mb-3"></i>
                            <h5>Fashion</h5>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="/products?category=rumah-tangga" class="text-decoration-none">
                        <div class="category-card">
                            <i class="bi bi-house-door fs-1 mb-3"></i>
                            <h5>Rumah Tangga</h5>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 col-6">
                    <a href="/products?category=kecantikan" class="text-decoration-none">
                        <div class="category-card">
                            <i class="bi bi-heart fs-1 mb-3"></i>
                            <h5>Kecantikan</h5>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Produk Terlaris</h2>
                <a href="/products" class="btn btn-link">Lihat Semua <i class="bi bi-arrow-right"></i></a>
            </div>
            
            <div class="row g-4">
                <?php
                // Sample products
                $featured_products = [
                    [
                        'id' => 1,
                        'name' => 'Smartphone Android 2024',
                        'price' => 2500000,
                        'image' => 'default.jpg',
                        'category' => 'Elektronik',
                        'stock' => 50
                    ],
                    [
                        'id' => 2,
                        'name' => 'Laptop Gaming RTX 3060',
                        'price' => 15000000,
                        'image' => 'default.jpg',
                        'category' => 'Elektronik',
                        'stock' => 20
                    ],
                    [
                        'id' => 3,
                        'name' => 'Kaos Polo Premium Cotton',
                        'price' => 150000,
                        'image' => 'default.jpg',
                        'category' => 'Fashion',
                        'stock' => 100
                    ],
                    [
                        'id' => 4,
                        'name' => 'Blender Multifungsi 5in1',
                        'price' => 350000,
                        'image' => 'default.jpg',
                        'category' => 'Rumah Tangga',
                        'stock' => 30
                    ]
                ];
                
                foreach ($featured_products as $product):
                ?>
                <div class="col-lg-3 col-md-6">
                    <div class="card product-card">
                        <img src="assets/images/products/<?php echo $product['image']; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             style="height: 200px; object-fit: cover;">
                        <div class="card-body">
                            <span class="badge bg-info mb-2"><?php echo $product['category']; ?></span>
                            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
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
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-5">
        <div class="container">
            <h2 class="text-center mb-5">Mengapa Memilih Kami?</h2>
            <div class="row g-4">
                <div class="col-md-3 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>
                    <h5>Pengiriman Cepat</h5>
                    <p class="text-muted">Pesanan diproses dalam 24 jam dan dikirim langsung ke customer</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-shield-check"></i>
                    </div>
                    <h5>Garansi Produk</h5>
                    <p class="text-muted">Garansi 30 hari untuk semua produk yang kami sediakan</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>
                    <h5>Support 24/7</h5>
                    <p class="text-muted">Tim support siap membantu Anda kapan saja</p>
                </div>
                <div class="col-md-3 text-center">
                    <div class="feature-icon">
                        <i class="bi bi-graph-up"></i>
                    </div>
                    <h5>Margin Tinggi</h5>
                    <p class="text-muted">Dapatkan keuntungan hingga 50% dari setiap penjualan</p>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="text-center mb-5">Cara Kerja Dropship</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 text-primary mb-3">1</div>
                            <h4>Daftar Gratis</h4>
                            <p>Buat akun reseller secara gratis tanpa biaya pendaftaran</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 text-primary mb-3">2</div>
                            <h4>Pilih Produk</h4>
                            <p>Pilih produk dari katalog kami dan pasang di toko online Anda</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card border-0 shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="fs-1 text-primary mb-3">3</div>
                            <h4>Jual & Untung</h4>
                            <p>Kami kirimkan pesanan ke customer, Anda dapatkan keuntungan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="mb-3">
                        <i class="bi bi-shop"></i> DropshipStore
                    </h5>
                    <p>Platform dropship terpercaya dengan ribuan produk siap jual. Tanpa stok, tanpa risiko.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp fs-5"></i></a>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="mb-3">Menu</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/" class="text-white-50 text-decoration-none">Home</a></li>
                        <li class="mb-2"><a href="/products" class="text-white-50 text-decoration-none">Produk</a></li>
                        <li class="mb-2"><a href="/categories" class="text-white-50 text-decoration-none">Kategori</a></li>
                        <li class="mb-2"><a href="/about" class="text-white-50 text-decoration-none">Tentang</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3">Bantuan</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="/help" class="text-white-50 text-decoration-none">Cara Berbelanja</a></li>
                        <li class="mb-2"><a href="/shipping" class="text-white-50 text-decoration-none">Pengiriman</a></li>
                        <li class="mb-2"><a href="/payment" class="text-white-50 text-decoration-none">Pembayaran</a></li>
                        <li class="mb-2"><a href="/contact" class="text-white-50 text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="mb-3">Kontak</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-geo-alt"></i> Jl. Contoh No. 123, Jakarta</li>
                        <li class="mb-2"><i class="bi bi-telephone"></i> (021) 123-4567</li>
                        <li class="mb-2"><i class="bi bi-envelope"></i> info@dropshipstore.com</li>
                        <li class="mb-2"><i class="bi bi-clock"></i> Buka 24/7</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> DropshipStore. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <img src="assets/images/payment-methods.png" alt="Payment Methods" height="30">
                </div>
            </div>
        </div>
    </footer>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 end-0 p-3">
        <!-- Toasts will be inserted here by JavaScript -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    
    // Initialize cart count on page load
    updateCartCount(<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>);
    </script>
</body>
</html>
