<?php include_once '../../includes/sidebar_admin.php'; ?>
<h4 class="page-title">Data Pelatih</h4>
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Pelatih</a>
        <div class="d-flex gap-2">
            <input type="text" id="searchInput" class="form-control" placeholder="Cari nama / no KTA..." onkeyup="filterTable()" style="width:250px;">
        </div>
    </div>
    <table class="table table-bordered table-hover" id="dataTable">
        <thead>
            <tr>
                <th>No</th>
                <th>No KTA</th>
                <th>Nama</th>
                <th>Sabuk</th>
                <th>Kontak</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM pelatih ORDER BY no_kta ASC");
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['no_kta']) ?></td>
                    <td><?= htmlspecialchars($d['nama_pelatih']) ?></td>
                    <td><?= htmlspecialchars($d['tingkatan_sabuk']) ?></td>
                    <td><?= htmlspecialchars($d['kontak']) ?></td>
                    <td>
                        <a href="edit.php?id=<?= $d['id_pelatih'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $d['id_pelatih'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<script>
    function filterTable() {
        var input = document.getElementById('searchInput').value.toLowerCase();
        var rows = document.querySelectorAll('#dataTable tbody tr');
        rows.forEach(function(row) {
            var text = row.textContent.toLowerCase();
            row.style.display = text.indexOf(input) > -1 ? '' : 'none';
        });
    }
</script>
<?php include_once '../../includes/footer.php'; ?>