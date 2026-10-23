<?php include_once '../../includes/sidebar_pusat.php'; ?>
<?php
if ($_POST) {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_user']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $kontak = mysqli_real_escape_string($koneksi, $_POST['kontak']);
    $password = hash_password($_POST['password']);
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM user WHERE username='$username'"));
    if ($cek['jml'] > 0) {
        echo alert('Username sudah digunakan!', 'danger');
    } else {
        mysqli_query($koneksi, "INSERT INTO user (nama_user, kontak, username, password, role) VALUES ('$nama', '$kontak', '$username', '$password', 'pusat')");
        set_flash('User pusat berhasil ditambahkan!');
        header('Location: index.php');
        exit;
    }
}
?>
<h4 class="page-title">Tambah User Pusat</h4>
<div class="content-wrapper">
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama User</label>
                <input type="text" name="nama_user" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak</label>
                <input type="text" name="kontak" class="form-control">
            </div>
            <div class="col-md-12 mb-3">
                <p class="mb-0"><strong>Tipe Pengguna :</strong> Admin Pusat</p>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>