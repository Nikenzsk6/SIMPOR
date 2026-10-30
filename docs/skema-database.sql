-- =====================================================================
--  SIMPOR - SKEMA DATABASE (TANPA DATA)
--  Dibuat otomatis dari database lokal. Aman untuk repo publik:
--  hanya struktur tabel, tidak ada data pengguna.
-- =====================================================================


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `anggota`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `anggota` (
  `id_anggota` int NOT NULL AUTO_INCREMENT,
  `no_kta` varchar(30) NOT NULL,
  `nama_anggota` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `tingkatan_sabuk` varchar(50) NOT NULL,
  `kontak` varchar(20) DEFAULT NULL,
  `alamat` text,
  `status` enum('warga','non-warga') DEFAULT 'warga',
  PRIMARY KEY (`id_anggota`),
  UNIQUE KEY `no_kta` (`no_kta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `detail_pendaftaran`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `detail_pendaftaran` (
  `id_detail` int NOT NULL AUTO_INCREMENT,
  `id_pendaftaran` int NOT NULL,
  `id_anggota` int NOT NULL,
  `status_pembayaran` enum('lunas','belum') DEFAULT 'belum',
  PRIMARY KEY (`id_detail`),
  KEY `id_pendaftaran` (`id_pendaftaran`),
  KEY `id_anggota` (`id_anggota`),
  CONSTRAINT `detail_pendaftaran_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran_ujian` (`id_pendaftaran`) ON DELETE CASCADE,
  CONSTRAINT `detail_pendaftaran_ibfk_2` FOREIGN KEY (`id_anggota`) REFERENCES `anggota` (`id_anggota`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `hasil_ujian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `hasil_ujian` (
  `id_hasil` int NOT NULL AUTO_INCREMENT,
  `id_detail` int NOT NULL,
  `rata_rata_nilai` decimal(5,2) DEFAULT NULL,
  `status_kelulusan` enum('LULUS','TIDAK LULUS') NOT NULL,
  `tgl_penetapan` date NOT NULL,
  PRIMARY KEY (`id_hasil`),
  UNIQUE KEY `id_detail` (`id_detail`),
  CONSTRAINT `hasil_ujian_ibfk_1` FOREIGN KEY (`id_detail`) REFERENCES `detail_pendaftaran` (`id_detail`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `log_aktivitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `log_aktivitas` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pelatih`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pelatih` (
  `id_pelatih` int NOT NULL AUTO_INCREMENT,
  `no_kta` varchar(30) NOT NULL,
  `nama_pelatih` varchar(100) NOT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') DEFAULT NULL,
  `tingkatan_sabuk` varchar(50) NOT NULL,
  `kontak` varchar(20) DEFAULT NULL,
  `alamat` text,
  `path_sertifikat` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_pelatih`),
  UNIQUE KEY `no_kta` (`no_kta`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pendaftaran_ujian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pendaftaran_ujian` (
  `id_pendaftaran` int NOT NULL AUTO_INCREMENT,
  `nama_ujian` varchar(100) DEFAULT NULL,
  `tingkat_ujian` varchar(50) NOT NULL,
  `tgl_buka` date NOT NULL,
  `tgl_tutup` date NOT NULL,
  `tgl_ujian` date NOT NULL,
  `status` enum('dibuka','ditutup') DEFAULT 'dibuka',
  PRIMARY KEY (`id_pendaftaran`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `pengesahan`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pengesahan` (
  `id_pengesahan` int NOT NULL AUTO_INCREMENT,
  `id_hasil` int NOT NULL,
  `file_pdf` varchar(255) DEFAULT NULL,
  `status` enum('dikirim','diterima_pusat','disetujui','ditolak') DEFAULT 'dikirim',
  `tgl_kirim` datetime DEFAULT CURRENT_TIMESTAMP,
  `tgl_diterima` datetime DEFAULT NULL,
  `tgl_disetujui` datetime DEFAULT NULL,
  `id_user_pusat` int DEFAULT NULL,
  `catatan` text,
  PRIMARY KEY (`id_pengesahan`),
  UNIQUE KEY `id_hasil` (`id_hasil`),
  KEY `id_user_pusat` (`id_user_pusat`),
  CONSTRAINT `pengesahan_ibfk_1` FOREIGN KEY (`id_hasil`) REFERENCES `hasil_ujian` (`id_hasil`) ON DELETE CASCADE,
  CONSTRAINT `pengesahan_ibfk_2` FOREIGN KEY (`id_user_pusat`) REFERENCES `user` (`id_user`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penguji_ujian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penguji_ujian` (
  `id_pendaftaran` int NOT NULL,
  `id_pelatih` int NOT NULL,
  PRIMARY KEY (`id_pendaftaran`,`id_pelatih`),
  KEY `id_pelatih` (`id_pelatih`),
  CONSTRAINT `penguji_ujian_ibfk_1` FOREIGN KEY (`id_pendaftaran`) REFERENCES `pendaftaran_ujian` (`id_pendaftaran`) ON DELETE CASCADE,
  CONSTRAINT `penguji_ujian_ibfk_2` FOREIGN KEY (`id_pelatih`) REFERENCES `pelatih` (`id_pelatih`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `penilaian`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `penilaian` (
  `id_nilai` int NOT NULL AUTO_INCREMENT,
  `id_detail` int NOT NULL,
  `id_pelatih` int NOT NULL,
  `file_penilaian` varchar(255) DEFAULT NULL,
  `nilai_teori` decimal(5,2) DEFAULT NULL,
  `nilai_sejarah` decimal(5,2) DEFAULT NULL,
  `nilai_jurus` decimal(5,2) DEFAULT NULL,
  `nilai_mental` decimal(5,2) NOT NULL,
  `nilai_agama` decimal(5,2) NOT NULL,
  `nilai_akhir` decimal(5,2) DEFAULT NULL,
  PRIMARY KEY (`id_nilai`),
  KEY `id_detail` (`id_detail`),
  KEY `id_pelatih` (`id_pelatih`),
  CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_detail`) REFERENCES `detail_pendaftaran` (`id_detail`) ON DELETE CASCADE,
  CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_pelatih`) REFERENCES `pelatih` (`id_pelatih`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `profil`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `profil` (
  `id_profil` int NOT NULL AUTO_INCREMENT,
  `tipe` enum('cabang','pusat') NOT NULL,
  `nama_organisasi` varchar(150) NOT NULL,
  `singkatan` varchar(20) DEFAULT NULL,
  `alamat` text,
  `kontak` varchar(20) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `deskripsi` text,
  PRIMARY KEY (`id_profil`),
  UNIQUE KEY `tipe` (`tipe`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `id_user` int NOT NULL AUTO_INCREMENT,
  `nama_user` varchar(100) NOT NULL,
  `kontak` varchar(20) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pusat') NOT NULL DEFAULT 'admin',
  `status` enum('aktif','tidak_aktif') NOT NULL DEFAULT 'aktif',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
