# Keamanan Data — SIMPOR

Dokumen ini menjelaskan apa yang aman diunggah ke GitHub publik, apa yang
**tidak boleh**, dan pengamanan yang sudah ada di dalam kode.

---

## 1. Prinsip utama: repo publik = kode saja

Repository ini **hanya berisi kode aplikasi**. Database tidak ikut diunggah.

Artinya nama anggota, nomor kontak, alamat, nomor KTA, dan hash password
pengguna **tidak terlihat di GitHub** — selama aturan di bawah dipatuhi.

File yang sengaja tidak di-commit:

| File / folder | Isi | Status |
|---|---|---|
| `includes/config.php` | password database asli | diabaikan `.gitignore` |
| `/uploads/` | PDF sertifikat & hasil ujian | diabaikan |
| `/assets/upload/` | berkas unggahan pengguna | diabaikan |
| `/vendor/` | dependensi Composer | diabaikan (dipulihkan `composer install`) |
| `*.sql`, `*.dump`, `*.sql.gz` | dump database | diabaikan |
| `/backup/`, `/database/` | cadangan lokal | diabaikan |

Yang tetap ikut (aman, karena tanpa data asli):

- `includes/config.example.php` — contoh konfigurasi, password masih placeholder
- `docs/skema-database.sql` — struktur tabel saja, **tanpa satu baris data pun**
- `docs/seed-contoh.sql` — data demo sintetis (nama & kontak fiktif)
- `docs/CARA-HAPUS-DATA-CONTOH.sql` — skrip pembersih data demo

---

## 2. Cek sebelum push

Jalankan tiga perintah ini di folder proyek sebelum `git push`:

```powershell
# 1. Lihat file apa saja yang akan ikut ter-commit
git status --short

# 2. Pastikan tidak ada config/dump/upload yang ter-track
git ls-files | Select-String "config\.php$|\.sql$|^uploads/|^assets/upload/"

# 3. Pastikan config.php asli benar-benar terabaikan
git check-ignore -v includes/config.php
```

Hasil yang benar untuk nomor 2 hanya empat file:

```
docs/CARA-HAPUS-DATA-CONTOH.sql
docs/seed-contoh.sql
docs/skema-database.sql
includes/config.example.php
```

Kalau muncul `includes/config.php` atau file `.sql` lain, **jangan push** —
hapus dari staging dengan `git rm --cached <file>` lalu cek ulang.

Cek juga riwayat git, karena file yang pernah masuk tetap tersimpan di riwayat
walaupun sudah dihapus:

```powershell
git log --all --pretty=format: --name-only | Sort-Object -Unique |
  Select-String "config\.php$|\.sql$|^uploads/|\.env$"
```

---

## 3. Yang sudah diperkuat di kode

### Password pengguna

- Password **baru** disimpan dengan `password_hash()` (bcrypt), bukan MD5.
  Bcrypt punya salt acak dan sengaja lambat, sehingga jauh lebih sulit
  dipecahkan kalau database sampai bocor.
- Password lama yang masih MD5 **tetap bisa login**, lalu otomatis diganti
  ke bcrypt saat login berhasil. Pengguna tidak perlu reset password.
- Verifikasi memakai `password_verify()` / `hash_equals()` (perbandingan
  waktu-konstan).

Diuji: user dengan hash MD5 berhasil login, dan hash di database berubah
dari 32 karakter menjadi 60 karakter berawalan `$2y$`.

### Sesi & cookie

- Cookie sesi `HttpOnly` — tidak bisa dibaca JavaScript (membatasi dampak XSS).
- `SameSite=Lax` — cookie tidak ikut terkirim dari situs lain.
- `session.use_strict_mode` — ID sesi buatan penyerang ditolak.
- `session_regenerate_id(true)` setiap login berhasil — mencegah
  *session fixation*.
- ID sesi diganti ulang setiap 30 menit.
- Logout menghapus data sesi **dan** cookie di browser.

### Login

- Query login memakai *prepared statement*, bukan penyisipan string.
- Pembatasan percobaan: 5 kali gagal → terkunci 60 detik.
- Setiap kegagalan diberi jeda 200 ms untuk memperlambat tebak password.
- Pesan error dibuat umum ("Username atau password salah") agar tidak
  membocorkan username mana yang terdaftar.

### Lain-lain

- Semua input tulis di halaman CRUD sudah melewati
  `mysqli_real_escape_string()`, dan ID di-cast ke `(int)`.
- Output teks pengguna dibungkus `htmlspecialchars()`.
- Header `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`
  dikirim di setiap halaman.
- Penghitung percobaan login disimpan di sesi, jadi tidak menambah kolom
  atau tabel baru.

---

## 4. Yang masih perlu dilakukan saat dipasang di server

Ini aplikasi latihan/portofolio, bukan sistem produksi. Sebelum dipakai
sungguhan:

1. **Wajib HTTPS.** Tanpa HTTPS, cookie dan password bisa disadap di jaringan.
   Setelah HTTPS aktif, cookie otomatis ditandai `Secure` oleh kode ini.
2. **Ganti password akun default** (`admin`, `pusat1`) sebelum dipakai.
3. **Belum ada token CSRF.** Form yang mengubah data masih bisa dipicu dari
   situs lain selama admin sedang login. Untuk produksi, tambahkan token
   per-form.
4. **Jangan pakai data asli untuk demo.** Gunakan `docs/seed-contoh.sql`.
5. Batasi akses phpMyAdmin dan folder `docs/` di server.
6. Pertimbangkan migrasi bertahap ke *prepared statement* untuk semua query
   (saat ini input sudah di-escape, tetapi prepared statement lebih aman).

---

## 5. Kalau rahasia terlanjur ter-push

1. **Segera ganti** password database dan password akun yang bocor.
   Mengandalkan penghapusan file saja tidak cukup — anggap data sudah bocor.
2. Hapus file dari riwayat (bukan hanya commit terakhir):

   ```powershell
   git filter-repo --path includes/config.php --invert-paths
   git push --force
   ```

3. Kalau repo berisi dump database: ganti semua password user aplikasi,
   lalu umumkan bahwa hash lama sudah tidak berlaku (login berikutnya
   otomatis memakai bcrypt).

---

## 6. Catatan tentang data demo

`docs/seed-contoh.sql` memakai nomor KTA contoh 14 digit berawalan
`2025000000` (anggota) dan `2024000000` (pelatih), dengan nama serta kontak
fiktif. Data ini hanya untuk mengisi grafik dashboard saat presentasi dan
bisa dihapus dengan `docs/CARA-HAPUS-DATA-CONTOH.sql`.
