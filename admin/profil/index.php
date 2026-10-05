<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='cabang'"));
$id = $profil['id_profil'] ?? 0;

if ($_POST) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_organisasi']);
    $singkatan = mysqli_real_escape_string($koneksi, $_POST['singkatan']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    if ($id > 0) {
        mysqli_query($koneksi, "UPDATE profil SET nama_organisasi='$nama', singkatan='$singkatan', alamat='$alamat', kontak='$kontak' WHERE id_profil=$id");
    } else {
        mysqli_query($koneksi, "INSERT INTO profil (tipe, nama_organisasi, singkatan, alamat, kontak) VALUES ('cabang', '$nama', '$singkatan', '$alamat', '$kontak')");
        $id = mysqli_insert_id($koneksi);
    }
    echo alert('Profil cabang berhasil disimpan!');
    echo '<script>window.location="index.php";</script>';
}
?>
<h4 class="page-title">Profil Cabang</h4>
<div class="content-wrapper">
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Cabang</label>
                <input type="text" name="nama_organisasi" class="form-control" value="<?= htmlspecialchars($profil['nama_organisasi'] ?? '') ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Singkatan</label>
                <input type="text" name="singkatan" class="form-control" value="<?= htmlspecialchars($profil['singkatan'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($profil['kontak'] ?? '') ?>">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($profil['alamat'] ?? '') ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>