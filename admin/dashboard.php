<?php include_once '../includes/sidebar_admin.php'; ?>
<?php
$q1 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pelatih");
$q2 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM anggota");
$q3 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pendaftaran_ujian");
$q4 = mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM pengesahan WHERE status IN ('dikirim','diterima_pusat')");
$jml_pelatih = $q1 ? (int)mysqli_fetch_assoc($q1)['jml'] : 0;
$jml_anggota = $q2 ? (int)mysqli_fetch_assoc($q2)['jml'] : 0;
$jml_ujian   = $q3 ? (int)mysqli_fetch_assoc($q3)['jml'] : 0;
$jml_pending = $q4 ? (int)mysqli_fetch_assoc($q4)['jml'] : 0;
?>
<h4 class="page-title">Dashboard Admin</h4>
<div class="row g-4 mb-4">
    <div class="col-md-3">
        <div class="small-box bg-primary">
            <div class="inner">
                <h3><?= $jml_pelatih ?></h3>
                <p>Total Pelatih</p>
            </div>
            <div class="icon"><i class="bi bi-people"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $jml_anggota ?></h3>
                <p>Total Anggota</p>
            </div>
            <div class="icon"><i class="bi bi-person-badge"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $jml_ujian ?></h3>
                <p>Total Ujian</p>
            </div>
            <div class="icon"><i class="bi bi-journal-check"></i></div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="small-box bg-merah">
            <div class="inner">
                <h3><?= $jml_pending ?></h3>
                <p>Pending Pengesahan</p>
            </div>
            <div class="icon"><i class="bi bi-send-check"></i></div>
        </div>
    </div>
</div>
<div class="content-wrapper">
    <h5 class="fw-bold mb-3"><i class="bi bi-clock-history"></i> Ujian Terbaru</h5>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ujian</th>
                <th>Tanggal</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE tgl_ujian >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) ORDER BY tgl_ujian DESC LIMIT 5");
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= htmlspecialchars($d['tingkat_ujian']) ?></td>
                    <td><?= tgl_indonesia($d['tgl_ujian']) ?></td>
                    <td><?= status_label($d['status']) ?></td>
                    <td><a href="ujian/detail.php?id=<?= $d['id_pendaftaran'] ?>" class="btn btn-sm btn-info">Detail</a></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../includes/footer.php'; ?>