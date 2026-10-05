<?php include_once '../../includes/sidebar_admin.php'; ?>
<h4 class="page-title">Pengesahan Ujian</h4>
<div class="content-wrapper">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Ujian</th>
                <th>Peserta</th>
                <th>Status</th>
                <th>Tgl Kirim</th>
                <th>Catatan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
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
            if (mysqli_num_rows($q) == 0): ?>
                <tr>
                    <td colspan="7" class="text-center text-muted">Belum ada data pengesahan</td>
                </tr>
            <?php endif;
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['tingkat_ujian']) ?><br><small class="text-muted"><?= tgl_indonesia($d['tgl_ujian']) ?></small></td>
                    <td><?= $d['jml_lulus'] ?> / <?= $d['jml_peserta'] ?> lulus</td>
                    <td><?= $d['status'] ? status_label($d['status']) : '<span class="badge bg-secondary">Belum dikirim</span>' ?></td>
                    <td><?= $d['tgl_kirim'] ? tgl_indonesia_detik($d['tgl_kirim']) : '-' ?></td>
                    <td><?= htmlspecialchars($d['catatan'] ?? '-') ?></td>
                    <td>
                        <a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="btn btn-sm btn-outline-info"><i class="bi bi-eye"></i> Detail</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>