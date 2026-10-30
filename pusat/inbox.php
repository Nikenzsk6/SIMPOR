<?php
include_once '../includes/sidebar_pusat.php'; ?>
<?php
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'semua';
$diizinkan = ['dikirim', 'diterima_pusat', 'disetujui', 'ditolak'];

$having = '';
if (in_array($filter, $diizinkan, true)) {
    $having = "HAVING MIN(ps.status)='$filter'";
}

$q = mysqli_query($koneksi, "
    SELECT pu.id_pendaftaran, pu.nama_ujian, pu.tingkat_ujian,
        MIN(ps.id_pengesahan) AS id_pengesahan,
        MIN(ps.status) AS status,
        MAX(ps.tgl_kirim) AS tgl_kirim,
        (SELECT COUNT(*) FROM hasil_ujian h2
         JOIN detail_pendaftaran dp2 ON h2.id_detail=dp2.id_detail
         WHERE dp2.id_pendaftaran=pu.id_pendaftaran AND h2.status_kelulusan='LULUS') as jml_lulus
    FROM pengesahan ps
    JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    JOIN pendaftaran_ujian pu ON dp.id_pendaftaran = pu.id_pendaftaran
    GROUP BY pu.id_pendaftaran, pu.nama_ujian, pu.tingkat_ujian
    $having
    ORDER BY MAX(ps.tgl_kirim) DESC
");
$total = $q ? mysqli_num_rows($q) : 0;

$q_badge = mysqli_query($koneksi, "
    SELECT COUNT(DISTINCT dp.id_pendaftaran) as j
    FROM pengesahan ps
    JOIN hasil_ujian h ON ps.id_hasil = h.id_hasil
    JOIN detail_pendaftaran dp ON h.id_detail = dp.id_detail
    WHERE ps.status='dikirim'
");
$j_badge = (int) mysqli_fetch_assoc($q_badge)['j'];

$cabang = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
$nama_cabang = htmlspecialchars($cabang['nama_organisasi'] ?? 'Cabang');
?>

<h4 class="page-title">Inbox Pengesahan</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-envelope"></i> Kotak Masuk
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> pengajuan</span>
        </h5>
    </div>

    <ul class="nav nav-tabs px-3 pt-3 mb-0">
        <li class="nav-item"><a class="nav-link <?= $filter == 'semua' ? 'active' : '' ?>" href="?filter=semua">Semua</a></li>
        <li class="nav-item">
            <a class="nav-link <?= $filter == 'dikirim' ? 'active' : '' ?>" href="?filter=dikirim">
                Baru Dikirim
                <?php if ($j_badge > 0): ?><span class="badge bg-danger ms-1"><?= $j_badge ?></span><?php endif; ?>
            </a>
        </li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'diterima_pusat' ? 'active' : '' ?>" href="?filter=diterima_pusat">Diterima</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'disetujui' ? 'active' : '' ?>" href="?filter=disetujui">Disetujui</a></li>
        <li class="nav-item"><a class="nav-link <?= $filter == 'ditolak' ? 'active' : '' ?>" href="?filter=ditolak">Ditolak</a></li>
    </ul>

    <div class="panel-body">
        <?php if ($total == 0): ?>
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                <span>Tidak ada pengesahan pada filter ini</span>
            </div>
        <?php endif; ?>

        <?php
        $ikon = [
            'dikirim'        => 'bi-envelope-exclamation',
            'disetujui'      => 'bi-check-circle',
            'ditolak'        => 'bi-x-circle',
            'diterima_pusat' => 'bi-inbox',
        ];
        while ($d = mysqli_fetch_assoc($q)):
            $is_unread = ($d['status'] == 'dikirim') ? 'unread' : '';
            $ico = $ikon[$d['status']] ?? 'bi-envelope';
        ?>
            <a href="detail.php?id=<?= $d['id_pengesahan'] ?>" class="text-decoration-none">
                <div class="inbox-item <?= $is_unread ?> p-3 mb-2">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <div class="inbox-icon"><i class="bi <?= $ico ?>"></i></div>
                        </div>
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <strong><?= $nama_cabang ?></strong>
                                <small class="text-muted"><?= tgl_indonesia_detik($d['tgl_kirim']) ?></small>
                            </div>
                            <div class="text-body">Pengesahan Ujian <?= htmlspecialchars($d['nama_ujian'] ?: $d['tingkat_ujian']) ?></div>
                            <small class="text-muted"><?= (int) $d['jml_lulus'] ?> peserta lulus</small>
                        </div>
                        <div class="col-auto"><?= status_label($d['status']) ?></div>
                    </div>
                </div>
            </a>
        <?php endwhile; ?>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
