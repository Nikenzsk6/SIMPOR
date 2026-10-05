<?php
include_once 'includes/config.php';
session_start();

if ($_POST) {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = md5($_POST['password']);

    $q = mysqli_query($koneksi, "SELECT * FROM user WHERE username='$username' AND password='$password'");
    $d = mysqli_fetch_assoc($q);

    if ($d) {
        $_SESSION['id_user'] = $d['id_user'];
        $_SESSION['role']    = $d['role'];

        if ($d['role'] == 'admin')
            header('Location: admin/dashboard.php');
        else
            header('Location: pusat/dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIM PORSIGAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>
    <div class="login-wrapper">
        <div class="login-card-merah">
            <div class="row g-0">
                <div class="col-md-5 login-left">
                    <img src="assets/img/logo.png" alt="SIM PORSIGAL" class="login-logo">
                    <h1>SIM PORSIGAL</h1>
                    <p class="login-subtitle">Sistem Informasi Manajemen Porsigal (SIMPOR)</p>
                </div>
                <div class="col-md-7 login-right">
                    <div class="login-card-gading">
                        <h3>LOGIN</h3>

                        <?php if (isset($error)): ?>
                            <div class="login-toast" id="loginToast">
                                <i class="bi bi-exclamation-circle-fill"></i>
                                <span><?= $error ?></span>
                            </div>
                        <?php endif; ?>

                        <form method="post" id="loginForm">
                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" name="username" class="form-control" placeholder="Masukkan username" required autofocus>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required id="passwordInput">
                                    <span class="input-group-text" id="togglePassword" style="background:#fff;border:1.5px solid var(--gading-dark);border-left:none;cursor:pointer;color:var(--text-muted)">
                                        <i class="bi bi-eye-slash" id="toggleIcon"></i>
                                    </span>
                                </div>
                            </div>
                            <button type="submit" class="btn-login" id="loginBtn">
                                <i class="bi bi-box-arrow-in-right me-1"></i> LOGIN
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('togglePassword').addEventListener('click', function() {
        const input = document.getElementById('passwordInput');
        const icon = document.getElementById('toggleIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('bi-eye-slash', 'bi-eye');
        } else {
            input.type = 'password';
            icon.classList.replace('bi-eye', 'bi-eye-slash');
        }
    });

    const toast = document.getElementById('loginToast');
    if (toast) {
        setTimeout(() => {
            toast.style.transition = 'opacity 0.35s ease, transform 0.35s ease';
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(30px)';
            setTimeout(() => toast.remove(), 400);
        }, 4500);
    }

    document.getElementById('loginForm').addEventListener('submit', function() {
        const btn = document.getElementById('loginBtn');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> MEMPROSES...';
    });
    </script>
</body>

</html>
