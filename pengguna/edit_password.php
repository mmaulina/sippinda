<?php

// Buat instance database dan ambil koneksi
$database = new Database();
$conn = $database->getConnection();

// Mengambil nilai id_user di URL, lalu disimpan di $id_user
$id_user = isset($_GET['id_user']) ? intval($_GET['id_user']) : 0;

// Periksa jika tombol simpan diklik
if (isset($_POST['btn_simpan'])) {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];

    // Cek jika id_user valid
    if ($id_user > 0) {
        // Query untuk mendapatkan password lama
        $stmt = $conn->prepare("SELECT password FROM users WHERE id_user = :id_user");
        $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifikasi password lama dan update password baru
        if ($data && password_verify($password_lama, $data['password'])) {
            // Hash password baru menggunakan Bcrypt
            $hashed_password_baru = password_hash($password_baru, PASSWORD_BCRYPT);

            $stmt = $conn->prepare("UPDATE users SET password = :password WHERE id_user = :id_user");
            $stmt->bindParam(":password", $hashed_password_baru, PDO::PARAM_STR);
            $stmt->bindParam(":id_user", $id_user, PDO::PARAM_INT);
            $query = $stmt->execute();

            if ($query) {
                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Memperbarui Data";
                echo "<meta http-equiv='refresh' content='0;url=?page=home'>";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Memperbarui Data";
                echo "<meta http-equiv='refresh' content='0;url=?page=edit_password'>";
            }
        } else {
            echo "<script>alert('Password lama salah!');</script>";
            echo "<meta http-equiv='refresh' content='0;url=?page=edit_password'>";
        }
    }
}
?>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Ganti Password</h6>
            <a href="?page=home" class="btn btn-primary btn-icon-split btn-sm">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-left" style="vertical-align: middle; margin-top: 5px;"></i>
                </span>
                <span class="text">Kembali</span>
            </a>
        </div>
        <div class="card-body">
            <form action="" method="post">
                <div class="form-group mb-3">
                    <label for="inputPasswordLama" class="form-label">Password Lama</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="inputPasswordLama" name="password_lama" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword()">
                            <i class="bi bi-eye-slash" id="toggleIcon"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group mb-3">
                    <label for="inputPasswordBaru" class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="inputPasswordBaru" name="password_baru" required>
                        <span class="input-group-text" style="cursor: pointer;" onclick="togglePassword2()">
                            <i class="bi bi-eye-slash" id="toggleIcon2"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn btn-success" name="btn_simpan">Simpan Perubahan</button>
                <button type="reset" class="btn btn-secondary">Batal</button>
            </form>

        </div>
    </div>
</div>


<script>
    function togglePassword() {
        const passwordField = document.getElementById("inputPasswordLama");
        const toggleIcon = document.getElementById("toggleIcon");

        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        toggleIcon.classList.toggle("bi-eye");
        toggleIcon.classList.toggle("bi-eye-slash");
    }
</script>
<script>
    function togglePassword2() {
        const passwordField = document.getElementById("inputPasswordBaru");
        const toggleIcon = document.getElementById("toggleIcon2");

        const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
        passwordField.setAttribute("type", type);

        toggleIcon.classList.toggle("bi-eye");
        toggleIcon.classList.toggle("bi-eye-slash");
    }
</script>

<script src="../assets/js/bootstrap.bundle.min.js"></script>

</script>