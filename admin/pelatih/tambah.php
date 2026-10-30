<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$errors = [];
$old = [
    'no_kta'          => input('no_kta'),
    'nama_pelatih'    => input('nama_pelatih'),
    'tempat_lahir'    => input('tempat_lahir'),
    'tanggal_lahir'   => input('tanggal_lahir'),
    'jenis_kelamin'   => input('jenis_kelamin'),
    'tingkatan_sabuk' => input('tingkatan_sabuk'),
    'kontak'          => input('kontak'),
    'alamat'          => input('alamat'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Nama pelatih: wajib, hanya huruf dan spasi
    if ($old['nama_pelatih'] === '') {
        $errors[] = 'Nama Pelatih wajib diisi.';
    } elseif (!validasi_nama($old['nama_pelatih'])) {
        $errors[] = 'Nama Pelatih tidak boleh mengandung angka atau karakter khusus.';
    }

    // No. KTA: wajib, tepat 14 digit angka
    if ($old['no_kta'] === '') {
        $errors[] = 'No. KTA wajib diisi.';
    } elseif (!validasi_kta($old['no_kta'])) {
        $errors[] = 'No. KTA harus tepat 14 digit angka.';
    }

    if ($old['tempat_lahir'] === '') {
        $errors[] = 'Tempat Lahir wajib diisi.';
    }

    $tgl_lahir = validasi_tanggal($old['tanggal_lahir'], false);
    if ($old['tanggal_lahir'] === '') {
        $errors[] = 'Tanggal Lahir wajib diisi.';
    } elseif ($tgl_lahir === false) {
        $errors[] = 'Format Tanggal Lahir harus dd-mm-yyyy.';
    } elseif (tanggal_masa_depan($tgl_lahir)) {
        $errors[] = 'Tanggal Lahir tidak boleh berada di masa depan.';
    }

    if (!validasi_pilihan($old['jenis_kelamin'], ['Laki-laki', 'Perempuan'])) {
        $errors[] = 'Jenis Kelamin wajib dipilih.';
    }

    if ($old['alamat'] === '') {
        $errors[] = 'Alamat wajib diisi.';
    }

    if ($old['kontak'] === '') {
        $errors[] = 'Nomor Telepon wajib diisi.';
    } elseif (!validasi_kontak($old['kontak'])) {
        $errors[] = 'Nomor Telepon hanya boleh angka (boleh diawali +) dan minimal 10 digit.';
    }

    if (!validasi_pilihan($old['tingkatan_sabuk'], daftar_sabuk())) {
        $errors[] = 'Tingkat Sabuk wajib dipilih.';
    }

    if (!$errors) {
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM pelatih WHERE no_kta='" . mysqli_real_escape_string($koneksi, $old['no_kta']) . "'"));
        if ($cek['jml'] > 0) {
            $errors[] = 'No. KTA sudah digunakan oleh pelatih lain.';
        }
    }

    if (!$errors) {
        $path_sertifikat = simpan_gambar('sertifikat', 'uploads/sertifikat', 10 * 1024 * 1024);
        if ($path_sertifikat === false) {
            $errors[] = 'Sertifikat harus berupa gambar JPG/PNG/WEBP dengan ukuran maksimal 10 MB.';
        } else {
            $no_kta          = mysqli_real_escape_string($koneksi, $old['no_kta']);
            $nama_pelatih    = mysqli_real_escape_string($koneksi, $old['nama_pelatih']);
            $tempat_lahir    = mysqli_real_escape_string($koneksi, $old['tempat_lahir']);
            $jenis_kelamin   = mysqli_real_escape_string($koneksi, $old['jenis_kelamin']);
            $tingkatan_sabuk = mysqli_real_escape_string($koneksi, $old['tingkatan_sabuk']);
            $kontak          = mysqli_real_escape_string($koneksi, $old['kontak']);
            $alamat          = mysqli_real_escape_string($koneksi, $old['alamat']);
            $path_sertifikat = $path_sertifikat ? mysqli_real_escape_string($koneksi, $path_sertifikat) : '';

            $q = mysqli_query($koneksi, "INSERT INTO pelatih
                (no_kta, nama_pelatih, tempat_lahir, tanggal_lahir, jenis_kelamin, tingkatan_sabuk, kontak, alamat, path_sertifikat)
                VALUES ('$no_kta', '$nama_pelatih', '$tempat_lahir', '$tgl_lahir', '$jenis_kelamin', '$tingkatan_sabuk', '$kontak', '$alamat', '$path_sertifikat')");
            if ($q) {
                $id_baru = mysqli_insert_id($koneksi);
                catat_log('TAMBAH', 'pelatih', 'Menambah pelatih: ' . $old['nama_pelatih'], $id_baru, null, [
                    'no_kta'          => $old['no_kta'],
                    'nama_pelatih'    => $old['nama_pelatih'],
                    'tempat_lahir'    => $old['tempat_lahir'],
                    'tanggal_lahir'   => $tgl_lahir,
                    'jenis_kelamin'   => $old['jenis_kelamin'],
                    'tingkatan_sabuk' => $old['tingkatan_sabuk'],
                    'kontak'          => $old['kontak'],
                    'alamat'          => $old['alamat'],
                    'path_sertifikat' => $path_sertifikat,
                ]);
                set_flash('Data pelatih berhasil disimpan.');
                header('Location: index.php');
                exit;
            }
            $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
}
?>
<h4 class="page-title">Tambah Pelatih</h4>
<div class="content-wrapper">
    <?php foreach ($errors as $e) echo alert(htmlspecialchars($e), 'danger'); ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No. KTA <span class="text-danger">*</span></label>
                <input type="text" name="no_kta" class="form-control" value="<?= htmlspecialchars($old['no_kta']) ?>" placeholder="14 digit angka" maxlength="14" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Pelatih <span class="text-danger">*</span></label>
                <input type="text" name="nama_pelatih" class="form-control" value="<?= htmlspecialchars($old['nama_pelatih']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tempat Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tempat_lahir" class="form-control" value="<?= htmlspecialchars($old['tempat_lahir']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                <input type="text" name="tanggal_lahir" class="form-control" value="<?= htmlspecialchars($old['tanggal_lahir']) ?>" placeholder="dd-mm-yyyy" maxlength="10" required>
                <small class="text-muted">Contoh: 17-08-2000</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                <select name="jenis_kelamin" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki" <?= $old['jenis_kelamin'] === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                    <option value="Perempuan" <?= $old['jenis_kelamin'] === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tingkat Sabuk <span class="text-danger">*</span></label>
                <select name="tingkatan_sabuk" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach (daftar_sabuk() as $s): ?>
                        <option <?= $old['tingkatan_sabuk'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($old['kontak']) ?>" placeholder="Minimal 10 digit" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Sertifikat (JPG/PNG/WEBP, maks 10 MB)</label>
                <input type="file" name="sertifikat" class="form-control" accept=".jpg,.jpeg,.png,.webp">
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($old['alamat']) ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>
