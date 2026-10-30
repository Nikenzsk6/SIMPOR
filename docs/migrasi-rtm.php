<?php
/**
 * Migrasi database untuk memenuhi RTM SIMPOR (Kelompok 7).
 * HANYA bisa dijalankan dari terminal:
 *
 *   php docs/migrasi-rtm.php
 *
 * Skrip ini aman dijalankan berulang kali (kolom yang sudah ada dilewati).
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Skrip ini hanya boleh dijalankan dari terminal.');
}

require __DIR__ . '/../includes/config.php';

$kolom_baru = [
    'pelatih' => [
        'tempat_lahir'  => "VARCHAR(100) NULL AFTER nama_pelatih",
        'tanggal_lahir' => "DATE NULL AFTER tempat_lahir",
        'jenis_kelamin' => "ENUM('Laki-laki','Perempuan') NULL AFTER tanggal_lahir",
    ],
    'anggota' => [
        'tempat_lahir'  => "VARCHAR(100) NULL AFTER nama_anggota",
        'tanggal_lahir' => "DATE NULL AFTER tempat_lahir",
    ],
    'user' => [
        'status' => "ENUM('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif' AFTER role",
    ],
    'pendaftaran_ujian' => [
        'nama_ujian' => "VARCHAR(100) NULL AFTER id_pendaftaran",
    ],
    'profil' => [
        'logo'      => "VARCHAR(255) NULL AFTER kontak",
        'deskripsi' => "TEXT NULL AFTER logo",
    ],
];

$berhasil = 0;
$dilewati = 0;

foreach ($kolom_baru as $tabel => $kolom) {
    // Pastikan tabel ada
    $cek_tabel = mysqli_query($koneksi, "SHOW TABLES LIKE '$tabel'");
    if (!$cek_tabel || mysqli_num_rows($cek_tabel) === 0) {
        echo "LEWATI  tabel '$tabel' tidak ditemukan\n";
        continue;
    }

    foreach ($kolom as $nama => $definisi) {
        $q = mysqli_query($koneksi, "SHOW COLUMNS FROM `$tabel` LIKE '$nama'");
        if ($q && mysqli_num_rows($q) > 0) {
            echo "LEWATI  $tabel.$nama (sudah ada)\n";
            $dilewati++;
            continue;
        }

        $sql = "ALTER TABLE `$tabel` ADD COLUMN `$nama` $definisi";
        if (mysqli_query($koneksi, $sql)) {
            echo "OK      $tabel.$nama ditambahkan\n";
            $berhasil++;
        } else {
            echo "GAGAL   $tabel.$nama: " . mysqli_error($koneksi) . "\n";
        }
    }
}

// Pastikan status user lama menjadi aktif
mysqli_query($koneksi, "UPDATE user SET status='aktif' WHERE status IS NULL OR status=''");

// Isi nama_ujian untuk data ujian lama agar kolom baru tidak kosong
mysqli_query($koneksi, "UPDATE pendaftaran_ujian SET nama_ujian = CONCAT('Ujian ', tingkat_ujian) WHERE nama_ujian IS NULL OR nama_ujian = ''");

// Tabel log aktivitas: hanya untuk pelacakan di database, tidak tampil di aplikasi
$cek_log = mysqli_query($koneksi, "SHOW TABLES LIKE 'log_aktivitas'");
if ($cek_log && mysqli_num_rows($cek_log) > 0) {
    echo "LEWATI  tabel log_aktivitas (sudah ada)\n";
    $dilewati++;
} else {
    $sql_log = "CREATE TABLE `log_aktivitas` (
        `id_log` bigint unsigned NOT NULL AUTO_INCREMENT,
        `id_user` int DEFAULT NULL,
        `username` varchar(50) DEFAULT NULL,
        `role` varchar(20) DEFAULT NULL,
        `aksi` varchar(50) NOT NULL,
        `modul` varchar(50) NOT NULL,
        `id_referensi` varchar(50) DEFAULT NULL,
        `deskripsi` text,
        `data_lama` longtext,
        `data_baru` longtext,
        `ip_address` varchar(45) DEFAULT NULL,
        `user_agent` varchar(255) DEFAULT NULL,
        `waktu` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id_log`),
        KEY `idx_log_user` (`id_user`),
        KEY `idx_log_modul` (`modul`),
        KEY `idx_log_aksi` (`aksi`),
        KEY `idx_log_waktu` (`waktu`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci";

    if (mysqli_query($koneksi, $sql_log)) {
        echo "OK      tabel log_aktivitas dibuat\n";
        $berhasil++;
    } else {
        echo "GAGAL   tabel log_aktivitas: " . mysqli_error($koneksi) . "\n";
    }
}

echo "\nSelesai: $berhasil perubahan ditambahkan, $dilewati dilewati.\n";
