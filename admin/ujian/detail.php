<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$id = (int)$_GET['id'];
$ujian = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT * FROM pendaftaran_ujian WHERE id_pendaftaran=$id"));
if (!$ujian) {
    set_flash('Data tidak ditemukan', 'danger');
    header('Location: index.php');
    exit;
}

if ($ujian['status'] == 'dibuka' && $ujian['tgl_tutup'] < date('Y-m-d')) {
    mysqli_query($koneksi, "UPDATE pendaftaran_ujian SET status='ditutup' WHERE id_pendaftaran=$id");
    $ujian['status'] = 'ditutup';
}

$is_open = ($ujian['status'] == 'dibuka');

$nama_ujian_log = $ujian['nama_ujian'] ?: $ujian['tingkat_ujian'];

if (isset($_POST['tambah_penguji'])) {
    $id_pelatih = (int)$_POST['id_pelatih'];
    $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pelatih FROM pelatih WHERE id_pelatih=$id_pelatih"));
    mysqli_query($koneksi, "INSERT IGNORE INTO penguji_ujian (id_pendaftaran, id_pelatih) VALUES ($id, $id_pelatih)");
    if (mysqli_affected_rows($koneksi) > 0) {
        catat_log('TAMBAH_PENGUJI', 'ujian', 'Menambahkan penguji ' . ($p['nama_pelatih'] ?? '') . ' ke ujian ' . $nama_ujian_log, $id, null, [
            'id_pelatih'   => $id_pelatih,
            'nama_pelatih' => $p['nama_pelatih'] ?? null,
        ]);
    }
    header('Location: detail.php?id=' . $id);
    exit;
}

if (isset($_GET['hapus_penguji'])) {
    $id_pelatih = (int)$_GET['hapus_penguji'];
    $p = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_pelatih FROM pelatih WHERE id_pelatih=$id_pelatih"));
    mysqli_query($koneksi, "DELETE FROM penguji_ujian WHERE id_pendaftaran=$id AND id_pelatih=$id_pelatih");
    if (mysqli_affected_rows($koneksi) > 0) {
        catat_log('HAPUS_PENGUJI', 'ujian', 'Menghapus penguji ' . ($p['nama_pelatih'] ?? '') . ' dari ujian ' . $nama_ujian_log, $id, [
            'id_pelatih'   => $id_pelatih,
            'nama_pelatih' => $p['nama_pelatih'] ?? null,
        ], null);
    }
    header('Location: detail.php?id=' . $id);
    exit;
}

if (isset($_POST['tambah_peserta'])) {
    $id_anggota = (int)$_POST['id_anggota'];
    $a = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT nama_anggota, no_kta FROM anggota WHERE id_anggota=$id_anggota"));
    $cek = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) as jml FROM detail_pendaftaran WHERE id_pendaftaran=$id AND id_anggota=$id_anggota"));
    if ($cek['jml'] == 0) {
        mysqli_query($koneksi, "INSERT INTO detail_pendaftaran (id_pendaftaran, id_anggota) VALUES ($id, $id_anggota)");
        if (mysqli_affected_rows($koneksi) > 0) {
            catat_log('TAMBAH_PESERTA', 'ujian', 'Mendaftarkan peserta ' . ($a['nama_anggota'] ?? '') . ' ke ujian ' . $nama_ujian_log, $id, null, [
                'id_anggota'   => $id_anggota,
                'no_kta'       => $a['no_kta'] ?? null,
                'nama_anggota' => $a['nama_anggota'] ?? null,
            ]);
        }
    }
    header('Location: detail.php?id=' . $id);
    exit;
}

if (isset($_GET['hapus_peserta'])) {
    $id_detail = (int)$_GET['hapus_peserta'];
    $ps = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT dp.id_detail, a.id_anggota, a.no_kta, a.nama_anggota
        FROM detail_pendaftaran dp
        JOIN anggota a ON dp.id_anggota = a.id_anggota
        WHERE dp.id_detail=$id_detail AND dp.id_pendaftaran=$id
    "));
    mysqli_query($koneksi, "DELETE FROM detail_pendaftaran WHERE id_detail=$id_detail AND id_pendaftaran=$id");
    if (mysqli_affected_rows($koneksi) > 0 && $ps) {
        catat_log('HAPUS_PESERTA', 'ujian', 'Menghapus peserta ' . $ps['nama_anggota'] . ' dari ujian ' . $nama_ujian_log, $id, $ps, null);
    }
    header('Location: detail.php?id=' . $id);
    exit;
}

if (isset($_GET['bayar'])) {
    $id_detail = (int)$_GET['bayar'];
    $dp = mysqli_fetch_assoc(mysqli_query($koneksi, "
        SELECT dp.status_pembayaran, a.no_kta, a.nama_anggota
        FROM detail_pendaftaran dp
        JOIN anggota a ON dp.id_anggota = a.id_anggota
        WHERE dp.id_detail=$id_detail AND dp.id_pendaftaran=$id
    "));
    $new_status = ($dp['status_pembayaran'] == 'lunas') ? 'belum' : 'lunas';
    mysqli_query($koneksi, "UPDATE detail_pendaftaran SET status_pembayaran='$new_status' WHERE id_detail=$id_detail");
    if (mysqli_affected_rows($koneksi) > 0 && $dp) {
        catat_log('UBAH_PEMBAYARAN', 'ujian', 'Mengubah pembayaran ' . $dp['nama_anggota'] . ' (' . $dp['no_kta'] . ') menjadi ' . $new_status . ' pada ujian ' . $nama_ujian_log, $id, [
            'id_detail'         => $id_detail,
            'no_kta'            => $dp['no_kta'],
            'nama_anggota'      => $dp['nama_anggota'],
            'status_pembayaran' => $dp['status_pembayaran'],
        ], [
            'id_detail'         => $id_detail,
            'no_kta'            => $dp['no_kta'],
            'nama_anggota'      => $dp['nama_anggota'],
            'status_pembayaran' => $new_status,
        ]);
    }
    header('Location: detail.php?id=' . $id);
    exit;
}
?>
<h4 class="page-title">Detail Ujian</h4>

<div class="content-wrapper p-3 mb-3">
    <div class="row g-2">
        <div class="col-md-3">
            <small class="text-muted">Nama Ujian</small>
            <div class="fw-bold"><?= htmlspecialchars($ujian['nama_ujian'] ?: $ujian['tingkat_ujian']) ?></div>
        </div>
        <div class="col-md-3">
            <small class="text-muted">Tingkat Ujian</small>
            <div class="fw-bold"><?= htmlspecialchars($ujian['tingkat_ujian']) ?></div>
        </div>
        <div class="col-md-2">
            <small class="text-muted">Status</small>
            <div><?= status_label($ujian['status']) ?></div>
        </div>
        <div class="col-md-2">
            <small class="text-muted">Tgl Buka</small>
            <div><?= tgl_indonesia($ujian['tgl_buka']) ?></div>
        </div>
        <div class="col-md-2">
            <small class="text-muted">Tgl Tutup</small>
            <div><?= tgl_indonesia($ujian['tgl_tutup']) ?></div>
        </div>
    </div>
    <div class="row g-2 mt-1">
        <div class="col-md-3">
            <small class="text-muted">Tgl Ujian</small>
            <div><?= tgl_indonesia($ujian['tgl_ujian']) ?></div>
        </div>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-5">
        <div class="content-wrapper p-3 mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-people"></i> Penguji</h6>
            <?php if (true): ?>
                <form method="post" id="formPenguji">
                    <input type="hidden" name="id_pelatih" id="id_pelatih">
                    <input type="text" class="form-control form-control-sm mb-1" id="searchPenguji" placeholder="Cari penguji..." autocomplete="off">
                    <div id="hasilPenguji" class="search-results mb-1"></div>
                    <div id="selectedPenguji" class="selected-item mb-1" style="display:none;">
                        <span class="badge bg-merah py-2 px-3" id="selectedPengujiLabel"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="batalPilihPenguji()">Batal</button>
                    </div>
                    <button type="submit" name="tambah_penguji" class="btn btn-success btn-sm" id="btnTambahPenguji" disabled>Tambah</button>
                </form>
            <?php else: ?>
                <div class="text-muted small mb-2">Pendaftaran ditutup.</div>
            <?php endif; ?>
<table class="table table-bordered table-sm mt-2 mb-0">
        <thead>
            <tr>
                <th>Nama</th>
                <th>Sabuk</th><?php if (true): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_penguji = mysqli_query($koneksi, "
                        SELECT p.* FROM penguji_ujian pu 
                        JOIN pelatih p ON pu.id_pelatih = p.id_pelatih 
                        WHERE pu.id_pendaftaran = $id
                    ");
                    while ($p = mysqli_fetch_assoc($q_penguji)):
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($p['nama_pelatih']) ?></td>
                            <td><?= htmlspecialchars($p['tingkatan_sabuk']) ?></td>
                            <?php if (true): ?>
                                <td class="text-center">
                                    <a href="?id=<?= $id ?>&hapus_penguji=<?= $p['id_pelatih'] ?>" class="btn btn-sm btn-danger py-0 px-1" onclick="return confirm('Yakin?')"><i class="bi bi-x"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-7">
        <div class="content-wrapper p-3 mb-3">
            <h6 class="fw-bold mb-2"><i class="bi bi-person-badge"></i> Peserta</h6>
            <?php if (true): ?>
                <form method="post" id="formPeserta">
                    <input type="hidden" name="id_anggota" id="id_anggota">
                    <input type="text" class="form-control form-control-sm mb-1" id="searchPeserta" placeholder="Cari peserta..." autocomplete="off">
                    <div id="hasilPeserta" class="search-results mb-1"></div>
                    <div id="selectedPeserta" class="selected-item mb-1" style="display:none;">
                        <span class="badge bg-merah py-2 px-3" id="selectedPesertaLabel"></span>
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-1" onclick="batalPilihPeserta()">Batal</button>
                    </div>
                    <button type="submit" name="tambah_peserta" class="btn btn-success btn-sm" id="btnTambahPeserta" disabled>Daftarkan</button>
                </form>
            <?php else: ?>
                <div class="text-muted small mb-2">Pendaftaran ditutup.</div>
            <?php endif; ?>
            <table class="table table-bordered table-sm mt-2 mb-0">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No KTA</th>
                        <th>Nama</th>
                        <th>Sabuk</th>
                        <th>Pembayaran</th><?php if (true): ?><th>Aksi</th><?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $q_peserta = mysqli_query($koneksi, "
                        SELECT dp.*, a.no_kta, a.nama_anggota, a.tingkatan_sabuk 
                        FROM detail_pendaftaran dp 
                        JOIN anggota a ON dp.id_anggota = a.id_anggota 
                        WHERE dp.id_pendaftaran = $id 
                        ORDER BY a.nama_anggota
                    ");
                    $no = 1;
                    while ($ps = mysqli_fetch_assoc($q_peserta)):
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= htmlspecialchars($ps['no_kta']) ?></td>
                            <td><?= htmlspecialchars($ps['nama_anggota']) ?></td>
                            <td><?= htmlspecialchars($ps['tingkatan_sabuk']) ?></td>
                            <td>
                                <?php if (true): ?>
                                    <a href="?id=<?= $id ?>&bayar=<?= $ps['id_detail'] ?>" class="text-decoration-none"><?= status_label($ps['status_pembayaran']) ?></a>
                                <?php else: ?>
                                    <?= status_label($ps['status_pembayaran']) ?>
                                <?php endif; ?>
                            </td>
                            <?php if (true): ?>
                                <td class="text-center">
                                    <a href="?id=<?= $id ?>&hapus_peserta=<?= $ps['id_detail'] ?>" class="btn btn-sm btn-danger py-0 px-1" onclick="return confirm('Yakin?')"><i class="bi bi-trash"></i></a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php if (true): ?>
    <div class="content-wrapper p-3">
        <h6 class="fw-bold mb-2"><i class="bi bi-file-earmark-excel"></i> Download Template Penilaian</h6>
        <div class="d-flex gap-2 flex-wrap">
            <?php
            $q_penguji_dl = mysqli_query($koneksi, "
            SELECT p.* FROM penguji_ujian pu 
            JOIN pelatih p ON pu.id_pelatih = p.id_pelatih 
            WHERE pu.id_pendaftaran = $id
        ");
            if (mysqli_num_rows($q_penguji_dl) > 0):
                while ($p_dl = mysqli_fetch_assoc($q_penguji_dl)):
            ?>
                    <a href="<?= BASE_URL ?>admin/penilaian/download_excel.php?id=<?= $id ?>&pelatih=<?= $p_dl['id_pelatih'] ?>" class="btn btn-sm btn-outline-info">
                        <i class="bi bi-file-earmark-excel"></i> <?= htmlspecialchars($p_dl['nama_pelatih']) ?>
                    </a>
                <?php
                endwhile;
            else:
                ?>
                <div class="text-muted small">Belum ada penguji.</div>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<div class="mt-2">
    <a href="index.php" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
</div>

<script>
    <?php
    // Generate data for search
    $q_all_pelatih = mysqli_query($koneksi, "SELECT id_pelatih, nama_pelatih, tingkatan_sabuk FROM pelatih ORDER BY nama_pelatih");
    $q_all_anggota = mysqli_query($koneksi, "SELECT id_anggota, nama_anggota, no_kta, tingkatan_sabuk FROM anggota ORDER BY nama_anggota");
    ?>
    var pelatihData = [
        <?php while ($p = mysqli_fetch_assoc($q_all_pelatih)): ?> {
                id: <?= $p['id_pelatih'] ?>,
                nama: '<?= addslashes($p['nama_pelatih']) ?>',
                sabuk: '<?= addslashes($p['tingkatan_sabuk']) ?>'
            },
        <?php endwhile; ?>
    ];
    var anggotaData = [
        <?php while ($a = mysqli_fetch_assoc($q_all_anggota)): ?> {
                id: <?= $a['id_anggota'] ?>,
                nama: '<?= addslashes($a['nama_anggota']) ?>',
                no_kta: '<?= addslashes($a['no_kta']) ?>',
                sabuk: '<?= addslashes($a['tingkatan_sabuk']) ?>'
            },
        <?php endwhile; ?>
    ];

    function renderResults(data, containerId, clickHandler) {
        var container = document.getElementById(containerId);
        if (data.length === 0) {
            container.innerHTML = '';
            return;
        }
        var html = '';
        for (var i = 0; i < data.length; i++) {
            html += '<div class="search-result-item" onclick="' + clickHandler + '(' + data[i].id + ',\'' + data[i].nama.replace(/'/g, "\\'") + '\')">' + data[i].nama + ' <small class="text-muted">(' + data[i].sabuk + ')</small></div>';
        }
        container.innerHTML = html;
    }

    function filterPelatih() {
        var keyword = document.getElementById('searchPenguji').value.toLowerCase();
        if (!keyword) {
            document.getElementById('hasilPenguji').innerHTML = '';
            return;
        }
        var filtered = pelatihData.filter(function(p) {
            return p.nama.toLowerCase().indexOf(keyword) > -1 || p.sabuk.toLowerCase().indexOf(keyword) > -1;
        });
        renderResults(filtered, 'hasilPenguji', 'pilihPenguji');
    }

    function pilihPenguji(id, nama) {
        document.getElementById('id_pelatih').value = id;
        document.getElementById('selectedPengujiLabel').textContent = nama;
        document.getElementById('selectedPenguji').style.display = 'block';
        document.getElementById('hasilPenguji').innerHTML = '';
        document.getElementById('searchPenguji').value = nama;
        document.getElementById('searchPenguji').disabled = true;
        document.getElementById('btnTambahPenguji').disabled = false;
    }

    function batalPilihPenguji() {
        document.getElementById('id_pelatih').value = '';
        document.getElementById('selectedPenguji').style.display = 'none';
        document.getElementById('searchPenguji').value = '';
        document.getElementById('searchPenguji').disabled = false;
        document.getElementById('searchPenguji').focus();
        document.getElementById('btnTambahPenguji').disabled = true;
    }

    function filterPeserta() {
        var keyword = document.getElementById('searchPeserta').value.toLowerCase();
        if (!keyword) {
            document.getElementById('hasilPeserta').innerHTML = '';
            return;
        }
        var filtered = anggotaData.filter(function(a) {
            return a.nama.toLowerCase().indexOf(keyword) > -1 || a.no_kta.toLowerCase().indexOf(keyword) > -1 || a.sabuk.toLowerCase().indexOf(keyword) > -1;
        });
        renderResults(filtered, 'hasilPeserta', 'pilihPeserta');
    }

    function pilihPeserta(id, nama) {
        document.getElementById('id_anggota').value = id;
        document.getElementById('selectedPesertaLabel').textContent = nama;
        document.getElementById('selectedPeserta').style.display = 'block';
        document.getElementById('hasilPeserta').innerHTML = '';
        document.getElementById('searchPeserta').value = nama;
        document.getElementById('searchPeserta').disabled = true;
        document.getElementById('btnTambahPeserta').disabled = false;
    }

    function batalPilihPeserta() {
        document.getElementById('id_anggota').value = '';
        document.getElementById('selectedPeserta').style.display = 'none';
        document.getElementById('searchPeserta').value = '';
        document.getElementById('searchPeserta').disabled = false;
        document.getElementById('searchPeserta').focus();
        document.getElementById('btnTambahPeserta').disabled = true;
    }

    var elPenguji = document.getElementById('searchPenguji');
    var elPeserta = document.getElementById('searchPeserta');
    if (elPenguji) elPenguji.addEventListener('input', filterPelatih);
    if (elPeserta) elPeserta.addEventListener('input', filterPeserta);
</script>
<?php include_once '../../includes/footer.php'; ?>