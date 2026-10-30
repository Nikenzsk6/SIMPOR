<?php
include_once '../../includes/session.php';
include_once '../../includes/funtion.php';

if ($role !== 'admin') {
    set_flash('Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.', 'danger');
    header('Location: ' . BASE_URL . ($role === 'pusat' ? 'pusat/dashboard.php' : 'login.php'));
    exit;
}

$id_ujian = (int) ($_GET['id'] ?? 0);

$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

// Pastikan ada peserta lulus sebelum mengirim
$ada_lulus = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT COUNT(*) AS jml FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    WHERE dp.id_pendaftaran=$id_ujian AND h.status_kelulusan='LULUS'
"));
if (!$ada_lulus || $ada_lulus['jml'] == 0) {
    set_flash('Belum ada peserta LULUS pada ujian ini. Pengesahan tidak dapat dikirim.', 'danger');
    header('Location: ../penilaian/hasil.php?id=' . $id_ujian);
    exit;
}

// Generate PDF first
require_once '../../vendor/autoload.php';

use Dompdf\Dompdf;

$cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
$nama_cabang = $cabang ? $cabang['nama_organisasi'] : 'Cabang';

$html = '<html><head><meta charset="UTF-8"><style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
    h2 { text-align: center; color: #8C1616; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th { background: #8C1616; color: white; padding: 8px; text-align: left; }
    td { padding: 6px; border: 1px solid #ddd; }
    .footer { margin-top: 30px; text-align: center; font-size: 10px; color: #888; }
</style></head><body>
<h2>HASIL UJIAN KENAIKAN SABUK</h2>
<p>' . htmlspecialchars($nama_cabang) . '</p>
<p>Tingkat Ujian: <strong>' . htmlspecialchars($ujian['tingkat_ujian']) . '</strong><br>
Nama Ujian: ' . htmlspecialchars($ujian['nama_ujian'] ?: $ujian['tingkat_ujian']) . '<br>
Tanggal Ujian: ' . tgl_indonesia($ujian['tgl_ujian']) . '</p>
<table>
<tr><th>No</th><th>No KTA</th><th>Nama</th><th>Sabuk Awal</th><th>Nilai Akhir</th><th>Status</th></tr>';

$q = mysqli_query($koneksi, "
    SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk, h.rata_rata_nilai, h.status_kelulusan
    FROM detail_pendaftaran dp
    JOIN anggota a ON dp.id_anggota = a.id_anggota
    JOIN hasil_ujian h ON dp.id_detail = h.id_detail
    WHERE dp.id_pendaftaran = $id_ujian AND h.status_kelulusan = 'LULUS'
    ORDER BY a.nama_anggota
");
$no = 1;
while ($d = mysqli_fetch_assoc($q)) {
    $html .= '<tr><td>' . $no++ . '</td><td>' . htmlspecialchars($d['no_kta']) . '</td><td>' . htmlspecialchars($d['nama_anggota']) . '</td>
    <td>' . htmlspecialchars($d['tingkatan_sabuk']) . '</td>
    <td>' . number_format($d['rata_rata_nilai'], 2) . '</td>
    <td>LULUS</td></tr>';
}
$html .= '</table><br><br><p>Mengetahui,<br>' . htmlspecialchars($nama_cabang) . '<br><br><br><br>' . htmlspecialchars($data_user['nama_user']) . '</p>';
$html .= '<div class="footer">Dokumen ini dicetak dari SIM PORSIGAL</div></body></html>';

$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

$pdf_name = 'hasil_ujian_' . $id_ujian . '_' . time() . '.pdf';
$pdf_path = 'uploads/pdf/' . $pdf_name;
$dir_pdf = __DIR__ . '/../../uploads/pdf';
if (!is_dir($dir_pdf)) {
    mkdir($dir_pdf, 0775, true);
}
file_put_contents(__DIR__ . '/../../' . $pdf_path, $dompdf->output());

// Create pengesahan record for each lulus result
$q_hasil = mysqli_query($koneksi, "
    SELECT h.id_hasil FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    WHERE dp.id_pendaftaran = $id_ujian AND h.status_kelulusan = 'LULUS'
");

while ($h = mysqli_fetch_assoc($q_hasil)) {
    $id_hasil = $h['id_hasil'];
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE id_hasil=$id_hasil"));
    if ($cek['jml'] == 0) {
        mysqli_query($koneksi, "INSERT INTO pengesahan (id_hasil, file_pdf, status, tgl_kirim) VALUES ($id_hasil, '$pdf_path', 'dikirim', NOW())");
    }
}

set_flash('PDF berhasil dibuat dan dikirim ke pusat!');
header('Location: index.php');
exit;
