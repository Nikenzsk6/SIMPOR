<?php
include_once '../../includes/sidebar_admin.php';
$id = (int)$_GET['id'];
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user=$id AND role='admin'"));
if ($d) {
    mysqli_query($koneksi, "DELETE FROM user WHERE id_user=$id");
    set_flash('User berhasil dihapus!');
    header('Location: index.php');
    exit;
} else {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}
