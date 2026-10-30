<?php
session_start();

// Jangan simpan halaman terproteksi di cache
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

// Catat logout sebelum sesi dihapus (log hanya di database)
include_once __DIR__ . '/includes/config.php';
if (isset($_SESSION['id_user'])) {
    catat_log('LOGOUT', 'auth', 'Logout dari aplikasi');
}

// Hapus semua data sesi
$_SESSION = [];

// Hapus cookie sesi di browser
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $p['path'],
        $p['domain'],
        $p['secure'],
        $p['httponly']
    );
}

session_destroy();
header('Location: login.php');
exit;
