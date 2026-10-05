<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id_ujian = (int)$_GET['id'];
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id_ujian"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}
?>
<h4 class="page-title">Hasil Ujian - <?= htmlspecialchars($ujian['tingkat_ujian']) ?></h4>
<div class="content-wrapper mb-3">
    <a href="../pengesahan/kirim.php?id=<?= $id_ujian ?>" class="btn btn-success"><i class="bi bi-send"></i> Kirim ke Pusat</a>
    <a href="../laporan/cetak_hasil.php?id=<?= $id_ujian ?>" class="btn btn-info" target="_blank"><i class="bi bi-printer"></i> Cetak PDF</a>
</div>
<div class="content-wrapper">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>No</th>
                <th>Peserta</th>
                <th>No KTA</th>
                <th>Sabuk Awal</th>
                <th>Rata-rata Nilai</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "
                SELECT dp.*, a.nama_anggota, a.no_kta, a.tingkatan_sabuk, h.rata_rata_nilai, h.status_kelulusan
                FROM detail_pendaftaran dp
                JOIN anggota a ON dp.id_anggota = a.id_anggota
                LEFT JOIN hasil_ujian h ON dp.id_detail = h.id_detail
                WHERE dp.id_pendaftaran = $id_ujian
                ORDER BY a.nama_anggota
            ");
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($d['no_kta']) ?></td>
                    <td><?= htmlspecialchars($d['tingkatan_sabuk']) ?></td>
                    <td class="text-center"><?= $d['rata_rata_nilai'] ? number_format($d['rata_rata_nilai'], 2) : '-' ?></td>
                    <td><?= $d['status_kelulusan'] ? status_label($d['status_kelulusan']) : '-' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>