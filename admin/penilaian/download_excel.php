<?php
include_once '../../includes/session.php';
require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$id_ujian = (int)$_GET['id'];
$id_pelatih = (int)$_GET['pelatih'];

$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
$pelatih = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelatih WHERE id_pelatih=$id_pelatih"));

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

$sheet->setCellValue('A1', 'FORM PENILAIAN UJIAN KENAIKAN SABUK');
$sheet->mergeCells('A1:H1');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

$sheet->setCellValue('A2', 'Tingkat Ujian: ' . $ujian['tingkat_ujian']);
$sheet->mergeCells('A2:H2');
$sheet->setCellValue('A3', 'Penguji: ' . $pelatih['nama_pelatih'] . ' (' . $pelatih['tingkatan_sabuk'] . ')');
$sheet->mergeCells('A3:H3');

$headers = ['No', 'No KTA', 'Nama Peserta', 'Teori', 'Sejarah', 'Jurus', 'Mental', 'Agama'];
$col = 'A';
foreach ($headers as $h) {
    $sheet->setCellValue($col . '8', $h);
    $sheet->getStyle($col . '8')->getFont()->setBold(true);
    $sheet->getStyle($col . '8')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('5C0E0E');
    $sheet->getStyle($col . '8')->getFont()->getColor()->setRGB('FFFFFF');
    $col++;
}

$q = mysqli_query($koneksi, "
    SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk
    FROM detail_pendaftaran dp
    JOIN anggota a ON dp.id_anggota = a.id_anggota
    WHERE dp.id_pendaftaran = $id_ujian
    ORDER BY a.nama_anggota
");
$no = 1;
$row = 9;
while ($d = mysqli_fetch_assoc($q)) {
    $sheet->setCellValue('A' . $row, $no++);
    $sheet->setCellValue('B' . $row, $d['no_kta']);
    $sheet->setCellValue('C' . $row, $d['nama_anggota']);
    $sheet->setCellValue('D' . $row, '');
    $sheet->setCellValue('E' . $row, '');
    $sheet->setCellValue('F' . $row, '');
    $sheet->setCellValue('G' . $row, '');
    $sheet->setCellValue('H' . $row, '');
    $row++;
}

$sheet->getColumnDimension('A')->setWidth(5);
$sheet->getColumnDimension('B')->setWidth(15);
$sheet->getColumnDimension('C')->setWidth(30);
$sheet->getColumnDimension('D')->setWidth(10);
$sheet->getColumnDimension('E')->setWidth(10);
$sheet->getColumnDimension('F')->setWidth(10);
$sheet->getColumnDimension('G')->setWidth(10);
$sheet->getColumnDimension('H')->setWidth(10);

$filename = 'penilaian_' . $ujian['tingkat_ujian'] . '_' . $pelatih['nama_pelatih'] . '.xlsx';
$filename = str_replace([' ', '/'], '_', $filename);

ob_end_clean();
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
