<?php
include_once '../includes/sidebar_pusat.php'; ?>
<?php
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';

$where = '';
if ($filter == 'dikirim') $where = "WHERE ps.status='dikirim'";
elseif ($filter == 'diterima_pusat') $where = "WHERE ps.status='diterima_pusat'";
elseif ($filter == 'disetujui') $where = "WHERE ps.status='disetujui'";
elseif ($filter == 'ditolak') $where = "WHERE ps.status='ditolak'";
?>

<h4 class="page-title">Inbox Pengesahan</h4>

<div class="content-wrapper">
    <ul class="nav nav-tabs mb-3">
        <li class="nav-item"><a class="nav-link <?= $filter == 'semua' ? 'active' : '' ?>" href="?filter=semua">Semua</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'dikirim' ? 'active' : '' ?>" href="?filter=dikirim">Baru Dikirim
                <?php
                $q_badge = mysqli_query($koneksi, "SELECT COUNT(*) as j FROM pengesahan WHERE status='dikirim'");
                $j_badge = mysqli_fetch_assoc($q_badge)['j'];
                if ($j_badge > 0) echo '<span class="badge bg-danger ms-1">' . $j_badge . '</span>';
                ?>
            </a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'diterima_pusat' ? 'active' : '' ?>" href="?filter=diterima_pusat">Diterima</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'disetujui' ? 'active' : '' ?>" href="?filter=disetujui">Disetujui</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'ditolak' ? 'active' : '' ?>" href="?filter=ditolak">Ditolak</a></li>
    </ul>

    <?php
    $q = mysqli_query($koneksi, "
        SELECT ps.*, pu.tingkat_ujian,
            (SELECT COUNT(*) FROM hasil_ujian h2
             JOIN detail_pendaftaran dp2 ON h2.id_detail=dp2.id_detail
             WHERE dp2.id_pendaftaran=pu.id_pendaftaran AND h2.status_kelulusan='LULUS') as jml_lulus
        FROM pengesahan ps
        JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
        JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
        JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
        $where
        ORDER BY ps.tgl_kirim DESC
    ");

    $cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
    $nama_cabang = htmlspecialchars($cabang['nama_organisasi'] ?? 'Cabang');

    if (mysqli_num_rows($q) == 0): ?>
        <div class="text-center text-muted py-5">
            <i class="bi bi-inbox fs-1"></i>
            <p class="mt-2">Tidak ada pengesahan</p>
        </div>
    <?php endif;

    while ($d = mysqli_fetch_assoc($q)):
        $is_unread = ($d['status'] == 'dikirim') ? 'unread' : '';
        $icon = $d['status'] == 'dikirim' ? '📩' : ($d['status'] == 'disetujui' ? '✅' : ($d['status'] == 'ditolak' ? '❌' : '📨'));
    ?>
        <a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="text-decoration-none text-dark">
            <div class="inbox-item <?= $is_unread ?> p-3 mb-2 border rounded">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="inbox-icon"><?= $icon ?></div>
                    </div>
                    <div class="col">
                        <div class="d-flex justify-content-between">
                            <strong><?= $nama_cabang ?></strong>
                            <small class="text-muted"><?= tgl_indonesia_detik($d['tgl_kirim']) ?></small>
                        </div>
                        <div>Pengesahan Ujian <?= htmlspecialchars($d['tingkat_ujian']) ?></div>
                        <small class="text-muted"><?= $d['jml_lulus'] ?> peserta lulus</small>
                    </div>
                    <div class="col-auto"><?= status_label($d['status']) ?></div>
                </div>
            </div>
        </a>
    <?php endwhile; ?>
</div>

<?php include_once '../includes/footer.php'; ?>