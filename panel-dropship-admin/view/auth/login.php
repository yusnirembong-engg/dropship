<?php
// Login View
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: /panel-dropship-admin/dashboard');
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Panel Admin Dropship</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            height: 100vh;
            display: flex;
            align-items: center;
        }
        .login-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 15px 15px 0 0;
            padding: 30px;
            color: white;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card login-card">
                    <div class="login-header">
                        <h2><i class="bi bi-shop"></i> Dropship Panel</h2>
                        <p class="mb-0">Admin Login</p>
                    </div>
                    <div class="card-body p-4">
                        <div id="login-message"></div>
                        <form id="login-form">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                </div>
                            </div>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary" id="login-btn">
                                    <span id="login-text">Login</span>
                                    <span id="login-spinner" class="spinner-border spinner-border-sm d-none" role="status"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="text-center mt-3 text-white">
                    <small>&copy; <?= date('Y') ?> Dropship System</small>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('login-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const btn = document.getElementById('login-btn');
        const spinner = document.getElementById('login-spinner');
        const loginText = document.getElementById('login-text');
        
        // Show loading
        btn.disabled = true;
        spinner.classList.remove('d-none');
        loginText.textContent = 'Memproses...';
        
        // Get form data
        const formData = {
            username: document.getElementById('username').value,
            password: document.getElementById('password').value
        };
        
        // Send login request
        fetch('/panel-dropship-admin/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(formData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showMessage('success', 'Login berhasil! Mengalihkan...');
                
                // Redirect to dashboard after 1 second
                setTimeout(() => {
                    window.location.href = '/panel-dropship-admin/dashboard';
                }, 1000);
            } else {
                // Show error message
                showMessage('danger', data.message || 'Login gagal!');
                
                // Reset button
                btn.disabled = false;
                spinner.classList.add('d-none');
                loginText.textContent = 'Login';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('danger', 'Terjadi kesalahan. Coba lagi.');
            
            // Reset button
            btn.disabled = false;
            spinner.classList.add('d-none');
            loginText.textContent = 'Login';
        });
    });
    
    function showMessage(type, text) {
        const messageDiv = document.getElementById('login-message');
        messageDiv.innerHTML = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${text}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Auto dismiss after 5 seconds
        setTimeout(() => {
            const alert = messageDiv.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                setTimeout(() => alert.remove(), 150);
            }
        }, 5000);
    }
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
