<?php
include_once '../../includes/session.php';
if ($role != 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}
include_once '../../includes/funtion.php';

$id = (int) ($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id"));
if (!$d) {
    set_flash('Data ujian tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

// Relasi detail_pendaftaran, penguji_ujian, penilaian, hasil_ujian, dan
// pengesahan memakai ON DELETE CASCADE sehingga ikut terhapus otomatis.
if (mysqli_query($koneksi, "DELETE FROM pendaftaran_ujian WHERE id_pendaftaran=$id")) {
    catat_log('HAPUS', 'ujian', 'Menghapus ujian: ' . ($d['nama_ujian'] ?: $d['tingkat_ujian']), $id, $d, null);
    set_flash('Data ujian berhasil dihapus!');
} else {
    set_flash('Gagal menghapus data ujian: ' . mysqli_error($koneksi), 'danger');
}
header('Location: index.php');
exit;
