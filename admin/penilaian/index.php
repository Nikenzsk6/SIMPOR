<?php include_once '../../includes/sidebar_admin.php'; ?>
<h4 class="page-title">Penilaian Ujian</h4>
<div class="content-wrapper">
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat Ujian</th>
                <th>Tgl Ujian</th>
                <th>Jml Penguji</th>
                <th>Jml Peserta</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT pu.*, 
                (SELECT COUNT(*) FROM penguji_ujian WHERE id_pendaftaran = pu.id_pendaftaran) as jml_penguji,
                (SELECT COUNT(*) FROM detail_pendaftaran WHERE id_pendaftaran = pu.id_pendaftaran) as jml_peserta
                FROM pendaftaran_ujian pu ORDER BY pu.id_pendaftaran DESC");
            $data = [];
            while ($row = mysqli_fetch_assoc($q)):
                if ($row['jml_peserta'] == 0) continue;
                $data[] = $row;
            endwhile;
            if (empty($data)): ?>
                <tr><td colspan="6" class="text-center text-muted py-4">Data masih kosong</td></tr>
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
                    <td><?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_ujian']) ?></td>
                    <td><?= $d['jml_penguji'] ?></td>
                    <td><?= $d['jml_peserta'] ?></td>
                    <td>
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