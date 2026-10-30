<?php include_once '../../includes/sidebar_pusat.php'; ?>
<?php
$profil = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM profil WHERE tipe='pusat'"));
$id = $profil['id_profil'] ?? 0;

$errors = [];
$old = [
    'nama_organisasi' => input('nama_organisasi', $profil['nama_organisasi'] ?? ''),
    'singkatan'       => input('singkatan', $profil['singkatan'] ?? ''),
    'alamat'          => input('alamat', $profil['alamat'] ?? ''),
    'kontak'          => input('kontak', $profil['kontak'] ?? ''),
    'deskripsi'       => input('deskripsi', $profil['deskripsi'] ?? ''),
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($old['nama_organisasi'] === '') {
        $errors[] = 'Nama Organisasi wajib diisi.';
    } elseif (!validasi_nama_organisasi($old['nama_organisasi'])) {
        $errors[] = 'Nama Organisasi tidak boleh mengandung karakter khusus.';
    }

    if ($old['alamat'] === '') {
        $errors[] = 'Alamat wajib diisi.';
    }

    if ($old['kontak'] === '') {
        $errors[] = 'Nomor Telepon wajib diisi.';
    } elseif ($old['kontak'] !== ($profil['kontak'] ?? '') && !validasi_kontak_angka($old['kontak'])) {
        $errors[] = 'Nomor Telepon hanya boleh angka dan minimal 10 digit.';
    }

    if (!$errors) {
        $logo = simpan_gambar('logo', 'uploads/logo', 5 * 1024 * 1024);
        if ($logo === false) {
            $errors[] = 'Logo harus berupa gambar JPG/PNG/WEBP dengan ukuran maksimal 5 MB.';
        } else {
            if ($logo === null) {
                $logo = $profil['logo'] ?? '';
            }

            $nama      = mysqli_real_escape_string($koneksi, $old['nama_organisasi']);
            $singkatan = mysqli_real_escape_string($koneksi, $old['singkatan']);
            $alamat    = mysqli_real_escape_string($koneksi, $old['alamat']);
            $kontak    = mysqli_real_escape_string($koneksi, $old['kontak']);
            $deskripsi = mysqli_real_escape_string($koneksi, $old['deskripsi']);
            $logo      = $logo ? mysqli_real_escape_string($koneksi, $logo) : '';

            if ($id > 0) {
                $q = mysqli_query($koneksi, "UPDATE profil SET nama_organisasi='$nama', singkatan='$singkatan',
                    alamat='$alamat', kontak='$kontak', logo='$logo', deskripsi='$deskripsi' WHERE id_profil=$id");
            } else {
                $q = mysqli_query($koneksi, "INSERT INTO profil (tipe, nama_organisasi, singkatan, alamat, kontak, logo, deskripsi)
                    VALUES ('pusat', '$nama', '$singkatan', '$alamat', '$kontak', '$logo', '$deskripsi')");
            }

            if ($q) {
                set_flash('Profil pusat berhasil disimpan!');
                header('Location: index.php');
                exit;
            }
            $errors[] = 'Gagal menyimpan: ' . mysqli_error($koneksi);
        }
    }
}
?>
<h4 class="page-title">Profil Pusat</h4>
<div class="content-wrapper">
    <?php foreach ($errors as $e) echo alert(htmlspecialchars($e), 'danger'); ?>
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Nama Organisasi <span class="text-danger">*</span></label>
                <input type="text" name="nama_organisasi" class="form-control" value="<?= htmlspecialchars($old['nama_organisasi']) ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Singkatan</label>
                <input type="text" name="singkatan" class="form-control" value="<?= htmlspecialchars($old['singkatan']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                <input type="text" name="kontak" class="form-control" value="<?= htmlspecialchars($old['kontak']) ?>" placeholder="Minimal 10 digit" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Logo Organisasi (JPG/PNG/WEBP, maks 5 MB)</label>
                <input type="file" name="logo" class="form-control" accept=".jpg,.jpeg,.png,.webp">
                <?php if (!empty($profil['logo'])): ?>
                    <div class="mt-2">
                        <img src="<?= BASE_URL . htmlspecialchars($profil['logo']) ?>" alt="Logo" style="max-height:70px;border-radius:8px;">
                    </div>
                <?php endif; ?>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Alamat <span class="text-danger">*</span></label>
                <textarea name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($old['alamat']) ?></textarea>
            </div>
            <div class="col-md-12 mb-3">
                <label class="form-label">Deskripsi Organisasi</label>
                <textarea name="deskripsi" class="form-control" rows="4"><?= htmlspecialchars($old['deskripsi']) ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-save"></i> Simpan</button>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>
