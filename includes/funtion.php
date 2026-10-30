<?php
function status_label($status)
{
    $labels = [
        'dibuka' => 'badge bg-success',
        'ditutup'        => 'badge bg-secondary',
        'lunas'          => 'badge bg-success',
        'belum'          => 'badge bg-warning text-dark',
        'LULUS'          => 'badge bg-success',
        'TIDAK LULUS'    => 'badge bg-danger',
        'dikirim'        => 'badge bg-primary',
        'diterima_pusat' => 'badge bg-info text-dark',
        'disetujui'      => 'badge bg-success',
        'ditolak'        => 'badge bg-danger',
        'warga'          => 'badge bg-success',
        'non-warga'      => 'badge bg-secondary',
    ];
    $class = isset($labels[$status]) ? $labels[$status] : 'badge bg-secondary';
    return '<span class="' . $class . '">' . htmlspecialchars($status) . '</span>';
}
function tgl_indonesia($date)
{
    if (!$date || $date == '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $tgl = date('j', strtotime($date));
    $bln = $bulan[(int)date('m', strtotime($date))];
    $thn = date('Y', strtotime($date));
    return "$tgl $bln $thn";
}

function tgl_indonesia_detik($datetime)
{
    if (!$datetime) return '-';
    $tgl = date('j', strtotime($datetime));
    $bulan = [
        1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];
    $bln = $bulan[(int)date('m', strtotime($datetime))];
    $thn = date('Y', strtotime($datetime));
    $jam = date('H:i', strtotime($datetime));
    return "$tgl $bln $thn $jam";
}

function alert($pesan, $tipe = 'success')
{
    return '<div class="alert alert-' . $tipe . ' alert-dismissible fade show" role="alert">
        ' . $pesan . '
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>';
}

function set_flash($pesan, $tipe = 'success')
{
    $_SESSION['flash'] = ['pesan' => $pesan, 'tipe' => $tipe];
}

function flash()
{
    if (isset($_SESSION['flash'])) {
        $f = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $icons = ['success' => 'bi-check-circle-fill', 'danger' => 'bi-x-circle-fill', 'warning' => 'bi-exclamation-triangle-fill', 'info' => 'bi-info-circle-fill'];
        $icon = $icons[$f['tipe']] ?? 'bi-info-circle-fill';
        return '<div class="toast-notification toast-' . $f['tipe'] . '"><i class="bi ' . $icon . '"></i><span>' . $f['pesan'] . '</span></div>';
    }
    return '';
}

function auto_status_sabuk($tingkatan)
{
    $warga = ['Gladi Gatra', 'Gladi Taruna', 'Gladi Satya', 'Wira Tama'];
    return in_array($tingkatan, $warga) ? 'warga' : 'non-warga';
}

/* ============================================================
   KEAMANAN PASSWORD
   ------------------------------------------------------------
   Hash baru memakai password_hash() (bcrypt), bukan md5.
   password_verify() dipakai untuk mencocokkan.

   Hash md5 lama tetap bisa login lewat verify_password(), lalu
   otomatis diganti ke bcrypt saat login berhasil (lihat login.php).
   Dengan begitu pengguna lama tidak perlu reset password.
   ============================================================ */

function hash_password($plain)
{
    return password_hash($plain, PASSWORD_DEFAULT);
}

function is_legacy_md5($hash)
{
    return is_string($hash) && strlen($hash) === 32 && ctype_xdigit($hash);
}

/**
 * Cek password terhadap hash tersimpan.
 * Kembalikan array: [bool valid, bool perlu_rehash]
 */
function verify_password($plain, $stored)
{
    if (is_string($stored) && $stored !== '' && password_verify($plain, $stored)) {
        return [true, password_needs_rehash($stored, PASSWORD_DEFAULT)];
    }

    // Kompatibilitas hash md5 lama
    if (is_legacy_md5($stored)) {
        return [hash_equals(strtolower($stored), md5($plain)), true];
    }

    return [false, false];
}

/* ============================================================
   VALIDASI FORM (mengacu pada RTM SIMPOR)
   ============================================================ */

/** Ambil nilai POST dan rapikan spasi di ujung. */
function input($key, $default = '')
{
    return isset($_POST[$key]) ? trim((string) $_POST[$key]) : $default;
}

/** Nama orang: wajib, hanya huruf dan spasi. */
function validasi_nama($v)
{
    return $v !== '' && preg_match('/^[A-Za-z ]+$/', $v) === 1;
}

/** Nama user: wajib, tidak boleh mengandung angka. */
function validasi_nama_user($v)
{
    return $v !== '' && preg_match('/[0-9]/', $v) === 0
        && preg_match("/^[A-Za-z ._']+$/", $v) === 1;
}

/** Username: hanya huruf/angka, tidak boleh hanya angka. */
function validasi_username($v)
{
    if ($v === '' || preg_match('/^[A-Za-z0-9]+$/', $v) !== 1) {
        return false;
    }
    return !ctype_digit($v);
}

/** Password minimal 8 karakter. */
function validasi_password($v)
{
    return strlen($v) >= 8;
}

/** No. KTA: tepat 14 digit angka. */
function validasi_kta($v)
{
    return preg_match('/^[0-9]{14}$/', $v) === 1;
}

/** Kontak pelatih: angka (boleh diawali +), minimal 10 digit. */
function validasi_kontak($v)
{
    if (preg_match('/^\+?[0-9]+$/', $v) !== 1) {
        return false;
    }
    return strlen(preg_replace('/\D/', '', $v)) >= 10;
}

/** Kontak anggota/user/profil: hanya angka, minimal 10 digit. */
function validasi_kontak_angka($v)
{
    return preg_match('/^[0-9]+$/', $v) === 1 && strlen($v) >= 10;
}

/**
 * Tanggal: terima dd-mm-yyyy (sesuai RTM) atau yyyy-mm-dd (input date).
 * Kembalikan format database (Y-m-d) atau false jika tidak valid.
 * Set $izinkan_iso = false untuk field yang wajib dd-mm-yyyy (tanggal lahir).
 */
function validasi_tanggal($v, $izinkan_iso = true)
{
    $v = trim($v);
    if ($v === '') {
        return false;
    }
    $format_list = $izinkan_iso ? ['d-m-Y', 'Y-m-d'] : ['d-m-Y'];
    foreach ($format_list as $format) {
        $d = DateTime::createFromFormat('!' . $format, $v);
        if ($d && $d->format($format) === $v) {
            return $d->format('Y-m-d');
        }
    }
    return false;
}

/** True jika tanggal (Y-m-d) melewati hari ini. */
function tanggal_masa_depan($ymd)
{
    return $ymd && $ymd > date('Y-m-d');
}

/** Tampilkan tanggal database sebagai dd-mm-yyyy. */
function tanggal_tampil($ymd)
{
    if (!$ymd) {
        return '-';
    }
    $t = strtotime($ymd);
    return $t ? date('d-m-Y', $t) : '-';
}

/** Nama organisasi: wajib, tolak karakter khusus yang umum. */
function validasi_nama_organisasi($v)
{
    if (trim($v) === '') {
        return false;
    }
    return preg_match('/[@#$%^*_+={}\[\]|\\\\<>~`]/', $v) !== 1;
}

/** Cek nilai termasuk daftar pilihan yang diizinkan. */
function validasi_pilihan($v, $daftar)
{
    return in_array($v, $daftar, true);
}

/** Daftar tingkatan sabuk yang dipakai aplikasi. */
function daftar_sabuk()
{
    return ['Prapta', 'Kawi', 'Gladi Gatra', 'Gladi Taruna', 'Gladi Satya', 'Wira Tama'];
}

/** Daftar tingkatan ujian yang dipakai aplikasi. */
function daftar_tingkat_ujian()
{
    return [
        'Prapta ke Kawi',
        'Kawi ke Gladi Gatra',
        'Gladi Gatra ke Gladi Taruna',
        'Gladi Taruna ke Gladi Satya',
        'Gladi Satya ke Wira Tama',
    ];
}

/** Simpan file unggahan gambar dengan aman. Kembalikan path atau false. */
function simpan_gambar($field, $folder, $maks_byte = 10485760)
{
    if (!isset($_FILES[$field]) || !is_array($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // tidak ada file baru
    }

    $f = $_FILES[$field];
    if ($f['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    if ($f['size'] > $maks_byte) {
        return false;
    }

    $izin = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    if (!isset($izin[$ext])) {
        return false;
    }

    $info = @getimagesize($f['tmp_name']);
    if (!$info || !in_array($info['mime'], $izin, true)) {
        return false;
    }

    // $folder selalu relatif terhadap root aplikasi, mis. "uploads/logo".
    // Path yang dikembalikan juga relatif root supaya bisa langsung dipakai
    // bersama BASE_URL saat ditampilkan.
    $rel = trim(str_replace('\\', '/', $folder), '/');
    $dir = dirname(__DIR__) . '/' . $rel . '/';
    if (!is_dir($dir) && !mkdir($dir, 0775, true) && !is_dir($dir)) {
        return false;
    }

    $nama = bin2hex(random_bytes(8)) . '.' . $ext;
    if (!move_uploaded_file($f['tmp_name'], $dir . $nama)) {
        return false;
    }

    return $rel . '/' . $nama;
}

/* ============================================================
   LOG AKTIVITAS (HANYA DATABASE)
   ------------------------------------------------------------
   Mencatat perubahan data oleh admin cabang maupun pusat ke
   tabel log_aktivitas. Tidak ditampilkan di aplikasi.

   Fungsi ini tidak boleh menghentikan alur aplikasi: jika tabel
   belum ada atau pencatatan gagal, cukup kembalikan false.
   ============================================================ */

/**
 * Simpan satu baris log aktivitas.
 *
 * @param string     $aksi         Contoh: TAMBAH, UBAH, HAPUS, LOGIN.
 * @param string     $modul        Contoh: pelatih, anggota, ujian, user.
 * @param string     $deskripsi    Keterangan singkat yang mudah dibaca.
 * @param mixed      $id_referensi ID data yang terpengaruh (opsional).
 * @param mixed      $data_lama    Data sebelum perubahan (opsional, array/string).
 * @param mixed      $data_baru    Data sesudah perubahan (opsional, array/string).
 * @param array|null $identitas    Paksa identitas pelaku, mis. saat login gagal:
 *                                 ['id_user'=>..,'username'=>..,'role'=>..].
 * @return bool
 */
function catat_log($aksi, $modul, $deskripsi = '', $id_referensi = null, $data_lama = null, $data_baru = null, $identitas = null)
{
    global $koneksi;

    if (!isset($koneksi) || !($koneksi instanceof mysqli)) {
        return false;
    }

    // Identitas pelaku dari sesi (fallback ke parameter $identitas)
    $id_user  = $_SESSION['id_user'] ?? null;
    $username = $_SESSION['username'] ?? null;
    $role     = $_SESSION['role'] ?? null;

    if (is_array($identitas)) {
        if (array_key_exists('id_user', $identitas))  $id_user  = $identitas['id_user'];
        if (array_key_exists('username', $identitas)) $username = $identitas['username'];
        if (array_key_exists('role', $identitas))     $role     = $identitas['role'];
    }

    // Lengkapi username/role dari database bila sesi hanya menyimpan id_user
    if ($id_user !== null && ($username === null || $role === null)) {
        $id_cek = (int) $id_user;
        $q = @mysqli_query($koneksi, "SELECT username, role FROM user WHERE id_user=$id_cek LIMIT 1");
        if ($q && ($row = mysqli_fetch_assoc($q))) {
            if ($username === null) $username = $row['username'];
            if ($role === null)     $role     = $row['role'];
        }
    }

    $id_user      = $id_user === null ? null : (int) $id_user;
    $username     = $username === null ? null : substr((string) $username, 0, 50);
    $role         = $role === null ? null : substr((string) $role, 0, 20);
    $aksi         = substr((string) $aksi, 0, 50);
    $modul        = substr((string) $modul, 0, 50);
    $id_referensi = $id_referensi === null ? null : substr((string) $id_referensi, 0, 50);
    $deskripsi    = $deskripsi === null ? null : (string) $deskripsi;

    $ip = $_SERVER['REMOTE_ADDR'] ?? null;
    $ua = isset($_SERVER['HTTP_USER_AGENT'])
        ? substr((string) $_SERVER['HTTP_USER_AGENT'], 0, 255)
        : null;

    $json_lama = null;
    if ($data_lama !== null) {
        $json_lama = json_encode($data_lama, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json_lama === false) $json_lama = null;
    }

    $json_baru = null;
    if ($data_baru !== null) {
        $json_baru = json_encode($data_baru, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        if ($json_baru === false) $json_baru = null;
    }

    $stmt = @mysqli_prepare($koneksi, "INSERT INTO log_aktivitas
        (id_user, username, role, aksi, modul, id_referensi, deskripsi, data_lama, data_baru, ip_address, user_agent)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if (!$stmt) {
        return false; // tabel belum ada / gagal menyiapkan query
    }

    mysqli_stmt_bind_param(
        $stmt,
        'issssssssss',
        $id_user,
        $username,
        $role,
        $aksi,
        $modul,
        $id_referensi,
        $deskripsi,
        $json_lama,
        $json_baru,
        $ip,
        $ua
    );

    $ok = @mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    return (bool) $ok;
}
