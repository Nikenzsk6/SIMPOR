-- =====================================================================
--  SIMPOR — HAPUS DATA CONTOH (SEED)
-- =====================================================================
--  Jalankan file ini kalau sudah selesai demo/presentasi dan ingin
--  mengembalikan database ke kondisi data awal.
--
--  Cara aman: hanya menghapus data yang BENAR-BENAR dari seed,
--  berdasarkan pola nama / kode yang dipakai di seed-contoh.sql:
--     anggota.no_kta       LIKE 'KTA-C%'
--     pelatih.no_kta       LIKE 'KTP-C%'
--  Data asli kamu (KTA-0001, dst) tidak akan tersentuh.
--
--  Jalankan: mysql -u AMP -p simpor < CARA-HAPUS-DATA-CONTOH.sql
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. Hapus penilaian untuk peserta contoh
DELETE FROM penilaian
WHERE id_detail IN (
    SELECT dp.id_detail
    FROM detail_pendaftaran dp
    JOIN anggota a ON a.id_anggota = dp.id_anggota
    WHERE a.no_kta LIKE 'KTA-C%'
);

-- 2. Hapus pengesahan untuk hasil ujian peserta contoh
DELETE FROM pengesahan
WHERE id_hasil IN (
    SELECT h.id_hasil
    FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON dp.id_detail = h.id_detail
    JOIN anggota a ON a.id_anggota = dp.id_anggota
    WHERE a.no_kta LIKE 'KTA-C%'
);

-- 3. Hapus hasil ujian peserta contoh
DELETE FROM hasil_ujian
WHERE id_detail IN (
    SELECT dp.id_detail
    FROM detail_pendaftaran dp
    JOIN anggota a ON a.id_anggota = dp.id_anggota
    WHERE a.no_kta LIKE 'KTA-C%'
);

-- 4. Hapus detail pendaftaran peserta contoh
DELETE dp FROM detail_pendaftaran dp
JOIN anggota a ON a.id_anggota = dp.id_anggota
WHERE a.no_kta LIKE 'KTA-C%';

-- 5. Hapus ujian contoh
--    Ujian contoh = ujian yang SETELAHH-nya tidak punya peserta contoh,
--    atau tanggal bukanya >= 11 bulan lalu (pola dari seed).
DELETE FROM pendaftaran_ujian
WHERE NOT EXISTS (
    SELECT 1 FROM detail_pendaftaran dp WHERE dp.id_pendaftaran = pendaftaran_ujian.id_pendaftaran
)
AND tgl_buka >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH);

-- 6. Hapus anggota contoh
DELETE FROM anggota WHERE no_kta LIKE 'KTA-C%';

-- 7. Hapus pelatih contoh
DELETE FROM pelatih WHERE no_kta LIKE 'KTP-C%';

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  Selesai. Refresh dashboard -> grafik kembali sesuai data asli.
-- =====================================================================