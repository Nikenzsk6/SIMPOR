-- =====================================================================
--  SIMPOR — DATA CONTOH (SEED) UNTUK MENGUJI DASHBOARD
-- =====================================================================
--  File OPSIONAL. Hanya untuk mengisi grafik di dashboard supaya
--  kelihatan saat demo / presentasi.
--
--  CARA PAKAI
--    1. Backup dulu:
--       mysqldump -u AMP -p simpor > backup-sebelum-seed.sql
--    2. Jalankan file ini:
--       - lewat phpMyAdmin: tab "Import" -> pilih file ini -> Go
--       - lewat terminal:
--           mysql -u AMP -p simpor < seed-contoh.sql
--    3. Buka dashboard -> grafik tren / donut / bar jadi terisi.
--    4. Sudah selesai presentasi? Hapus dengan:
--       docs/CARA-HAPUS-DATA-CONTOH.sql
--
--  TIDAK ADA kode aplikasi yang diubah. Murni tambahan baris data.
--  Data lama kamu tidak dihapus atau diubah.
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Ingat ID terakhir SEBELUM menambah data.
SET @a_akhir := (SELECT COALESCE(MAX(id_anggota), 0) FROM anggota);
SET @u_akhir := (SELECT COALESCE(MAX(id_pendaftaran), 0) FROM pendaftaran_ujian);

-- ---------------------------------------------------------------------
--  1. ANGGOTA  — 28 anggota contoh
-- ---------------------------------------------------------------------
INSERT INTO anggota (no_kta, nama_anggota, tingkatan_sabuk, kontak, alamat, status) VALUES
('KTA-C001', 'Ahmad Fauzi',        'Gladi Gatra', '0812-1001-0001', 'Jl. Melati No.1',   'warga'),
('KTA-C002', 'Bunga Lestari',      'Gladi Gatra', '0812-1001-0002', 'Jl. Kenanga No.2',  'warga'),
('KTA-C003', 'Candra Wijaya',      'Gladi Taruna','0812-1001-0003', 'Jl. Mawar No.3',    'warga'),
('KTA-C004', 'Dewi Anggraini',     'Gladi Taruna','0812-1001-0004', 'Jl. Melur No.4',    'warga'),
('KTA-C005', 'Eko Prasetyo',       'Gladi Satya', '0812-1001-0005', 'Jl. Anggrek No.5',  'warga'),
('KTA-C006', 'Fitri Handayani',    'Gladi Satya', '0812-1001-0006', 'Jl. Dahlia No.6',   'warga'),
('KTA-C007', 'Gilang Ramadhan',    'Gladi Satya', '0812-1001-0007', 'Jl. Teratai No.7',  'warga'),
('KTA-C008', 'Hana Kusuma',        'Wira Tama',   '0812-1001-0008', 'Jl. Flamboyan No.8','warga'),
('KTA-C009', 'Indra Saputra',      'Wira Tama',   '0812-1001-0009', 'Jl. Kamboja No.9',  'warga'),
('KTA-C010', 'Jihan Nuraini',      'Wira Tama',   '0812-1001-0010', 'Jl. Bougain No.10', 'warga'),
('KTA-C011', 'Krisna Bayu',        'Wira Tama',   '0812-1001-0011', 'Jl. Kamboja No.11', 'warga'),
('KTA-C012', 'Lina Marlina',       'Wira Tama',   '0812-1001-0012', 'Jl. Anthurium No.12','warga'),
('KTA-C013', 'Miko Ramadhan',       'Wira Tama',   '0812-1001-0013', 'Jl. Mawar No.13',   'warga'),
('KTA-C014', 'Nadia Puspita',      'Daruna',      '0812-1001-0014', 'Jl. Melati No.14',  'non-warga'),
('KTA-C015', 'Oscar Ferdinand',    'Daruna',      '0812-1001-0015', 'Jl. Kenanga No.15', 'non-warga'),
('KTA-C016', 'Putri Ayu',          'Daruna',      '0812-1001-0016', 'Jl. Mawar No.16',   'non-warga'),
('KTA-C017', 'Rizky Aditya',       'Daruna',      '0812-1001-0017', 'Jl. Melur No.17',   'non-warga'),
('KTA-C018', 'Sari Wulandari',     'Daruna',      '0812-1001-0018', 'Jl. Anggrek No.18', 'non-warga'),
('KTA-C019', 'Taufik Hidayat',     'Daruna',      '0812-1001-0019', 'Jl. Dahlia No.19',  'non-warga'),
('KTA-C020', 'Umar Maulana',       'Pradana',     '0812-1001-0020', 'Jl. Teratai No.20', 'non-warga'),
('KTA-C021', 'Vina Oktaviani',     'Pradana',     '0812-1001-0021', 'Jl. Flamboyan No.21','non-warga'),
('KTA-C022', 'Wawan Setiawan',     'Pradana',     '0812-1001-0022', 'Jl. Kamboja No.22', 'non-warga'),
('KTA-C023', 'Yuni Shara',         'Pradana',     '0812-1001-0023', 'Jl. Bougain No.23', 'non-warga'),
('KTA-C024', 'Zaki Pratama',       'Garuda',      '0812-1001-0024', 'Jl. Anthurium No.24','non-warga'),
('KTA-C025', 'Andika Wijaya',      'Garuda',      '0812-1001-0025', 'Jl. Mawar No.25',   'non-warga'),
('KTA-C026', 'Bella Safitri',      'Garuda',      '0812-1001-0026', 'Jl. Melati No.26',  'non-warga'),
('KTA-C027', 'Cahyo Nugroho',      'Garuda',      '0812-1001-0027', 'Jl. Kenanga No.27', 'non-warga'),
('KTA-C028', 'Dinda Larasati',     'Garuda',      '0812-1001-0028', 'Jl. Mawar No.28',   'non-warga');

-- ---------------------------------------------------------------------
--  2. PELATIH — 9 pelatih contoh
-- ---------------------------------------------------------------------
INSERT INTO pelatih (no_kta, nama_pelatih, tingkatan_sabuk, kontak, alamat, path_sertifikat) VALUES
('KTP-C001', 'Gunawan Saputra', 'Wira Tama', '0813-2001-0001', 'Jl. Pahlawan No.1',       ''),
('KTP-C002', 'Hesti Rahmawati', 'Wira Tama', '0813-2001-0002', 'Jl. Merdeka No.2',        ''),
('KTP-C003', 'Iwan Setiawan',   'Wira Tama', '0813-2001-0003', 'Jl. Kemdikbud No.3',      ''),
('KTP-C004', 'Joko Santoso',    'Daruna',    '0813-2001-0004', 'Jl. Sudirman No.4',       ''),
('KTP-C005', 'Kartika Sari',    'Daruna',    '0813-2001-0005', 'Jl. Diponegoro No.5',    ''),
('KTP-C006', 'Lukman Hakim',    'Daruna',    '0813-2001-0006', 'Jl. Gajah Mada No.6',     ''),
('KTP-C007', 'Martha Sari',     'Pradana',   '0813-2001-0007', 'Jl. Ahmad Yani No.7',     ''),
('KTP-C008', 'Nanda Putra',     'Pradana',   '0813-2001-0008', 'Jl. Cut Nyak Dien No.8', ''),
('KTP-C009', 'Oktavia Rahma',   'Garuda',    '0813-2001-0009', 'Jl. Rajawali No.9',       '');

-- ---------------------------------------------------------------------
--  3. PENDAFTARAN UJIAN — 12 bulan spre motors
--     Tanggal relatif thd CURDATE() supaya tren selalu "terbaru".
-- ---------------------------------------------------------------------
INSERT INTO pendaftaran_ujian (tingkat_ujian, tgl_buka, tgl_tutup, tgl_ujian, status) VALUES
('Gladi Gatra',  DATE_SUB(CURDATE(), INTERVAL 11 MONTH), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), INTERVAL 21 DAY), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), INTERVAL 21 DAY), 'ditutup'),
('Gladi Gatra',  DATE_SUB(CURDATE(), INTERVAL 10 MONTH), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 10 MONTH), INTERVAL 21 DAY), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 10 MONTH), INTERVAL 21 DAY), 'ditutup'),
('Gladi Taruna', DATE_SUB(CURDATE(), INTERVAL 9 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 9 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 9 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Gladi Taruna', DATE_SUB(CURDATE(), INTERVAL 8 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 8 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 8 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Gladi Satya',  DATE_SUB(CURDATE(), INTERVAL 7 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 7 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 7 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Gladi Satya',  DATE_SUB(CURDATE(), INTERVAL 6 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Wira Tama',    DATE_SUB(CURDATE(), INTERVAL 5 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Wira Tama',    DATE_SUB(CURDATE(), INTERVAL 4 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Daruna',       DATE_SUB(CURDATE(), INTERVAL 3 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Pradana',      DATE_SUB(CURDATE(), INTERVAL 2 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Garuda',       DATE_SUB(CURDATE(), INTERVAL 70 DAY),   DATE_ADD(CURDATE(), INTERVAL 14 DAY),                          DATE_SUB(CURDATE(), INTERVAL 10 DAY),                          'ditutup'),
('Pradana',      DATE_SUB(CURDATE(), INTERVAL 60 DAY),   DATE_ADD(CURDATE(), INTERVAL 10 DAY),                          DATE_SUB(CURDATE(), INTERVAL 3 DAY),                           'dibuka');

-- ID 4 ujian terbaru yang dipakai untuk peserta
SET @u1 := @u_akhir + 9;
SET @u2 := @u_akhir + 10;
SET @u3 := @u_akhir + 11;
SET @u4 := @u_akhir + 12;

-- ---------------------------------------------------------------------
--  4. DETAIL PENDAFTARAN — peserta didaftarkan ke 4 ujian terbaru
-- ---------------------------------------------------------------------
INSERT INTO detail_pendaftaran (id_pendaftaran, id_anggota, status_pembayaran) VALUES
(@u1, @a_akhir + 13,'lunas'), (@u1, @a_akhir + 14,'lunas'), (@u1, @a_akhir + 15,'lunas'),
(@u1, @a_akhir + 16,'lunas'), (@u1, @a_akhir + 17,'lunas'), (@u1, @a_akhir + 18,'lunas'),
(@u1, @a_akhir + 19,'belum'),
(@u2, @a_akhir + 20,'lunas'), (@u2, @a_akhir + 21,'lunas'), (@u2, @a_akhir + 22,'lunas'),
(@u2, @a_akhir + 23,'lunas'), (@u2, @a_akhir + 24,'lunas'), (@u2, @a_akhir + 25,'belum'),
(@u3, @a_akhir + 26,'lunas'), (@u3, @a_akhir + 27,'lunas'), (@u3, @a_akhir + 28,'lunas'),
(@u3, @a_akhir + 29,'belum'),
(@u4, @a_akhir + 30,'lunas'), (@u4, @a_akhir + 31,'lunas'), (@u4, @a_akhir + 32,'lunas'),
(@u4, @a_akhir + 33,'lunas');

-- ---------------------------------------------------------------------
--  5. HASIL UJIAN — nilai 65-95, 72% lulus
-- ---------------------------------------------------------------------
INSERT INTO hasil_ujian (id_detail, rata_rata_nilai, status_kelulusan, tgl_penetapan)
SELECT dp.id_detail,
       ROUND(65 + RAND() * 30, 1),
       IF(RAND() < 0.72, 'LULUS', 'TIDAK LULUS'),
       DATE_ADD(CURDATE(), INTERVAL 5 DAY)
FROM detail_pendaftaran dp
WHERE dp.id_pendaftaran IN (@u1, @u2, @u3, @u4)
  AND NOT EXISTS (SELECT 1 FROM hasil_ujian h WHERE h.id_detail = dp.id_detail);

-- ---------------------------------------------------------------------
--  6. PENGESAHAN — status beragam supaya donut di dashboard pusat terisi
-- ---------------------------------------------------------------------
INSERT INTO pengesahan (id_hasil, file_pdf, status, tgl_kirim, tgl_diterima, tgl_disetujui, id_user_pusat, catatan)
SELECT h.id_hasil,
       'contoh.pdf',
       CASE MOD(h.id_hasil, 4)
           WHEN 0 THEN 'dikirim'
           WHEN 1 THEN 'disetujui'
           WHEN 2 THEN 'disetujui'
           ELSE        'ditolak'
       END,
       -- tgl_kirim disebar ±6 bulan supaya tren di dashboard pusat terisi
       DATE_SUB(NOW(), INTERVAL (MOD(h.id_hasil, 9) * 22 + 2) DAY),
       IF(MOD(h.id_hasil,4) IN (1,2,3), DATE_ADD(DATE_SUB(NOW(), INTERVAL (MOD(h.id_hasil, 9) * 22 + 2) DAY), INTERVAL 1 DAY), NULL),
       IF(MOD(h.id_hasil,4) IN (1,2),   DATE_ADD(DATE_SUB(NOW(), INTERVAL (MOD(h.id_hasil, 9) * 22 + 2) DAY), INTERVAL 2 DAY), NULL),
       (SELECT id_user FROM user WHERE role = 'pusat' ORDER BY id_user LIMIT 1),
       IF(MOD(h.id_hasil,4) = 3, 'Nilai belum mencapai batas kelulusan', NULL)
FROM hasil_ujian h
WHERE h.id_detail IN (SELECT id_detail FROM detail_pendaftaran WHERE id_pendaftaran IN (@u1,@u2,@u3,@u4))
  AND NOT EXISTS (SELECT 1 FROM pengesahan p WHERE p.id_hasil = h.id_hasil);

-- ---------------------------------------------------------------------
--  7. PENILAIAN — supaya menu Penilaian & Laporan punya isi
-- ---------------------------------------------------------------------
INSERT INTO penilaian (id_detail, id_pelatih, file_penilaian, nilai_teori, nilai_sejarah, nilai_jurus, nilai_mental, nilai_agama, nilai_akhir)
SELECT dp.id_detail,
       1 + (MOD(dp.id_detail, 3)),
       'contoh.xlsx',
       ROUND(60 + RAND() * 38, 0),
       ROUND(60 + RAND() * 38, 0),
       ROUND(60 + RAND() * 38, 0),
       ROUND(60 + RAND() * 38, 0),
       ROUND(60 + RAND() * 38, 0),
       ROUND(62 + RAND() * 34, 0)
FROM detail_pendaftaran dp
WHERE dp.id_pendaftaran IN (@u1, @u2, @u3, @u4);

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  SELESAI. Refresh dashboard -> tren, donut, dan bar terisi.
--  Untuk membatalkan: docs/CARA-HAPUS-DATA-CONTOH.sql
-- =====================================================================