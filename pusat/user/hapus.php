<?php
include_once '../../includes/sidebar_pusat.php';
$id = (int) ($_GET['id'] ?? 0);
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user=$id AND role='pusat'"));
if ($d) {
    if (mysqli_query($koneksi, "DELETE FROM user WHERE id_user=$id")) {
        catat_log('HAPUS', 'user', 'Menghapus user pusat: ' . $d['username'], $id, [
            'nama_user' => $d['nama_user'],
            'username'  => $d['username'],
            'kontak'    => $d['kontak'],
            'role'      => $d['role'],
            'status'    => $d['status'] ?? 'aktif',
        ], null);
        set_flash('User berhasil dihapus!');
    } else {
        set_flash('Gagal menghapus user: ' . mysqli_error($koneksi), 'danger');
    }
    header('Location: index.php');
    exit;
}
set_flash('Data tidak ditemukan', 'danger');
header('Location: index.php');
exit;
