<?php
include_once '../../includes/session.php';
if ($role != 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}
include_once '../../includes/funtion.php';

$id = (int) ($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_pendaftaran FROM pendaftaran_ujian WHERE id_pendaftaran=$id"));
if (!$d) {
    set_flash('Data ujian tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

// Relasi detail_pendaftaran, penguji_ujian, penilaian, hasil_ujian, dan
// pengesahan memakai ON DELETE CASCADE sehingga ikut terhapus otomatis.
mysqli_query($koneksi, "DELETE FROM pendaftaran_ujian WHERE id_pendaftaran=$id");
set_flash('Data ujian berhasil dihapus!');
header('Location: index.php');
exit;
