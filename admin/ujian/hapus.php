<?php
include_once '../../includes/session.php';
$id = (int)$_GET['id'];
mysqli_query($koneksi, "DELETE FROM pendaftaran_ujian WHERE id_pendaftaran=$id");
header('Location: index.php');
