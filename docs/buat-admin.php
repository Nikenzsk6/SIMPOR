<?php
/**
 * Membuat / mereset akun admin pertama.
 * HANYA bisa dijalankan dari terminal (CLI), tidak lewat browser.
 *
 * Pemakaian:
 *   php docs/buat-admin.php <username> <password> [nama] [role]
 *
 * Contoh:
 *   php docs/buat-admin.php admin "PasswordBaru123" "Administrator" admin
 *
 * Role yang valid: admin | pusat
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Skrip ini hanya boleh dijalankan dari terminal.');
}

if ($argc < 3) {
    echo "Pemakaian: php docs/buat-admin.php <username> <password> [nama] [role]\n";
    exit(1);
}

$username = trim($argv[1]);
$password = $argv[2];
$nama     = isset($argv[3]) ? trim($argv[3]) : $username;
$role     = isset($argv[4]) ? trim($argv[4]) : 'admin';

if ($username === '' || strlen($password) < 8) {
    echo "Username wajib diisi dan password minimal 8 karakter.\n";
    exit(1);
}

if (!in_array($role, ['admin', 'pusat'], true)) {
    echo "Role harus 'admin' atau 'pusat'.\n";
    exit(1);
}

require __DIR__ . '/../includes/config.php';

$hash = hash_password($password);

$stmt = mysqli_prepare($koneksi, "SELECT id_user FROM user WHERE username = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$hasil = mysqli_stmt_get_result($stmt);
$ada   = mysqli_fetch_assoc($hasil);
mysqli_stmt_close($stmt);

if ($ada) {
    $stmt = mysqli_prepare($koneksi, "UPDATE user SET nama_user = ?, password = ?, role = ? WHERE id_user = ?");
    mysqli_stmt_bind_param($stmt, 'sssi', $nama, $hash, $role, $ada['id_user']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "Akun '$username' sudah ada — password direset.\n";
} else {
    $stmt = mysqli_prepare($koneksi, "INSERT INTO user (nama_user, kontak, username, password, role) VALUES (?, NULL, ?, ?, ?)");
    mysqli_stmt_bind_param($stmt, 'ssss', $nama, $username, $hash, $role);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    echo "Akun '$username' berhasil dibuat dengan role '$role'.\n";
}
