<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$q = mysqli_query($koneksi, "
    SELECT ps.*, pu.tingkat_ujian, pu.tgl_ujian, pu.id_pendaftaran,
        (SELECT COUNT(*) FROM hasil_ujian h
         JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
         WHERE dp.id_pendaftaran = pu.id_pendaftaran) as jml_peserta,
        (SELECT COUNT(*) FROM hasil_ujian h
         JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
         WHERE dp.id_pendaftaran = pu.id_pendaftaran AND h.status_kelulusan='LULUS') as jml_lulus
    FROM pengesahan ps
    JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
    ORDER BY ps.tgl_kirim DESC
");
$total = $q ? mysqli_num_rows($q) : 0;
?>
<h4 class="page-title">Pengesahan Ujian</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-send-check"></i> Riwayat Pengesahan
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> pengajuan</span>
        </h5>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Ujian</th>
                <th>Peserta</th>
                <th>Status</th>
                <th>Tgl Kirim</th>
                <th>Catatan</th>
                <th class="text-end" style="width:96px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($total == 0): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <span>Belum ada data pengesahan</span>
                        </div>
                    </td>
                </tr>
            <?php endif;
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <div class="fw-semibold"><?= htmlspecialchars($d['tingkat_ujian']) ?></div>
                        <small class="text-muted"><?= tgl_indonesia($d['tgl_ujian']) ?></small>
                    </td>
                    <td><?= $d['jml_lulus'] ?> / <?= $d['jml_peserta'] ?> lulus</td>
                    <td><?= $d['status'] ? status_label($d['status']) : '<span class="badge bg-secondary">Belum dikirim</span>' ?></td>
                    <td><?= $d['tgl_kirim'] ? tgl_indonesia_detik($d['tgl_kirim']) : '-' ?></td>
                    <td><small><?= htmlspecialchars($d['catatan'] ?? '-') ?></small></td>
                    <td class="text-end text-nowrap">
                        <a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="btn btn-sm btn-info" title="Detail"><i class="bi bi-eye"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>