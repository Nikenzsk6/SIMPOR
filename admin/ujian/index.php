<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
// Auto close registration if past tgl_tutup
mysqli_query($koneksi, "UPDATE pendaftaran_ujian SET status='ditutup' WHERE tgl_tutup < CURDATE() AND status='dibuka'");
?>
<h4 class="page-title">Data Ujian</h4>
<div class="content-wrapper">
    <a href="tambah.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Tambah Ujian</a>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Tingkat Ujian</th>
                <th>Tgl Buka</th>
                <th>Tgl Tutup</th>
                <th>Tgl Ujian</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian ORDER BY id_pendaftaran DESC");
            if (mysqli_num_rows($q) == 0): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Data masih kosong</td></tr>
            <?php endif;
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_buka']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_tutup']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_ujian']) ?></td>
                    <td><?= status_label($d['status']) ?></td>
                    <td>
                        <a href="detail.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                        <a href="hapus.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus? Semua data terkait akan ikut terhapus.')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>