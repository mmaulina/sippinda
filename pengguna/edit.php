<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

// Pastikan pengguna sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}


$id = $_GET['id_user'];
$role = $_SESSION['role']; // default fallback ke 'umum' jika tidak tersedia

$database = new Database();
$pdo = $database->getConnection();

// Ambil data bidang berdasarkan ID
$sql = "SELECT * FROM users WHERE id_user = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$id]);
$users = $stmt->fetch(PDO::FETCH_ASSOC);



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    function sanitize_input($users)
    {
        return trim(strip_tags($users));
    }

    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $no_telp = sanitize_input($_POST['no_telp']);
    $role = sanitize_input($_POST['role']);
    $status = sanitize_input($_POST['status']);

    $sql = "UPDATE users SET username = ?, email = ?, no_telp = ?, role = ?, status = ? WHERE id_user = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$username, $email, $no_telp, $role, $status, $id]);

    if ($success) {
        echo "<script>alert('data berhasil diperbarui!'); window.location.href='?page=pengguna_tampil';</script>";
    } else {
        echo "<script>alert('Gagal memperbarui data. Silakan coba lagi.');</script>";
    }
}
?>


<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Edit Pengguna</h6>
            <a href="?page=pengguna_tampil" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group mb-2">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required maxlength="200" value="<?php echo $users['username']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Masukkan Email" required maxlength="200" value="<?php echo $users['email']; ?>"></input>
                </div>
                <div class="form-group mb-2">
                    <label>No. Telp</label>
                    <input type="text" class="form-control" name="no_telp" placeholder="Masukkan No. Telp" required maxlength="200" value="<?php echo $users['no_telp']; ?>"></input>
                </div>
                <?php if ($_SESSION['role'] != 'umum') : ?>
                <div class="mb-3">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <?php if ($_SESSION['role'] == 'superadmin') { ?>
                            <option value="superadmin" <?= $users['role'] == 'superadmin' ? 'selected' : ''; ?>>SuperAdmin</option>
                        <?php } ?>
                        <option value="admin" <?= $users['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="umum" <?= $users['role'] == 'umum' ? 'selected' : ''; ?>>Umum</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="">-- Pilih Status --</option>
                        <option value="diajukan" <?= $users['status'] == 'diajukan' ? 'selected' : ''; ?>>Diajukan</option>
                        <option value="diverifikasi" <?= $users['status'] == 'diverifikasi' ? 'selected' : ''; ?>>Diverifikasi</option>
                        <option value="ditolak" <?= $users['status'] == 'ditolak' ? 'selected' : ''; ?>>Ditolak</option>
                    </select>
                </div>
                <?php endif; ?>
                <div class="mt-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>