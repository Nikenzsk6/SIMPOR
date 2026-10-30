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
--
--     PENTING: tingkatan_sabuk WAJIB salah satu dari 6 nilai di dropdown
--     admin/anggota/tambah.php & edit.php:
--       Prapta, Kawi, Gladi Gatra, Gladi Taruna, Gladi Satya, Wira Tama
--     Jangan mengarang nilai lain (mis. 'Daruna' atau 'Garuda').
--
--     Status warga/non-warga DIBUTAKAN di sini hanya supaya tidak bergantung
--     auto_status_sabuk() saat bulk insert. Nilai yang sama akan dihasilkan
--     fungsi itu: 'Gladi Gatra'..'Wira Tama' = warga, selain itu non-warga.
-- ---------------------------------------------------------------------
INSERT INTO anggota (no_kta, nama_anggota, tempat_lahir, tanggal_lahir, tingkatan_sabuk, kontak, alamat, status) VALUES
('20250000000001', 'Ahmad Fauzi',        'Bandung', '2000-01-01', 'Gladi Gatra', '081210010001', 'Jl. Melati No.1',   'warga'),
('20250000000002', 'Bunga Lestari',      'Bandung', '2000-01-01', 'Gladi Gatra', '081210010002', 'Jl. Kenanga No.2',  'warga'),
('20250000000003', 'Candra Wijaya',      'Bandung', '2000-01-01', 'Gladi Taruna','081210010003', 'Jl. Mawar No.3',    'warga'),
('20250000000004', 'Dewi Anggraini',     'Bandung', '2000-01-01', 'Gladi Taruna','081210010004', 'Jl. Melur No.4',    'warga'),
('20250000000005', 'Eko Prasetyo',       'Bandung', '2000-01-01', 'Gladi Satya', '081210010005', 'Jl. Anggrek No.5',  'warga'),
('20250000000006', 'Fitri Handayani',    'Bandung', '2000-01-01', 'Gladi Satya', '081210010006', 'Jl. Dahlia No.6',   'warga'),
('20250000000007', 'Gilang Ramadhan',    'Bandung', '2000-01-01', 'Gladi Satya', '081210010007', 'Jl. Teratai No.7',  'warga'),
('20250000000008', 'Hana Kusuma',        'Bandung', '2000-01-01', 'Wira Tama',   '081210010008', 'Jl. Flamboyan No.8','warga'),
('20250000000009', 'Indra Saputra',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010009', 'Jl. Kamboja No.9',  'warga'),
('20250000000010', 'Jihan Nuraini',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010010', 'Jl. Bougain No.10', 'warga'),
('20250000000011', 'Krisna Bayu',        'Bandung', '2000-01-01', 'Wira Tama',   '081210010011', 'Jl. Kamboja No.11', 'warga'),
('20250000000012', 'Lina Marlina',       'Bandung', '2000-01-01', 'Wira Tama',   '081210010012', 'Jl. Anthurium No.12','warga'),
('20250000000013', 'Miko Ramadhan',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010013', 'Jl. Mawar No.13',   'warga'),
('20250000000014', 'Nadia Puspita',      'Bandung', '2000-01-01', 'Prapta',      '081210010014', 'Jl. Melati No.14',  'non-warga'),
('20250000000015', 'Oscar Ferdinand',    'Bandung', '2000-01-01', 'Kawi',        '081210010015', 'Jl. Kenanga No.15', 'non-warga'),
('20250000000016', 'Putri Ayu',          'Bandung', '2000-01-01', 'Gladi Gatra', '081210010016', 'Jl. Mawar No.16',   'warga'),
('20250000000017', 'Rizky Aditya',       'Bandung', '2000-01-01', 'Gladi Taruna','081210010017', 'Jl. Melur No.17',   'warga'),
('20250000000018', 'Sari Wulandari',     'Bandung', '2000-01-01', 'Gladi Satya', '081210010018', 'Jl. Anggrek No.18', 'warga'),
('20250000000019', 'Taufik Hidayat',     'Bandung', '2000-01-01', 'Gladi Satya', '081210010019', 'Jl. Dahlia No.19',  'warga'),
('20250000000020', 'Umar Maulana',       'Bandung', '2000-01-01', 'Gladi Satya', '081210010020', 'Jl. Teratai No.20', 'warga'),
('20250000000021', 'Vina Oktaviani',     'Bandung', '2000-01-01', 'Gladi Satya', '081210010021', 'Jl. Flamboyan No.21','warga'),
('20250000000022', 'Wawan Setiawan',     'Bandung', '2000-01-01', 'Gladi Satya', '081210010022', 'Jl. Kamboja No.22', 'warga'),
('20250000000023', 'Yuni Shara',         'Bandung', '2000-01-01', 'Wira Tama',   '081210010023', 'Jl. Bougain No.23', 'warga'),
('20250000000024', 'Zaki Pratama',       'Bandung', '2000-01-01', 'Wira Tama',   '081210010024', 'Jl. Anthurium No.24','warga'),
('20250000000025', 'Andika Wijaya',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010025', 'Jl. Mawar No.25',   'warga'),
('20250000000026', 'Bella Safitri',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010026', 'Jl. Melati No.26',  'warga'),
('20250000000027', 'Cahyo Nugroho',      'Bandung', '2000-01-01', 'Wira Tama',   '081210010027', 'Jl. Kenanga No.27', 'warga'),
('20250000000028', 'Dinda Larasati',     'Bandung', '2000-01-01', 'Wira Tama',   '081210010028', 'Jl. Mawar No.28',   'warga');

-- ---------------------------------------------------------------------
--  2. PELATIH — 9 pelatih contoh
--     Sama seperti anggota: tingkatan_sabuk harus salah satu dari
--     Prapta, Kawi, Gladi Gatra, Gladi Taruna, Gladi Satya, Wira Tama
-- ---------------------------------------------------------------------
INSERT INTO pelatih (no_kta, nama_pelatih, tempat_lahir, tanggal_lahir, jenis_kelamin, tingkatan_sabuk, kontak, alamat, path_sertifikat) VALUES
('20240000000001', 'Gunawan Saputra', 'Bandung', '1985-01-01', 'Laki-laki', 'Wira Tama',   '081320010001', 'Jl. Pahlawan No.1',       ''),
('20240000000002', 'Hesti Rahmawati', 'Bandung', '1985-01-01', 'Perempuan', 'Wira Tama',   '081320010002', 'Jl. Merdeka No.2',        ''),
('20240000000003', 'Iwan Setiawan',   'Bandung', '1985-01-01', 'Laki-laki', 'Gladi Satya', '081320010003', 'Jl. Kemdikbud No.3',      ''),
('20240000000004', 'Joko Santoso',    'Bandung', '1985-01-01', 'Laki-laki', 'Gladi Satya', '081320010004', 'Jl. Sudirman No.4',       ''),
('20240000000005', 'Kartika Sari',    'Bandung', '1985-01-01', 'Perempuan', 'Gladi Satya', '081320010005', 'Jl. Diponegoro No.5',    ''),
('20240000000006', 'Lukman Hakim',    'Bandung', '1985-01-01', 'Laki-laki', 'Gladi Satya', '081320010006', 'Jl. Gajah Mada No.6',     ''),
('20240000000007', 'Martha Sari',     'Bandung', '1985-01-01', 'Perempuan', 'Gladi Satya', '081320010007', 'Jl. Ahmad Yani No.7',     ''),
('20240000000008', 'Nanda Putra',     'Bandung', '1985-01-01', 'Laki-laki', 'Gladi Satya', '081320010008', 'Jl. Cut Nyak Dien No.8', ''),
('20240000000009', 'Oktavia Rahma',   'Bandung', '1985-01-01', 'Perempuan', 'Gladi Satya', '081320010009', 'Jl. Rajawali No.9',       '');

-- ---------------------------------------------------------------------
--  3. PENDAFTARAN UJIAN — 12 bulan spre motors
--     Tanggal relatif thd CURDATE() supaya tren selalu "terbaru".
--
--     PENTING: tingkat_ujian WAJIB salah satu dari 5 nilai di dropdown
--     admin/ujian/tambah.php. Formatnya "<sabuk asal> ke <sabuk tujuan>",
--     bukan nama sabuk telanjang. Daftar sah:
--       Prapta ke Kawi
--       Kawi ke Gladi Gatra
--       Gladi Gatra ke Gladi Taruna
--       Gladi Taruna ke Gladi Satya
--       Gladi Satya ke Wira Tama
--     Jangan mengarang nilai lain (mis. 'Daruna' atau 'Garuda'):
--     nilai itu tidak akan pernah muncul dari form tambah ujian.
-- ---------------------------------------------------------------------
INSERT INTO pendaftaran_ujian (nama_ujian, tingkat_ujian, tgl_buka, tgl_tutup, tgl_ujian, status) VALUES
('Ujian Kenaikan Sabuk 1',  'Prapta ke Kawi',               DATE_SUB(CURDATE(), INTERVAL 11 MONTH), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), INTERVAL 21 DAY), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 11 MONTH), INTERVAL 21 DAY), 'ditutup'),
('Ujian Kenaikan Sabuk 2',  'Prapta ke Kawi',               DATE_SUB(CURDATE(), INTERVAL 10 MONTH), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 10 MONTH), INTERVAL 21 DAY), DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 10 MONTH), INTERVAL 21 DAY), 'ditutup'),
('Ujian Kenaikan Sabuk 3',  'Kawi ke Gladi Gatra',          DATE_SUB(CURDATE(), INTERVAL 9 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 9 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 9 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 4',  'Kawi ke Gladi Gatra',          DATE_SUB(CURDATE(), INTERVAL 8 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 8 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 8 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 5',  'Gladi Gatra ke Gladi Taruna',  DATE_SUB(CURDATE(), INTERVAL 7 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 7 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 7 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 6',  'Gladi Gatra ke Gladi Taruna',  DATE_SUB(CURDATE(), INTERVAL 6 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 6 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 7',  'Gladi Taruna ke Gladi Satya',  DATE_SUB(CURDATE(), INTERVAL 5 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 5 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 8',  'Gladi Taruna ke Gladi Satya',  DATE_SUB(CURDATE(), INTERVAL 4 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 4 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 9',  'Gladi Satya ke Wira Tama',     DATE_SUB(CURDATE(), INTERVAL 3 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 3 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 10', 'Gladi Satya ke Wira Tama',     DATE_SUB(CURDATE(), INTERVAL 2 MONTH),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), INTERVAL 21 DAY),  DATE_ADD(DATE_SUB(CURDATE(), INTERVAL 2 MONTH), INTERVAL 21 DAY),  'ditutup'),
('Ujian Kenaikan Sabuk 11', 'Gladi Satya ke Wira Tama',     DATE_SUB(CURDATE(), INTERVAL 70 DAY),   DATE_ADD(CURDATE(), INTERVAL 14 DAY),                          DATE_SUB(CURDATE(), INTERVAL 10 DAY),                          'ditutup'),
('Ujian Kenaikan Sabuk 12', 'Gladi Satya ke Wira Tama',     DATE_SUB(CURDATE(), INTERVAL 60 DAY),   DATE_ADD(CURDATE(), INTERVAL 10 DAY),                          DATE_SUB(CURDATE(), INTERVAL 3 DAY),                           'dibuka');

-- ID 4 ujian terbaru yang dipakai untuk peserta
SET @u1 := @u_akhir + 9;
SET @u2 := @u_akhir + 10;
SET @u3 := @u_akhir + 11;
SET @u4 := @u_akhir + 12;

-- ---------------------------------------------------------------------
--  4. DETAIL PENDAFTARAN — peserta didaftarkan ke 4 ujian terbaru
--
--     Syarat: tingkatan_sabuk peserta harus sama dengan SABUK ASAL
--     di tingkat_ujian. Contoh: ujian 'Gladi Satya ke Wira Tama' hanya
--     boleh diikuti peserta bersabuk 'Gladi Satya'.
--
--     Ke-4 ujian di atas (@u1 s.d. @u4) semuanya berjenjang
--     'Gladi Satya ke Wira Tama', jadi pesertanya harus bersabuk
--     'Gladi Satya': C005, C006, C007, C018, C019, C020, C021, C022
--     (dari seed anggota, nomornya berurutan mulai 1).
-- ---------------------------------------------------------------------
INSERT INTO detail_pendaftaran (id_pendaftaran, id_anggota, status_pembayaran) VALUES
(@u1, @a_akhir +  5,'lunas'), (@u1, @a_akhir +  6,'lunas'), (@u1, @a_akhir +  7,'lunas'),
(@u1, @a_akhir + 18,'lunas'), (@u1, @a_akhir + 19,'lunas'),
(@u1, @a_akhir + 20,'belum'),
(@u2, @a_akhir + 20,'lunas'), (@u2, @a_akhir + 21,'lunas'), (@u2, @a_akhir + 22,'lunas'),
(@u2, @a_akhir +  5,'lunas'), (@u2, @a_akhir +  6,'lunas'),
(@u2, @a_akhir +  7,'belum'),
(@u3, @a_akhir +  7,'lunas'), (@u3, @a_akhir + 18,'lunas'),
(@u3, @a_akhir + 19,'lunas'),
(@u3, @a_akhir + 20,'belum'),
(@u4, @a_akhir + 21,'lunas'), (@u4, @a_akhir + 22,'lunas'), (@u4, @a_akhir +  5,'lunas'),
(@u4, @a_akhir +  6,'lunas');

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
       'uploads/pdf/contoh.pdf',
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
       -- id_pelatih diambil dari tabel yang ada, jangan angka tetap:
       -- kolom ini punya FOREIGN KEY ke pelatih(id_pelatih).
       (SELECT MIN(id_pelatih) FROM pelatih),
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