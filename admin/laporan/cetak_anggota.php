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

$html = '<html><head><meta charset="UTF-8"><style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; }
    h2 { text-align: center; color: #8C1616; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background: #8C1616; color: white; padding: 8px; text-align: left; }
    td { padding: 6px; border: 1px solid #ddd; }
    .footer { margin-top: 40px; text-align: right; font-size: 10px; }
</style></head><body>
<h2>LAPORAN DATA ANGGOTA</h2>
<table><tr><th>No</th><th>No KTA</th><th>Nama</th><th>Sabuk</th><th>Kontak</th><th>Status</th></tr>';

$q = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY nama_anggota");
$no = 1;
while ($d = mysqli_fetch_assoc($q)) {
    $html .= '<tr><td>' . $no++ . '</td><td>' . htmlspecialchars($d['no_kta']) . '</td>
    <td>' . htmlspecialchars($d['nama_anggota']) . '</td>
    <td>' . htmlspecialchars($d['tingkatan_sabuk']) . '</td>
    <td>' . htmlspecialchars($d['kontak']) . '</td>
    <td>' . $d['status'] . '</td></tr>';
}
$html .= '</table>
<div class="ttd">' . tgl_indonesia(date('Y-m-d')) . '<br>Admin Cabang<br><br><br><br>' . htmlspecialchars($data_user['nama_user']) . '</div>
<div class="footer">Dicetak dari SIM PORSIGAL</div></body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();
$dompdf->stream('laporan_anggota.pdf', ['Attachment' => false]);
