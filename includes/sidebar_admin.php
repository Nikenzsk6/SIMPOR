<?php
include_once dirname(__DIR__) . '/includes/session.php';
include_once dirname(__DIR__) . '/includes/funtion.php';
if ($role != 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}
$menu_path = $_SERVER['PHP_SELF'];
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - SIM Porsigal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/theme.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark navbar-merah shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>admin/dashboard.php">
                <img src="<?= BASE_URL ?>assets/img/logo.png" alt="SIM PORSIGAL" height="30" class="me-2"> SIM PORSIGAL - CABANG
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle"></i> <?= htmlspecialchars($data_user['nama_user']) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 p-0 sidebar">
                <ul class="nav flex-column pt-2">
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, 'admin/dashboard.php') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/pelatih/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/pelatih/index.php">
                            <i class="bi bi-people"></i> Pelatih
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/anggota/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/anggota/index.php">
                            <i class="bi bi-person-badge"></i> Anggota
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/ujian/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/ujian/index.php">
                            <i class="bi bi-journal-check"></i> Ujian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/penilaian/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/penilaian/index.php">
                            <i class="bi bi-clipboard-data"></i> Penilaian
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/pengesahan/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/pengesahan/index.php">
                            <i class="bi bi-send-check"></i> Pengesahan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/laporan/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/laporan/index.php">
                            <i class="bi bi-printer"></i> Laporan
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/profil/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/profil/index.php">
                            <i class="bi bi-building"></i> Profil Cabang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= strpos($menu_path, '/admin/user/') !== false ? 'active' : '' ?>" href="<?= BASE_URL ?>admin/user/index.php">
                            <i class="bi bi-people"></i> Kelola User
                        </a>
                    </li>
                </ul>
            </div>
            <div class="col-md-10 p-4">
                <?= flash() ?>