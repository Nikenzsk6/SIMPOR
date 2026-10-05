<?php
include_once '../../includes/session.php';
$id = (int)$_GET['id'];
mysqli_query($koneksi, "DELETE FROM anggota WHERE id_anggota=$id");
header('Location: index.php');
