<?php
// Home Page
require_once __DIR__ . '/../includes/header.php';
?>

<!-- Hero Section -->
<section class="hero-section" style="background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('assets/images/hero-bg.jpg'); background-size: cover; background-position: center; color: white; padding: 100px 0;">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-3">Temukan Produk Terbaik untuk Dropship</h1>
                <p class="lead mb-4">Ribuan produk siap dikirim langsung ke customer Anda. Tanpa stok, tanpa risiko.</p>
                <div class="d-flex gap-3">
                    <a href="/website-dropship/products" class="btn btn-primary btn-lg">Lihat Katalog</a>
                    <a href="/website-dropship/register" class="btn btn-outline-light btn-lg">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Categories -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Kategori Populer</h2>
        <div class="row" id="categories-container">
            <!-- Categories will be loaded by JavaScript -->
        </div>
    </div>
</section>

<!-- Featured Products -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Produk Terbaru</h2>
            <a href="/website-dropship/products" class="btn btn-link">Lihat Semua →</a>
        </div>
        <div class="row" id="featured-products">
            <!-- Products will be loaded by JavaScript -->
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-5">Mengapa Memilih Kami?</h2>
        <div class="row text-center">
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-truck"></i>
                        </div>
                        <h5>Pengiriman Cepat</h5>
                        <p>Pesanan diproses dalam 24 jam dan dikirim langsung ke customer</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <h5>Garansi Produk</h5>
                        <p>Garansi 30 hari untuk semua produk yang kami sediakan</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-headset"></i>
                        </div>
                        <h5>Support 24/7</h5>
                        <p>Tim support siap membantu Anda kapan saja</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div class="fs-1 text-primary mb-3">
                            <i class="bi bi-graph-up"></i>
                        </div>
                        <h5>Margin Tinggi</h5>
                        <p>Dapatkan keuntungan hingga 50% dari setiap penjualan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadFeaturedData();
});

function loadFeaturedData() {
    // Load featured products
    fetch('/website-dropship/api/products.php?limit=4')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById('featured-products');
                container.innerHTML = '';
                
                data.data.forEach(product => {
                    const productCard = createProductCard(product);
                    container.appendChild(productCard);
                });
            }
        });
    
    // Load categories
    fetch('/website-dropship/api/products.php?limit=1')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.filters.categories) {
                const container = document.getElementById('categories-container');
                container.innerHTML = '';
                
                // Show first 4 categories
                data.filters.categories.slice(0, 4).forEach(category => {
                    const col = document.createElement('div');
                    col.className = 'col-md-3 mb-4';
                    col.innerHTML = `
                        <a href="/website-dropship/products?category_id=${category.id}" class="text-decoration-none">
                            <div class="card category-card h-100">
                                <div class="card-body text-center">
                                    <div class="fs-1 text-primary mb-3">
                                        <i class="bi bi-tags"></i>
                                    </div>
                                    <h5 class="card-title">${category.name}</h5>
                                </div>
                            </div>
                        </a>
                    `;
                    container.appendChild(col);
                });
            }
        });
}

function createProductCard(product) {
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-4';
    
    col.innerHTML = `
        <div class="card product-card h-100">
            <img src="/website-dropship/public/assets/images/products/${product.image}" 
                 class="card-img-top" alt="${product.name}" style="height: 200px; object-fit: cover;">
            <div class="card-body d-flex flex-column">
                <h5 class="card-title">${product.name}</h5>
                <p class="card-text text-muted small flex-grow-1">${product.description.substring(0, 80)}...</p>
                <div class="mt-auto">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="h5 text-primary mb-0">Rp ${formatNumber(product.price)}</span>
                        <span class="badge bg-success">Stok: ${product.stock}</span>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="/website-dropship/product/${product.id}" class="btn btn-primary">Detail</a>
                        <button class="btn btn-outline-success add-to-cart" data-id="${product.id}">
                            <i class="bi bi-cart-plus"></i> Keranjang
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Add event listener for add to cart button
    const addToCartBtn = col.querySelector('.add-to-cart');
    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', function() {
            addToCart(product.id);
        });
    }
    
    return col;
}

function formatNumber(num) {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function addToCart(productId) {
    fetch('/website-dropship/api/cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            action: 'add',
            product_id: productId,
            quantity: 1
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Produk ditambahkan ke keranjang!', 'success');
            updateCartCount();
        } else {
            showToast(data.message, 'danger');
        }
    });
}

function showToast(message, type) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-bg-${type} border-0`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    // Add to container
    const container = document.getElementById('toast-container') || createToastContainer();
    container.appendChild(toast);
    
    // Initialize and show
    const bsToast = new bootstrap.Toast(toast);
    bsToast.show();
    
    // Remove after hide
    toast.addEventListener('hidden.bs.toast', function() {
        toast.remove();
    });
}

function createToastContainer() {
    const container = document.createElement('div');
    container.id = 'toast-container';
    container.className = 'toast-container position-fixed top-0 end-0 p-3';
    document.body.appendChild(container);
    return container;
}

function updateCartCount() {
    const cartCount = document.querySelector('.cart-count');
    if (cartCount) {
        fetch('/website-dropship/api/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    cartCount.textContent = data.count;
                }
            });
    }
}
</script>

<style>
.product-card {
    transition: transform 0.3s, box-shadow 0.3s;
    border: 1px solid #e0e0e0;
}
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.category-card {
    transition: all 0.3s;
    border: 1px solid #e0e0e0;
}
.category-card:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
