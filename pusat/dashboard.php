<?php include_once '../includes/sidebar_pusat.php'; ?>
<?php
$jml_dikirim   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='dikirim'"))['jml'];
$jml_disetujui = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='disetujui'"))['jml'];
$jml_ditolak   = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status='ditolak'"))['jml'];
$jml_total     = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan"))['jml'];
?>
<h4 class="page-title">Dashboard Pusat</h4>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $jml_dikirim ?></h3>
                <p>Baru Dikirim</p>
            </div>
            <div class="icon"><i class="bi bi-envelope"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $jml_disetujui ?></h3>
                <p>Disetujui</p>
            </div>
            <div class="icon"><i class="bi bi-check-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3><?= $jml_ditolak ?></h3>
                <p>Ditolak</p>
            </div>
            <div class="icon"><i class="bi bi-x-circle"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-secondary">
            <div class="inner">
                <h3><?= $jml_total ?></h3>
                <p>Total Pengesahan</p>
            </div>
            <div class="icon"><i class="bi bi-inboxes"></i></div>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Pengajuan Terbaru</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Cabang</th>
                <th>Perihal</th>
                <th>Status</th>
                <th>Tanggal Kirim</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "
                SELECT p.*, h.status_kelulusan, dp.id_pendaftaran, pu.tingkat_ujian
                FROM pengesahan p
                LEFT JOIN hasil_ujian h ON p.id_hasil = h.id_hasil
                LEFT JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
                LEFT JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
                ORDER BY p.tgl_kirim DESC LIMIT 5
            ");
            $cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
            $nama_cabang = htmlspecialchars($cabang['nama_organisasi'] ?? 'Cabang');
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= htmlspecialchars($cabang['nama_organisasi'] ?? 'Cabang') ?></td>
                    <td>Ujian <?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                    <td><?= status_label($d['status']) ?></td>
                    <td><?= tgl_indonesia_detik($d['tgl_kirim']) ?></td>
                    <td><a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="btn btn-sm btn-info">Detail</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../includes/footer.php'; ?>