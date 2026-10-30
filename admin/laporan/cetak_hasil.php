<?php
include_once '../../includes/session.php';
include_once '../../includes/funtion.php';
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;

if ($role !== 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}

$id_ujian = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$html = '<html><head><meta charset="UTF-8"><style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    h2 { text-align: center; color: #8C1616; margin-bottom: 5px; }
    .sub { text-align: center; color: #666; margin-bottom: 20px; }
    table { width: 100%; border-collapse: collapse; }
    th { background: #8C1616; color: white; padding: 8px; text-align: left; font-size: 10px; }
    td { padding: 6px; border: 1px solid #ddd; }
    .lulus { color: green; font-weight: bold; }
    .tidak { color: red; font-weight: bold; }
    .footer { margin-top: 40px; text-align: right; font-size: 10px; }
    .ttd { margin-top: 60px; text-align: right; }
</style></head><body>';

if ($id_ujian > 0) {
    $ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
    if (!$ujian) {
        http_response_code(404);
        exit('Data ujian tidak ditemukan.');
    }
    $html .= '<h2>LAPORAN HASIL UJIAN KENAIKAN SABUK</h2>';
    $html .= '<div class="sub">' . htmlspecialchars($ujian['nama_ujian'] ?: $ujian['tingkat_ujian']) . ' | Tingkat: ' . htmlspecialchars($ujian['tingkat_ujian']) . ' | Tanggal: ' . tgl_indonesia($ujian['tgl_ujian']) . '</div>';
    $html .= '<table><tr><th>No</th><th>No KTA</th><th>Nama</th><th>Sabuk</th><th>Nilai</th><th>Status</th></tr>';

    $q = mysqli_query($koneksi, "
        SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk, h.rata_rata_nilai, h.status_kelulusan
        FROM detail_pendaftaran dp
        JOIN anggota a ON dp.id_anggota = a.id_anggota
        LEFT JOIN hasil_ujian h ON dp.id_detail = h.id_detail
        WHERE dp.id_pendaftaran = $id_ujian
        ORDER BY a.nama_anggota
    ");
    $no = 1;
    while ($d = mysqli_fetch_assoc($q)) {
        $cls = ($d['status_kelulusan'] == 'LULUS') ? 'lulus' : 'tidak';
        $html .= '<tr><td>' . $no++ . '</td><td>' . htmlspecialchars($d['no_kta']) . '</td>
        <td>' . htmlspecialchars($d['nama_anggota']) . '</td>
        <td>' . htmlspecialchars($d['tingkatan_sabuk']) . '</td>
        <td>' . ($d['rata_rata_nilai'] ? number_format($d['rata_rata_nilai'], 2) : '-') . '</td>
        <td class="' . $cls . '">' . ($d['status_kelulusan'] ?? '-') . '</td></tr>';
    }
    $html .= '</table>';
} else {
    // All exams
    $html .= '<h2>LAPORAN SEMUA HASIL UJIAN</h2>';
    $q_ujian = mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian ORDER BY tgl_ujian DESC");
    while ($ujian = mysqli_fetch_assoc($q_ujian)) {
        $html .= '<h4 style="margin-top:20px;">' . $ujian['tingkat_ujian'] . ' - ' . tgl_indonesia($ujian['tgl_ujian']) . '</h4>';
        $html .= '<table><tr><th>No</th><th>No KTA</th><th>Nama</th><th>Sabuk</th><th>Nilai</th><th>Status</th></tr>';
        $q = mysqli_query($koneksi, "
            SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk, h.rata_rata_nilai, h.status_kelulusan
            FROM detail_pendaftaran dp
            JOIN anggota a ON dp.id_anggota = a.id_anggota
            LEFT JOIN hasil_ujian h ON dp.id_detail = h.id_detail
            WHERE dp.id_pendaftaran = {$ujian['id_pendaftaran']}
            ORDER BY a.nama_anggota
        ");
        $no = 1;
        while ($d = mysqli_fetch_assoc($q)) {
            $cls = ($d['status_kelulusan'] == 'LULUS') ? 'lulus' : 'tidak';
            $html .= '<tr><td>' . $no++ . '</td><td>' . htmlspecialchars($d['no_kta']) . '</td>
            <td>' . htmlspecialchars($d['nama_anggota']) . '</td>
            <td>' . htmlspecialchars($d['tingkatan_sabuk']) . '</td>
            <td>' . ($d['rata_rata_nilai'] ? number_format($d['rata_rata_nilai'], 2) : '-') . '</td>
            <td class="' . $cls . '">' . ($d['status_kelulusan'] ?? '-') . '</td></tr>';
        }
        $html .= '</table>';
    }
}

$html .= '<div class="ttd">' . tgl_indonesia(date('Y-m-d')) . '<br>Admin Cabang<br><br><br><br>' . htmlspecialchars($data_user['nama_user']) . '</div>';
$html .= '<div class="footer">Dicetak dari SIM PORSIGAL</div></body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('laporan_hasil_ujian.pdf', ['Attachment' => false]);
