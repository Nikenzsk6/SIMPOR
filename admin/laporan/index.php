<?php include_once '../../includes/sidebar_admin.php'; ?>

<h4 class="page-title">Laporan</h4>

<div class="report-grid">
    <div class="report-card">
        <div class="report-ico"><i class="bi bi-file-earmark-pdf"></i></div>
        <h5>Laporan Hasil Ujian</h5>
        <p>Cetak PDF hasil ujian per periode ujian</p>
        <a href="cetak_hasil.php" class="btn btn-success" target="_blank">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>

    <div class="report-card">
        <div class="report-ico"><i class="bi bi-people"></i></div>
        <h5>Laporan Anggota</h5>
        <p>Cetak PDF daftar anggota beserta tingkatan sabuk</p>
        <a href="cetak_anggota.php" class="btn btn-success" target="_blank">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>

    <div class="report-card">
        <div class="report-ico"><i class="bi bi-person-video3"></i></div>
        <h5>Laporan Pelatih</h5>
        <p>Cetak PDF daftar pelatih yang sudah tersertifikasi</p>
        <a href="cetak_pelatih.php" class="btn btn-success" target="_blank">
            <i class="bi bi-printer"></i> Cetak Laporan
        </a>
    </div>
</div>

<?php include_once '../../includes/footer.php'; ?>
