<?php
include_once 'includes/config.php';

// --- Pengamanan cookie sesi (harus diset sebelum session_start) ---
ini_set('session.use_strict_mode', '1');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => '/',
    'httponly' => true,   // tidak bisa dibaca JavaScript
    'samesite' => 'Lax',  // tidak ikut terkirim dari situs lain
    'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
]);
session_start();

// Jangan simpan halaman login di cache (mencegah tombol Back setelah logout)
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// --- Batas percobaan login (dasar, mencegah tebak password beruntun) ---
const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCK_SECONDS = 60;

if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['login_lock_until'])) {
    $_SESSION['login_lock_until'] = 0;
}

if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $plain    = $_POST['password'] ?? '';

    // --- Validasi format sesuai RTM (Login Cabang & Pusat) ---
    if ($_SESSION['login_lock_until'] > time()) {
        $sisa  = $_SESSION['login_lock_until'] - time();
        $error = 'Terlalu banyak percobaan. Coba lagi dalam ' . $sisa . ' detik.';
    } elseif ($username === '') {
        $error = 'Username wajib diisi!';
    } elseif (preg_match('/[^A-Za-z0-9]/', $username)) {
        $error = 'Username tidak boleh menggunakan karakter khusus atau spasi!';
    } elseif (ctype_digit($username)) {
        $error = 'Username tidak boleh hanya berupa angka!';
    } elseif ($plain === '') {
        $error = 'Password wajib diisi!';
    } elseif (strlen($plain) < 8) {
        $error = 'Password minimal 8 karakter!';
    } else {
        // Prepared statement: username tidak pernah disisipkan langsung ke query
        $stmt = mysqli_prepare($koneksi, "SELECT id_user, role, password, status FROM user WHERE username = ? LIMIT 1");
        mysqli_stmt_bind_param($stmt, 's', $username);
        mysqli_stmt_execute($stmt);
        $hasil = mysqli_stmt_get_result($stmt);
        $d     = mysqli_fetch_assoc($hasil);
        mysqli_stmt_close($stmt);

        $valid = false;
        $perlu_rehash = false;
        if ($d) {
            list($valid, $perlu_rehash) = verify_password($plain, $d['password']);
        }

        if ($valid && ($d['status'] ?? 'aktif') !== 'aktif') {
            $error = 'Akun Anda tidak aktif. Silakan hubungi administrator.';
        } elseif ($valid) {
            // Login berhasil: bersihkan penghitung, ganti ID sesi
            // (mencegah session fixation), lalu simpan identitas.
            unset($_SESSION['login_attempts'], $_SESSION['login_lock_until']);
            session_regenerate_id(true);

            $_SESSION['id_user'] = (int) $d['id_user'];
            $_SESSION['role']    = $d['role'];

            // Hash md5 lama otomatis dinaikkan ke bcrypt saat login berhasil
            if ($perlu_rehash) {
                $hash_baru = hash_password($plain);
                $up = mysqli_prepare($koneksi, "UPDATE user SET password = ? WHERE id_user = ?");
                mysqli_stmt_bind_param($up, 'si', $hash_baru, $d['id_user']);
                mysqli_stmt_execute($up);
                mysqli_stmt_close($up);
            }

            if ($d['role'] == 'admin')
                header('Location: admin/dashboard.php');
            else
                header('Location: pusat/dashboard.php');
            exit;
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= LOGIN_MAX_ATTEMPTS) {
                $_SESSION['login_lock_until'] = time() + LOGIN_LOCK_SECONDS;
                $_SESSION['login_attempts']   = 0;
                $error = 'Terlalu banyak percobaan. Coba lagi dalam ' . LOGIN_LOCK_SECONDS . ' detik.';
            } else {
                $error = 'Username atau password salah!';
            }
            usleep(200000); // perlambat percobaan beruntun
        }
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
    <link rel="stylesheet" href="assets/css/theme.css">
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
