<?php
// Website Header
$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dropship Store' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/website-dropship/public/assets/css/style.css">
    <style>
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
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
        .product-card {
            transition: transform 0.3s;
        }
        .product-card:hover {
            transform: translateY(-5px);
        }
        .category-filter .btn.active {
            background-color: #0d6efd;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="/website-dropship">
                <i class="bi bi-shop text-primary"></i> Dropship<span class="text-primary">Store</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link active" href="/website-dropship">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/website-dropship/products">Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/website-dropship/categories">Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/website-dropship/about">Tentang</a>
                    </li>
                </ul>
                
                <!-- Search Form -->
                <form class="d-flex me-3" action="/website-dropship/products" method="GET">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Cari produk..." name="search">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
                
                <!-- Cart and Account -->
                <div class="d-flex align-items-center">
                    <a href="/website-dropship/cart" class="btn btn-outline-primary position-relative me-3">
                        <i class="bi bi-cart"></i>
                        <span class="cart-count"><?= $cart_count ?></span>
                    </a>
                    
                    <?php if (isset($_SESSION['customer_id'])): ?>
                    <div class="dropdown">
                        <a href="#" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-person"></i> Akun
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="/website-dropship/account">Dashboard</a></li>
                            <li><a class="dropdown-item" href="/website-dropship/account/orders">Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/website-dropship/logout">Logout</a></li>
                        </ul>
                    </div>
                    <?php else: ?>
                    <div class="btn-group">
                        <a href="/website-dropship/login" class="btn btn-outline-primary">Login</a>
                        <a href="/website-dropship/register" class="btn btn-primary">Daftar</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
    
    <div id="toast-container"></div>
    
    <main class="py-4">
