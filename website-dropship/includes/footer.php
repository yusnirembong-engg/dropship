    </main>
    
    <!-- Footer -->
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Dropship Store</h5>
                    <p>Platform dropship terpercaya dengan ribuan produk siap jual.</p>
                    <div class="social-icons">
                        <a href="#" class="text-white me-3"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white me-3"><i class="bi bi-twitter"></i></a>
                        <a href="#" class="text-white"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
                <div class="col-md-2">
                    <h5>Menu</h5>
                    <ul class="list-unstyled">
                        <li><a href="/website-dropship" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="/website-dropship/products" class="text-white-50 text-decoration-none">Produk</a></li>
                        <li><a href="/website-dropship/categories" class="text-white-50 text-decoration-none">Kategori</a></li>
                        <li><a href="/website-dropship/about" class="text-white-50 text-decoration-none">Tentang</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Bantuan</h5>
                    <ul class="list-unstyled">
                        <li><a href="/website-dropship/help" class="text-white-50 text-decoration-none">Cara Berbelanja</a></li>
                        <li><a href="/website-dropship/shipping" class="text-white-50 text-decoration-none">Pengiriman</a></li>
                        <li><a href="/website-dropship/payment" class="text-white-50 text-decoration-none">Pembayaran</a></li>
                        <li><a href="/website-dropship/contact" class="text-white-50 text-decoration-none">Kontak</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Kontak</h5>
                    <ul class="list-unstyled">
                        <li><i class="bi bi-geo-alt"></i> Jl. Contoh No. 123, Jakarta</li>
                        <li><i class="bi bi-telephone"></i> (021) 123-4567</li>
                        <li><i class="bi bi-envelope"></i> info@dropshipstore.com</li>
                        <li><i class="bi bi-clock"></i> Buka 24/7</li>
                    </ul>
                </div>
            </div>
            <hr class="bg-white">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; <?= date('Y') ?> Dropship Store. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <img src="/website-dropship/public/assets/images/payment-methods.png" alt="Payment Methods" height="30">
                </div>
            </div>
        </div>
    </footer>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/website-dropship/public/assets/js/main.js"></script>
    <script>
    // Update cart count
    function updateCartCount() {
        fetch('/website-dropship/api/cart.php?action=count')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCounts = document.querySelectorAll('.cart-count');
                    cartCounts.forEach(count => {
                        count.textContent = data.count;
                    });
                }
            });
    }
    
    // Show toast notification
    function showToast(message, type = 'info') {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        container.appendChild(toast);
        
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
    
    // Format currency
    function formatCurrency(amount) {
        return 'Rp ' + parseInt(amount).toLocaleString('id-ID');
    }
    
    // Initialize when page loads
    document.addEventListener('DOMContentLoaded', function() {
        updateCartCount();
    });
    </script>
</body>
</html>
