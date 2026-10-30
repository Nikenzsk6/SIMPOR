<?php
include_once '../../includes/session.php';
if ($role != 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}
include_once '../../includes/funtion.php';

$id = (int) ($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_pelatih FROM pelatih WHERE id_pelatih=$id"));
if (!$d) {
    set_flash('Data pelatih tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

mysqli_query($koneksi, "DELETE FROM pelatih WHERE id_pelatih=$id");
set_flash('Data pelatih berhasil dihapus!');
header('Location: index.php');
exit;
