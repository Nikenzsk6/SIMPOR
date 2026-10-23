<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id = (int)$_GET['id'];
$d = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM user WHERE id_user=$id AND role='admin'"));
if (!$d) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

if ($_POST) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_user']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $q = "UPDATE user SET nama_user='$nama', kontak='$kontak', username='$username'";
    if (!empty($_POST['password'])) {
        $q .= ", password='" . hash_password($_POST['password']) . "'";
    }
    $q .= " WHERE id_user=$id";
    mysqli_query($koneksi, $q);
    set_flash('User berhasil diupdate!');
    header('Location: index.php');
    exit;
}
?>
<h4 class="page-title">Edit User Admin</h4>
<div class="content-wrapper">
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama User</label>
                <input type="text" name="nama_user" class="form-control" value="<?= htmlspecialchars($d['nama_user']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($d['username']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password (kosongkan jika tidak diubah)</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($d['kontak'] ?? '') ?>">
            </div>
            <div class="col-md-12 mb-3">
                <p class="mb-0"><strong>Tipe Pengguna :</strong> Admin Cabang</p>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>