<?php include_once '../includes/sidebar_pusat.php'; ?>
<?php
$id = (int)$_GET['id'];
$ps = mysqli_fetch_assoc(mysqli_query($koneksi, "
    SELECT ps.*, h.id_detail, dp.id_pendaftaran, pu.nama_ujian, pu.tingkat_ujian, pu.tgl_ujian
    FROM pengesahan ps
    JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
    WHERE ps.id_pengesahan = $id
"));
if (!$ps) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: inbox.php');
    exit;
}

// Handle Terima
if (isset($_GET['terima'])) {
    if ($ps['status'] !== 'dikirim') {
        set_flash('Pengesahan ini sudah diproses sebelumnya.', 'danger');
        header('Location: detail.php?id=' . $id);
        exit;
    }
    $id_ujian = (int) $ps['id_pendaftaran'];
    mysqli_query($koneksi, "
        UPDATE pengesahan ps
        JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        SET ps.status='diterima_pusat', ps.tgl_diterima=NOW()
        WHERE dp.id_pendaftaran=$id_ujian AND ps.status='dikirim'
    ");
    set_flash('Pengesahan diterima. Silakan periksa lalu setujui atau tolak.');
    header('Location: detail.php?id=' . $id);
    exit;
}

// Handle Setujui
if (isset($_GET['setujui'])) {
    if ($ps['status'] !== 'diterima_pusat') {
        set_flash('Pengesahan harus diterima terlebih dahulu sebelum disetujui.', 'danger');
        header('Location: detail.php?id=' . $id);
        exit;
    }
    $id_ujian = (int) $ps['id_pendaftaran'];

    // Get all participants who LULUS in this exam
    $q_lulus = mysqli_query($koneksi, "
        SELECT dp.id_anggota, a.tingkatan_sabuk, pu.tingkat_ujian
        FROM hasil_ujian h
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
        JOIN anggota a ON dp.id_anggota = a.id_anggota
        WHERE dp.id_pendaftaran = $id_ujian AND h.status_kelulusan = 'LULUS'
    ");

    while ($l = mysqli_fetch_assoc($q_lulus)) {
        // Parse target belt from tingkat_ujian (e.g., "Prapta ke Kawi")
        $parts = explode(' ke ', $l['tingkat_ujian']);
        $sabuk_baru = isset($parts[1]) ? trim($parts[1]) : $l['tingkatan_sabuk'];

        // Update anggota's belt and auto-set status
        $status_baru = auto_status_sabuk($sabuk_baru);
        $sabuk_esc = mysqli_real_escape_string($koneksi, $sabuk_baru);
        mysqli_query($koneksi, "UPDATE anggota SET tingkatan_sabuk='$sabuk_esc', status='$status_baru' WHERE id_anggota={$l['id_anggota']}");
    }

    $id_pusat = (int) $data_user['id_user'];
    mysqli_query($koneksi, "
        UPDATE pengesahan ps
        JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        SET ps.status='disetujui', ps.tgl_disetujui=NOW(), ps.id_user_pusat=$id_pusat
        WHERE dp.id_pendaftaran=$id_ujian
    ");
    set_flash('Pengesahan disetujui! Sabuk peserta lulus otomatis ter-upgrade.');
    header('Location: detail.php?id=' . $id);
    exit;
}

// Handle Tolak (with reason)
if ($_POST && isset($_POST['tolak'])) {
    if ($ps['status'] !== 'diterima_pusat') {
        set_flash('Pengesahan harus diterima terlebih dahulu sebelum ditolak.', 'danger');
        header('Location: detail.php?id=' . $id);
        exit;
    }
    $id_ujian = (int) $ps['id_pendaftaran'];
    $catatan = mysqli_real_escape_string($koneksi, $_POST['catatan']);
    $id_pusat = (int) $data_user['id_user'];
    mysqli_query($koneksi, "
        UPDATE pengesahan ps
        JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        SET ps.status='ditolak', ps.tgl_disetujui=NOW(), ps.id_user_pusat=$id_pusat, ps.catatan='$catatan'
        WHERE dp.id_pendaftaran=$id_ujian
    ");
    set_flash('Pengesahan ditolak.', 'danger');
    header('Location: detail.php?id=' . $id);
    exit;
}

// Get list of passed participants
$q_peserta = mysqli_query($koneksi, "
    SELECT a.nama_anggota, a.no_kta, a.tingkatan_sabuk, h.rata_rata_nilai
    FROM hasil_ujian h
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN anggota a ON dp.id_anggota = a.id_anggota
    WHERE dp.id_pendaftaran = {$ps['id_pendaftaran']} AND h.status_kelulusan = 'LULUS'
");

// Get examiners
$q_penguji = mysqli_query($koneksi, "
    SELECT p.nama_pelatih, p.tingkatan_sabuk FROM penguji_ujian pu
    JOIN pelatih p ON pu.id_pelatih = p.id_pelatih
    WHERE pu.id_pendaftaran = {$ps['id_pendaftaran']}
");
?>
<h4 class="page-title"><i class="bi bi-envelope-open"></i> Detail Pengesahan</h4>

<div class="content-wrapper mb-4">
    <a href="inbox.php" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i> Kembali ke Inbox</a>

    <div class="card border-0 bg-gading p-4 mb-4">
        <div class="row">
            <div class="col-md-6">
                <?php $cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'")); ?>
                <p><strong>Dari:</strong> <?= htmlspecialchars($cabang['nama_organisasi'] ?? 'Admin Cabang') ?></p>
                <p><strong>Perihal:</strong> Pengesahan Hasil Ujian <?= htmlspecialchars($ps['nama_ujian'] ?: $ps['tingkat_ujian']) ?></p>
                <p><strong>Tingkat:</strong> <?= htmlspecialchars($ps['tingkat_ujian']) ?></p>
                <p><strong>Tanggal Ujian:</strong> <?= tgl_indonesia($ps['tgl_ujian']) ?></p>
                <p><strong>Dikirim:</strong> <?= tgl_indonesia_detik($ps['tgl_kirim']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Status:</strong> <?= status_label($ps['status']) ?></p>
                <?php if ($ps['tgl_diterima']): ?>
                    <p><strong>Diterima:</strong> <?= tgl_indonesia_detik($ps['tgl_diterima']) ?></p>
                <?php endif; ?>
                <?php if ($ps['tgl_disetujui']): ?>
                    <p><strong><?= $ps['status'] == 'disetujui' ? 'Disetujui' : 'Ditolak' ?>:</strong> <?= tgl_indonesia_detik($ps['tgl_disetujui']) ?></p>
                <?php endif; ?>
                <?php if ($ps['catatan']): ?>
                    <p><strong>Catatan:</strong> <?= htmlspecialchars($ps['catatan']) ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($ps['file_pdf']): ?>
        <?php $file_pdf_ada = is_file(dirname(__DIR__, 1) . '/' . ltrim($ps['file_pdf'], '/')); ?>
        <div class="mb-3">
            <strong><i class="bi bi-paperclip"></i> Lampiran PDF:</strong>
            <?php if ($file_pdf_ada): ?>
                <a href="<?= BASE_URL . htmlspecialchars($ps['file_pdf']) ?>" class="btn btn-sm btn-info" target="_blank"><i class="bi bi-file-pdf"></i> Lihat PDF</a>
                <a href="<?= BASE_URL . htmlspecialchars($ps['file_pdf']) ?>" class="btn btn-sm btn-outline-info" download><i class="bi bi-download"></i> Download</a>
            <?php else: ?>
                <span class="text-muted"><i class="bi bi-exclamation-circle"></i> File PDF tidak tersedia (data contoh).</span>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <h5 class="fw-bold mt-4"><i class="bi bi-list-check"></i> Daftar Peserta LULUS</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>No KTA</th>
                <th>Nama</th>
                <th>Sabuk Awal</th>
                <th>Sabuk Baru</th>
                <th>Nilai</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            $parts = explode(' ke ', $ps['tingkat_ujian']);
            $sabuk_baru = isset($parts[1]) ? trim($parts[1]) : '-';
            while ($psr = mysqli_fetch_assoc($q_peserta)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($psr['no_kta']) ?></td>
                    <td><?= htmlspecialchars($psr['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($psr['tingkatan_sabuk']) ?></td>
                    <td><strong><?= $sabuk_baru ?></strong></td>
                    <td><?= number_format($psr['rata_rata_nilai'], 2) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h5 class="fw-bold mt-4"><i class="bi bi-person-badge"></i> Penguji</h5>
    <ul>
        <?php while ($pg = mysqli_fetch_assoc($q_penguji)): ?>
            <li><?= htmlspecialchars($pg['nama_pelatih']) ?> (<?= htmlspecialchars($pg['tingkatan_sabuk']) ?>)</li>
        <?php endwhile; ?>
    </ul>

    <hr class="my-4">

    <?php if ($ps['status'] == 'dikirim'): ?>
        <div class="d-flex gap-3">
            <a href="?id=<?= $id ?>&terima=1" class="btn btn-info text-white" onclick="return confirm('Terima pengesahan ini?')"><i class="bi bi-envelope-open"></i> Terima</a>
        </div>
    <?php elseif ($ps['status'] == 'diterima_pusat'): ?>
        <div class="d-flex gap-3">
            <a href="?id=<?= $id ?>&setujui=1" class="btn btn-approve" onclick="return confirm('Setujui pengesahan ini? Sabuk peserta akan otomatis ter-upgrade.')"><i class="bi bi-check-circle"></i> Setujui</a>
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#modalTolak"><i class="bi bi-x-circle"></i> Tolak</button>
        </div>

        <!-- Modal Tolak -->
        <div class="modal fade" id="modalTolak" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form method="post">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">Tolak Pengesahan</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Alasan Penolakan</label>
                                <textarea name="catatan" class="form-control" rows="4" required placeholder="Masukkan alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="tolak" class="btn btn-danger">Ya, Tolak</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php elseif ($ps['status'] == 'disetujui'): ?>
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill"></i> Pengesahan ini sudah <strong>DISETUJUI</strong> pada <?= tgl_indonesia_detik($ps['tgl_disetujui']) ?>.
            Sabuk peserta telah otomatis ter-upgrade.
        </div>
    <?php elseif ($ps['status'] == 'ditolak'): ?>
        <div class="alert alert-danger">
            <i class="bi bi-x-circle-fill"></i> Pengesahan ini <strong>DITOLAK</strong> pada <?= tgl_indonesia_detik($ps['tgl_disetujui']) ?>.
            <?php if ($ps['catatan']): ?><br>Alasan: <?= htmlspecialchars($ps['catatan']) ?><?php endif; ?>
        </div>
    <?php endif; ?>
</div>
<?php include_once '../includes/footer.php'; ?>