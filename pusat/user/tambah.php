<?php include_once '../../includes/sidebar_pusat.php'; ?>
<?php
$errors = [];
$old = [
    'nama_user' => input('nama_user'),
    'username'  => input('username'),
    'kontak'    => input('kontak'),
    'role'      => input('role'),
    'status'    => input('status', 'aktif'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($old['username'] === '') {
        $errors[] = 'Username wajib diisi.';
    } elseif (!validasi_username($old['username'])) {
        $errors[] = 'Username hanya boleh huruf/angka, tanpa spasi atau karakter khusus, dan tidak boleh hanya angka.';
    }

    if (input('password') === '') {
        $errors[] = 'Password wajib diisi.';
    } elseif (!validasi_password($_POST['password'])) {
        $errors[] = 'Password minimal 8 karakter.';
    }

    if ($old['nama_user'] === '') {
        $errors[] = 'Nama User wajib diisi.';
    } elseif (!validasi_nama_user($old['nama_user'])) {
        $errors[] = 'Nama User tidak boleh mengandung angka.';
    }

    if ($old['role'] !== 'pusat') {
        $errors[] = 'Hak Akses wajib dipilih (Admin Pusat).';
    }

    if (!validasi_pilihan($old['status'], ['aktif', 'tidak_aktif'])) {
        $errors[] = 'Status User wajib dipilih.';
    }

    if ($old['kontak'] === '') {
        $errors[] = 'Kontak wajib diisi.';
    } elseif (!validasi_kontak_angka($old['kontak'])) {
        $errors[] = 'Kontak hanya boleh angka dan minimal 10 digit.';
    }

    if (!$errors) {
        $u = mysqli_real_escape_string($koneksi, $old['username']);
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM user WHERE username='$u'"));
        if ($cek['jml'] > 0) {
            $errors[] = 'Username sudah digunakan, silakan pilih username lain.';
        }
    }

    if (!$errors) {
        $nama     = mysqli_real_escape_string($koneksi, $old['nama_user']);
        $username = mysqli_real_escape_string($koneksi, $old['username']);
        $kontak   = mysqli_real_escape_string($koneksi, $old['kontak']);
        $password = hash_password($_POST['password']);
        $status   = $old['status'];

        $q = mysqli_query($koneksi, "INSERT INTO user (nama_user, kontak, username, password, role, status)
            VALUES ('$nama', '$kontak', '$username', '$password', 'pusat', '$status')");
        if ($q) {
            $id_baru = mysqli_insert_id($koneksi);
            catat_log('TAMBAH', 'user', 'Menambah user pusat: ' . $old['username'], $id_baru, null, [
                'nama_user' => $old['nama_user'],
                'username'  => $old['username'],
                'kontak'    => $old['kontak'],
                'role'      => 'pusat',
                'status'    => $status,
            ]);
            set_flash('User pusat berhasil ditambahkan!');
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah User Pusat</h4>
<div class="content-wrapper">
    <?php foreach ($errors as $e) echo alert(htmlspecialchars($e), 'danger'); ?>
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama User <span class="text-danger">*</span></label>
                <input type="text" name="nama_user" class="form-control" value="<?= htmlspecialchars($old['nama_user']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Username <span class="text-danger">*</span></label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($old['username']) ?>" required>
                <small class="text-muted">Tanpa spasi/karakter khusus, tidak boleh hanya angka.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Password <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" minlength="8" required>
                <small class="text-muted">Minimal 8 karakter.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak <span class="text-danger">*</span></label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($old['kontak']) ?>" placeholder="Minimal 10 digit" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Hak Akses <span class="text-danger">*</span></label>
                <select name="role" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="pusat" <?= $old['role'] === 'pusat' ? 'selected' : '' ?>>Admin Pusat</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Status User <span class="text-danger">*</span></label>
                <select name="status" class="form-control" required>
                    <option value="aktif" <?= $old['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="tidak_aktif" <?= $old['status'] === 'tidak_aktif' ? 'selected' : '' ?>>Tidak Aktif</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
        <a href="index.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>
