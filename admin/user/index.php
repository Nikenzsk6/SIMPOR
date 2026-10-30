<?php include_once '../../includes/sidebar_admin.php'; ?>
<?php
$q = mysqli_query($koneksi, "SELECT * FROM user WHERE role='admin' ORDER BY id_user ASC");
$total = $q ? mysqli_num_rows($q) : 0;
?>
<h4 class="page-title">Kelola User Admin</h4>

<div class="panel">
    <div class="panel-head">
        <h5 class="panel-title">
            <i class="bi bi-person-gear"></i> Pengguna Admin Cabang
            <span class="panel-count"><?= number_format($total, 0, ',', '.') ?> user</span>
        </h5>
        <div class="panel-actions">
            <a href="tambah.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah User</a>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th style="width:56px">No</th>
                <th>Nama User</th>
                <th>Username</th>
                <th>Kontak</th>
                <th>Tipe Pengguna</th>
                <th>Status</th>
                <th class="text-end" style="width:96px">Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($total == 0): ?>
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-person-x"></i>
                            <span>Belum ada user admin</span>
                        </div>
                    </td>
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
                    <td><span class="badge bg-info">Admin Cabang</span></td>
                    <td>
                        <?php if (($d['status'] ?? 'aktif') === 'aktif'): ?>
                            <span class="badge bg-success">Aktif</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        <?php endif; ?>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="edit.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-warning" title="Edit"><i class="bi bi-pencil"></i></a>
                        <a href="hapus.php?id=<?= $d['id_user'] ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Hapus user <?= htmlspecialchars($d['nama_user']) ?>?')"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php include_once '../../includes/footer.php'; ?>