<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
// Hanya ujian yang sudah punya peserta
$q = mysqli_query($koneksi, "SELECT pu.*,
    (SELECT COUNT(*) FROM penguji_ujian WHERE id_pendaftaran = pu.id_pendaftaran) as jml_penguji,
    (SELECT COUNT(*) FROM detail_pendaftaran WHERE id_pendaftaran = pu.id_pendaftaran) as jml_peserta
    FROM pendaftaran_ujian pu ORDER BY pu.id_pendaftaran DESC");

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    if ($row['jml_peserta'] == 0) continue;
    $data[] = $row;
}
$total = count($data);
?>
<h4 class="page-title">Penilaian Ujian</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-clipboard-data"></i> Daftar Ujian Dinilai
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> ujian</span>
        </h5>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Tingkat Ujian</th>
                <th>Tgl Ujian</th>
                <th class="text-center">Jml Penguji</th>
                <th class="text-center">Jml Peserta</th>
                <th class="text-end" style="width:150px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($total == 0): ?>
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-clipboard-data"></i>
                            <span>Belum ada ujian yang punya peserta untuk dinilai</span>
                        </div>
                    </td>
                </tr>
            <?php endif;

            $no = 1;
            foreach ($data as $d):
                $id_ujian = $d['id_pendaftaran'];
                $ada_hasil = mysqli_fetch_assoc(mysqli_query($koneksi, "
                    SELECT COUNT(*) as jml FROM hasil_ujian h
                    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
                    WHERE dp.id_pendaftaran = $id_ujian
                "))['jml'];
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td class="fw-semibold"><?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_ujian']) ?></td>
                    <td class="text-center"><?= $d['jml_penguji'] ?></td>
                    <td class="text-center"><?= $d['jml_peserta'] ?></td>
                    <td class="text-end text-nowrap">
                        <?php if ($ada_hasil > 0): ?>
                            <a href="hasil.php?id=<?= $id_ujian ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i> Hasil</a>
                        <?php else: ?>
                            <a href="proses.php?id=<?= $id_ujian ?>" class="btn btn-sm btn-success"><i class="bi bi-pencil-square"></i> Input Nilai</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>
