<?php include_once '../../includes/sidebar_admin.php';
$id = (int)$_GET['id'];
mysqli_query($koneksi, "DELETE FROM pelatih WHERE id_pelatih=$id");
header('Location: index.php');
