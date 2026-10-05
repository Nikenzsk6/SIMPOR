<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id_ujian = (int)$_GET['id'];
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_POST && isset($_FILES['file_excel'])) {
    $id_pelatih = (int)$_POST['id_pelatih'];
    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
    $nama_file = $_FILES['file_excel']['name'];

    if ($ext == 'xlsx' || $ext == 'xls') {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            $sukses = 0;
            foreach ($rows as $i => $row) {
                if ($i < 8) continue;
                if (empty($row[0]) || empty($row[2])) continue;

                $no_kta = trim($row[1] ?? '');
                $teori = (float)($row[3] ?? 0);
                $sejarah = (float)($row[4] ?? 0);
                $jurus = (float)($row[5] ?? 0);
                $mental = (float)($row[6] ?? 0);
                $agama = (float)($row[7] ?? 0);
                $na = ($teori + $sejarah + $jurus + $mental + $agama) / 5;

                if ($no_kta) {
                    $q_ps = mysqli_query($koneksi, "
                        SELECT dp.id_detail FROM detail_pendaftaran dp
                        JOIN anggota a ON dp.id_anggota = a.id_anggota
                        WHERE dp.id_pendaftaran = $id_ujian AND a.no_kta = '$no_kta'
                    ");
                    $ps = mysqli_fetch_assoc($q_ps);
                    if ($ps) {
                        $id_detail = $ps['id_detail'];
                        $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM penilaian WHERE id_detail=$id_detail AND id_pelatih=$id_pelatih"));
                        if ($cek['jml'] > 0) {
                            mysqli_query($koneksi, "UPDATE penilaian SET nilai_teori=$teori, nilai_sejarah=$sejarah, nilai_jurus=$jurus, nilai_mental=$mental, nilai_agama=$agama, nilai_akhir=$na, file_penilaian='$nama_file' WHERE id_detail=$id_detail AND id_pelatih=$id_pelatih");
                        } else {
                            mysqli_query($koneksi, "INSERT INTO penilaian (id_detail, id_pelatih, nilai_teori, nilai_sejarah, nilai_jurus, nilai_mental, nilai_agama, nilai_akhir, file_penilaian) VALUES ($id_detail, $id_pelatih, $teori, $sejarah, $jurus, $mental, $agama, $na, '$nama_file')");
                        }
                        $sukses++;
                    }
                }
            }
            set_flash("Berhasil mengimport $sukses data nilai dari $nama_file", 'success');
            header("Location: proses.php?id=$id_ujian");
            exit;
        } catch (Exception $e) {
            set_flash('Gagal membaca file: ' . $e->getMessage(), 'danger');
            header("Location: proses.php?id=$id_ujian");
            exit;
        }
    } else {
        set_flash('Format file harus .xlsx atau .xls', 'danger');
        header("Location: proses.php?id=$id_ujian");
        exit;
    }
}
?>
<h4 class="page-title">Upload Excel Penilaian - <?= htmlspecialchars($ujian['tingkat_ujian']) ?></h4>
<div class="content-wrapper">
    <form method="post" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Pilih Penguji</label>
                <select name="id_pelatih" class="form-control" required>
                    <option value="">-- Pilih --</option>
                    <?php
                    $q_p = mysqli_query($koneksi, "
                        SELECT p.* FROM penguji_ujian pu 
                        JOIN pelatih p ON pu.id_pelatih = p.id_pelatih 
                        WHERE pu.id_pendaftaran = $id_ujian
                    ");
                    while ($p = mysqli_fetch_assoc($q_p)):
                    ?>
                        <option value="<?= $p['id_pelatih'] ?>"><?= htmlspecialchars($p['nama_pelatih']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">File Excel (.xlsx / .xls)</label>
                <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload</button>
        <a href="proses.php?id=<?= $id_ujian ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>