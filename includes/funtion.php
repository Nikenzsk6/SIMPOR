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
