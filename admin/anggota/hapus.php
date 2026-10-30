<?php
include_once '../../includes/session.php';
if ($role != 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}
include_once '../../includes/funtion.php';

$id = (int) ($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM anggota WHERE id_anggota=$id"));
if (!$d) {
    set_flash('Data anggota tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

if (mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota=$id")) {
    catat_log('HAPUS', 'anggota', 'Menghapus anggota: ' . ($d['nama_anggota'] ?? ''), $id, $d, null);
    set_flash('Data anggota berhasil dihapus!');
} else {
    set_flash('Gagal menghapus data anggota: ' . mysqli_error($koneksi), 'danger');
}
header('Location: index.php');
exit;
