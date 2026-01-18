<?php
session_start();
require_once 'config/database.php';

// Cek login
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

$db = new Database();
$conn = $db->getConnection();

// Ambil statistik
$stats = [];
$queries = [
    'total_orders' => "SELECT COUNT(*) as total FROM orders WHERE status != 'cancelled'",
    'pending_orders' => "SELECT COUNT(*) as total FROM orders WHERE status = 'pending'",
    'total_products' => "SELECT COUNT(*) as total FROM products WHERE status = 'active'",
    'total_sales' => "SELECT SUM(total_amount) as total FROM orders WHERE status = 'completed'"
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    $stats[$key] = $result->fetch_assoc()['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Panel Dropship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</head>
<body>
    <!-- Sidebar -->
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 d-md-block bg-light sidebar">
                <div class="position-sticky pt-3">
                    <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                        Menu Utama
                    </h6>
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="dashboard.php">
                                <i class="bi bi-speedometer2"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="products/manage.php">
                                <i class="bi bi-box"></i>
                                Produk
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="orders/list.php">
                                <i class="bi bi-receipt"></i>
                                Pesanan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="suppliers/list.php">
                                <i class="bi bi-truck"></i>
                                Supplier
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between pt-3">
                    <h2>Dashboard</h2>
                    <a href="logout.php" class="btn btn-outline-danger">Logout</a>
                </div>

                <!-- Stats Cards -->
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Pesanan</h5>
                                <h2><?= $stats['total_orders'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Pending</h5>
                                <h2><?= $stats['pending_orders'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Produk</h5>
                                <h2><?= $stats['total_products'] ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Penjualan</h5>
                                <h2>Rp <?= number_format($stats['total_sales'], 0, ',', '.') ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header">
                                <h5>Pesanan Terbaru</h5>
                            </div>
                            <div class="card-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Customer</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sql = "SELECT o.*, c.name as customer_name 
                                                FROM orders o 
                                                LEFT JOIN customers c ON o.customer_id = c.id 
                                                ORDER BY o.created_at DESC LIMIT 10";
                                        $result = $conn->query($sql);
                                        while($row = $result->fetch_assoc()):
                                        ?>
                                        <tr>
                                            <td>#<?= $row['id'] ?></td>
                                            <td><?= $row['customer_name'] ?></td>
                                            <td>Rp <?= number_format($row['total_amount'], 0, ',', '.') ?></td>
                                            <td>
                                                <span class="badge bg-<?= 
                                                    $row['status'] == 'completed' ? 'success' : 
                                                    ($row['status'] == 'pending' ? 'warning' : 'secondary')
                                                ?>">
                                                    <?= ucfirst($row['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('d/m/Y', strtotime($row['created_at'])) ?></td>
                                            <td>
                                                <a href="orders/detail.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Detail</a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
