<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
if ($_POST) {
    $no_kta         = mysqli_real_escape_string($koneksi, $_POST['no_kta']);
    $nama_anggota   = mysqli_real_escape_string($koneksi, $_POST['nama_anggota']);
    $tingkatan_sabuk = mysqli_real_escape_string($koneksi, $_POST['tingkatan_sabuk']);
    $kontak         = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $status         = auto_status_sabuk($tingkatan_sabuk);
    $q = mysqli_query($koneksi, "INSERT INTO anggota (no_kta, nama_anggota, tingkatan_sabuk, kontak, alamat, status) 
        VALUES ('$no_kta', '$nama_anggota', '$tingkatan_sabuk', '$kontak', '$alamat', '$status')");
    if ($q) {
        set_flash('Data berhasil disimpan');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Gagal: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah Anggota</h4>
<div class="content-wrapper">
    <?php if (isset($error)) echo alert($error, 'danger'); ?>
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No KTA</label>
                <input type="text" name="no_kta" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Anggota</label>
                <input type="text" name="nama_anggota" class="form-control" required>
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
                <small class="text-muted">Status (Warga/Non-Warga) otomatis ditentukan dari sabuk.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>