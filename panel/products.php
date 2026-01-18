<?php
require_once __DIR__ . '/includes/header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Manajemen Produk</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportProducts()">
                    <i class="bi bi-download"></i> Export
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="importProducts()">
                    <i class="bi bi-upload"></i> Import
                </button>
            </div>
            <a href="#" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle"></i> Tambah Produk
            </a>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <input type="text" class="form-control" id="searchProduct" placeholder="Cari produk...">
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterCategory">
                                <option value="">Semua Kategori</option>
                                <option value="1">Elektronik</option>
                                <option value="2">Fashion</option>
                                <option value="3">Rumah Tangga</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="filterStatus">
                                <option value="">Semua Status</option>
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="filterProducts()">
                                <i class="bi bi-funnel"></i> Filter
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Products Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="productsTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Gambar</th>
                                    <th>Nama Produk</th>
                                    <th>Kategori</th>
                                    <th>Harga</th>
                                    <th>Stok</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Sample products data
                                $products = [
                                    ['id' => 1, 'name' => 'Smartphone Android', 'category' => 'Elektronik', 'price' => 2500000, 'stock' => 50, 'status' => 'active', 'image' => 'product1.jpg'],
                                    ['id' => 2, 'name' => 'Laptop Gaming', 'category' => 'Elektronik', 'price' => 15000000, 'stock' => 20, 'status' => 'active', 'image' => 'product2.jpg'],
                                    ['id' => 3, 'name' => 'Kaos Polo Premium', 'category' => 'Fashion', 'price' => 150000, 'stock' => 100, 'status' => 'active', 'image' => 'product3.jpg'],
                                    ['id' => 4, 'name' => 'Blender Multifungsi', 'category' => 'Rumah Tangga', 'price' => 350000, 'stock' => 30, 'status' => 'active', 'image' => 'product4.jpg'],
                                    ['id' => 5, 'name' => 'Headset Wireless', 'category' => 'Elektronik', 'price' => 500000, 'stock' => 0, 'status' => 'inactive', 'image' => 'product5.jpg'],
                                ];
                                
                                foreach ($products as $product):
                                ?>
                                <tr>
                                    <td>#<?php echo $product['id']; ?></td>
                                    <td>
                                        <img src="/website/assets/images/products/<?php echo $product['image']; ?>" 
                                             alt="<?php echo htmlspecialchars($product['name']); ?>" 
                                             width="50" height="50" style="object-fit: cover;">
                                    </td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>
                                        <span class="badge bg-info"><?php echo htmlspecialchars($product['category']); ?></span>
                                    </td>
                                    <td>Rp <?php echo number_format($product['price'], 0, ',', '.'); ?></td>
                                    <td>
                                        <?php if ($product['stock'] > 0): ?>
                                        <span class="badge bg-success"><?php echo $product['stock']; ?> pcs</span>
                                        <?php else: ?>
                                        <span class="badge bg-danger">Habis</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($product['status'] === 'active'): ?>
                                        <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary">Nonaktif</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <button class="btn btn-info" onclick="viewProduct(<?php echo $product['id']; ?>)">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <button class="btn btn-warning" onclick="editProduct(<?php echo $product['id']; ?>)">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center mt-3">
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
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductModalLabel">Tambah Produk Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addProductForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productName" class="form-label">Nama Produk *</label>
                            <input type="text" class="form-control" id="productName" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productCategory" class="form-label">Kategori *</label>
                            <select class="form-control" id="productCategory" required>
                                <option value="">Pilih Kategori</option>
                                <option value="1">Elektronik</option>
                                <option value="2">Fashion</option>
                                <option value="3">Rumah Tangga</option>
                                <option value="4">Kecantikan</option>
                                <option value="5">Olahraga</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productPrice" class="form-label">Harga Jual *</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="productPrice" required>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productCost" class="form-label">Harga Modal</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" class="form-control" id="productCost">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="productStock" class="form-label">Stok *</label>
                            <input type="number" class="form-control" id="productStock" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="productStatus" class="form-label">Status</label>
                            <select class="form-control" id="productStatus">
                                <option value="active">Aktif</option>
                                <option value="inactive">Nonaktif</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="productDescription" class="form-label">Deskripsi</label>
                        <textarea class="form-control" id="productDescription" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="productImage" class="form-label">Gambar Produk</label>
                        <input type="file" class="form-control" id="productImage" accept="image/*">
                        <div class="form-text">Ukuran maksimal 2MB. Format: JPG, PNG, GIF</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary" onclick="saveProduct()">Simpan Produk</button>
            </div>
        </div>
    </div>
</div>

<script>
function filterProducts() {
    const search = document.getElementById('searchProduct').value;
    const category = document.getElementById('filterCategory').value;
    const status = document.getElementById('filterStatus').value;
    
    // In a real application, this would make an AJAX request
    console.log('Filtering products:', { search, category, status });
    alert('Filtering products... (This is a demo)');
}

function viewProduct(id) {
    window.location.href = '/panel/product/' + id;
}

function editProduct(id) {
    alert('Edit product ' + id + ' (This is a demo)');
}

function deleteProduct(id) {
    if (confirm('Apakah Anda yakin ingin menghapus produk ini?')) {
        alert('Product ' + id + ' deleted (This is a demo)');
    }
}

function saveProduct() {
    const productData = {
        name: document.getElementById('productName').value,
        category: document.getElementById('productCategory').value,
        price: document.getElementById('productPrice').value,
        cost: document.getElementById('productCost').value,
        stock: document.getElementById('productStock').value,
        status: document.getElementById('productStatus').value,
        description: document.getElementById('productDescription').value
    };
    
    console.log('Saving product:', productData);
    alert('Product saved successfully! (This is a demo)');
    
    // Close modal
    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
    modal.hide();
    
    // Reset form
    document.getElementById('addProductForm').reset();
}

function exportProducts() {
    alert('Exporting products to CSV (This is a demo)');
}

function importProducts() {
    alert('Import products from CSV (This is a demo)');
}
</script>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
