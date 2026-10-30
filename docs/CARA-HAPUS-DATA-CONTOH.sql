-- =====================================================================
--  SIMPOR — HAPUS DATA CONTOH (SEED)
-- =====================================================================
--  Jalankan file ini kalau sudah selesai demo/presentasi dan ingin
--  mengembalikan database ke kondisi data awal.
--
--  Cara aman: hanya menghapus data yang BENAR-BENAR dari seed,
--  berdasarkan pola nama / kode yang dipakai di seed-contoh.sql:
--     anggota.no_kta       LIKE '2025000000%'  (14 digit, diawali 2025)
--     pelatih.no_kta       LIKE '2024000000%'  (14 digit, diawali 2024)
--  Data asli kamu (mis. KTA 25001, dst) tidak akan tersentuh.
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
    WHERE a.no_kta LIKE '2025000000%'
);

-- 2. Hapus pengesahan untuk hasil ujian peserta contoh
DELETE FROM pengesahan
WHERE id_hasil IN (
    SELECT h.id_hasil
    FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON dp.id_detail = h.id_detail
    JOIN anggota a ON a.id_anggota = dp.id_anggota
    WHERE a.no_kta LIKE '2025000000%'
);

-- 3. Hapus hasil ujian peserta contoh
DELETE FROM hasil_ujian
WHERE id_detail IN (
    SELECT dp.id_detail
    FROM detail_pendaftaran dp
    JOIN anggota a ON a.id_anggota = dp.id_anggota
    WHERE a.no_kta LIKE '2025000000%'
);

-- 4. Hapus detail pendaftaran peserta contoh
DELETE dp FROM detail_pendaftaran dp
JOIN anggota a ON a.id_anggota = dp.id_anggota
WHERE a.no_kta LIKE '2025000000%';

-- 5. Hapus ujian contoh
--    Hanya ujian yang namanya persis pola seed ("Ujian Kenaikan Sabuk 1"
--    s.d. "Ujian Kenaikan Sabuk 12") DAN sudah tidak punya peserta.
--    Ujian asli buatan pengguna tidak ikut terhapus.
DELETE FROM pendaftaran_ujian
WHERE nama_ujian REGEXP '^Ujian Kenaikan Sabuk (1|2|3|4|5|6|7|8|9|10|11|12)$'
  AND NOT EXISTS (
      SELECT 1 FROM detail_pendaftaran dp WHERE dp.id_pendaftaran = pendaftaran_ujian.id_pendaftaran
  );

-- 6. Hapus anggota contoh
DELETE FROM anggota WHERE no_kta LIKE '2025000000%';

-- 7. Hapus pelatih contoh
DELETE FROM pelatih WHERE no_kta LIKE '2024000000%';

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  Catatan: tabel log_aktivitas sengaja TIDAK dihapus karena merupakan
--  jejak audit perubahan data. Jika ingin mengosongkannya juga, jalankan
--  manual (opsional):
--      TRUNCATE TABLE log_aktivitas;
-- =====================================================================

-- =====================================================================
--  Selesai. Refresh dashboard -> grafik kembali sesuai data asli.
-- =====================================================================