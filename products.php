<?php
require_once 'includes/database.php';
require_once 'includes/functions.php';

$db = new Database();
$conn = $db->getConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Filter kategori
$category_id = isset($_GET['category']) ? $_GET['category'] : '';
$where = "WHERE status = 'active'";
if ($category_id) {
    $where .= " AND category_id = '$category_id'";
}

// Hitung total produk
$total_result = $conn->query("SELECT COUNT(*) as total FROM products $where");
$total_products = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_products / $limit);

// Ambil produk
$sql = "SELECT * FROM products $where ORDER BY created_at DESC LIMIT $limit OFFSET $offset";
$products = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produk - Dropship Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'includes/header.php'; ?>

    <div class="container mt-4">
        <!-- Filter Kategori -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5>Filter Kategori</h5>
                        <div class="btn-group">
                            <a href="products.php" class="btn btn-outline-primary">Semua</a>
                            <?php
                            $categories = $conn->query("SELECT * FROM categories WHERE status = 'active'");
                            while($cat = $categories->fetch_assoc()):
                            ?>
                            <a href="products.php?category=<?= $cat['id'] ?>" 
                               class="btn btn-outline-primary <?= $category_id == $cat['id'] ? 'active' : '' ?>">
                                <?= $cat['name'] ?>
                            </a>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Produk -->
        <div class="row">
            <?php while($product = $products->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card product-card">
                    <img src="assets/images/products/<?= $product['image'] ?: 'default.jpg' ?>" 
                         class="card-img-top" alt="<?= $product['name'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $product['name'] ?></h5>
                        <p class="card-text text-muted"><?= substr($product['description'], 0, 100) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="h5 text-primary">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                            <span class="badge bg-success">Stok: <?= $product['stock'] ?></span>
                        </div>
                        <div class="d-grid gap-2 mt-3">
                            <a href="product-detail.php?id=<?= $product['id'] ?>" class="btn btn-primary">Detail</a>
                            <button class="btn btn-outline-success add-to-cart" 
                                    data-id="<?= $product['id'] ?>">
                                <i class="bi bi-cart-plus"></i> Tambah ke Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <!-- Pagination -->
        <?php if($total_pages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="products.php?page=<?= $page-1 ?><?= $category_id ? '&category='.$category_id : '' ?>">Previous</a>
                </li>
                <?php endif; ?>

                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                    <a class="page-link" href="products.php?page=<?= $i ?><?= $category_id ? '&category='.$category_id : '' ?>"><?= $i ?></a>
                </li>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="products.php?page=<?= $page+1 ?><?= $category_id ? '&category='.$category_id : '' ?>">Next</a>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/cart.js"></script>
</body>
</html>
