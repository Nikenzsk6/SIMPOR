<?php
ob_start();
session_start();
include_once __DIR__ . '/config.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

$query = mysqli_query($koneksi, "SELECT * FROM user WHERE id_user = $id_user");
$data_user = mysqli_fetch_assoc($query);

if (!$data_user) {
    session_destroy();
    header('Location: ' . BASE_URL . 'login.php');
    exit;
}
