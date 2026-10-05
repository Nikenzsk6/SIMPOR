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
