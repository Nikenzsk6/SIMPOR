<?php
include_once '../../includes/sidebar_admin.php';

function hitung_hasil_ujian($koneksi, $id_ujian)
{
    $q_peserta = mysqli_query($koneksi, "SELECT id_detail FROM detail_pendaftaran WHERE id_pendaftaran=$id_ujian");
    while ($ps = mysqli_fetch_assoc($q_peserta)) {
        $id_detail = $ps['id_detail'];
        $q_nilai = mysqli_query($koneksi, "SELECT AVG(nilai_akhir) as rata_rata FROM penilaian WHERE id_detail=$id_detail");
        $rata = mysqli_fetch_assoc($q_nilai)['rata_rata'];
        $rata = $rata ? round($rata, 2) : 0;
        $status = ($rata >= 70) ? 'LULUS' : 'TIDAK LULUS';
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM hasil_ujian WHERE id_detail=$id_detail"));
        if ($cek['jml'] > 0) {
            mysqli_query($koneksi, "UPDATE hasil_ujian SET rata_rata_nilai=$rata, status_kelulusan='$status', tgl_penetapan=CURDATE() WHERE id_detail=$id_detail");
        } else {
            mysqli_query($koneksi, "INSERT INTO hasil_ujian (id_detail, rata_rata_nilai, status_kelulusan, tgl_penetapan) VALUES ($id_detail, $rata, '$status', CURDATE())");
        }
    }
}

$id_ujian = (int)$_GET['id'];
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

// Handle hitung akhir
if (isset($_GET['hitung'])) {
    hitung_hasil_ujian($koneksi, $id_ujian);
    $ringkas = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT COUNT(*) AS jumlah,
               SUM(h.status_kelulusan='LULUS') AS lulus,
               SUM(h.status_kelulusan='TIDAK LULUS') AS tidak_lulus
        FROM hasil_ujian h
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        WHERE dp.id_pendaftaran = $id_ujian
    "));
    catat_log('HITUNG_NILAI', 'penilaian', 'Menghitung hasil akhir ujian ' . ($ujian['nama_ujian'] ?: $ujian['tingkat_ujian']), $id_ujian, null, [
        'id_ujian'    => $id_ujian,
        'jumlah'      => (int) ($ringkas['jumlah'] ?? 0),
        'lulus'       => (int) ($ringkas['lulus'] ?? 0),
        'tidak_lulus' => (int) ($ringkas['tidak_lulus'] ?? 0),
    ]);
    set_flash('Hasil akhir ujian berhasil dihitung!', 'success');
    header("Location: hasil.php?id=$id_ujian");
    exit;
}

// Get examiners with status
$q_penguji = mysqli_query($koneksi, "
    SELECT p.* FROM penguji_ujian pu 
    JOIN pelatih p ON pu.id_pelatih = p.id_pelatih 
    WHERE pu.id_pendaftaran = $id_ujian
");
$penguji_data = [];
$semua_diupload = true;
while ($p = mysqli_fetch_assoc($q_penguji)) {
    $q_cek = mysqli_query($koneksi, "
        SELECT COUNT(*) as jml, MIN(n.file_penilaian) as file_penilaian
        FROM penilaian n
        JOIN detail_pendaftaran dp ON n.id_detail = dp.id_detail
        WHERE dp.id_pendaftaran = $id_ujian AND n.id_pelatih = {$p['id_pelatih']}
    ");
    $status = mysqli_fetch_assoc($q_cek);
    $p['status_upload'] = ($status['jml'] > 0 && $status['file_penilaian']) ? 'Diupload' : 'Pending';
    $p['file_penilaian'] = $status['file_penilaian'];
    if ($p['status_upload'] == 'Pending') $semua_diupload = false;
    $penguji_data[] = $p;
}

// Get participants for preview
$q_peserta = mysqli_query($koneksi, "
    SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk
    FROM detail_pendaftaran dp 
    JOIN anggota a ON dp.id_anggota = a.id_anggota 
    WHERE dp.id_pendaftaran = $id_ujian
    ORDER BY a.nama_anggota
");
?>
<h4 class="page-title">Input Nilai - <?= htmlspecialchars($ujian['tingkat_ujian']) ?></h4>
<p class="text-muted"><?= tgl_indonesia($ujian['tgl_ujian']) ?></p>

<!-- Upload Form -->
<div class="content-wrapper mb-3">
    <form action="upload_excel.php?id=<?= $id_ujian ?>" method="post" enctype="multipart/form-data" class="row g-2 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Pilih Penguji</label>
            <select name="id_pelatih" class="form-control" required>
                <option value="">-- Pilih --</option>
                <?php foreach ($penguji_data as $p): ?>
                    <option value="<?= $p['id_pelatih'] ?>"><?= htmlspecialchars($p['nama_pelatih']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">File Excel (.xlsx / .xls)</label>
            <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100"><i class="bi bi-upload"></i> Upload</button>
        </div>
    </form>
</div>

<!-- Download Template -->
<div class="content-wrapper mb-3">
    <div class="d-flex gap-2 flex-wrap align-items-center">
        <strong>Download Template:</strong>
        <?php foreach ($penguji_data as $p): ?>
            <a href="download_excel.php?id=<?= $id_ujian ?>&pelatih=<?= $p['id_pelatih'] ?>" class="btn btn-sm btn-outline-info">
                <i class="bi bi-file-earmark-excel"></i> <?= htmlspecialchars($p['nama_pelatih']) ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Status Table -->
<div class="content-wrapper mb-3">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Penguji</th>
                <th>Status</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            foreach ($penguji_data as $p): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($p['nama_pelatih']) ?></td>
                    <td>
                        <?php if ($p['status_upload'] == 'Diupload'): ?>
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Diupload</span>
                        <?php else: ?>
                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split"></i> Pending</span>
                        <?php endif; ?>
                    </td>
                    <td><?= $p['file_penilaian'] ? htmlspecialchars($p['file_penilaian']) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php if ($semua_diupload): ?>
        <a href="proses.php?id=<?= $id_ujian ?>&hitung=1" class="btn btn-info"><i class="bi bi-calculator"></i> Hitung Hasil Akhir</a>
    <?php else: ?>
        <button class="btn btn-secondary" disabled><i class="bi bi-calculator"></i> Hitung Hasil Akhir</button>
        <small class="text-muted ms-2">Semua penguji harus upload file terlebih dahulu</small>
    <?php endif; ?>
    <a href="index.php" class="btn btn-secondary ms-1">Kembali</a>
</div>

<!-- Preview Nilai (readonly) -->
<div class="content-wrapper">
    <h5 class="mb-3">Preview Nilai Peserta</h5>
    <div class="alert alert-secondary">
        <i class="bi bi-info-circle"></i> Data nilai bersifat <strong>readonly</strong>. Untuk mengubah/upload nilai gunakan form di atas.
    </div>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr class="text-center">
                    <th rowspan="2">No</th>
                    <th rowspan="2">Peserta</th>
                    <th rowspan="2">Sabuk</th>
                    <?php foreach ($penguji_data as $p): ?>
                        <th colspan="6">Penguji: <?= htmlspecialchars($p['nama_pelatih']) ?></th>
                    <?php endforeach; ?>
                </tr>
                <tr class="text-center">
                    <?php foreach ($penguji_data as $p): ?>
                        <th>Teori</th>
                        <th>Sejarah</th>
                        <th>Jurus</th>
                        <th>Mental</th>
                        <th>Agama</th>
                        <th style="background:#FAF0E6;">Rata2</th>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                while ($ps = mysqli_fetch_assoc($q_peserta)):
                ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
                        <td><?= htmlspecialchars($ps['nama_anggota']) ?><br><small class="text-muted"><?= htmlspecialchars($ps['no_kta']) ?></small></td>
                        <td><?= htmlspecialchars($ps['tingkatan_sabuk']) ?></td>
                        <?php foreach ($penguji_data as $p):
                            $existing = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM penilaian WHERE id_detail={$ps['id_detail']} AND id_pelatih={$p['id_pelatih']}"));
                        ?>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="<?= $existing['nilai_teori'] ?? '' ?>" readonly style="width:65px;background:var(--bg-body);"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="<?= $existing['nilai_sejarah'] ?? '' ?>" readonly style="width:65px;background:var(--bg-body);"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="<?= $existing['nilai_jurus'] ?? '' ?>" readonly style="width:65px;background:var(--bg-body);"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="<?= $existing['nilai_mental'] ?? '' ?>" readonly style="width:65px;background:var(--bg-body);"></td>
                            <td><input type="number" step="0.01" class="form-control form-control-sm" value="<?= $existing['nilai_agama'] ?? '' ?>" readonly style="width:65px;background:var(--bg-body);"></td>
                            <td style="background:#FAF0E6;">
                                <?php if ($existing && $existing['nilai_akhir']): ?>
                                    <?= number_format($existing['nilai_akhir'], 2) ?>
                                <?php else: ?>
                                    0.00
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include_once '../../includes/footer.php'; ?>