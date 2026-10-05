<?php include_once '../../includes/sidebar_admin.php'; ?>
<h4 class="page-title">Laporan</h4>
<div class="row g-4">
    <div class="col-md-4">
        <div class="card card-dashboard">
            <div class="card-body text-center">
                <i class="bi bi-file-pdf fs-1 text-merah"></i>
                <h5 class="mt-2">Laporan Hasil Ujian</h5>
                <p class="text-muted small">Cetak PDF hasil ujian per periode</p>
                <a href="cetak_hasil.php" class="btn btn-info btn-sm" target="_blank">Cetak</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard">
            <div class="card-body text-center">
                <i class="bi bi-people fs-1 text-merah"></i>
                <h5 class="mt-2">Laporan Anggota</h5>
                <p class="text-muted small">Cetak PDF daftar anggota per sabuk</p>
                <a href="cetak_anggota.php" class="btn btn-info btn-sm" target="_blank">Cetak</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-dashboard">
            <div class="card-body text-center">
                <i class="bi bi-person-video fs-1 text-merah"></i>
                <h5 class="mt-2">Laporan Pelatih</h5>
                <p class="text-muted small">Cetak PDF daftar pelatih</p>
                <a href="cetak_pelatih.php" class="btn btn-info btn-sm" target="_blank">Cetak</a>
            </div>
        </div>
    </div>
</div>
<?php include_once '../../includes/footer.php'; ?>