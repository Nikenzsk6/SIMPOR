<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$errors = [];
$old = [
    'nama_ujian'    => input('nama_ujian'),
    'tingkat_ujian' => input('tingkat_ujian'),
    'tgl_buka'      => input('tgl_buka'),
    'tgl_tutup'     => input('tgl_tutup'),
    'tgl_ujian'     => input('tgl_ujian'),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($old['nama_ujian'] === '') {
        $errors[] = 'Nama/Data Ujian wajib diisi.';
    } elseif (preg_match('/[@#$%^*_+={}\[\]|\\\\<>~`]/', $old['nama_ujian'])) {
        $errors[] = 'Nama/Data Ujian tidak boleh mengandung karakter khusus.';
    }

    if (!validasi_pilihan($old['tingkat_ujian'], daftar_tingkat_ujian())) {
        $errors[] = 'Tingkatan Ujian wajib dipilih dari opsi yang tersedia.';
    }

    $buka  = validasi_tanggal($old['tgl_buka']);
    $tutup = validasi_tanggal($old['tgl_tutup']);
    $ujian = validasi_tanggal($old['tgl_ujian']);

    if ($old['tgl_buka'] === '') {
        $errors[] = 'Tanggal Buka Pendaftaran wajib diisi.';
    } elseif ($buka === false) {
        $errors[] = 'Format Tanggal Buka Pendaftaran tidak valid.';
    }

    if ($old['tgl_tutup'] === '') {
        $errors[] = 'Tanggal Tutup Pendaftaran wajib diisi.';
    } elseif ($tutup === false) {
        $errors[] = 'Format Tanggal Tutup Pendaftaran tidak valid.';
    }

    if ($old['tgl_ujian'] === '') {
        $errors[] = 'Tanggal Ujian wajib diisi.';
    } elseif ($ujian === false) {
        $errors[] = 'Format Tanggal Ujian tidak valid.';
    }

    if ($buka && $tutup && $buka > $tutup) {
        $errors[] = 'Tanggal Tutup Pendaftaran tidak boleh lebih awal dari Tanggal Buka.';
    }
    if ($tutup && $ujian && $tutup > $ujian) {
        $errors[] = 'Tanggal Ujian tidak boleh lebih awal dari Tanggal Tutup Pendaftaran.';
    }

    if (!$errors) {
        $nama_ujian    = mysqli_real_escape_string($koneksi, $old['nama_ujian']);
        $tingkat_ujian = mysqli_real_escape_string($koneksi, $old['tingkat_ujian']);
        $status = (date('Y-m-d') >= $buka && date('Y-m-d') <= $tutup) ? 'dibuka' : 'ditutup';

        $q = mysqli_query($koneksi, "INSERT INTO pendaftaran_ujian (nama_ujian, tingkat_ujian, tgl_buka, tgl_tutup, tgl_ujian, status)
            VALUES ('$nama_ujian', '$tingkat_ujian', '$buka', '$tutup', '$ujian', '$status')");
        if ($q) {
            $id_baru = mysqli_insert_id($koneksi);
            catat_log('TAMBAH', 'ujian', 'Menambah ujian: ' . $old['nama_ujian'], $id_baru, null, [
                'nama_ujian'    => $old['nama_ujian'],
                'tingkat_ujian' => $old['tingkat_ujian'],
                'tgl_buka'      => $buka,
                'tgl_tutup'     => $tutup,
                'tgl_ujian'     => $ujian,
                'status'        => $status,
            ]);
            set_flash('Data ujian berhasil disimpan.');
            header('Location: detail.php?id=' . $id_baru);
            exit;
        }
        $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
    }
}
?>
<h4 class="page-title">Tambah Ujian</h4>
<div class="content-wrapper">
    <?php foreach ($errors as $e) echo alert(htmlspecialchars($e), 'danger'); ?>
    <form method="post">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama/Data Ujian <span class="text-danger">*</span></label>
                <input type="text" name="nama_ujian" class="form-control" value="<?= htmlspecialchars($old['nama_ujian']) ?>" placeholder="Contoh: Ujian Kenaikan Sabuk Periode 1" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Tingkat Ujian <span class="text-danger">*</span></label>
                <select name="tingkat_ujian" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <?php foreach (daftar_tingkat_ujian() as $t): ?>
                        <option <?= $old['tingkat_ujian'] === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Buka Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="tgl_buka" class="form-control" value="<?= htmlspecialchars($old['tgl_buka']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Tutup Pendaftaran <span class="text-danger">*</span></label>
                <input type="date" name="tgl_tutup" class="form-control" value="<?= htmlspecialchars($old['tgl_tutup']) ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Tanggal Ujian <span class="text-danger">*</span></label>
                <input type="date" name="tgl_ujian" class="form-control" value="<?= htmlspecialchars($old['tgl_ujian']) ?>" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success">Simpan</button>
        <a href="index.php" class="btn btn-secondary">Batal</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>
