<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
if ($_POST) {
    $tingkat_ujian = mysqli_real_escape_string($koneksi, $_POST['tingkat_ujian']);
    $tgl_buka      = mysqli_real_escape_string($koneksi, $_POST['tgl_buka']);
    $tgl_tutup     = mysqli_real_escape_string($koneksi, $_POST['tgl_tutup']);
    $tgl_ujian     = mysqli_real_escape_string($koneksi, $_POST['tgl_ujian']);
    $status        = (date('Y-m-d') >= $tgl_buka && date('Y-m-d') <= $tgl_tutup) ? 'dibuka' : 'ditutup';
    $q = mysqli_query($koneksi, "INSERT INTO pendaftaran_ujian (tingkat_ujian, tgl_buka, tgl_tutup, tgl_ujian, status) 
        VALUES ('$tingkat_ujian', '$tgl_buka', '$tgl_tutup', '$tgl_ujian', '$status')");
    if ($q) {
        $id_baru = mysqli_insert_id($koneksi);
        set_flash('Data berhasil disimpan');
        header('Location: detail.php?id=' . $id_baru);
        exit;
    } else {
        $error = 'Gagal: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah Ujian</h4>
<div class="content-wrapper">
    <?php if (isset($error)) echo alert($error, 'danger'); ?>
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tingkat Ujian</label>
                <select name="tingkat_ujian" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option>Prapta ke Kawi</option>
                    <option>Kawi ke Gladi Gatra</option>
                    <option>Gladi Gatra ke Gladi Taruna</option>
                    <option>Gladi Taruna ke Gladi Satya</option>
                    <option>Gladi Satya ke Wira Tama</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Buka Pendaftaran</label>
                <input type="date" name="tgl_buka" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Tutup Pendaftaran</label>
                <input type="date" name="tgl_tutup" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Ujian</label>
                <input type="date" name="tgl_ujian" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>