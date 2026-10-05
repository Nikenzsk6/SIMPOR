<?php
include_once '../../includes/sidebar_pusat.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="page-title mb-0">Kelola User Pusat</h4>
</div>

<div class="content-wrapper">
    <a href="tambah.php" class="btn btn-success mb-3">
        <i class="bi bi-plus-circle"></i> Tambah User
    </a>
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th>No.</th>
                <th>Nama User</th>
                <th>Username</th>
                <th>Kontak</th>
                <th>Tipe Pengguna</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $q = mysqli_query($koneksi, "SELECT * FROM user WHERE role='pusat' ORDER BY id_user ASC");
            if (mysqli_num_rows($q) == 0): ?>
                <tr>
                    <td colspan="6" class="text-center text-muted">Belum ada user pusat</td>
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
                    <td>Admin Pusat</td>
                    <td>
                        <a href="ubah.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus user <?= htmlspecialchars($d['nama_user']) ?>?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>