<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include "koneksi.php";

if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!'); window.location.href='login/login.php';</script>";
    exit();
}

$id_user = $_SESSION['id_user'];
$role = $_SESSION['role'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    function sanitize_input($data)
    {
        return trim(strip_tags($data));
    }
    $db = new Database();
    $conn = $db->getConnection();

    $username = sanitize_input($_POST['username']);
    $email = sanitize_input($_POST['email']);
    $password = sanitize_input($_POST['password']);
    $no_telp = sanitize_input($_POST['no_telp']);
    $role = sanitize_input($_POST['role']);
    $status = 'diverifikasi' ;

    $sql = "INSERT INTO users (username, email, password, no_telp, role, status) 
                    VALUES (:username, :email, :password, :no_telp, :role, :status)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->bindParam(':no_telp', $no_telp, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_STR);
            $stmt->bindParam(':role', $role, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo "<script>alert('Data berhasil ditambahkan!'); window.location.href='?page=pengguna_tampil';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan Data.');</script>";
            }
}
?>



<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Tambah Pengguna</h6>
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
                    <input type="text" class="form-control" name="username" placeholder="Masukkan Username" required >
                </div>
                <div class="form-group mb-2">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" placeholder="Masukkan Email" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Masukkan Password" required maxlength="200"></input>
                </div>
                <div class="form-group mb-2">
                    <label>No. Telp</label>
                    <input type="text" class="form-control" name="no_telp" placeholder="Masukkan No. Telp" required maxlength="200"></input>
                </div>
                <div class="form-group mb-3">
                    <label for="role">Role</label>
                    <select name="role" id="role" class="form-control" required>
                        <option value="">-- Pilih Role --</option>
                        <?php if ($_SESSION['role'] == 'superadmin') { ?>
                                <option value="superadmin">SuperAdmin</option>
                        <?php } ?>
                        <option value="admin">Admin</option>
                        <option value="umum">Tidak</option>
                    </select>
                </div>
                <!-- Tombol Simpan dan Batal -->
                <div class="mb-3">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="reset" class="btn btn-secondary">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>