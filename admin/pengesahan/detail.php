<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id = (int)$_GET['id'];
$p = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT ps.*, h.status_kelulusan, h.rata_rata_nilai, h.id_detail,
           dp.id_pendaftaran, pu.nama_ujian, pu.tingkat_ujian, pu.tgl_ujian, pu.tgl_buka, pu.tgl_tutup
    FROM pengesahan ps
    JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
    WHERE ps.id_pengesahan = $id
"));
if (!$p) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

// Get all participants in this exam with their scores
$q_peserta = mysqli_query($koneksi, "
    SELECT a.nama_anggota, a.no_kta, a.tingkatan_sabuk, 
           h.rata_rata_nilai, h.status_kelulusan
    FROM detail_pendaftaran dp
    JOIN anggota a ON dp.id_anggota = a.id_anggota
    LEFT JOIN hasil_ujian h ON dp.id_detail = h.id_detail
    WHERE dp.id_pendaftaran = {$p['id_pendaftaran']}
    ORDER BY a.nama_anggota
");

// Get examiners
$q_penguji = mysqli_query($koneksi, "
    SELECT p.nama_pelatih, p.tingkatan_sabuk 
    FROM penguji_ujian pu
    JOIN pelatih p ON pu.id_pelatih = p.id_pelatih
    WHERE pu.id_pendaftaran = {$p['id_pendaftaran']}
");
?>
<h4 class="page-title">Detail Pengesahan</h4>
<div class="content-wrapper">
    <a href="index.php" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

    <div class="card border-0 bg-gading p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Ujian:</strong> <?= htmlspecialchars($p['nama_ujian'] ?: $p['tingkat_ujian']) ?></p>
                <p><strong>Tingkat Ujian:</strong> <?= htmlspecialchars($p['tingkat_ujian']) ?></p>
                <p><strong>Tanggal Ujian:</strong> <?= tgl_indonesia($p['tgl_ujian']) ?></p>
                <p><strong>Periode Pendaftaran:</strong> <?= tgl_indonesia($p['tgl_buka']) ?> s.d. <?= tgl_indonesia($p['tgl_tutup']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> <?= status_label($p['status']) ?></p>
                <p><strong>Dikirim:</strong> <?= $p['tgl_kirim'] ? tgl_indonesia_detik($p['tgl_kirim']) : '-' ?></p>
                <?php if ($p['tgl_diterima']): ?>
                    <p><strong>Diterima Pusat:</strong> <?= tgl_indonesia_detik($p['tgl_diterima']) ?></p>
                <?php endif; ?>
                <?php if ($p['tgl_disetujui']): ?>
                    <p><strong><?= $p['status'] == 'disetujui' ? 'Disetujui' : 'Ditolak' ?>:</strong> <?= tgl_indonesia_detik($p['tgl_disetujui']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        <?php if ($p['catatan']): ?>
            <div class="mt-2"><strong>Catatan:</strong> <?= htmlspecialchars($p['catatan']) ?></div>
        <?php endif; ?>
    </div>

    <?php if ($p['file_pdf']): ?>
        <?php $file_pdf_ada = is_file(dirname(__DIR__, 2) . '/' . ltrim($p['file_pdf'], '/')); ?>
        <div class="mb-3">
            <strong>Lampiran PDF:</strong>
            <?php if ($file_pdf_ada): ?>
                <a href="<?= BASE_URL . htmlspecialchars($p['file_pdf']) ?>" class="btn btn-sm btn-info" target="_blank"><i class="bi bi-file-pdf"></i> Lihat PDF</a>
                <a href="<?= BASE_URL . htmlspecialchars($p['file_pdf']) ?>" class="btn btn-sm btn-outline-info" download><i class="bi bi-download"></i> Download</a>
            <?php else: ?>
                <span class="text-muted"><i class="bi bi-exclamation-circle"></i> File PDF tidak tersedia (data contoh).</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold mt-4">Daftar Peserta</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No KTA</th>
                <th>Nama</th>
                <th>Sabuk</th>
                <th>Nilai Akhir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1;
            while ($psr = mysqli_fetch_assoc($q_peserta)): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($psr['no_kta']) ?></td>
                    <td><?= htmlspecialchars($psr['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($psr['tingkatan_sabuk']) ?></td>
                    <td class="text-center"><?= $psr['rata_rata_nilai'] ? number_format($psr['rata_rata_nilai'], 2) : '-' ?></td>
                    <td><?= $psr['status_kelulusan'] ? status_label($psr['status_kelulusan']) : '-' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h5 class="fw-bold mt-4">Penguji</h5>
    <ul>
        <?php while ($pg = mysqli_fetch_assoc($q_penguji)): ?>
            <li><?= htmlspecialchars($pg['nama_pelatih']) ?> (<?= htmlspecialchars($pg['tingkatan_sabuk']) ?>)</li>
        <?php endwhile; ?>
    </ul>
</div>
<?php include_once '../../includes/footer.php'; ?>