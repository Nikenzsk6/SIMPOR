<?php
ob_start();

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

include_once __DIR__ . '/config.php';

// Header keamanan dasar
if (!headers_sent()) {
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('Referrer-Policy: same-origin');
    // Cegah halaman terproteksi tampil dari cache saat tombol Back ditekan
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

if (!isset($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

// Ganti ID sesi berkala (membatasi masa pakai sesi yang dicuri)
if (empty($_SESSION['last_regenerate'])) {
    $_SESSION['last_regenerate'] = time();
} elseif (time() - $_SESSION['last_regenerate'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['last_regenerate'] = time();
}

$id_user = (int) $_SESSION['id_user'];
$role    = $_SESSION['role'];

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user");
$data_user = mysqli_fetch_assoc($query);

if (!$data_user) {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
