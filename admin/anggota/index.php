<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$q = mysqli_query($koneksi, "SELECT * FROM anggota ORDER BY no_kta ASC");
$total = $q ? mysqli_num_rows($q) : 0;
?>
<h4 class="page-title">Data Anggota</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-person-badge"></i> Daftar Anggota
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> orang</span>
        </h5>
        <div class="panel-actions">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama / no KTA..." onkeyup="filterTable()">
            </div>
            <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Anggota</a>
        </div>
    </div>

    <table class="table table-bordered table-hover" id="dataTable">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>No KTA</th>
                <th>Nama</th>
                <th>Sabuk</th>
                <th>Kontak</th>
                <th>Status</th>
                <th class="text-end" style="width:96px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['no_kta']) ?></td>
                    <td><?= htmlspecialchars($d['nama_anggota']) ?></td>
                    <td><?= htmlspecialchars($d['tingkatan_sabuk']) ?></td>
                    <td><?= htmlspecialchars($d['kontak']) ?></td>
                    <td><?= status_label($d['status']) ?></td>
                    <td class="text-end text-nowrap">
                        <a href="edit.php?id=<?= $d['id_anggota'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $d['id_anggota'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div id="emptyState" class="empty-state d-none">
        <i class="bi bi-search"></i>
        <span>Tidak ada anggota yang cocok dengan pencarian</span>
    </div>
</div>

<script>
    function filterTable() {
        var input = document.getElementById('searchInput').value.toLowerCase();
        var rows = document.querySelectorAll('#dataTable tbody tr');
        var visible = 0;
        rows.forEach(function (row) {
            var match = row.textContent.toLowerCase().indexOf(input) > -1;
            row.style.display = match ? '' : 'none';
            if (match) visible++;
        });
        document.getElementById('emptyState').classList.toggle('d-none', visible > 0);
    }
</script>
<?php include_once '../../includes/footer.php'; ?>