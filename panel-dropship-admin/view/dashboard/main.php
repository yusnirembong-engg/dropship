<?php
// Dashboard View
require_once __DIR__ . '/../../includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary">Hari Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Minggu Ini</button>
                <button type="button" class="btn btn-sm btn-outline-secondary">Bulan Ini</button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row" id="stats-cards">
        <!-- Stats will be loaded by JavaScript -->
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Pesanan</h6>
                            <h2 id="total-orders">0</h2>
                        </div>
                        <i class="bi bi-cart fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Pending</h6>
                            <h2 id="pending-orders">0</h2>
                        </div>
                        <i class="bi bi-clock fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Produk</h6>
                            <h2 id="total-products">0</h2>
                        </div>
                        <i class="bi bi-box fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Penjualan</h6>
                            <h2 id="total-sales">Rp 0</h2>
                        </div>
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Tables -->
    <div class="row">
        <!-- Recent Orders -->
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5>Pesanan Terbaru</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="recent-orders">
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
                                <!-- Data will be loaded by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Top Products -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5>Produk Terlaris</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush" id="top-products">
                        <!-- Data will be loaded by JavaScript -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Load dashboard data
document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    
    // Auto-refresh every 60 seconds
    setInterval(loadDashboardData, 60000);
});

function loadDashboardData() {
    fetch('/panel-dropship-admin/api/dashboard.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stats cards
                document.getElementById('total-orders').textContent = data.stats.total_orders;
                document.getElementById('pending-orders').textContent = data.stats.pending_orders;
                document.getElementById('total-products').textContent = data.stats.total_products;
                document.getElementById('total-sales').textContent = formatRupiah(data.stats.total_sales);
                
                // Update recent orders table
                const ordersTable = document.querySelector('#recent-orders tbody');
                ordersTable.innerHTML = '';
                data.recent_orders.forEach(order => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>#${order.id}</td>
                        <td>${order.customer_name || 'Guest'}</td>
                        <td>${formatRupiah(order.total_amount)}</td>
                        <td><span class="badge bg-${getStatusColor(order.status)}">${order.status}</span></td>
                        <td>${formatDate(order.created_at)}</td>
                        <td>
                            <a href="/panel-dropship-admin/orders/view?id=${order.id}" class="btn btn-sm btn-info">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    `;
                    ordersTable.appendChild(row);
                });
                
                // Update top products
                const topProductsList = document.getElementById('top-products');
                topProductsList.innerHTML = '';
                data.top_products.forEach(product => {
                    const item = document.createElement('li');
                    item.className = 'list-group-item d-flex justify-content-between align-items-center';
                    item.innerHTML = `
                        ${product.name}
                        <span class="badge bg-primary rounded-pill">${product.total_sold} terjual</span>
                    `;
                    topProductsList.appendChild(item);
                });
            }
        })
        .catch(error => {
            console.error('Error loading dashboard data:', error);
        });
}

function formatRupiah(amount) {
    return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID');
}

function getStatusColor(status) {
    const colors = {
        'pending': 'warning',
        'processing': 'info',
        'shipped': 'primary',
        'completed': 'success',
        'cancelled': 'danger'
    };
    return colors[status] || 'secondary';
}
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
