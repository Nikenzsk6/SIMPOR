<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
if ($_POST) {
    $no_kta         = mysqli_real_escape_string($koneksi, $_POST['no_kta']);
    $nama_pelatih   = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $tingkatan_sabuk = mysqli_real_escape_string($koneksi, $_POST['tingkatan_sabuk']);
    $kontak         = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $path_sertifikat = '';
    if ($_FILES['sertifikat']['name']) {
        $ext = pathinfo($_FILES['sertifikat']['name'], PATHINFO_EXTENSION);
        $path_sertifikat = 'uploads/sertifikat/' . time() . '.' . $ext;
        move_uploaded_file($_FILES['sertifikat']['tmp_name'], '../../' . $path_sertifikat);
    }
    $q = mysqli_query($koneksi, "INSERT INTO pelatih (no_kta, nama_pelatih, tingkatan_sabuk, kontak, alamat, path_sertifikat) 
        VALUES ('$no_kta', '$nama_pelatih', '$tingkatan_sabuk', '$kontak', '$alamat', '$path_sertifikat')");
    if ($q) {
        set_flash('Data berhasil disimpan');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Gagal: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah Pelatih</h4>
<div class="content-wrapper">
    <?php if (isset($error)) echo alert($error, 'danger'); ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No KTA</label>
                <input type="text" name="no_kta" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pelatih</label>
                <input type="text" name="nama_pelatih" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tingkatan Sabuk</label>
                <select name="tingkatan_sabuk" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option>Prapta</option>
                    <option>Kawi</option>
                    <option>Gladi Gatra</option>
                    <option>Gladi Taruna</option>
                    <option>Gladi Satya</option>
                    <option>Wira Tama</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">File Sertifikat</label>
                <input type="file" name="sertifikat" class="form-control">
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>