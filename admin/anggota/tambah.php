<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$errors = [];
$old = [
    'no_kta'          => input('no_kta'),
    'nama_anggota'    => input('nama_anggota'),
    'tempat_lahir'    => input('tempat_lahir'),
    'tanggal_lahir'   => input('tanggal_lahir'),
    'tingkatan_sabuk' => input('tingkatan_sabuk'),
    'kontak'          => input('kontak'),
    'alamat'          => input('alamat'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($old['no_kta'] === '') {
        $errors[] = 'No. KTA wajib diisi.';
    } elseif (!validasi_kta($old['no_kta'])) {
        $errors[] = 'No. KTA harus tepat 14 digit angka.';
    }

    if ($old['nama_anggota'] === '') {
        $errors[] = 'Nama Anggota wajib diisi.';
    } elseif (!validasi_nama($old['nama_anggota'])) {
        $errors[] = 'Nama Anggota hanya boleh berisi huruf dan spasi.';
    } else {
        // Huruf awal setiap kata otomatis dijadikan kapital
        $old['nama_anggota'] = ucwords(strtolower($old['nama_anggota']));
    }

    if (!validasi_pilihan($old['tingkatan_sabuk'], daftar_sabuk())) {
        $errors[] = 'Tingkatan Sabuk wajib dipilih.';
    }

    if ($old['kontak'] === '') {
        $errors[] = 'Kontak wajib diisi.';
    } elseif (!validasi_kontak_angka($old['kontak'])) {
        $errors[] = 'Kontak hanya boleh angka dan minimal 10 digit.';
    }

    if ($old['alamat'] === '') {
        $errors[] = 'Alamat wajib diisi.';
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

    if (!$errors) {
        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM anggota WHERE no_kta='" . mysqli_real_escape_string($koneksi, $old['no_kta']) . "'"));
        if ($cek['jml'] > 0) {
            $errors[] = 'No. KTA sudah digunakan oleh anggota lain.';
        }
    }

    if (!$errors) {
        $no_kta          = mysqli_real_escape_string($koneksi, $old['no_kta']);
        $nama_anggota    = mysqli_real_escape_string($koneksi, $old['nama_anggota']);
        $tempat_lahir    = mysqli_real_escape_string($koneksi, $old['tempat_lahir']);
        $tingkatan_sabuk = mysqli_real_escape_string($koneksi, $old['tingkatan_sabuk']);
        $kontak          = mysqli_real_escape_string($koneksi, $old['kontak']);
        $alamat          = mysqli_real_escape_string($koneksi, $old['alamat']);
        $status          = auto_status_sabuk($old['tingkatan_sabuk']);

        $q = mysqli_query($koneksi, "INSERT INTO anggota
            (no_kta, nama_anggota, tempat_lahir, tanggal_lahir, tingkatan_sabuk, kontak, alamat, status)
            VALUES ('$no_kta', '$nama_anggota', '$tempat_lahir', '$tgl_lahir', '$tingkatan_sabuk', '$kontak', '$alamat', '$status')");
        if ($q) {
            $id_baru = mysqli_insert_id($koneksi);
            catat_log('TAMBAH', 'anggota', 'Menambah anggota: ' . $old['nama_anggota'], $id_baru, null, [
                'no_kta'          => $old['no_kta'],
                'nama_anggota'    => $old['nama_anggota'],
                'tempat_lahir'    => $old['tempat_lahir'],
                'tanggal_lahir'   => $tgl_lahir,
                'tingkatan_sabuk' => $old['tingkatan_sabuk'],
                'kontak'          => $old['kontak'],
                'alamat'          => $old['alamat'],
                'status'          => $status,
            ]);
            set_flash('Data anggota berhasil disimpan.');
            header('Location: index.php');
            exit;
        }
        $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah Anggota</h4>
<div class="content-wrapper">
    <?php foreach ($errors as $e) echo alert(htmlspecialchars($e), 'danger'); ?>
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">No. KTA <span class="text-danger">*</span></label>
                <input type="text" name="no_kta" class="form-control" value="<?= htmlspecialchars($old['no_kta']) ?>" placeholder="14 digit angka" maxlength="14" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Anggota <span class="text-danger">*</span></label>
                <input type="text" name="nama_anggota" class="form-control" value="<?= htmlspecialchars($old['nama_anggota']) ?>" required>
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
                <label class="form-label">Tingkatan Sabuk <span class="text-danger">*</span></label>
                <select name="tingkatan_sabuk" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach (daftar_sabuk() as $s): ?>
                        <option <?= $old['tingkatan_sabuk'] === $s ? 'selected' : '' ?>><?= $s ?></option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Status (Warga/Non-Warga) otomatis ditentukan dari sabuk.</small>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Kontak <span class="text-danger">*</span></label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($old['kontak']) ?>" placeholder="Minimal 10 digit" required>
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
