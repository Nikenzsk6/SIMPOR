<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
// Auto close registration if past tgl_tutup (logika asli, tidak diubah)
mysqli_query($koneksi, "UPDATE pendaftaran_ujian SET status='ditutup' WHERE tgl_tutup < CURDATE() AND status='dibuka'");

$q = mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian ORDER BY id_pendaftaran DESC");
$total = $q ? mysqli_num_rows($q) : 0;
?>
<h4 class="page-title">Data Ujian</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-journal-check"></i> Daftar Ujian
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> ujian</span>
        </h5>
        <div class="panel-actions">
            <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Ujian</a>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Tingkat Ujian</th>
                <th>Tgl Buka</th>
                <th>Tgl Tutup</th>
                <th>Tgl Ujian</th>
                <th>Status</th>
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
                            <span>Belum ada ujian yang terdaftar</span>
                        </div>
                    </td>
                </tr>
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
                    <td class="text-end text-nowrap">
                        <a href="detail.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-info" title="Detail"><i class="bi bi-eye"></i></a>
                        <a href="hapus.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus? Semua data terkait akan ikut terhapus.')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>