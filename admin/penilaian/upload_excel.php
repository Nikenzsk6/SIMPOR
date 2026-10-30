<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id_ujian = (int) ($_GET['id'] ?? 0);
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

require_once '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $id_pelatih = (int) ($_POST['id_pelatih'] ?? 0);
    $file = $_FILES['file_excel']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['file_excel']['name'], PATHINFO_EXTENSION));
    $nama_file = $_FILES['file_excel']['name'];
    $errors = [];

    // Pastikan penguji benar-benar bertugas pada ujian ini
    $cek_penguji = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM penguji_ujian WHERE id_pendaftaran=$id_ujian AND id_pelatih=$id_pelatih"));
    if (!$cek_penguji || $cek_penguji['jml'] == 0) {
        $errors[] = 'Penguji yang dipilih tidak terdaftar pada ujian ini.';
    }

    if (!in_array($ext, ['xlsx', 'xls'], true)) {
        $errors[] = 'Format file harus .xlsx atau .xls.';
    }
    if (($_FILES['file_excel']['size'] ?? 0) > 5 * 1024 * 1024) {
        $errors[] = 'Ukuran file maksimal 5 MB.';
    }

    if (!$errors) {
        try {
            $spreadsheet = IOFactory::load($file);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray();

            // Daftar peserta sah pada ujian ini (no_kta => id_detail)
            $peserta = [];
            $q_peserta = mysqli_query($koneksi, "
                SELECT dp.id_detail, a.no_kta
                FROM detail_pendaftaran dp
                JOIN anggota a ON dp.id_anggota = a.id_anggota
                WHERE dp.id_pendaftaran = $id_ujian
            ");
            while ($ps = mysqli_fetch_assoc($q_peserta)) {
                $peserta[(string) $ps['no_kta']] = (int) $ps['id_detail'];
            }

            $data_nilai = [];
            $sudah = [];

            foreach ($rows as $i => $row) {
                if ($i < 8) continue;

                $no_kta = trim((string) ($row[1] ?? ''));
                if ($no_kta === '') continue; // baris kosong

                $baris_excel = $i + 1;
                if (!isset($peserta[$no_kta])) {
                    $errors[] = "Baris $baris_excel: No. KTA $no_kta bukan peserta ujian ini.";
                    continue;
                }
                if (isset($sudah[$no_kta])) {
                    $errors[] = "Baris $baris_excel: No. KTA $no_kta muncul lebih dari sekali.";
                    continue;
                }
                $sudah[$no_kta] = true;

                $kolom = ['Teori' => 3, 'Sejarah' => 4, 'Jurus' => 5, 'Mental' => 6, 'Agama' => 7];
                $nilai = [];
                foreach ($kolom as $label => $idx) {
                    $raw = $row[$idx] ?? null;
                    if ($raw === null || $raw === '' || !is_numeric($raw)) {
                        $errors[] = "Baris $baris_excel: Nilai $label wajib diisi dengan angka.";
                        continue;
                    }
                    $angka = (float) $raw;
                    if ($angka < 0 || $angka > 100) {
                        $errors[] = "Baris $baris_excel: Nilai $label harus antara 0 sampai 100.";
                        continue;
                    }
                    $nilai[$label] = $angka;
                }

                if (count($nilai) === 5) {
                    $data_nilai[] = [
                        'id_detail' => $peserta[$no_kta],
                        'teori'     => $nilai['Teori'],
                        'sejarah'   => $nilai['Sejarah'],
                        'jurus'     => $nilai['Jurus'],
                        'mental'    => $nilai['Mental'],
                        'agama'     => $nilai['Agama'],
                    ];
                }
            }

            // Semua peserta wajib ada di file
            foreach ($peserta as $kta => $_) {
                if (!isset($sudah[$kta])) {
                    $errors[] = "Peserta dengan No. KTA $kta belum ada di file Excel.";
                }
            }

            if (!$errors && count($data_nilai) === 0) {
                $errors[] = 'Tidak ada data nilai yang bisa disimpan.';
            }

            if ($errors) {
                $ringkas = array_slice($errors, 0, 8);
                $pesan = implode('<br>', array_map('htmlspecialchars', $ringkas));
                if (count($errors) > 8) {
                    $pesan .= '<br>... dan ' . (count($errors) - 8) . ' kesalahan lainnya.';
                }
                set_flash('File ditolak, nilai tidak disimpan:<br>' . $pesan, 'danger');
                header("Location: proses.php?id=$id_ujian");
                exit;
            }

            // Simpan setelah seluruh baris valid
            $nama_file_esc = mysqli_real_escape_string($koneksi, $nama_file);
            $sukses = 0;
            foreach ($data_nilai as $n) {
                $id_detail = (int) $n['id_detail'];
                $na = ($n['teori'] + $n['sejarah'] + $n['jurus'] + $n['mental'] + $n['agama']) / 5;
                $na = round($na, 2);

                $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS jml FROM penilaian WHERE id_detail=$id_detail AND id_pelatih=$id_pelatih"));
                if ($cek['jml'] > 0) {
                    mysqli_query($koneksi, "UPDATE penilaian SET nilai_teori={$n['teori']}, nilai_sejarah={$n['sejarah']},
                        nilai_jurus={$n['jurus']}, nilai_mental={$n['mental']}, nilai_agama={$n['agama']},
                        nilai_akhir=$na, file_penilaian='$nama_file_esc'
                        WHERE id_detail=$id_detail AND id_pelatih=$id_pelatih");
                } else {
                    mysqli_query($koneksi, "INSERT INTO penilaian
                        (id_detail, id_pelatih, nilai_teori, nilai_sejarah, nilai_jurus, nilai_mental, nilai_agama, nilai_akhir, file_penilaian)
                        VALUES ($id_detail, $id_pelatih, {$n['teori']}, {$n['sejarah']}, {$n['jurus']}, {$n['mental']}, {$n['agama']}, $na, '$nama_file_esc')");
                }
                $sukses++;
            }

            set_flash("Berhasil mengimport $sukses data nilai dari " . htmlspecialchars($nama_file), 'success');
            header("Location: proses.php?id=$id_ujian");
            exit;
        } catch (Exception $e) {
            set_flash('Gagal membaca file: ' . htmlspecialchars($e->getMessage()), 'danger');
            header("Location: proses.php?id=$id_ujian");
            exit;
        }
    } else {
        set_flash(implode('<br>', array_map('htmlspecialchars', $errors)), 'danger');
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
                <label class="form-label">File Excel (.xlsx / .xls, maks 5 MB)</label>
                <input type="file" name="file_excel" class="form-control" accept=".xlsx,.xls" required>
            </div>
        </div>
        <button type="submit" class="btn btn-success"><i class="bi bi-upload"></i> Upload</button>
        <a href="proses.php?id=<?= $id_ujian ?>" class="btn btn-secondary">Kembali</a>
    </form>
</div>
<?php include_once '../../includes/footer.php'; ?>
