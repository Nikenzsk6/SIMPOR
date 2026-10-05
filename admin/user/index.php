<?php include_once '../../includes/sidebar_admin.php'; ?>
<h4 class="page-title">Kelola User Admin</h4>
<div class="content-wrapper">
    <a href="tambah.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Tambah User</a>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama User</th>
                <th>Username</th>
                <th>Kontak</th>
                <th>Tipe Pengguna</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM user WHERE role='admin' ORDER BY id_user ASC");
            if (mysqli_num_rows($q) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada user admin</td>
                </tr>
            <?php endif;
            $no = 1;
            while ($d = mysqli_fetch_assoc($q)):
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= htmlspecialchars($d['nama_user']) ?></td>
                    <td><?= htmlspecialchars($d['username']) ?></td>
                    <td><?= htmlspecialchars($d['kontak'] ?? '-') ?></td>
                    <td>Admin Cabang</td>
                    <td>
                        <a href="edit.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user <?= htmlspecialchars($d['nama_user']) ?>?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>