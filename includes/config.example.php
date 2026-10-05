<?php
/**
 * ============================================================
 *  CONTOH KONFIGURASI — SALIN KE config.php
 * ============================================================
 *  File ini AMAN untuk di-commit ke repository.
 *
 *  Cara pakai di komputer baru / hasil clone:
 *    1. Salin file ini  ->  includes/config.php
 *    2. Isi password di bawah sesuai database lokal kamu
 *    3. Selesai
 *
 *  File includes/config.php Asli TIDAK ikut di-commit
 *  (diabaikan oleh .gitignore) supaya password tidak bocor.
 * ============================================================
 */

$host = 'localhost';
$user = 'AMP';
$pass = 'ISI_PASSWORD_DATABASE_KAMU_DISINI';
$db   = 'simpor';

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die('Koneksi database gagal: ' . mysqli_connect_error());
}

require_once __DIR__ . '/funtion.php';

// Folder instalasi di web server Laragon.
// Kalau diakses lewat http://localhost/SIMPOR/ -> isi '/SIMPOR/'
define('BASE_URL', '/SIMPOR/');