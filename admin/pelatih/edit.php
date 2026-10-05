<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id = (int)$_GET['id'];
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pelatih WHERE id_pelatih=$id"));
if (!$d) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

if ($_POST) {
    $no_kta         = mysqli_real_escape_string($koneksi, $_POST['no_kta']);
    $nama_pelatih   = mysqli_real_escape_string($koneksi, $_POST['nama_pelatih']);
    $tingkatan_sabuk = mysqli_real_escape_string($koneksi, $_POST['tingkatan_sabuk']);
    $kontak         = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $alamat         = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $path_sertifikat = $d['path_sertifikat'];
    if ($_FILES['sertifikat']['name']) {
        $ext = pathinfo($_FILES['sertifikat']['name'], PATHINFO_EXTENSION);
        $path_sertifikat = 'uploads/sertifikat/' . time() . '.' . $ext;
        move_uploaded_file($_FILES['sertifikat']['tmp_name'], '../../' . $path_sertifikat);
    }
    $q = mysqli_query($koneksi, "UPDATE pelatih SET 
        no_kta='$no_kta', nama_pelatih='$nama_pelatih', tingkatan_sabuk='$tingkatan_sabuk', 
        kontak='$kontak', alamat='$alamat', path_sertifikat='$path_sertifikat' 
        WHERE id_pelatih=$id");
    if ($q) {
        set_flash('Data berhasil diupdate');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Gagal: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Edit Pelatih</h4>
<div class="content-wrapper">
    <?php if (isset($error)) echo alert($error, 'danger'); ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No KTA</label>
                <input type="text" name="no_kta" class="form-control" value="<?= htmlspecialchars($d['no_kta']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pelatih</label>
                <input type="text" name="nama_pelatih" class="form-control" value="<?= htmlspecialchars($d['nama_pelatih']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tingkatan Sabuk</label>
                <select name="tingkatan_sabuk" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach (['Prapta', 'Kawi', 'Gladi Gatra', 'Gladi Taruna', 'Gladi Satya', 'Wira Tama'] as $s): ?>
                        <option <?= $d['tingkatan_sabuk'] == $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($d['kontak']) ?>">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat</label>
                <textarea name="alamat" class="form-control" rows="3"><?= htmlspecialchars($d['alamat']) ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">File Sertifikat</label>
                <input type="file" name="sertifikat" class="form-control">
                <?php if ($d['path_sertifikat']): ?>
                    <small class="text-muted">File saat ini: <?= $d['path_sertifikat'] ?></small>
                <?php endif; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>